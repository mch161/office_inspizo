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
        Schema::create('agenda', function (Blueprint $table) {
            $table->id("kd_agenda");
            $table->string('title');
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->unsignedBigInteger('kd_karyawan')->nullable();
            $table->string('dibuat_oleh', 50);
            $table->timestamps();

            $table->foreign('kd_karyawan')->references('kd_karyawan')->on('karyawan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};