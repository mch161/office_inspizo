<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    protected $table = 'tiket';

    protected $primaryKey = 'kd_tiket';

    protected $fillable = [
        'prioritas',
        'jenis',
        'deskripsi',
        'tempat',
        'kd_pelanggan',
        'via',
        'tanggal',
        'dibuat_oleh'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }
}
