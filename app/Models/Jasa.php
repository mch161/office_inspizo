<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jasa extends Model
{
    protected $table = 'jasa';
    
    protected $primaryKey = 'kd_jasa';
    
    protected $fillable = [
        'nama_jasa',
        'tarif',
    ];
}
