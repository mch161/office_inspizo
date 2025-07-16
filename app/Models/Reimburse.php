<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimburse extends Model
{
    protected $table = 'reimburse';
    protected $primaryKey = 'kd_reimburse';

    protected $fillable = [
        'kd_karyawan',
        'tanggal',
        'jam',
        'foto',
        'keterangan',
        'status',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
