<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PekerjaanGaleri extends Model
{
    protected $table = 'pekerjaan_galeri';

    protected $primaryKey = 'kd_galeri';

    protected $fillable = [
        'kd_pekerjaan',
        'kd_karyawan',
        'foto',
        'dibuat_oleh',
    ];
}