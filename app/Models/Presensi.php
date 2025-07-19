<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'presensi';


    protected $fillable = [
        'kd_karyawan',
        'nama',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'terlambat',
        'pulang_cepat',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
   protected $casts = [
        'tanggal' => 'date',
    ];
}