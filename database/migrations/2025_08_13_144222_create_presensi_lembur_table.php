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
        Schema::create('presensi_lembur', function (Blueprint $table) {
            $table->id('kd_lembur');
            $table->unsignedBigInteger('kd_karyawan');
            $table->date('tanggal');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('jumlah_jam');
            $table->string('keterangan');
            $table->string('verifikasi');
            $table->string('dibuat_oleh');
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_lembur');
    }
};
