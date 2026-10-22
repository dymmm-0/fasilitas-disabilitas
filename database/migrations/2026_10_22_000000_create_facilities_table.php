<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menampung fasilitas hasil impor dataset (lihat `facilities:import`).
     * Kolom mengikuti skema `Dataset/akseskita_facilities_medan.csv`.
     */
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 20)->unique();
            $table->string('name');
            $table->string('category', 60);
            $table->string('source_category', 60);
            $table->string('source_category_detail', 60)->nullable();
            $table->string('address')->nullable();
            $table->string('village')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Lima indikator aksesibilitas: `unknown` berarti belum ada bukti, bukan "tidak ada".
            $table->enum('ramp_status', ['yes', 'no', 'unknown'])->default('unknown');
            $table->text('ramp_details')->nullable();
            $table->enum('toilet_status', ['yes', 'no', 'unknown'])->default('unknown');
            $table->text('toilet_details')->nullable();
            $table->enum('lift_status', ['yes', 'no', 'unknown'])->default('unknown');
            $table->text('lift_details')->nullable();
            $table->enum('path_status', ['yes', 'no', 'unknown'])->default('unknown');
            $table->text('path_details')->nullable();
            $table->enum('entrance_status', ['yes', 'no', 'unknown'])->default('unknown');
            $table->unsignedSmallInteger('entrance_width_cm')->nullable();
            $table->boolean('entrance_width_measured')->default(false);
            $table->text('entrance_details')->nullable();

            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_method')->nullable();
            $table->string('evidence_url')->nullable();

            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_ref_type', 20)->nullable();
            $table->string('source_ref_id', 30)->nullable();
            $table->date('collected_at')->nullable();

            $table->unsignedTinyInteger('data_completeness_percent')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('category');
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
