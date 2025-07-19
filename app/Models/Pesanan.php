<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'kd_pesanan';
    protected $fillable = [
        'deskripsi_pesanan',
        'status',
        'progres',
        'kd_karyawan',
        'dibuat_oleh',
    ];
}