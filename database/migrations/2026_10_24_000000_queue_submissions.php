<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antrean pengajuan: status baru `queued` untuk pengajuan yang diterima tetapi
     * belum masuk meja verifikasi, plus index `contact` karena halaman status guest
     * mencari pengajuan lewat surel pengaju. Murni aditif: nilai default tetap
     * `menunggu`, jadi baris lama dan insert langsung tidak berubah perilakunya.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('status', ['queued', 'menunggu', 'disetujui', 'perbaikan', 'ditolak'])
                ->default('menunggu')
                ->change();
            $table->index('contact');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['contact']);
            $table->enum('status', ['menunggu', 'disetujui', 'perbaikan', 'ditolak'])
                ->default('menunggu')
                ->change();
        });
    }
};
