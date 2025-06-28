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
        Schema::create('keuangan', function (Blueprint $table) {
            $table->id('kd_keuangan');
            $table->integer('kd_karyawan');
            $table->string('jenis', 100);
            $table->string('status', 50);
            $table->string('masuk', 200);
            $table->string('keluar', 200);
            $table->string('kotak', 200);
            $table->string('kategori', 100);
            $table->text('keterangan');
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
            $table->foreign('kotak')->references('nama')->on('keuangan_kotak');
            $table->foreign('kategori')->references('nama')->on('keuangan_kategori');
        });

        Schema::create('keuangan_kotak', function (Blueprint $table) {
            $table->id('kd_kotak');
            $table->string('nama', 100);
        });

        Schema::create('keuangan_kategori', function (Blueprint $table) {
            $table->id('kd_kategori');
            $table->string('nama', 100);
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan');
        Schema::dropIfExists('keuangan_kotak');
        Schema::dropIfExists('keuangan_kategori');
    }
};
