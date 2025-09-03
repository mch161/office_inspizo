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
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_barang;
            CREATE TRIGGER trg_barang
            AFTER INSERT ON barang
            FOR EACH ROW
            BEGIN
                INSERT INTO stok (
                    kd_karyawan,
                    kd_barang,
                    stok_masuk,
                    stok_keluar,
                    klasifikasi,
                    keterangan,
                    dibuat_oleh,
                    created_at,
                    updated_at
                ) VALUES (
                    NEW.kd_karyawan,
                    NEW.kd_barang,
                    NEW.stok,
                    0,
                    "Stok Awal",
                    "Stok awal saat barang dibuat.",
                    NEW.dibuat_oleh,
                    NOW(),
                    NOW()
                );
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_barang');
    }
};
