<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $primaryKey = 'kd_kunjungan';

    protected $fillable = [
        'kd_pesanan',
        'kd_pelanggan',
        'kd_karyawan',
        'tanggal',
        'status',
        'keterangan',
        'dibuat_oleh',
    ];

    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'kunjungan_karyawan', 'kd_kunjungan', 'kd_karyawan');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'kd_pesanan', 'kd_pesanan');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }
}
