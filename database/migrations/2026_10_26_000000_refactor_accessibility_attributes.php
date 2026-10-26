<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Delapan indikator aksesibilitas dengan kunci netral bahasa. Kolom lama yang
 * maknanya sama hanya diganti nama (data tetap, tidak ada baris yang ditulis ulang);
 * tiga indikator baru ditambahkan dengan status `unknown` karena belum ada bukti.
 */
return new class extends Migration
{
    /** Kolom lama → kolom baru; satu vocab dengan `Facility::ATTRIBUTE_COLUMNS`. */
    private const RENAMES = [
        'ramp_status' => 'accessible_ramp_status',
        'ramp_details' => 'accessible_ramp_details',
        'toilet_status' => 'accessible_toilet_status',
        'toilet_details' => 'accessible_toilet_details',
        'lift_status' => 'accessible_elevator_status',
        'lift_details' => 'accessible_elevator_details',
        'path_status' => 'accessible_pedestrian_pathway_status',
        'path_details' => 'accessible_pedestrian_pathway_details',
        'entrance_status' => 'accessible_entrance_status',
        'entrance_details' => 'accessible_entrance_details',
    ];

    /** Indikator baru; dataset belum memuatnya, jadi seluruh baris `unknown`. */
    private const NEW_ATTRIBUTES = [
        'tactile_guidance_path',
        'accessible_information_signage',
        'accessible_parking',
    ];

    /** Kunci atribut lama → kunci baru, untuk isian pengajuan yang sudah tersimpan. */
    private const ATTRIBUTE_KEY_MAP = [
        'ramp' => 'accessible_ramp',
        'toilet' => 'accessible_toilet',
        'lift' => 'accessible_elevator',
        'path' => 'accessible_pedestrian_pathway',
        'entrance' => 'accessible_entrance',
    ];

    /** Nilai status lama (kata Indonesia) → status baku. */
    private const STATUS_MAP = ['tersedia' => 'yes', 'tidak' => 'no', 'belum' => 'unknown'];

    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            foreach (self::RENAMES as $from => $to) {
                $table->renameColumn($from, $to);
            }
        });

        Schema::table('facilities', function (Blueprint $table) {
            foreach (self::NEW_ATTRIBUTES as $attribute) {
                $table->enum("{$attribute}_status", ['yes', 'no', 'unknown'])->default('unknown');
                $table->text("{$attribute}_details")->nullable();
            }
        });

        $this->remapSubmissionPayloads();
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            foreach (self::NEW_ATTRIBUTES as $attribute) {
                $table->dropColumn(["{$attribute}_status", "{$attribute}_details"]);
            }
        });

        Schema::table('facilities', function (Blueprint $table) {
            foreach (self::RENAMES as $from => $to) {
                $table->renameColumn($to, $from);
            }
        });
    }

    /**
     * Pengajuan lama menyimpan kunci atribut dan nilai status dalam kosakata lama.
     * Tanpa dipetakan, riwayat kontribusi tidak lagi terbaca kode baru: `field`
     * koreksi tidak ada di `Facility::ATTRIBUTE_COLUMNS` dan label atribut jatuh
     * kembali ke kunci mentah.
     */
    private function remapSubmissionPayloads(): void
    {
        DB::table('submissions')->select('id', 'payload')->orderBy('id')
            ->each(function (object $row) {
                $payload = json_decode((string) $row->payload, true);

                if (! is_array($payload)) {
                    return;
                }

                if (isset($payload['accessibility']) && is_array($payload['accessibility'])) {
                    $payload['accessibility'] = $this->remapValues($payload['accessibility']);
                }

                if (isset($payload['field'])) {
                    $payload['field'] = self::ATTRIBUTE_KEY_MAP[$payload['field']] ?? $payload['field'];
                }

                foreach (['from', 'to'] as $key) {
                    if (isset($payload[$key])) {
                        $payload[$key] = self::STATUS_MAP[$payload[$key]] ?? $payload[$key];
                    }
                }

                DB::table('submissions')->where('id', $row->id)->update([
                    'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            });
    }

    /** @param  array<string, string>  $values */
    private function remapValues(array $values): array
    {
        $mapped = [];

        foreach ($values as $key => $value) {
            $mapped[self::ATTRIBUTE_KEY_MAP[$key] ?? $key] = self::STATUS_MAP[$value] ?? $value;
        }

        return $mapped;
    }
};
