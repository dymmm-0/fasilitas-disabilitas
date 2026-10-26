<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman status guest: pengajuan dicari lewat surel pengaju, karena nama boleh sama.
 * Hanya field non-sensitif yang boleh keluar dari endpoint publik ini.
 */
class SubmissionStatusTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    private function submission(array $overrides = []): Submission
    {
        $this->sequence++;

        return Submission::create($overrides + [
            'public_id' => 'SUB-'.str_pad((string) $this->sequence, 4, '0', STR_PAD_LEFT),
            'type' => 'usulan',
            'facility_name' => 'Taman Kota Baru '.$this->sequence,
            'category' => 'rekreasi',
            'city' => 'Kota Medan',
            'submitted_by' => 'Rani',
            'contact' => 'rani@contoh.test',
            'summary' => 'Ringkasan rahasia.',
            'payload' => ['lat' => 3.6, 'lng' => 98.68, 'notes' => 'Catatan pribadi.'],
            'status' => 'menunggu',
        ]);
    }

    private function lookup(array $query)
    {
        return $this->getJson('/api/submissions/status?'.http_build_query($query));
    }

    public function test_pengajuan_ditemukan_lewat_surel_tanpa_login(): void
    {
        $this->submission();
        $this->submission(['submitted_by' => 'Budi', 'contact' => 'budi@contoh.test']);

        $response = $this->lookup(['email' => 'RANI@Contoh.Test']);

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('submissions.0.id', 'SUB-0001')
            ->assertJsonPath('submissions.0.status', 'menunggu')
            ->assertJsonPath('submissions.0.submittedBy', null);
    }

    public function test_nama_sama_dengan_surel_berbeda_dipisahkan(): void
    {
        $this->submission(['submitted_by' => 'Budi', 'contact' => 'budi.satu@contoh.test']);
        $this->submission(['submitted_by' => 'Budi', 'contact' => 'budi.dua@contoh.test']);

        $this->lookup(['email' => 'budi.satu@contoh.test'])->assertJsonPath('total', 1);
        $this->lookup(['email' => 'budi.dua@contoh.test'])->assertJsonPath('total', 1);
    }

    public function test_surel_sama_dengan_nama_berbeda_tetap_satu_daftar(): void
    {
        $this->submission(['submitted_by' => 'Rani']);
        $this->submission(['submitted_by' => 'Rani Sitompul']);

        $this->lookup(['email' => 'rani@contoh.test'])
            ->assertJsonPath('total', 2)
            ->assertJsonPath('submissions.0.submittedBy', null);
    }

    public function test_nama_opsional_menyaring_hasil(): void
    {
        $this->submission(['submitted_by' => 'Rani']);
        $this->submission(['submitted_by' => 'Budi']);

        $this->lookup(['email' => 'rani@contoh.test', 'name' => 'Budi'])->assertJsonPath('total', 1);
        $this->lookup(['email' => 'rani@contoh.test', 'name' => 'Tidak Ada'])->assertJsonPath('total', 0);
    }

    public function test_surel_tidak_dikenal_mengembalikan_daftar_kosong(): void
    {
        $this->submission();

        $this->lookup(['email' => 'tidak.ada@contoh.test'])
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonPath('submissions', []);
    }

    public function test_surel_wajib_dan_harus_valid(): void
    {
        $this->lookup([])->assertStatus(422)->assertJsonValidationErrors('email');
        $this->lookup(['email' => 'bukan-surel'])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_field_sensitif_tidak_pernah_dikirim(): void
    {
        $facility = Facility::create([
            'external_id' => 'AKS-0001',
            'name' => 'Puskesmas Contoh',
            'category' => 'kesehatan',
            'source_category' => 'health_puskesmas_clinic',
            'city' => 'Kota Medan',
            'latitude' => 3.59,
            'longitude' => 98.67,
            'verification_status' => 'pending',
        ]);

        $this->submission([
            'type' => 'koreksi',
            'facility_id' => $facility->id,
            'payload' => ['field' => 'accessible_toilet', 'from' => 'unknown', 'to' => 'yes'],
        ]);

        $item = $this->lookup(['email' => 'rani@contoh.test'])->json('submissions.0');

        $this->assertSame(
            ['id', 'type', 'facilityName', 'category', 'city', 'status', 'submittedAt', 'decidedAt', 'readyAt', 'facilityId', 'createdFacilityId'],
            array_keys($item),
        );
        $this->assertSame('AKS-0001', $item['facilityId']);
    }

    public function test_pengajuan_dalam_antrean_menyertakan_waktu_proses(): void
    {
        $this->submission(['status' => 'queued']);

        $item = $this->lookup(['email' => 'rani@contoh.test'])->json('submissions.0');

        $this->assertSame('queued', $item['status']);
        $this->assertNotNull($item['readyAt']);
    }

    public function test_daftar_dibatasi_lima_puluh_pengajuan_terbaru(): void
    {
        for ($i = 0; $i < 51; $i++) {
            $this->submission();
        }

        $this->lookup(['email' => 'rani@contoh.test'])->assertJsonPath('total', 50);
    }
}
