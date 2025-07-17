<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;
    protected $table = 'stok';

    protected $fillable = [
        'kd_barang',
        'kd_karyawan',
        'stok_masuk',
        'stok_keluar',
        'klasifikasi',
        'keterangan',
        'dibuat_oleh',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kd_barang', 'kd_barang');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}