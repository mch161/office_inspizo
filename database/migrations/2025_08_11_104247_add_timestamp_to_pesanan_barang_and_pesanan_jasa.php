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
        Schema::table('pesanan_barang', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('pesanan_jasa', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_barang', function (Blueprint $table) {
            $table->dropColumn('timestamp');
        });

        Schema::table('pesanan_jasa', function (Blueprint $table) {
            $table->dropColumn('timestamp');
        });
    }
};
