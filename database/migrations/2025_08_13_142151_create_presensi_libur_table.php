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
        Schema::create('presensi_libur', function (Blueprint $table) {
            $table->id('kd_presensi_libur');
            $table->date('tanggal');
            $table->enum('jenis_libur', ['Minggu', 'Nasional', 'Cuti Bersama', 'Internal']);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_libur');
    }
};
