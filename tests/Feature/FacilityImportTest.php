<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FacilityImportTest extends TestCase
{
    use RefreshDatabase;

    private const FIXTURE = 'tests/Fixtures/facilities.csv';

    private const VALID_ROWS = 5;

    private const INVALID_ROWS = 4;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_impor_memvalidasi_baris_dan_melaporkan_yang_gagal(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE])
            ->expectsOutputToContain('Laporan validasi')
            ->assertExitCode(1);

        $this->assertSame(self::VALID_ROWS, Facility::count());

        // Cacat format dataset: seluruh baris terbungkus tanda kutip.
        $wrapped = Facility::firstWhere('external_id', 'AKS-9002');
        $this->assertSame('RSU Terbungkus', $wrapped->name);
        $this->assertSame('Jalan Contoh 2, No. 3', $wrapped->address);

        // Cacat format dataset: tanda kutip nyasar sebelum id dan di dalam catatan.
        $stray = Facility::firstWhere('external_id', 'AKS-9003');
        $this->assertSame('Dr. Contoh, Sp.Kk.', $stray->name);
        $this->assertSame('kesehatan', $stray->category);
        $this->assertSame('Babura', $stray->village);
        $this->assertStringContainsString('lengkapi dari sumber resmi', $stray->notes);

        // Sinyal aksesibilitas OSM tetap tersimpan beserta keterangannya.
        $osm = Facility::firstWhere('external_id', 'AKS-9004');
        $this->assertSame('yes', $osm->accessible_pedestrian_pathway_status);
        $this->assertSame('yes', $osm->accessible_entrance_status);
        $this->assertSame(40, $osm->data_completeness_percent);
        $this->assertStringContainsString('bukan pemeriksaan 5 atribut', $osm->accessible_pedestrian_pathway_details);
        $this->assertSame('pending', $osm->verification_status);

        // Kategori penginapan + bukti verifikasi yang sudah ada tidak dihapus.
        $hotel = Facility::firstWhere('external_id', 'AKS-9005');
        $this->assertSame('penginapan', $hotel->category);
        $this->assertSame('hotel_accommodation', $hotel->source_category);
        $this->assertSame('verified', $hotel->verification_status);
        $this->assertSame('https://example.test/bukti.pdf', $hotel->evidence_url);
    }

    public function test_sinkronisasi_mencocokkan_source_ref_saat_facility_id_csv_berubah(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        $lines = file(self::FIXTURE, FILE_IGNORE_NEW_LINES);
        $this->assertIsArray($lines);
        $csv = implode(PHP_EOL, array_slice($lines, 0, 2)).PHP_EOL;
        $csv = str_replace('AKS-9001,RSU Contoh,health_hospital,', 'AKS-9010,RSU Contoh,health_hospital,', $csv, $replacements);
        $this->assertSame(1, $replacements);

        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])
                ->assertExitCode(0);
        } finally {
            unlink($path);
        }

        $this->assertSame(5, Facility::count());
        $this->assertDatabaseHas('facilities', [
            'external_id' => 'AKS-9001',
            'source_ref_type' => 'way',
            'source_ref_id' => '111',
        ]);
        $this->assertDatabaseMissing('facilities', ['external_id' => 'AKS-9010']);
    }

    public function test_impor_ulang_tidak_menduplikasi_dan_tidak_menimpa_data_terverifikasi(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);
        $this->assertSame(self::VALID_ROWS, Facility::count());

        Facility::firstWhere('external_id', 'AKS-9001')->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'notes' => 'Hasil survei lapangan.',
        ]);

        $this->artisan('facilities:import', ['file' => self::FIXTURE])->assertExitCode(1);

        $this->assertSame(self::VALID_ROWS, Facility::count());

        $verified = Facility::firstWhere('external_id', 'AKS-9001');
        $this->assertSame('verified', $verified->verification_status);
        $this->assertSame('Hasil survei lapangan.', $verified->notes);
    }

    public function test_dataset_berkas_penuh_memakai_kategori_baru_tanpa_baris_gagal(): void
    {
        // Dataset tidak ikut repo (lihat .gitignore), jadi di clone baru test ini di-skip.
        if (! is_file(base_path('Dataset/akseskita_facilities_medan.csv'))) {
            $this->markTestSkipped('Dataset/akseskita_facilities_medan.csv tidak tersedia.');
        }

        $this->artisan('facilities:import', ['file' => 'Dataset/akseskita_facilities_medan.csv'])
            ->assertExitCode(0);

        $this->assertSame(1104, Facility::count());
        $this->assertSame(2, Facility::where('category', 'ibadah')->count());
        $this->assertSame(10, Facility::where('category', 'olahraga')->count());
        $this->assertSame(1, Facility::where('category', 'perbankan')->count());
        $this->assertSame(11, Facility::where('category', 'pengiriman')->count());
        $this->assertSame(0, Facility::where('verification_status', '!=', 'pending')->count());
        $this->assertSame(8, Facility::where('accessible_pedestrian_pathway_status', 'yes')->count());
        $this->assertSame(4, Facility::where('accessible_pedestrian_pathway_status', 'no')->count());
        $this->assertSame('perbankan', Facility::firstWhere('external_id', 'AKS-1097')->category);
    }

    public function test_sinkronisasi_mempertahankan_source_id_dan_mengabaikan_id_csv(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);
        $csv = file(self::FIXTURE, FILE_IGNORE_NEW_LINES);
        $this->assertIsArray($csv);
        $csv = implode(PHP_EOL, array_slice($csv, 0, 2)).PHP_EOL;
        $csv = str_replace('AKS-9001,RSU Contoh,health_hospital,', 'AKS-9010,RSU Contoh,health_hospital,', $csv, $replacements);
        $this->assertSame(1, $replacements);
        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])->assertExitCode(0);
        } finally {
            unlink($path);
        }

        $this->assertDatabaseHas('facilities', ['external_id' => 'AKS-9001', 'source_ref_id' => '111']);
        $this->assertDatabaseMissing('facilities', ['external_id' => 'AKS-9010']);
    }

    public function test_sinkronisasi_melewati_kategori_belum_dipetakan_tanpa_membatalkan_baris_lain(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        // Detail tanpa kategori aplikasi; hotel di baris lain tetap harus tersinkron.
        $csv = str_replace(
            ['health_hospital', 'hotel_accommodation'],
            ['other_public_facility', 'other_public_facility'],
            (string) file_get_contents(self::FIXTURE),
            $replacements,
        );
        $this->assertSame(6, $replacements);
        $csv = str_replace(
            ['other_public_facility,hospital', 'other_public_facility,tourism_hotel'],
            ['other_public_facility,community_centre', 'other_public_facility,place_of_worship'],
            $csv,
        );

        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])
                ->expectsOutputToContain('other_public_facility/community_centre')
                ->assertExitCode(0);
        } finally {
            unlink($path);
        }

        $this->assertSame(self::VALID_ROWS, Facility::count());
        $this->assertSame('ibadah', Facility::firstWhere('external_id', 'AKS-9005')->category);
        $this->assertSame('kesehatan', Facility::firstWhere('external_id', 'AKS-9001')->category);
    }

    public function test_sinkronisasi_hanya_menulis_kolom_kategori(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        // CSV membawa koordinat, atribut aksesibilitas, dan status verifikasi berbeda.
        $csv = str_replace(
            ['AKS-9001,RSU Contoh,health_hospital', 'Jalan Contoh 1,Petisah Tengah,Medan Petisah,Kota Medan,Sumatera Utara,20112,3.5900000,98.6700000'],
            ['AKS-9001,RSU Contoh,government_public_service', 'Jalan Berbeda,Petisah Tengah,Medan Petisah,Kota Medan,Sumatera Utara,20112,3.5999999,98.6799999'],
            (string) file_get_contents(self::FIXTURE),
            $replacements,
        );
        $this->assertGreaterThan(0, $replacements);
        $csv = str_replace(',2026-10-21,unknown,', ',2026-10-21,yes,', $csv);
        $csv = str_replace('pending,,,,0,', 'rejected,,,,0,', $csv);

        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])->assertExitCode(0);
        } finally {
            unlink($path);
        }

        $facility = Facility::firstWhere('external_id', 'AKS-9001');
        $this->assertSame('pemerintahan', $facility->category);
        $this->assertSame('government_public_service', $facility->source_category);
        $this->assertSame(3.59, $facility->latitude);
        $this->assertSame(98.67, $facility->longitude);
        $this->assertSame('unknown', $facility->accessible_ramp_status);
        $this->assertSame('pending', $facility->verification_status);
        $this->assertSame('RSU Contoh', $facility->name);
    }

    public function test_sinkronisasi_menolak_ref_sumber_duplikat(): void
    {
        $csv = (string) file_get_contents(self::FIXTURE);
        $csv .= 'AKS-9010,RSU Duplikat,health_hospital,hospital,Jalan Duplikat,Petisah Tengah,Medan Petisah,Kota Medan,Sumatera Utara,20112,3.5950000,98.6750000,,way,111,OpenStreetMap,https://www.openstreetmap.org/way/111,2026-10-21,unknown,,unknown,,unknown,,unknown,,unknown,,no,,pending,,,,0,\n';
        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])->assertExitCode(1);
        } finally {
            unlink($path);
        }

        $this->assertSame(0, Facility::count());
    }

    public function test_sinkronisasi_menolak_source_identity_kosong(): void
    {
        $csv = str_replace(',,way,111,OpenStreetMap,', ',,,111,OpenStreetMap,', (string) file_get_contents(self::FIXTURE), $replacements);
        $this->assertGreaterThan(0, $replacements);
        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])->assertExitCode(1);
        } finally {
            unlink($path);
        }

        $this->assertSame(0, Facility::count());
    }

    public function test_sinkronisasi_menolak_facility_id_csv_duplikat(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);
        $csv = str_replace('AKS-9002,RSU Terbungkus', 'AKS-9001,RSU Terbungkus', (string) file_get_contents(self::FIXTURE), $replacements);
        $this->assertSame(1, $replacements);
        $path = tempnam(sys_get_temp_dir(), 'facilities-');
        $this->assertNotFalse($path);
        file_put_contents($path, $csv);

        try {
            $this->artisan('facilities:import', ['file' => $path, '--sync' => true])->assertExitCode(1);
        } finally {
            unlink($path);
        }

        $this->assertSame(self::VALID_ROWS, Facility::count());
    }

    public function test_api_fasilitas_mengirim_bentuk_yang_dipakai_frontend(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        $response = $this->getJson('/api/facilities');

        $response->assertOk()->assertJsonPath('total', self::VALID_ROWS);

        $response->assertJsonPath('facilities.0.id', 'AKS-9001');
        $response->assertJsonPath('facilities.0.category', 'kesehatan');
        // Belum ada bukti bukan berarti tidak tersedia.
        $response->assertJsonPath('facilities.0.accessibility.accessible_ramp', 'unknown');
        $response->assertJsonPath('facilities.0.verification', 'menunggu');
        $response->assertJsonPath('facilities.0.city', 'Kota Medan');
        $response->assertJsonPath('facilities.0.source.name', 'OpenStreetMap');

        $this->assertIsFloat($response->json('facilities.0.lat'));
        $this->assertCount(self::VALID_ROWS, $response->json('facilities'));
    }

    public function test_api_fasilitas_melayani_satu_halaman_untuk_tabel_admin(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        $first = $this->getJson('/api/facilities?page=1&per_page=2');
        $first->assertOk()
            ->assertJsonPath('total', self::VALID_ROWS)
            ->assertJsonPath('meta.currentPage', 1)
            ->assertJsonPath('meta.lastPage', 3)
            ->assertJsonPath('meta.perPage', 2)
            ->assertJsonPath('meta.from', 1)
            ->assertJsonPath('meta.to', 2);
        $this->assertCount(2, $first->json('facilities'));

        // Halaman terakhir tidak penuh, dan tidak ada baris yang muncul dua kali.
        $last = $this->getJson('/api/facilities?page=3&per_page=2');
        $last->assertJsonPath('meta.from', 5)->assertJsonPath('meta.to', 5);
        $this->assertCount(1, $last->json('facilities'));

        $ids = array_merge(
            array_column($first->json('facilities'), 'id'),
            array_column($last->json('facilities'), 'id'),
        );
        $this->assertSame($ids, array_unique($ids));

        // per_page di luar batas tetap dilayani, bukan error.
        $this->getJson('/api/facilities?page=1&per_page=999')
            ->assertOk()
            ->assertJsonPath('meta.perPage', 100)
            ->assertJsonPath('meta.lastPage', 1);
    }

    public function test_api_publik_menyembunyikan_fasilitas_yang_ditolak(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        Facility::firstWhere('external_id', 'AKS-9001')->update(['verification_status' => 'rejected']);

        $response = $this->getJson('/api/facilities');
        $response->assertOk()->assertJsonPath('total', self::VALID_ROWS - 1);

        $ids = array_column($response->json('facilities'), 'id');
        $this->assertNotContains('AKS-9001', $ids);
        // Baris lain tetap tampil, termasuk yang sudah terverifikasi.
        $this->assertContains('AKS-9005', $ids);

        // Pagination dihitung dari hasil tersaring, bukan seluruh tabel.
        $this->getJson('/api/facilities?page=1&per_page=999')
            ->assertOk()
            ->assertJsonPath('total', self::VALID_ROWS - 1);
    }
}
