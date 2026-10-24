<?php

namespace App\Jobs;

use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Jeda antrean: pengajuan tersimpan seketika dengan status `queued`, lalu job ini
 * memindahkannya ke `menunggu` setelah jeda sehingga baru muncul di meja verifikasi.
 * Idempoten — hanya baris yang masih `queued` yang dipindahkan, jadi percobaan ulang
 * atau dua job bersamaan tidak mengubah keputusan petugas yang sudah terlanjur masuk.
 */
class QueueSubmission implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Submission $submission) {}

    public function handle(): void
    {
        $this->moveToPending();
    }

    /**
     * Job gagal permanen tidak boleh membuat pengajuan tersangkut `queued` selamanya:
     * barisnya dilepas ke `menunggu` supaya tetap terlihat petugas.
     */
    public function failed(?Throwable $error): void
    {
        $this->moveToPending();
    }

    private function moveToPending(): void
    {
        $this->submission->newQuery()
            ->whereKey($this->submission->getKey())
            ->where('status', 'queued')
            ->update(['status' => 'menunggu']);
    }
}
