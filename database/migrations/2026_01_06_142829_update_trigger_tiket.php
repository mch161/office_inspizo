<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_tiket');

        DB::unprepared('
            CREATE TRIGGER trg_tiket AFTER INSERT ON tiket FOR EACH ROW
            BEGIN
                INSERT INTO pesanan (kd_pelanggan, progres, jenis, kd_tiket, dibuat_oleh, created_at, updated_at)
                VALUES (NEW.kd_pelanggan, "2", "Quotation", NEW.kd_tiket, NEW.dibuat_oleh, NOW(), NOW());

                INSERT INTO quotation (kd_tiket, dibuat_oleh, created_at, updated_at)
                VALUES (NEW.kd_tiket, NEW.dibuat_oleh, NOW(), NOW());
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_tiket');
    }
};