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
        $this->assertSame('yes', $osm->path_status);
        $this->assertSame('yes', $osm->entrance_status);
        $this->assertSame(40, $osm->data_completeness_percent);
        $this->assertStringContainsString('bukan pemeriksaan 5 atribut', $osm->path_details);
        $this->assertSame('pending', $osm->verification_status);

        // Kategori penginapan + bukti verifikasi yang sudah ada tidak dihapus.
        $hotel = Facility::firstWhere('external_id', 'AKS-9005');
        $this->assertSame('penginapan', $hotel->category);
        $this->assertSame('hotel_accommodation', $hotel->source_category);
        $this->assertSame('verified', $hotel->verification_status);
        $this->assertSame('https://example.test/bukti.pdf', $hotel->evidence_url);
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

    public function test_dataset_berkas_penuh_dapat_diimpor(): void
    {
        $this->artisan('facilities:import', ['file' => 'Dataset/akseskita_facilities_medan.csv'])
            ->assertExitCode(0);

        $this->assertSame(1117, Facility::count());
        $this->assertSame(0, Facility::where('verification_status', '!=', 'pending')->count());
        $this->assertSame(8, Facility::where('path_status', 'yes')->count());
        $this->assertSame(4, Facility::where('path_status', 'no')->count());
    }

    public function test_api_fasilitas_mengirim_bentuk_yang_dipakai_frontend(): void
    {
        $this->artisan('facilities:import', ['file' => self::FIXTURE]);

        $response = $this->getJson('/api/facilities');

        $response->assertOk()->assertJsonPath('total', self::VALID_ROWS);

        $response->assertJsonPath('facilities.0.id', 'AKS-9001');
        $response->assertJsonPath('facilities.0.category', 'kesehatan');
        // Belum ada bukti bukan berarti tidak tersedia.
        $response->assertJsonPath('facilities.0.accessibility.ramp', 'belum');
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
}
