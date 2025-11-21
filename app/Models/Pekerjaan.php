<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaan';

    protected $primaryKey = 'kd_pekerjaan';

    protected $fillable = [
        'kd_pelanggan',
        'kd_karyawan',
        'kd_tiket',
        'tanggal',
        'jenis',
        'keterangan_pekerjaan',
        'keterangan_barang',
        'status',
        'ttd_pelanggan',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }

    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'pekerjaan_karyawan', 'kd_pekerjaan', 'kd_karyawan');
    }

    public function karyawan()
    {
        return $this->belongsToMany(Karyawan::class, 'pekerjaan_karyawan', 'kd_pekerjaan', 'kd_karyawan');
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'kd_tiket', 'kd_tiket');
    }
}
