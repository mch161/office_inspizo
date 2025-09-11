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
            $table->unsignedBigInteger('kd_project')->nullable();
            $table->unsignedBigInteger('kd_karyawan')->nullable();
            $table->string('tanggal_mulai', 50);
            $table->string('tanggal_selesai', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 50);
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_pesanan')->references('kd_pesanan')->on('pesanan');
            $table->foreign('kd_project')->references('kd_project')->on('project');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
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
