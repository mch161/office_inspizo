<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    protected $table = 'keuangan';

    protected $primaryKey = 'kd_keuangan';

    protected $fillable = [
        'kd_karyawan',
        'jenis',
        'status',
        'masuk',
        'keluar',
        'kd_kotak',
        'kd_kategori',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function kotak()
    {
        return $this->belongsTo(Keuangan_Kotak::class, 'kd_kotak', 'kd_kotak');
    }

    public function kategori()
    {
        return $this->belongsTo(Keuangan_Kategori::class, 'kd_kategori', 'kd_kategori');
    }

}
