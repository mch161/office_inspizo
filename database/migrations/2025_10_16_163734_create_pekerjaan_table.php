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
        Schema::create('pekerjaan', function (Blueprint $table) {
            $table->id('kd_pekerjaan');
            $table->unsignedBigInteger('kd_project');
            $table->text('pekerjaan');
            $table->timestamps();

            $table->foreign('kd_project')->references('kd_project')->on('project')->onDelete('cascade');
        });

        Schema::create('pekerjaan_karyawan', function (Blueprint $table) {
            $table->id('kd_pekerjaan_karyawan');
            $table->unsignedBigInteger('kd_pekerjaan');
            $table->unsignedBigInteger('kd_karyawan');
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
        Schema::dropIfExists('pekerjaan');
        Schema::dropIfExists('pekerjaan_karyawan');
    }
};
