<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kd_barang';

    protected $fillable = [
        'kd_barang',
        'kd_kategori',
        'kd_karyawan',
        'nama_barang',
        'kode',
        'stok',
        'hpp',
        'harga_jual',
        'foto',
        'dibuat_oleh'
    ];
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
