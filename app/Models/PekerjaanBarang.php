<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PekerjaanBarang extends Model
{
    protected $table = 'pekerjaan_barang';

    protected $primaryKey = 'kd_pekerjaan_barang';

    protected $fillable = [
        'kd_pekerjaan',
        'kd_barang',
        'jumlah',
        'dibuat_oleh',
    ];

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'kd_pekerjaan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kd_barang');
    }
}
