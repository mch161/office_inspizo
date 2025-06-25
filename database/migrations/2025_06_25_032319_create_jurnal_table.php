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
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id('kd_jurnal');
            $table->integer('kd_user');
            $table->string('tanggal', 20);
            $table->string('jam', 20);
            $table->text('isi_jurnal');
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_user')->references('kd_karyawan')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
