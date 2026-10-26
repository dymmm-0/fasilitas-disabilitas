<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Antrean verifikasi gabungan: fasilitas dari basis data (termasuk baris dataset)
 * dan pengajuan masyarakat, plus keputusan verifikasi tingkat fasilitas.
 */
class VerificationQueueTest extends TestCase
{
    use RefreshDatabase;

    private ?User $admin = null;

    private int $sequence = 0;

    private function admin(): User
    {
        return $this->admin ??= User::factory()->create([
            'name' => 'Sari Verifikator',
            'email' => 'verifikator@akseskita.test',
        ]);
    }

    private function facility(array $overrides = []): Facility
    {
        $this->sequence++;

        return Facility::create($overrides + [
            'external_id' => 'AKS-'.str_pad((string) $this->sequence, 4, '0', STR_PAD_LEFT),
            'name' => 'Puskesmas Contoh '.$this->sequence,
            'category' => 'kesehatan',
            'source_category' => 'health_puskesmas_clinic',
            'city' => 'Kota Medan',
            'latitude' => 3.59,
            'longitude' => 98.67,
            'accessible_ramp_status' => 'yes',
            'accessible_toilet_status' => 'unknown',
            'accessible_elevator_status' => 'unknown',
            'accessible_pedestrian_pathway_status' => 'unknown',
            'accessible_entrance_status' => 'unknown',
            'verification_status' => 'pending',
            'source_name' => 'OpenStreetMap',
            'source_ref_type' => 'node',
            'source_ref_id' => '123',
            'data_completeness_percent' => 20,
        ]);
    }

    /**
     * Menjalankan job jeda antrean yang sudah jatuh tempo, supaya pengajuan berstatus
     * `menunggu` dan terlihat di antrean verifikasi seperti perilaku sebelum antrean.
     */
    private function drainQueue(): void
    {
        $this->travel(61)->seconds();
        $this->artisan('queue:work', ['--once' => true]);
        $this->travelBack();
    }

    private function koreksi(Facility $facility, string $field, string $to, string $by = 'Budi'): void
    {
        $this->postJson('/api/submissions', [
            'type' => 'koreksi',
            'facility_id' => $facility->external_id,
            'field' => $field,
            'to' => $to,
            'facility_name' => $facility->name,
            'category' => $facility->category,
            'city' => $facility->city,
            'submitted_by' => $by,
            'contact' => 'budi@contoh.test',
        ])->assertCreated();

        $this->drainQueue();
    }

    /** @return array<int, array<string, mixed>> */
    private function queue(array $query = []): array
    {
        return $this->actingAs($this->admin())
            ->getJson('/api/verification-queue?'.http_build_query($query))
            ->assertOk()
            ->json();
    }

    public function test_fasilitas_dataset_yang_belum_diverifikasi_masuk_antrean(): void
    {
        $this->facility();
        $this->facility(['verification_status' => 'verified', 'verified_at' => now(), 'verified_by' => 'Petugas Lama']);

        $queue = $this->queue();

        $this->assertSame(1, $queue['total']);
        $this->assertSame('fasilitas', $queue['items'][0]['kind']);
        $this->assertSame('AKS-0001', $queue['items'][0]['id']);
        $this->assertSame('menunggu', $queue['items'][0]['status']);
        $this->assertSame('Dataset OpenStreetMap', $queue['items'][0]['source']);
        $this->assertSame('yes', $queue['items'][0]['facility']['accessibility']['accessible_ramp']);
        $this->assertSame('unknown', $queue['items'][0]['facility']['accessibility']['accessible_toilet']);

        // Fasilitas yang sudah terverifikasi tidak dihitung lagi sebagai antrean.
        $this->assertSame(1, $queue['stats']['facilitiesPending']);
        $this->assertSame(1, $queue['stats']['facilitiesVerified']);
        $this->assertSame(1, $queue['stats']['datasetPending']);
        $this->assertSame(1, $queue['stats']['queueTotal']);
    }

    public function test_antrean_berhalaman_dan_tidak_menggandakan_baris(): void
    {
        $this->facility();
        $this->facility();
        $this->facility();

        $first = $this->queue(['per_page' => 2]);
        $second = $this->queue(['per_page' => 2, 'page' => 2]);

        $this->assertSame(3, $first['total']);
        $this->assertCount(2, $first['items']);
        $this->assertSame(2, $first['meta']['lastPage']);
        $this->assertCount(1, $second['items']);

        $ids = array_merge(
            array_column($first['items'], 'id'),
            array_column($second['items'], 'id'),
        );

        $this->assertCount(3, array_unique($ids));
    }

