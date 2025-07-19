<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fingerprint extends Model
{
    protected $table = 'fingerprint';

    protected $fillable = [
        'user_id',
        'timestamp',
        'verified',
        'status',
    ];
}
