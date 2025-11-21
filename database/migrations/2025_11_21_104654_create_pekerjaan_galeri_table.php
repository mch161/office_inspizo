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
        Schema::create('pekerjaan_galeri', function (Blueprint $table) {
            $table->id('kd_galeri');
            $table->unsignedBigInteger('kd_pekerjaan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->text('foto');
            $table->string('dibuat_oleh');
            $table->timestamps();

            $table->foreign('kd_pekerjaan')->references('kd_pekerjaan')->on('pekerjaan')->onDelete('cascade');
            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_galeri');
    }
};