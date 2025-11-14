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
        Schema::create('quotation', function (Blueprint $table) {
            $table->id('kd_quotation');
            $table->unsignedBigInteger('kd_tiket');
            $table->unsignedBigInteger('kd_pelanggan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('tanggal', 20);
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_tiket')->references('kd_tiket')->on('tiket')->onDelete('cascade');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan')->onDelete('cascade');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan')->onDelete('cascade');
        });

        Schema::create('quotation_item', function (Blueprint $table) {
            $table->id('kd_quotation_item');
            $table->unsignedBigInteger('kd_quotation');
            $table->unsignedBigInteger('kd_barang');
            $table->unsignedBigInteger('kd_jasa');
            $table->string('harga', 200);
            $table->string('jumlah', 200);
            $table->timestamps();

            $table->foreign('kd_quotation')->references('kd_quotation')->on('quotation')->onDelete('cascade');
            $table->foreign('kd_barang')->references('kd_barang')->on('barang')->onDelete('cascade');
            $table->foreign('kd_jasa')->references('kd_jasa')->on('jasa')->onDelete('cascade');
        });

        Schema::create('invoice', function (Blueprint $table) {
            $table->id('kd_invoice');
            $table->unsignedBigInteger('kd_quotation');
            $table->unsignedBigInteger('kd_pelanggan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('tanggal', 20);
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_quotation')->references('kd_quotation')->on('quotation')->onDelete('cascade');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan')->onDelete('cascade');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan')->onDelete('cascade');
        });

        Schema::create('invoice_item', function (Blueprint $table) {
            $table->id('kd_invoice_item');
            $table->unsignedBigInteger('kd_invoice');
            $table->unsignedBigInteger('kd_barang');
            $table->unsignedBigInteger('kd_jasa');
            $table->string('harga', 200);
            $table->string('jumlah', 200);
            $table->timestamps();

            $table->foreign('kd_invoice')->references('kd_invoice')->on('invoice')->onDelete('cascade');
            $table->foreign('kd_barang')->references('kd_barang')->on('barang')->onDelete('cascade');
            $table->foreign('kd_jasa')->references('kd_jasa')->on('jasa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation');
        Schema::dropIfExists('quotation_item');
        Schema::dropIfExists('invoice');
        Schema::dropIfExists('invoice_item');
    }
};
