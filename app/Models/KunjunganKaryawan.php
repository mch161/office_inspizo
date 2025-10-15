<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganKaryawan extends Model
{
    protected $table = 'kunjungan_karyawan';

    protected $primaryKey = 'kd_kunjungan_karyawan';

    protected $fillable = [
        'kd_kunjungan',
        'kd_karyawan',
        'dibuat_oleh',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kd_kunjungan', 'kd_kunjungan');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
