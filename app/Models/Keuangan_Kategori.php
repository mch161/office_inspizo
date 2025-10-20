<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan_Kategori extends Model
{
    protected $table = 'keuangan_kategori';

    protected $primaryKey = 'kd_kategori';

    protected $fillable = [
        'nama',
        'dibuat_oleh',
    ];

    /**
     * Get the keuangan associated with the Keuangan_Kotak
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'kd_kategori', 'kd_kategori');
    }

    public function karyawan() 
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
