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
        Schema::create('presensi_bulanan', function (Blueprint $table) {
            $table->id('kd_presensi_bulanan');
            $table->string('tahun');
            $table->string('bulan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('jumlah_tanggal');
            $table->string('jumlah_libur');
            $table->string('jumlah_hari_kerja_normal');
            $table->string('jumlah_hari_sakit');
            $table->string('jumlah_hari_izin');
            $table->string('jumlah_fingerprint');
            $table->string('jumlah_alpha');
            $table->string('jumlah_terlambat');
            $table->string('jumlah_jam_izin');
            $table->string('jumlah_hari_lembur');
            $table->string('jumlah_jam_lembur');
            $table->string('verifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_bulanan');
    }
};
