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
        Schema::create('pesanan_galeri', function (Blueprint $table) {
            $table->id('kd_galeri');
            $table->unsignedBigInteger('kd_pesanan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('foto');
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_pesanan')->references('kd_pesanan')->on('pesanan')->onDelete('cascade');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_galeri');
    }
};
