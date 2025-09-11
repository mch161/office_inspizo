<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPerintahKerja extends Model
{
    protected $table = 'surat_perintah_kerja';

    protected $primaryKey = 'kd_surat_perintah_kerja';

    protected $fillable = [
        'kd_pesanan',
        'kd_project',
        'kd_karyawan',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status',
        'dibuat_oleh',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'kd_pesanan', 'kd_pesanan');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'kd_project', 'kd_project');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
