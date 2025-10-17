<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaan';

    protected $primaryKey = 'kd_pekerjaan';

    protected $fillable = [
        'kd_project',
        'kd_karyawan',
        'pekerjaan',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'kd_project');
    }

    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'pekerjaan_karyawan', 'kd_pekerjaan', 'kd_karyawan');
    }

    public function karyawan()
    {
        return $this->belongsToMany(Karyawan::class, 'pekerjaan_karyawan', 'kd_pekerjaan', 'kd_karyawan');
    }
}
