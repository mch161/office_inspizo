<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Authenticatable;

class Karyawan extends Model
{
    use Authenticatable;

    protected $table = 'karyawan';

    protected $primaryKey = 'kd_karyawan';

    protected $fillable = [
        // ...
        'role',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}