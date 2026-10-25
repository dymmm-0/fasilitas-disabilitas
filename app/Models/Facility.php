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
    'verification_status', 'verified_at', 'verified_by', 'verification_method', 'verification_note', 'evidence_url',
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

    /**
     * Kategori sumber yang isinya bercampur, sehingga hanya bisa dipetakan lewat detailnya.
     * Detail di luar daftar ini belum punya kategori aplikasi yang benar — jangan dipaksa.
     */
    public const CATEGORY_DETAIL_MAP = [
        'other_public_facility' => [
            'place_of_worship' => 'ibadah',
            'bank' => 'perbankan',
            'courier_service' => 'pengiriman',
        ],
        'park_tourism_public_space' => [
            'sports_centre' => 'olahraga',
            'stadium' => 'olahraga',
            // OSM pernah menandai bank sebagai ruang publik; bank bukan rekreasi.
            'bank' => 'perbankan',
        ],
        'government_public_service' => ['post_office' => 'pengiriman'],
    ];

    /** Kategori aplikasi untuk satu baris dataset; detail menang atas kategori kasar. */
    public static function categoryFor(string $sourceCategory, ?string $detail): ?string
    {
        return self::CATEGORY_DETAIL_MAP[$sourceCategory][(string) $detail]
            ?? self::CATEGORY_MAP[$sourceCategory]
            ?? null;
    }

    /** Kategori yang boleh dipilih pengaju (sama dengan `resources/js/data/meta.js`). */
    public const CATEGORIES = [
        'transportasi', 'kesehatan', 'pendidikan', 'pemerintahan', 'perbelanjaan',
        'penginapan', 'rekreasi', 'ibadah', 'olahraga', 'perbankan', 'pengiriman',
    ];

    /** Indikator aksesibilitas → kolom status dan kolom keterangan. */
    public const ATTRIBUTE_COLUMNS = [
        'ramp' => ['ramp_status', 'ramp_details'],
        'toilet' => ['toilet_status', 'toilet_details'],
        'lift' => ['lift_status', 'lift_details'],
        'path' => ['path_status', 'path_details'],
        'entrance' => ['entrance_status', 'entrance_details'],
    ];

    /** Status tiga nilai: ketiadaan bukti bukan "tidak tersedia". */
    public const AVAILABILITY_MAP = [
        'yes' => 'tersedia',
        'no' => 'tidak',
        'unknown' => 'belum',
    ];

    /** Status verifikasi tingkat fasilitas → label klien (`resources/js/data/meta.js`). */
    public const VERIFICATION_MAP = [
        'pending' => 'menunggu',
        'verified' => 'terverifikasi',
        'needs_repair' => 'perlu-perbaikan',
        'rejected' => 'ditolak',
    ];

    /**
     * Status yang berarti fasilitas masih membutuhkan pemeriksaan. `rejected` tidak
     * termasuk: record-nya tetap ada dan hanya ditampilkan sebagai sudah diputuskan.
     */
    public const NEEDS_VERIFICATION = ['pending', 'needs_repair'];

    /** Hasil pemeriksaan yang boleh disimpan petugas. */
    public const VERIFICATION_ACTIONS = ['verified', 'needs_repair', 'rejected'];

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

    /**
     * Id publik berikutnya (`AKS-####`) untuk fasilitas yang lahir dari usulan
     * masyarakat. Diurut panjang lalu nilai supaya urutannya benar di MySQL maupun
     * SQLite tanpa fungsi SQL khusus.
     *
     * shortcut: dua keputusan yang disetujui bersamaan bisa menghasilkan id yang sama;
     * tambah retry atau sequence bila pengajuan sudah diproses paralel.
     */
    public static function nextExternalId(): string
    {
        $last = static::query()
            ->orderByRaw('LENGTH(external_id) DESC')
            ->orderByDesc('external_id')
            ->value('external_id');

        return 'AKS-'.str_pad((string) ((int) substr((string) $last, 4) + 1), 4, '0', STR_PAD_LEFT);
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
            // Pemeriksaan tingkat fasilitas; indikator aksesibilitas dicatat terpisah.
            'verifiedAt' => $this->verified_at?->toIso8601String(),
            'verifiedBy' => $this->verified_by,
            'verificationMethod' => $this->verification_method,
            'verificationNote' => $this->verification_note,
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
