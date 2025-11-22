<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $table = 'invoice_item';

    protected $primaryKey = 'kd_invoice_item';

    protected $fillable = [
        'kd_invoice',
        'kd_barang',
        'kd_jasa',
        'harga',
        'jumlah',
        'subtotal'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'kd_invoice');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kd_barang');
    }

    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'kd_jasa');
    }
}
