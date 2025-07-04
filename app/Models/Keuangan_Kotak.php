<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan_Kotak extends Model
{
    protected $table = 'keuangan_kotak';
    protected $primaryKey = 'kd_kotak';

    /**
     * Get the keuangan associated with the Keuangan_Kotak
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'kd_kotak', 'kd_kotak');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
