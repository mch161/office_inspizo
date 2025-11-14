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
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('jenis')->nullable()->after('progres');
            $table->unsignedBigInteger('kd_tiket')->nullable()->after('jenis');

            $table->foreign('kd_tiket')->references('kd_tiket')->on('tiket')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('jenis');
            $table->dropColumn('kd_tiket');
        });
    }
};
