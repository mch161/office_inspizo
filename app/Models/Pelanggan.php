<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pelanggan';
    protected $primaryKey = 'kd_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'nama_perusahaan',
        'alamat_pelanggan',
        'telp_pelanggan',
        'nik',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}