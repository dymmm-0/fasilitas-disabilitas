<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris per keputusan verifikasi: riwayat audit keputusan atas pengajuan
 * warga (`submission_id`) maupun atas fasilitas (`facility_id`).
 */
#[Fillable(['submission_id', 'facility_id', 'status_from', 'status_to', 'note', 'actor'])]
class VerificationEvent extends Model
{
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
