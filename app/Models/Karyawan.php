<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
    protected $table = 'karyawan';

    protected $primaryKey = 'kd_karyawan';

    protected $fillable = [
        'nama',
        'telp',
        'alamat',
        'nip',
        'nik',
        'email',
        'username',
        'password',
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