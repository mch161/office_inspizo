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
            $table->unsignedBigInteger('kd_karyawan')->nullable();
            $table->unsignedBigInteger('kd_pelanggan')->nullable();
            $table->text('deskripsi_pesanan');
            $table->string('tanggal', 20);
            $table->string('status', 10)->default('0');
            $table->string('progres', 20)->default('1');
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan');
        });

        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id('kd_pesanan_detail');
            $table->unsignedBigInteger('kd_karyawan')->nullable();
            $table->unsignedBigInteger('kd_pelanggan')->nullable();
            $table->unsignedBigInteger('kd_pesanan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan');
            $table->foreign('kd_pesanan')->references('kd_pesanan')->on('pesanan');
        });

        Schema::create('pesanan_barang', function (Blueprint $table) {
            $table->id('kd_pesanan_barang');
            $table->unsignedBigInteger('kd_pesanan_detail');
            $table->unsignedBigInteger('kd_barang')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('jenis', 20)->nullable();
            $table->string('hpp')->nullable();
            $table->string('laba')->nullable();
            $table->string('harga_jual', 200)->nullable();
            $table->string('jumlah', 200)->nullable();
            $table->string('subtotal', 200)->nullable();

            $table->foreign('kd_pesanan_detail')->references('kd_pesanan_detail')->on('pesanan_detail');
            $table->foreign('kd_barang')->references('kd_barang')->on('barang');
        });

        Schema::create('pesanan_jasa', function (Blueprint $table) {
            $table->id('kd_pesanan_jasa');
            $table->unsignedBigInteger('kd_pesanan_detail');
            $table->string('nama_jasa')->nullable();
            $table->string('harga_jasa', 200)->nullable();
            $table->string('jumlah', 200)->nullable();
            $table->string('subtotal', 200)->nullable();

            $table->foreign('kd_pesanan_detail')->references('kd_pesanan_detail')->on('pesanan_detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('pesanan_detail');
        Schema::dropIfExists('pesanan_barang');
        Schema::dropIfExists('pesanan_jasa');
    }
};