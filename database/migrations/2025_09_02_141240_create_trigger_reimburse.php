<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTriggerReimburse extends Migration
{
    public function up()
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_reimburse_accepted;
            CREATE TRIGGER trg_reimburse_accepted
            AFTER UPDATE ON reimburse
            FOR EACH ROW
            BEGIN
                IF NEW.status = 1 THEN
                    INSERT INTO keuangan (
                        kd_karyawan,
                        jenis,
                        keluar,
                        kd_kotak,
                        kd_kategori,
                        keterangan,
                        tanggal,
                        dibuat_oleh
                    ) VALUES (
                        NEW.kd_karyawan,
                        \'Keluar\',
                        NEW.nominal,
                        NEW.kotak,
                        NEW.kategori,
                        CONCAT(\'Reimburse: \', NEW.keterangan),
                        NEW.tanggal,
                        NEW.dibuat_oleh
                    );
                END IF;
            END;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_reimburse_accepted');
    }
}