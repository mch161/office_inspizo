<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiLibur extends Model
{
    protected $table = 'presensi_libur';

    protected $primaryKey = 'kd_presensi_libur';

    protected $fillable = [
        'tanggal',
        'jenis_libur',
        'keterangan'
    ];
}
