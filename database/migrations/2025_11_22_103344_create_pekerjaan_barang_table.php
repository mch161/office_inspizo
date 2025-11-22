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
        Schema::create('pekerjaan_barang', function (Blueprint $table) {
            $table->id('kd_pekerjaan_barang');
            $table->unsignedBigInteger('kd_pekerjaan');
            $table->unsignedBigInteger('kd_barang');
            $table->string('jumlah', 200);
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_pekerjaan')->references('kd_pekerjaan')->on('pekerjaan')->onDelete('cascade');
            $table->foreign('kd_barang')->references('kd_barang')->on('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_barang');
    }
};
