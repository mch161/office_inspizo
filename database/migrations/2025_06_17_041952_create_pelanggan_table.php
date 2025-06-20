<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id('kd_pelanggan');
            $table->string('nama_pelanggan', 255)->nullable();
            $table->string('nama_perusahaan', 255)->nullable();
            $table->text('alamat_pelanggan')->nullable();
            $table->string('telp_pelanggan', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password', 255);
            $table->string('role', 50)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};