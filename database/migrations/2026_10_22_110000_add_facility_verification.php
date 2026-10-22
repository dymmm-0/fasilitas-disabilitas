<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Verifikasi tingkat fasilitas. Murni aditif terhadap data: tidak ada baris
     * fasilitas yang diubah, hanya nilai enum yang ditambah dan kolom pencatatan
     * pemeriksaan. 1.117 baris dataset tetap `pending` karena memang belum pernah
     * diperiksa — audit menunjukkan `verified_at`, `verification_method`, dan
     * `evidence_url` masih kosong untuk seluruh baris.
     */
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            // `needs_repair` = pemeriksaan sudah dilakukan tetapi datanya harus diperbaiki.
            $table->enum('verification_status', ['pending', 'verified', 'needs_repair', 'rejected'])
                ->default('pending')
                ->change();
            $table->string('verified_by', 120)->nullable()->after('verified_at');
            $table->text('verification_note')->nullable()->after('verification_method');
        });

        // Riwayat keputusan kini mencatat dua subjek: pengajuan warga dan fasilitas.
        Schema::rename('submission_events', 'verification_events');

        Schema::table('verification_events', function (Blueprint $table) {
            $table->unsignedBigInteger('submission_id')->nullable()->change();
            $table->foreignId('facility_id')->nullable()->after('submission_id')
                ->constrained('facilities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('verification_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('facility_id');
            $table->unsignedBigInteger('submission_id')->change();
        });

        Schema::rename('verification_events', 'submission_events');

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['verified_by', 'verification_note']);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ->change();
        });
    }
};
