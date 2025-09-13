<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'kd_pesanan';

    protected $fillable = [
        'kd_karyawan',
        'kd_pelanggan',
        'deskripsi_pesanan',
        'status',
        'tanggal',
        'progres',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date:d-m-Y',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }

    /**
     * Get all of the details for the Pesanan.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'kd_pesanan', 'kd_pesanan');
    }
}