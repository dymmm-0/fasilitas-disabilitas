<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antrean kontribusi masyarakat: pengajuan (`submissions`), bukti pendukung
     * (`submission_proofs`), dan riwayat keputusan verifikasi (`submission_events`).
     * Tabel baru murni aditif — tabel fasilitas dan data hasil impor tidak diubah.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            // Identifier publik (`SUB-0001`) diisi dari primary key di dalam transaksi yang
            // sama, jadi tidak ada balapan nilai antar permintaan. Nullable hanya untuk
            // momen singkat itu: `create()` belum tahu id barisnya.
            $table->string('public_id', 20)->nullable()->unique();

            $table->enum('type', ['usulan', 'koreksi']);
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            $table->string('facility_name');
            $table->string('category', 60);
            $table->string('city');
            $table->string('submitted_by');
            $table->string('contact')->nullable();
            $table->string('summary', 500)->nullable();

            // Isian mentah pengaju (usulan: alamat/koordinat/klaim aksesibilitas;
            // koreksi: atribut, nilai lama, nilai yang diusulkan). Klaim pengaju tidak
            // pernah langsung dianggap terverifikasi.
            $table->json('payload');

            $table->enum('status', ['menunggu', 'disetujui', 'perbaikan', 'ditolak'])->default('menunggu');
            $table->string('decided_by')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('created_facility_id')->nullable()->constrained('facilities')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('submission_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('disk', 30)->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->timestamps();
        });

        Schema::create('submission_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('status_from', 20)->nullable();
            $table->string('status_to', 20);
            $table->text('note')->nullable();
            $table->string('actor')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_events');
        Schema::dropIfExists('submission_proofs');
        Schema::dropIfExists('submissions');
    }
};
