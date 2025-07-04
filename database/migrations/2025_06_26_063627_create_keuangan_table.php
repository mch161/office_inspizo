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
        Schema::create('keuangan_kotak', function (Blueprint $table) {
            $table->id('kd_kotak');
            $table->string('nama', 100)->unique();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();
        });

        Schema::create('keuangan_kategori', function (Blueprint $table) {
            $table->id('kd_kategori');
            $table->string('nama', 100)->unique();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();
        });

        Schema::create('keuangan', function (Blueprint $table) {
            $table->id('kd_keuangan');
            $table->unsignedBigInteger('kd_karyawan');
            $table->string('jenis', 100);
            $table->string('status', 50)->default('0');
            $table->string('masuk', 200)->nullable();
            $table->string('keluar', 200)->nullable();

            $table->unsignedBigInteger('kd_kotak')->nullable();
            $table->unsignedBigInteger('kd_kategori')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('tanggal', 20)->nullable();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan');
            $table->foreign('kd_kotak')->references('kd_kotak')->on('keuangan_kotak');
            $table->foreign('kd_kategori')->references('kd_kategori')->on('keuangan_kategori');
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
