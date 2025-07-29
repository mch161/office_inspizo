<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pesanan_detail';
    protected $primaryKey = 'kd_pesanan_detail';

    protected $fillable = [
        'kd_karyawan',
        'kd_pesanan',
        'keterangan',
        'kd_barang',
        'nama_barang',
        'jenis',
        'hpp',
        'laba',
        'harga_jual',
        'jumlah',
        'subtotal',
        'dibuat_oleh',
    ];

    /**
     * Get the main order that this detail belongs to.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'kd_pesanan', 'kd_pesanan');
    }
}