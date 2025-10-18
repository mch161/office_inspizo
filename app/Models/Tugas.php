<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $primaryKey = 'kd_tugas';

    protected $fillable = [
        'kd_pekerjaan',
        'kd_karyawan',
        'tanggal',
        'status',
    ];

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'kd_pekerjaan');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan');
    }
}