    public function test_penyaring_jenis_status_dan_pencarian(): void
    {
        $facility = $this->facility(['name' => 'Puskesmas Contoh 1']);
        $this->koreksi($facility, 'accessible_toilet', 'yes');

        $this->postJson('/api/submissions', [
            'type' => 'usulan',
            'facility_name' => 'Taman Kota Baru',
            'category' => 'rekreasi',
            'city' => 'Kota Medan',
            'submitted_by' => 'Rani',
            'contact' => 'rani@contoh.test',
            'address' => 'Jl. Contoh 1',
            'latitude' => 3.6,
            'longitude' => 98.68,
            'accessibility' => [
                'accessible_ramp' => 'yes', 'accessible_toilet' => 'unknown', 'accessible_elevator' => 'unknown',
                'accessible_pedestrian_pathway' => 'no', 'accessible_entrance' => 'yes',
                'tactile_guidance_path' => 'unknown', 'accessible_information_signage' => 'unknown',
                'accessible_parking' => 'unknown',
            ],
        ])->assertCreated();

        $this->drainQueue();

        $dataset = $this->queue(['bucket' => 'dataset']);
        $this->assertSame(['fasilitas'], array_column($dataset['items'], 'kind'));

        $usulan = $this->queue(['bucket' => 'usulan']);
        $this->assertSame(['usulan'], array_column($usulan['items'], 'kind'));

        $koreksi = $this->queue(['bucket' => 'koreksi']);
        $this->assertSame(['koreksi'], array_column($koreksi['items'], 'kind'));
        $this->assertSame('AKS-0001', $koreksi['items'][0]['submission']['facilityId']);

        // Fasilitas masih `pending`, jadi ia hanya muncul pada penyaring "menunggu".
        $this->assertSame(0, $this->queue(['bucket' => 'perbaikan'])['total']);
        $this->assertSame(3, $this->queue(['bucket' => 'menunggu'])['total']);
        $this->assertSame(0, $this->queue(['bucket' => 'selesai'])['total']);

        $search = $this->queue(['q' => 'Taman Kota']);
        $this->assertSame(1, $search['total']);
        $this->assertSame('SUB-0002', $search['items'][0]['id']);

        $byRef = $this->queue(['q' => 'AKS-0001']);
        $this->assertSame(1, $byRef['total']);
        $this->assertSame('AKS-0001', $byRef['items'][0]['id']);
    }

    public function test_tandai_terverifikasi_menyimpan_petugas_waktu_dan_riwayat(): void
    {
        $facility = $this->facility();

        $this->actingAs($this->admin())
            ->postJson('/api/facilities/AKS-0001/verification', [
                'action' => 'verified',
                'note' => 'Diperiksa langsung, ramp terpasang.',
            ])
            ->assertOk()
            ->assertJsonPath('facility.verification', 'terverifikasi')
            ->assertJsonPath('facility.verifiedBy', 'Sari Verifikator');

        $facility->refresh();

        $this->assertSame('verified', $facility->verification_status);
        $this->assertNotNull($facility->verified_at);
        $this->assertSame('Sari Verifikator', $facility->verified_by);
        $this->assertSame('pemeriksaan petugas', $facility->verification_method);
        $this->assertSame('Diperiksa langsung, ramp terpasang.', $facility->verification_note);

        // Keputusan tingkat fasilitas tidak mengubah indikator aksesibilitas.
        $this->assertSame('yes', $facility->accessible_ramp_status);
        $this->assertSame('unknown', $facility->accessible_toilet_status);
        $this->assertSame(20, $facility->data_completeness_percent);

        $this->assertDatabaseHas('verification_events', [
            'facility_id' => $facility->id,
            'submission_id' => null,
            'status_from' => 'pending',
            'status_to' => 'verified',
            'note' => 'Diperiksa langsung, ramp terpasang.',
            'actor' => 'Sari Verifikator',
        ]);

        // Tetap tersimpan setelah halaman dimuat ulang: tidak lagi di antrean, ada di riwayat.
        $queue = $this->queue();

        $this->assertSame(0, $queue['total']);
        $this->assertSame('AKS-0001', $queue['history'][0]['id']);
        $this->assertSame('fasilitas', $queue['history'][0]['kind']);
        $this->assertSame('terverifikasi', $queue['history'][0]['action']);
        $this->assertSame('Sari Verifikator', $queue['history'][0]['by']);

        $this->assertSame(1, $this->queue(['bucket' => 'selesai'])['total']);
    }

