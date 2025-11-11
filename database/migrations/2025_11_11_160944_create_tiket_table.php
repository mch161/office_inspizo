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
        Schema::create('tiket', function (Blueprint $table) {
            $table->id('kd_tiket');
            $table->string('prioritas');
            $table->string('jenis');
            $table->string('deskripsi');
            $table->unsignedBigInteger('kd_pelanggan');
            $table->string('via');
            $table->timestamps();

            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};
