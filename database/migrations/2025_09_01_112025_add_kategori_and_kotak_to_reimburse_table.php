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
        Schema::table('reimburse', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori')->nullable();
            $table->unsignedBigInteger('kotak')->nullable();

            $table->foreign('kategori')->references('kd_kategori')->on('keuangan_kategori');
            $table->foreign('kotak')->references('kd_kotak')->on('keuangan_kotak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimburse', function (Blueprint $table) {
            $table->dropColumn('kategori');
            $table->dropColumn('kotak');
        });
    }
};
