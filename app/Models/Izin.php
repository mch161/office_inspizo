<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'izin';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'kd_izin';



    protected $fillable = [''];
    /**
     * Define the relationship to the Karyawan model.
     * Assumes you have a Karyawan model.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
