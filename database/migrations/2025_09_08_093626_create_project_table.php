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
        Schema::create('project', function (Blueprint $table) {
            $table->id('kd_project');
            $table->string('nama_project');
            $table->string('foto')->nullable();
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('lokasi')->nullable();
            $table->string('tanggal_mulai');
            $table->string('tanggal_selesai')->nullable();
            $table->text('deskripsi');
            $table->string('status');
            $table->string('dibuat_oleh');
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
