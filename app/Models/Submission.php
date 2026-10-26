<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'public_id', 'type', 'facility_id', 'facility_name', 'category', 'city',
    'submitted_by', 'contact', 'summary', 'payload', 'status',
    'decided_by', 'decided_at', 'created_facility_id',
])]
class Submission extends Model
{
    public const TYPES = ['usulan', 'koreksi'];

    public const STATUSES = ['queued', 'menunggu', 'disetujui', 'perbaikan', 'ditolak'];

    /** Status akhir: keputusan tidak boleh diubah lagi. */
    public const FINAL_STATUSES = ['disetujui', 'ditolak'];

    /** Status yang boleh diputuskan petugas: `queued` belum masuk meja verifikasi. */
    public const DECIDABLE_STATUSES = ['menunggu', 'perbaikan'];

    /** Delapan indikator aksesibilitas yang boleh diklaim pengaju atau dikoreksi. */
    public const ATTRIBUTES = [
        'accessible_ramp', 'accessible_toilet', 'accessible_elevator',
        'accessible_pedestrian_pathway', 'accessible_entrance',
        'tactile_guidance_path', 'accessible_information_signage', 'accessible_parking',
    ];

    /**
     * Status klaim pengaju (sama dengan `resources/js/data/meta.js` → `AVAILABILITY`).
     * Nilainya identik dengan kolom fasilitas; `unknown` (belum ada bukti) tidak
     * pernah menjadi "tidak tersedia" maupun "tersedia".
     */
    public const AVAILABILITY = ['yes', 'no', 'unknown'];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'decided_at' => 'datetime',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function createdFacility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'created_facility_id');
    }

    public function proofs(): HasMany
    {
        return $this->hasMany(SubmissionProof::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(VerificationEvent::class);
    }

    public function isDecidable(): bool
    {
        return in_array($this->status, self::DECIDABLE_STATUSES, true);
    }

    /** Bentuk data yang dibaca antrean verifikasi (`resources/js/composables/useVerification.js`). */
    public function toPayload(): array
    {
        return [
            'id' => $this->public_id,
            'type' => $this->type,
            'facilityId' => $this->facility?->external_id,
            'facilityName' => $this->facility_name,
            'category' => $this->category,
            'city' => $this->city,
            'submittedBy' => $this->submitted_by,
            'contact' => $this->contact,
            'submittedAt' => $this->created_at?->toDateString(),
            'status' => $this->status,
            'summary' => $this->summary ?? '',
            'payload' => $this->payload,
            'decidedBy' => $this->decided_by,
            'decidedAt' => $this->decided_at?->toIso8601String(),
            'createdFacilityId' => $this->createdFacility?->external_id,
            'proofs' => $this->proofs->map(fn (SubmissionProof $proof) => [
                'id' => $proof->id,
                'type' => 'foto',
                'caption' => $proof->original_name,
                'date' => $proof->created_at?->toDateString(),
                'url' => "/api/submissions/{$this->public_id}/proofs/{$proof->id}",
            ])->all(),
        ];
    }
}
