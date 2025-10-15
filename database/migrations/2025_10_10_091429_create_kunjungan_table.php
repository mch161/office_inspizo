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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id('kd_kunjungan');
            $table->unsignedBigInteger('kd_pesanan')->nullable();
            $table->unsignedBigInteger('kd_pelanggan')->nullable();
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('tanggal', 20);
            $table->string('status', 10)->default('0');
            $table->text('keterangan')->nullable();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_pesanan')->references('kd_pesanan')->on('pesanan');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan');
        });

        Schema::create('kunjungan_karyawan', function (Blueprint $table) {
            $table->id('kd_kunjungan_karyawan');
            $table->unsignedBigInteger('kd_kunjungan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_kunjungan')->references('kd_kunjungan')->on('kunjungan');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan_karyawan');
        Schema::dropIfExists('kunjungan');
    }
};
