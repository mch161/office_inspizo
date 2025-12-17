<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('izin', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('tanggal');
            $table->integer('jumlah_hari')->default(1)->after('tanggal_selesai');
        });
    }

    public function down()
    {
        Schema::table('izin', function (Blueprint $table) {
            $table->dropColumn(['tanggal_selesai', 'jumlah_hari']);
        });
    }
};