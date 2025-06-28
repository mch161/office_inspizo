<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('kd_pesanan');
            $table->integer('kd_karyawan');
            $table->integer('kd_pelanggan');
            $table->text('deskripsi_pesanan');
            $table->string('status', 10);
            $table->string('progres', 20);
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan');
        });

        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id('kd_pesanan_detail');
            $table->integer('kd_karyawan');
            $table->integer('kd_pesanan');
            $table->text('keterangan');
            $table->integer('kd_barang');
            $table->string('nama_barang');
            $table->string('jenis', 20);
            $table->string('hpp');
            $table->string('laba');
            $table->string('harga_jual', 200);
            $table->string('jumlah', 200);
            $table->string('subtotal', 200);
            $table->string('dibuat_oleh', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('pesanan_detail');
    }
};
