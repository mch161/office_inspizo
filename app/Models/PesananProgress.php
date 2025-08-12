<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananProgress extends Model
{
    protected $table = 'pesanan_progress';

    protected $primaryKey = 'kd_pesanan_progress';

    protected $fillable = [
        'kd_pesanan',
        'kd_karyawan',
        'keterangan',
        'dibuat_oleh',
    ];
}
