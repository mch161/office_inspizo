<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pelanggan extends Authenticatable
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'kd_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'nama_perusahaan',
        'alamat_pelanggan',
        'telp_pelanggan',
        'nik',
        'username',
        'password',
        'email',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}