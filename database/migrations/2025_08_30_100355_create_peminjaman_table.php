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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('kd_peminjaman');
            $table->unsignedInteger('kd_barang');
            $table->string('jumlah', 200);
            $table->unsignedInteger('kd_karyawan');
            $table->string('status', 1)->default('0');
            $table->timestamps();

            $table->foreign('kd_barang')->references('kd_barang')->on('barang');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
