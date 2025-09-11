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
        Schema::table('pesanan_jasa', function (Blueprint $table) {
            $table->string('kd_jasa')->after('kd_pesanan_detail');

            $table->foreign('kd_jasa')->references('kd_jasa')->on('jasa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_jasa', function (Blueprint $table) {
            $table->dropColumn('kd_jasa');
        });
    }
};
