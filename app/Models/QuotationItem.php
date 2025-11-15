<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $table = 'quotation_item';
    protected $primaryKey = 'kd_quotation_item';

    protected $fillable = [
        'kd_quotation',
        'kd_barang',
        'kd_jasa',
        'harga',
        'jumlah',
    ];
}