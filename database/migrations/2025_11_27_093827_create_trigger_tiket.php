<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->string('tanggal', 20)->nullable()->after('deskripsi');
            $table->string('dibuat_oleh', 100)->nullable()->after('via');
        });

        DB::statement("
            UPDATE tiket t
            JOIN pesanan p ON t.kd_tiket = p.kd_tiket
            SET t.tanggal = p.tanggal
            WHERE p.tanggal IS NOT NULL
        ");

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('deskripsi_pesanan');
            $table->dropColumn('tanggal');
        });

        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign(['kd_pelanggan']);
            $table->dropColumn('kd_pelanggan');
            $table->dropColumn('tanggal');
        });

        DB::statement("
            CREATE TRIGGER trg_tiket
            AFTER INSERT ON tiket
            FOR EACH ROW
            BEGIN
                INSERT INTO pesanan (kd_pelanggan, deskripsi_pesanan, progress, jenis, kd_tiket, dibuat_oleh)
                VALUES (NEW.kd_pelanggan, NEW.deskripsi, '1', 'Quotation', NEW.kd_tiket, CONCAT(NEW.dibuat_oleh, ' (', NOW(), ')'));

                INSERT INTO quotation (kd_tiket, dibuat_oleh)
                VALUES (NEW.kd_tiket, CONCAT(NEW.dibuat_oleh, ' (', NOW(), ')'));
            END"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_tiket');
        
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('deskripsi_pesanan', 255)->nullable()->after('kd_tiket');
            $table->string('tanggal', 20)->nullable()->after('deskripsi_pesanan');
        });

        DB::statement("
            UPDATE pesanan p
            JOIN tiket t ON p.kd_tiket = t.kd_tiket
            SET p.tanggal = t.tanggal
            WHERE t.tanggal IS NOT NULL
        ");

        Schema::table('tiket', function (Blueprint $table) {
            $table->dropColumn('tanggal');
            $table->dropColumn('dibuat_oleh');
        });


        Schema::table('quotation', function (Blueprint $table) {
            $table->unsignedBigInteger('kd_pelanggan')->nullable()->after('kd_tiket');
            $table->foreign('kd_pelanggan')->references('kd_pelanggan')->on('pelanggan')->onDelete('cascade');
            $table->string('tanggal', 20)->nullable()->after('kd_pelanggan');
        });
    }
};