    public function test_perbaikan_dan_penolakan_fasilitas_wajib_bercatatan_dan_tidak_menghapus_baris(): void
    {
        $facility = $this->facility();

        foreach (['needs_repair', 'rejected'] as $action) {
            $this->actingAs($this->admin())
                ->postJson('/api/facilities/AKS-0001/verification', ['action' => $action])
                ->assertStatus(422)
                ->assertJsonValidationErrors('note');
        }

        $this->assertSame('pending', $facility->fresh()->verification_status);

        $this->actingAs($this->admin())
            ->postJson('/api/facilities/AKS-0001/verification', [
                'action' => 'needs_repair',
                'note' => 'Koordinat perlu dicek ulang.',
            ])
            ->assertOk()
            ->assertJsonPath('facility.verification', 'perlu-perbaikan');

        $facility->refresh();

        $this->assertSame('needs_repair', $facility->verification_status);
        $this->assertNull($facility->verified_at);
        $this->assertNull($facility->verified_by);
        $this->assertSame(1, Facility::count());

        // "Perlu perbaikan" tetap ada di antrean yang membutuhkan verifikasi.
        $this->assertSame(1, $this->queue()['total']);
        $this->assertSame('perlu-perbaikan', $this->queue()['items'][0]['status']);

        $this->actingAs($this->admin())
            ->postJson('/api/facilities/AKS-0001/verification', [
                'action' => 'rejected',
                'note' => 'Fasilitas tidak ada di lapangan.',
            ])
            ->assertOk()
            ->assertJsonPath('facility.verification', 'ditolak');

        // Ditolak bukan berarti baris fasilitas dihapus.
        $this->assertSame(1, Facility::count());
        $this->assertSame('rejected', $facility->fresh()->verification_status);
        $this->assertSame(0, $this->queue()['total']);
        $this->assertSame(1, $this->queue(['bucket' => 'selesai'])['total']);
        $this->assertSame(1, $this->queue()['stats']['facilitiesRejected']);
    }

    public function test_fasilitas_tidak_dapat_ditandai_terverifikasi_dua_kali(): void
    {
        $this->facility();

        $this->actingAs($this->admin())
            ->postJson('/api/facilities/AKS-0001/verification', ['action' => 'verified'])
            ->assertOk();

        $this->actingAs($this->admin())
            ->postJson('/api/facilities/AKS-0001/verification', ['action' => 'verified'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('action');

        $this->assertDatabaseCount('verification_events', 1);
    }

    public function test_guest_tidak_dapat_membaca_antrean_atau_memverifikasi_fasilitas(): void
    {
        $facility = $this->facility();

        $this->getJson('/api/verification-queue')->assertUnauthorized();
        $this->postJson('/api/facilities/AKS-0001/verification', ['action' => 'verified'])->assertUnauthorized();

        $this->assertSame('pending', $facility->fresh()->verification_status);
        $this->assertDatabaseCount('verification_events', 0);
    }

    public function test_koreksi_terhubung_ke_fasilitas_yang_tepat_tanpa_baris_ganda(): void
    {
        $facility = $this->facility();
        $this->facility();
        $this->koreksi($facility, 'accessible_toilet', 'yes');

        $queue = $this->queue();

        // Satu baris per fasilitas + satu baris per pengajuan: tidak ada penggandaan.
        $this->assertSame(3, $queue['total']);
        $this->assertSame(2, Facility::count());
        $this->assertDatabaseCount('submissions', 1);

        $items = collect($queue['items'])->keyBy('id');

        $this->assertSame([['id' => 'SUB-0001', 'status' => 'menunggu']], $items['AKS-0001']['pendingReports']);
        $this->assertSame([], $items['AKS-0002']['pendingReports']);
        $this->assertSame('AKS-0001', $items['SUB-0001']['submission']['facilityId']);
    }

    public function test_statistik_menghitung_setiap_record_sekali(): void
    {
        $facility = $this->facility();

        // Tiga laporan koreksi untuk satu fasilitas: fasilitasnya tetap terhitung satu.
        $this->koreksi($facility, 'accessible_toilet', 'yes', 'Budi');
        $this->koreksi($facility, 'accessible_elevator', 'no', 'Ani');
        $this->koreksi($facility, 'accessible_ramp', 'no', 'Cita');

        $stats = $this->queue()['stats'];

        $this->assertSame(1, $stats['facilitiesTotal']);
        $this->assertSame(1, $stats['facilitiesPending']);
        $this->assertSame(0, $stats['facilitiesNeedsRepair']);
        $this->assertSame(3, $stats['submissionsOpen']);
        $this->assertSame(0, $stats['submissionsDecided']);
        $this->assertSame(4, $stats['queueTotal']);
        $this->assertSame(0, $stats['communityPending']);
    }

    public function test_keputusan_atas_fasilitas_dan_pengajuan_muncul_di_riwayat_yang_sama(): void
    {
        $facility = $this->facility();
        $this->koreksi($facility, 'accessible_toilet', 'yes');

        $this->actingAs($this->admin())
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'disetujui'])
            ->assertOk();

        $this->actingAs($this->admin())
            ->postJson('/api/facilities/AKS-0001/verification', [
                'action' => 'verified',
                'note' => 'Toilet aksesibel dikonfirmasi.',
            ])
            ->assertOk();

        $history = $this->queue()['history'];

        $this->assertCount(2, $history);
        $this->assertSame(['fasilitas', 'koreksi'], array_column($history, 'kind'));
        $this->assertSame(['terverifikasi', 'disetujui'], array_column($history, 'action'));
        $this->assertSame(['AKS-0001', 'SUB-0001'], array_column($history, 'id'));

        // Koreksi yang disetujui tidak menandai fasilitasnya terverifikasi dengan sendirinya.
        $this->assertSame('verified', $facility->fresh()->verification_status);
        $this->assertSame(Submission::first()->createdFacility?->external_id, null);
    }
}
