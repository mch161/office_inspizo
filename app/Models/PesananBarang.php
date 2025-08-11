<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananBarang extends Model
{
    protected $table = 'pesanan_barang';

    protected $primaryKey = 'kd_pesanan_barang';

    protected $fillable = [
        'kd_pesanan_detail',
        'kd_barang',
        'nama_barang',
        'hpp',
        'laba',
        'harga_jual',
        'jumlah',
        'subtotal',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kd_barang', 'kd_barang');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'kd_pesanan', 'kd_pesanan');
    }
}