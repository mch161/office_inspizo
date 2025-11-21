<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {

            $table->unsignedBigInteger('kd_pelanggan')->nullable()->after('kd_pekerjaan');
            $table->unsignedBigInteger('kd_tiket')->nullable()->after('kd_pelanggan');

            $table->string('tanggal')->nullable()->after('kd_tiket');
            $table->string('jenis')->nullable()->after('tanggal');

            $table->text('keterangan_pekerjaan')->nullable()->after('jenis');
            $table->text('keterangan_barang')->nullable()->after('keterangan_pekerjaan');

            $table->string('status')->default('akan dikerjakan')->after('keterangan_barang');

            $table->text('ttd_pelanggan')->nullable()->after('status');

            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan')->onDelete('cascade');
            $table->foreign('kd_tiket')->references('kd_tiket')->on('tiket')->onDelete('cascade');
        });

        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropColumn('pekerjaan');

            $table->dropForeign('pekerjaan_kd_project_foreign');
            $table->dropColumn('kd_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->unsignedBigInteger('kd_project')->nullable()->after('kd_pekerjaan');
            $table->foreign('kd_project')->references('kd_project')->on('project')->onDelete('cascade');
            $table->text('pekerjaan')->nullable()->after('kd_project');
        });

        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropForeign('pekerjaan_kd_pelanggan_foreign');
            $table->dropForeign('pekerjaan_kd_tiket_foreign');

            $table->dropColumn([
                'kd_pelanggan',
                'kd_tiket',
                'tanggal',
                'jenis',
                'keterangan_pekerjaan',
                'keterangan_barang',
                'status',
                'ttd_pelanggan',
            ]);
        });
    }
};