<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'external_id', 'name', 'category', 'source_category', 'source_category_detail',
    'address', 'village', 'subdistrict', 'city', 'province', 'postal_code',
    'latitude', 'longitude',
    'ramp_status', 'ramp_details', 'toilet_status', 'toilet_details', 'lift_status', 'lift_details',
    'path_status', 'path_details', 'entrance_status', 'entrance_details',
    'entrance_width_cm', 'entrance_width_measured',
    'verification_status', 'verified_at', 'verification_method', 'evidence_url',
    'source_name', 'source_url', 'source_ref_type', 'source_ref_id', 'collected_at',
    'data_completeness_percent', 'notes',
])]
class Facility extends Model
{
    /** Kategori dataset → kategori aplikasi. Hanya pemetaan yang jelas; sisanya ditolak impor. */
    public const CATEGORY_MAP = [
        'health_hospital' => 'kesehatan',
        'health_puskesmas_clinic' => 'kesehatan',
        'government_public_service' => 'pemerintahan',
        'education_campus_school_library' => 'pendidikan',
        'shopping_mall_market' => 'perbelanjaan',
        'hotel_accommodation' => 'penginapan',
        'park_tourism_public_space' => 'rekreasi',
        'transport_station_terminal' => 'transportasi',
    ];

    /** Status tiga nilai: ketiadaan bukti bukan "tidak tersedia". */
    public const AVAILABILITY_MAP = [
        'yes' => 'tersedia',
        'no' => 'tidak',
        'unknown' => 'belum',
    ];

    public const VERIFICATION_MAP = [
        'pending' => 'menunggu',
        'verified' => 'terverifikasi',
        'rejected' => 'ditolak',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'entrance_width_cm' => 'integer',
            'entrance_width_measured' => 'boolean',
            'data_completeness_percent' => 'integer',
            'collected_at' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /** Bentuk data yang dibaca frontend (lihat `resources/js/composables/useFacilities.js`). */
    public function toPayload(): array
    {
        $date = ($this->collected_at ?? $this->updated_at)?->toDateString();

        return [
            'id' => $this->external_id,
            'name' => $this->name,
            'category' => $this->category,
            'address' => $this->address ?? '',
            'village' => $this->village,
            'subdistrict' => $this->subdistrict,
            'city' => $this->city,
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'accessibility' => [
                'ramp' => self::AVAILABILITY_MAP[$this->ramp_status],
                'toilet' => self::AVAILABILITY_MAP[$this->toilet_status],
                'lift' => self::AVAILABILITY_MAP[$this->lift_status],
                'path' => self::AVAILABILITY_MAP[$this->path_status],
                'entrance' => self::AVAILABILITY_MAP[$this->entrance_status],
            ],
            'accessibilityDetails' => [
                'ramp' => $this->ramp_details,
                'toilet' => $this->toilet_details,
                'lift' => $this->lift_details,
                'path' => $this->path_details,
                'entrance' => $this->entrance_details,
            ],
            'notes' => $this->notes ?? '',
            'proofs' => [],
            'verification' => self::VERIFICATION_MAP[$this->verification_status],
            'lastChecked' => $date,
            'updatedAt' => $date,
            'contributor' => $this->source_name ?? 'Tidak diketahui',
            'source' => [
                'name' => $this->source_name,
                'url' => $this->source_url,
                'ref' => $this->source_ref_type && $this->source_ref_id
                    ? "{$this->source_ref_type}/{$this->source_ref_id}"
                    : null,
                'collectedAt' => $date,
            ],
            'dataCompletenessPercent' => $this->data_completeness_percent,
        ];
    }
}
