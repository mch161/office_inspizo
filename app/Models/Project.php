<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';

    protected $primaryKey = 'kd_project';

    protected $fillable = [
        'nama_project',
        'foto',
        'kd_karyawan',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'status',
        'dibuat_oleh',
    ];
}
