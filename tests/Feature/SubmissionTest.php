<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Sari Verifikator',
            'email' => 'verifikator@akseskita.test',
        ]);
    }

    private function facility(array $overrides = []): Facility
    {
        return Facility::create($overrides + [
            'external_id' => 'AKS-0009',
            'name' => 'Puskesmas Contoh',
            'category' => 'kesehatan',
            'source_category' => 'health_puskesmas_clinic',
            'city' => 'Kota Medan',
            'latitude' => 3.59,
            'longitude' => 98.67,
            'ramp_status' => 'yes',
            'toilet_status' => 'unknown',
            'lift_status' => 'unknown',
            'path_status' => 'unknown',
            'entrance_status' => 'unknown',
            'verification_status' => 'pending',
            'data_completeness_percent' => 20,
        ]);
    }

    /**
     * Menjalankan job jeda antrean yang sudah jatuh tempo, supaya pengajuan berstatus
     * `menunggu` dan bisa diuji seperti perilaku sebelum antrean ada.
     */
    private function drainQueue(): void
    {
        $this->travel(61)->seconds();
        $this->artisan('queue:work', ['--once' => true]);
        $this->travelBack();
    }

    private function usulan(array $overrides = []): array
    {
        return $overrides + [
            'type' => 'usulan',
            'facility_name' => 'Taman Kota Baru',
            'category' => 'rekreasi',
            'city' => 'Kota Medan',
            'submitted_by' => 'Rani',
            'contact' => 'rani@contoh.test',
            'summary' => 'Usulan taman kota.',
            'address' => 'Jl. Contoh 1',
            'latitude' => 3.6,
            'longitude' => 98.68,
            'accessibility' => [
                'ramp' => 'tersedia',
                'toilet' => 'belum',
                'lift' => 'belum',
                'path' => 'tidak',
                'entrance' => 'tersedia',
            ],
            'notes' => 'Ramp terpasang September 2026.',
        ];
    }

    public function test_usulan_masyarakat_tersimpan_dan_muncul_di_antrean_verifikasi(): void
    {
        $response = $this->postJson('/api/submissions', $this->usulan());

        $response->assertCreated()
            ->assertJsonPath('submission.id', 'SUB-0001')
            ->assertJsonPath('submission.status', 'queued')
            ->assertJsonPath('submission.payload.accessibility.toilet', 'belum')
            ->assertJsonPath('submission.createdFacilityId', null);

        $this->drainQueue();

        $this->assertDatabaseCount('submissions', 1);
        $this->assertDatabaseCount('verification_events', 0);
        $this->assertSame(0, Facility::count());

        $queue = $this->actingAs($this->admin())->getJson('/api/verification-queue');

        $queue->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('items.0.kind', 'usulan')
            ->assertJsonPath('items.0.id', 'SUB-0001')
            ->assertJsonPath('items.0.submittedBy', 'Rani')
            ->assertJsonPath('items.0.submission.proofs', [])
            ->assertJsonPath('history', []);
    }

    public function test_kiriman_ganda_dalam_sekitar_waktu_yang_sama_ditolak(): void
    {
        $this->postJson('/api/submissions', $this->usulan())->assertCreated();

        $this->postJson('/api/submissions', $this->usulan())
            ->assertStatus(422)
            ->assertJsonValidationErrors('facility_name');

        $this->assertDatabaseCount('submissions', 1);
    }

    public function test_laporan_koreksi_harus_menunjuk_fasilitas_dan_nilai_yang_berbeda(): void
    {
        $this->facility();

        $this->postJson('/api/submissions', [
            'type' => 'koreksi',
            'facility_id' => 'AKS-9999',
            'field' => 'ramp',
            'to' => 'tersedia',
            'facility_name' => 'Puskesmas Contoh',
            'category' => 'kesehatan',
            'city' => 'Kota Medan',
            'submitted_by' => 'Budi',
            'contact' => 'budi@contoh.test',
        ])->assertStatus(422)->assertJsonValidationErrors('facility_id');

        // `ramp_status` fasilitas ini sudah `yes` → tersedia.
        $this->postJson('/api/submissions', [
            'type' => 'koreksi',
            'facility_id' => 'AKS-0009',
            'field' => 'ramp',
            'to' => 'tersedia',
            'facility_name' => 'Puskesmas Contoh',
            'category' => 'kesehatan',
            'city' => 'Kota Medan',
            'submitted_by' => 'Budi',
            'contact' => 'budi@contoh.test',
        ])->assertStatus(422)->assertJsonValidationErrors('to');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_guest_tidak_dapat_membaca_antrean_atau_memutuskan(): void
    {
        $this->facility();
        $this->postJson('/api/submissions', $this->usulan())->assertCreated();

        $this->drainQueue();

        $this->getJson('/api/verification-queue')->assertUnauthorized();
        $this->postJson('/api/submissions/SUB-0001/decision', ['action' => 'disetujui'])->assertUnauthorized();

        $this->assertSame('menunggu', Submission::first()->status);
    }

    public function test_setujui_usulan_membuat_fasilitas_tanpa_mengklaim_terverifikasi(): void
    {
        $this->facility();
        $this->postJson('/api/submissions', $this->usulan())->assertCreated();

        $this->drainQueue();

        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->postJson('/api/submissions/SUB-0001/decision', [
                'action' => 'disetujui',
                'note' => 'Foto dan koordinat sesuai.',
            ]);

        $response->assertOk()
            ->assertJsonPath('submission.status', 'disetujui')
            ->assertJsonPath('submission.decidedBy', 'Sari Verifikator')
            ->assertJsonPath('submission.createdFacilityId', 'AKS-0010');

        $facility = Facility::firstWhere('external_id', 'AKS-0010');

        $this->assertSame('Taman Kota Baru', $facility->name);
        $this->assertSame('rekreasi', $facility->category);
        $this->assertSame('Kota Medan', $facility->city);
        $this->assertSame('yes', $facility->ramp_status);
        $this->assertSame('no', $facility->path_status);
        // `belum` tetap unknown, bukan "tidak tersedia".
        $this->assertSame('unknown', $facility->toilet_status);
        $this->assertSame('unknown', $facility->lift_status);
        // Klaim pengaju belum diperiksa di lapangan.
        $this->assertSame('pending', $facility->verification_status);
        $this->assertSame(60, $facility->data_completeness_percent);
        $this->assertSame('SUB-0001', $facility->source_ref_id);

        $submission = Submission::first();
        $this->assertNotNull($submission->decided_at);
        $this->assertNotNull($submission->created_facility_id);

        $this->assertDatabaseHas('verification_events', [
            'submission_id' => $submission->id,
            'status_from' => 'menunggu',
            'status_to' => 'disetujui',
            'note' => 'Foto dan koordinat sesuai.',
            'actor' => 'Sari Verifikator',
        ]);

        // Riwayat tetap terbaca setelah halaman dimuat ulang.
        $this->actingAs($admin)
            ->getJson('/api/verification-queue')
            ->assertJsonPath('history.0.id', 'SUB-0001')
            ->assertJsonPath('history.0.kind', 'usulan')
            ->assertJsonPath('history.0.action', 'disetujui')
            ->assertJsonPath('history.0.by', 'Sari Verifikator');
    }

    public function test_tolak_usulan_wajib_bercatatan_dan_tidak_membuat_fasilitas(): void
    {
        $this->facility();
        $this->postJson('/api/submissions', $this->usulan())->assertCreated();

        $this->drainQueue();

        $admin = $this->admin();

        $this->actingAs($admin)
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'ditolak'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('note');

        $this->assertSame('menunggu', Submission::first()->status);

        $this->actingAs($admin)
            ->postJson('/api/submissions/SUB-0001/decision', [
                'action' => 'ditolak',
                'note' => 'Koordinat menunjuk lahan kosong.',
            ])
            ->assertOk()
            ->assertJsonPath('submission.status', 'ditolak');

        $this->assertSame(1, Facility::count());
        $this->assertDatabaseHas('verification_events', [
            'status_to' => 'ditolak',
            'note' => 'Koordinat menunjuk lahan kosong.',
        ]);
    }

    public function test_minta_perbaikan_menyimpan_catatan_tanpa_membuat_fasilitas(): void
    {
        $this->facility();
        $this->postJson('/api/submissions', $this->usulan())->assertCreated();

        $this->drainQueue();

        $this->actingAs($this->admin())
            ->postJson('/api/submissions/SUB-0001/decision', [
                'action' => 'perbaikan',
                'note' => 'Lampirkan foto tampak depan.',
            ])
            ->assertOk()
            ->assertJsonPath('submission.status', 'perbaikan');

        $this->assertSame(1, Facility::count());
        $this->assertNull(Submission::first()->created_facility_id);
    }

    public function test_setujui_koreksi_menerapkan_perubahan_pada_fasilitas(): void
    {
        $facility = $this->facility();

        $this->postJson('/api/submissions', [
            'type' => 'koreksi',
            'facility_id' => 'AKS-0009',
            'field' => 'toilet',
            'to' => 'tersedia',
            'facility_name' => 'Puskesmas Contoh',
            'category' => 'kesehatan',
            'city' => 'Kota Medan',
            'submitted_by' => 'Budi',
            'contact' => 'budi@contoh.test',
            'summary' => 'Toilet aksesibel sudah dipasang.',
        ])->assertCreated()->assertJsonPath('submission.payload.from', 'belum');

        $this->drainQueue();

        $this->actingAs($this->admin())
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'disetujui'])
            ->assertOk();

        $this->assertSame('yes', $facility->fresh()->toilet_status);
        // Fasilitas tidak berubah menjadi "terverifikasi" hanya karena satu koreksi.
        $this->assertSame('pending', $facility->fresh()->verification_status);
    }

    public function test_tolak_koreksi_tidak_mengubah_fasilitas(): void
    {
        $facility = $this->facility();

        $this->postJson('/api/submissions', [
            'type' => 'koreksi',
            'facility_id' => 'AKS-0009',
            'field' => 'toilet',
            'to' => 'tersedia',
            'facility_name' => 'Puskesmas Contoh',
            'category' => 'kesehatan',
            'city' => 'Kota Medan',
            'submitted_by' => 'Budi',
            'contact' => 'budi@contoh.test',
        ])->assertCreated();

        $this->drainQueue();

        $this->actingAs($this->admin())
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'ditolak', 'note' => 'Bukti tidak jelas.'])
            ->assertOk();

        $this->assertSame('unknown', $facility->fresh()->toilet_status);
        $this->assertSame(1, Facility::count());
    }

    public function test_pengajuan_yang_sudah_diputuskan_tidak_dapat_diputuskan_lagi(): void
    {
        $this->facility();
        $this->postJson('/api/submissions', $this->usulan())->assertCreated();

        $this->drainQueue();

        $admin = $this->admin();

        $this->actingAs($admin)
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'disetujui'])
            ->assertOk();

        $this->actingAs($admin)
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'ditolak', 'note' => 'Batal.'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('action');

        $this->assertSame('disetujui', Submission::first()->status);
        $this->assertDatabaseCount('verification_events', 1);
    }

    public function test_bukti_pendukung_tersimpan_privat_dan_hanya_untuk_petugas(): void
    {
        $this->postJson('/api/submissions', $this->usulan() + [
            'proof' => UploadedFile::fake()->image('tampak-depan.jpg'),
        ])->assertCreated()->assertJsonPath('submission.proofs.0.caption', 'tampak-depan.jpg');

        $proof = Submission::first()->proofs()->firstOrFail();

        Storage::disk('local')->assertExists($proof->path);
        $this->assertSame('image/jpeg', $proof->mime);

        $this->get('/api/submissions/SUB-0001/proofs/'.$proof->id)->assertUnauthorized();

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/api/submissions/SUB-0001/proofs/'.$proof->id)
            ->assertOk()
            ->assertHeader('content-type', 'image/jpeg');

        // Bukti milik pengajuan lain tidak dapat diakses lewat id pengajuan ini.
        $this->actingAs($admin)
            ->get('/api/submissions/SUB-9999/proofs/'.$proof->id)
            ->assertNotFound();
    }

    public function test_susunan_permintaan_yang_tidak_lengkap_ditolak_dengan_pesan_validasi(): void
    {
        $this->postJson('/api/submissions', [
            'type' => 'usulan',
            'facility_name' => '',
            'category' => 'kategori-ngawur',
            'city' => 'Kota Medan',
            'submitted_by' => 'Rani',
            'contact' => 'bukan-surel',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['facility_name', 'category', 'contact', 'address', 'accessibility.ramp']);

        $this->assertDatabaseCount('submissions', 0);
    }
}
