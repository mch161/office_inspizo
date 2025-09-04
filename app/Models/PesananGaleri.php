<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananGaleri extends Model
{
    protected $table = 'pesanan_galeri';

    protected $primaryKey = 'kd_galeri';

    protected $fillable = [
        'kd_pesanan',
        'kd_karyawan',
        'foto',
        'dibuat_oleh',
    ];
}
