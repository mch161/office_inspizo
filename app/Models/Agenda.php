<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
    protected $primaryKey = 'kd_agenda';

    protected $fillable = [
        'title',
        'color',
        'start',
        'end',
        'kd_karyawan',
        'dibuat_oleh',
    ];
}