<?php

namespace Tests\Unit;

use App\Models\Facility;
use App\Models\Submission;
use PHPUnit\Framework\TestCase;

/**
 * Kontrak atribut aksesibilitas: kunci yang sama dipakai kolom basis data, API,
 * dan frontend. Kalau salah satu daftar berubah sendiri, label atribut jatuh
 * kembali ke kunci mentah dan pengajuan koreksi kehilangan kolom tujuannya.
 */
class AccessibilityAttributesTest extends TestCase
{
    public function test_kunci_atribut_fasilitas_dan_pengajuan_sama(): void
    {
        $this->assertCount(8, Submission::ATTRIBUTES);
        $this->assertSame(Submission::ATTRIBUTES, array_keys(Facility::ATTRIBUTE_COLUMNS));
    }

    public function test_status_aksesibilitas_tiga_nilai_baku(): void
    {
        $this->assertSame(['yes', 'no', 'unknown'], Submission::AVAILABILITY);
    }

    public function test_setiap_atribut_punya_kolom_status_dan_keterangan(): void
    {
        foreach (Facility::ATTRIBUTE_COLUMNS as $attribute => [$statusColumn, $detailsColumn]) {
            $this->assertSame("{$attribute}_status", $statusColumn);
            $this->assertSame("{$attribute}_details", $detailsColumn);
        }
    }
}
