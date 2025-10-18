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
        Schema::table('jurnal', function (Blueprint $table) {
            $table->unsignedBigInteger('kd_tugas')->nullable()->after('kd_karyawan');

            $table->foreign('kd_tugas')->references('kd_tugas')->on('tugas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->dropForeign(['kd_tugas']);
            $table->dropColumn('kd_tugas');
        });
    }
};
