<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananJasa extends Model
{
    protected $table = 'pesanan_jasa';

    protected $primaryKey = 'kd_pesanan_jasa';

    protected $fillable = [
        'kd_pesanan_detail',
        'kd_jasa',
        'nama_jasa',
        'harga_jasa',
        'jumlah',
        'subtotal',
    ];
}