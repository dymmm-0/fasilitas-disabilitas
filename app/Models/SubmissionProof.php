<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Bukti pendukung pengajuan. Berkas privat di disk `local`, bukan URL publik. */
#[Fillable(['submission_id', 'disk', 'path', 'original_name', 'mime', 'size'])]
class SubmissionProof extends Model
{
    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
