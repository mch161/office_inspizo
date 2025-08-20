<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('foto')->nullable()->change();
            $table->string('hpp')->nullable()->change();
            $table->string('klasifikasi')->nullable()->after('foto');
            $table->string('dijual')->nullable()->after('klasifikasi');
            $table->string('status')->default('1')->after('dibuat_oleh');
            $table->string('barcode')->nullable()->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            //
        });
    }
};
