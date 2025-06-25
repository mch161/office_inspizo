<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jurnal';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'kd_jurnal';

    /**
     * Define the relationship to the Karyawan model.
     * Assumes you have a Karyawan model.
     */
    public function karyawan()
    {
        // Links this model's 'kd_user' to the 'karyawan' model's 'kd_karyawan'
        return $this->belongsTo(Karyawan::class, 'kd_user', 'kd_karyawan');
    }
}