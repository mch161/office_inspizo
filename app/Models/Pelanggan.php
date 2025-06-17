<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Authenticatable;

class Pelanggan extends Model
{
    use Authenticatable;

    protected $table = 'pelanggan';

    protected $primaryKey = 'kd_pelanggan';
}