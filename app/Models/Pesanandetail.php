<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail';

    protected $primaryKey = 'kd_pesanan_detail';

    protected $fillable = [
        'kd_pesanan',
        'kd_pelanggan',
        'keterangan',
        'dibuat_oleh',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'kd_pesanan', 'kd_pesanan');
    }
}