<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kd_barang';

    protected $fillable = [
        'kd_kategori',
        'kd_karyawan',
        'nama_barang',
        'barcode',
        'foto',
        'klasifikasi',
        'dijual',
        'kondisi',
        'keterangan',
        'kode',
        'hpp',
        'harga_jual',
        'stok',
        'dibuat_oleh'
    ];
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
