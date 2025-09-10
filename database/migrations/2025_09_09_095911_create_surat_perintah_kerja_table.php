<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_perintah_kerja', function (Blueprint $table) {
            $table->id('kd_surat_perintah_kerja');
            $table->unsignedBigInteger('kd_pesanan')->nullable();
            $table->unsignedBigInteger('kd_karyawan')->nullable();
            $table->string('tanggal_mulai', 50);
            $table->string('tanggal_selesai', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_perintah_kerja');
    }
};
