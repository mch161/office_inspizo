<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $primaryKey = 'kd_peminjaman';

    protected $fillable = [
        'kd_barang',
        'jumlah',
        'kd_karyawan',
        'status'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kd_barang');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan');
    }
}
