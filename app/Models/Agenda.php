<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
    protected $primaryKey = 'kd_agenda'; // This line is crucial

    protected $fillable = [
        'title',
        'start',
        'end',
        'kd_karyawan',
        'dibuat_oleh',
    ];
}