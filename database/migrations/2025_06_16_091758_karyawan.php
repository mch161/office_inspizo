<?php
// File: database/migrations/2024_01_01_000001_create_karyawan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('kd_karyawan');
            $table->string('nama', 255)->nullable();
            $table->string('telp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->integer('nip')->nullable();
            $table->bigInteger('nik')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('password', 255);
            $table->string('role', 50)->nullable();
            $table->string('dibuat_oleh', 100)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};