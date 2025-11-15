<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;
    
    protected $table = 'quotation';
    protected $primaryKey = 'kd_quotation';

    protected $fillable = [
        'kd_tiket',
        'kd_pelanggan',
        'tanggal',
        'dibuat_oleh',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class, 'kd_quotation', 'kd_quotation');
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'kd_tiket', 'kd_tiket');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }
}