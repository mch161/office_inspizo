<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';
    
    protected $primaryKey = 'kd_invoice';
    
    protected $fillable = [
        'kd_quotation',
        'kd_pelanggan',
        'kd_karyawan',
        'tanggal',
        'dibuat_oleh',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'kd_quotation');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan');
    }
}
