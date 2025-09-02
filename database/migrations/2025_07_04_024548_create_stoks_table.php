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
        Schema::create('stok', function (Blueprint $table) {
            $table->id('kd_stok');
            $table->unsignedBigInteger('kd_karyawan');
            $table->unsignedBigInteger('kd_barang');
            $table->string('stok_masuk', 200);
            $table->string('stok_keluar', 200);
            $table->string('klasifikasi', 100);
            $table->text('keterangan');
            $table->string('dibuat_oleh', 100);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
            $table->foreign('kd_barang')->references('kd_barang')->on('barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};
