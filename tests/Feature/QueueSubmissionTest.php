<?php

namespace Tests\Feature;

use App\Jobs\QueueSubmission;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

/**
 * Jeda antrean pengajuan: pengajuan baru disimpan sebagai `queued`, job memindahkannya
 * ke `menunggu` setelah jeda, dan selama `queued` ia belum masuk meja verifikasi.
 */
class QueueSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function submission(array $overrides = []): Submission
    {
        return Submission::create($overrides + [
            'public_id' => 'SUB-0001',
            'type' => 'usulan',
            'facility_name' => 'Taman Kota Baru',
            'category' => 'rekreasi',
            'city' => 'Kota Medan',
            'submitted_by' => 'Rani',
            'contact' => 'rani@contoh.test',
            'payload' => ['lat' => 3.6, 'lng' => 98.68],
            'status' => 'queued',
        ]);
    }

    public function test_pengajuan_baru_masuk_antrean_dengan_jeda(): void
    {
        Queue::fake();

        $this->postJson('/api/submissions', [
            'type' => 'usulan',
            'facility_name' => 'Taman Kota Baru',
            'category' => 'rekreasi',
            'city' => 'Kota Medan',
            'submitted_by' => 'Rani',
            'contact' => 'Rani@Contoh.Test',
            'address' => 'Jl. Contoh 1',
            'latitude' => 3.6,
            'longitude' => 98.68,
            'accessibility' => [
                'ramp' => 'tersedia', 'toilet' => 'belum', 'lift' => 'belum', 'path' => 'tidak', 'entrance' => 'tersedia',
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('submission.status', 'queued')
            ->assertJsonPath('submission.contact', 'rani@contoh.test')
            ->assertJsonStructure(['queue' => ['readyAt']]);

        Queue::assertPushed(QueueSubmission::class, function (QueueSubmission $job) {
            return $job->submission->status === 'queued'
                && $job->delay !== null
                && $job->delay->greaterThan(now()->addSeconds(30));
        });

        // Surel tersimpan terormalisasi: kunci identitas pengaju guest.
        $this->assertSame('rani@contoh.test', Submission::first()->contact);
    }

    public function test_job_memindahkan_pengajuan_ke_meja_verifikasi(): void
    {
        $submission = $this->submission();

        (new QueueSubmission($submission))->handle();

        $this->assertSame('menunggu', $submission->fresh()->status);
    }

    public function test_job_tidak_mengubah_pengajuan_yang_sudah_diputuskan(): void
    {
        $submission = $this->submission(['status' => 'disetujui']);

        (new QueueSubmission($submission))->handle();

        $this->assertSame('disetujui', $submission->fresh()->status);
    }

    public function test_job_gagal_tidak_membiarkan_pengajuan_tersangkut(): void
    {
        $submission = $this->submission();

        (new QueueSubmission($submission))->failed(new RuntimeException('pekerja gagal'));

        $this->assertSame('menunggu', $submission->fresh()->status);
    }

    public function test_pengajuan_dalam_antrean_tidak_muncul_di_meja_verifikasi(): void
    {
        $this->submission();

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->getJson('/api/verification-queue')
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonPath('stats.submissionsOpen', 0)
            ->assertJsonPath('stats.submissionsQueued', 1);

        // Belum masuk meja, jadi keputusan ditolak server.
        $this->actingAs($admin)
            ->postJson('/api/submissions/SUB-0001/decision', ['action' => 'disetujui'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('action');
    }
}
