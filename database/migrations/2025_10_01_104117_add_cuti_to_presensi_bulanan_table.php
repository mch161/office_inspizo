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
        Schema::table('presensi_bulanan', function (Blueprint $table) {
            $table->string('jumlah_hari_cuti')->nullable()->after('jumlah_libur');
            $table->string('jumlah_hari_minggu')->nullable()->after('jumlah_hari_kerja_normal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi_bulanan', function (Blueprint $table) {
            $table->dropColumn('jumlah_hari_cuti');
            $table->dropColumn('jumlah_hari_minggu');
        });
    }
};
