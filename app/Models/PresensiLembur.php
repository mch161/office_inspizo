<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiLembur extends Model
{
    protected $table = 'presensi_lembur';

    protected $primaryKey = 'kd_lembur';

    protected $fillable = [
        'kd_lembur',
        'kd_karyawan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jumlah_jam',
        'keterangan',
        'verifikasi',
        'dibuat_oleh'
    ];
}
