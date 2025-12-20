<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiBulanan extends Model
{
    protected $table = 'presensi_bulanan';

    protected $primaryKey = 'kd_presensi_bulanan';

    protected $fillable = [
        'kd_presensi_bulanan',
        'kd_karyawan',
        'bulan',
        'tahun',
        'jumlah_tanggal',
        'jumlah_libur',
        'jumlah_hari_cuti',
        'jumlah_hari_kerja_normal',
        'jumlah_hari_minggu',
        'jumlah_hari_sakit',
        'jumlah_hari_izin',
        'jumlah_fingerprint',
        'jumlah_alpha',
        'jumlah_terlambat',
        'jumlah_jam_izin',
        'jumlah_hari_lembur',
        'jumlah_jam_lembur',
        'verifikasi',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}