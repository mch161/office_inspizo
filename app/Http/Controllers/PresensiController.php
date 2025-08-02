<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = ($request->input('tanggal') ?? Carbon::now()->format('Y-m-d'));

        $rekapData = Presensi::whereDate('tanggal', $tanggal)
                            ->orderBy('nama', 'asc')
                            ->get();
                            
        $karyawans = Karyawan::all();

        return view('karyawan.presensi.presensi', [
            'rekapData' => $rekapData,
            'tanggal' => $tanggal,
            'karyawans' => $karyawans
        ]);
    }

    public function fetch(Request $request)
    {
        if ($request->has('all')) {
            Artisan::call('presensi:fetch --all');
            return redirect()->route('presensi.index')->with('success', 'Semua data presensi berhasil diambil.');
        }
        $tanggal = $request->input('tanggal');
        Artisan::call('presensi:fetch --dari=' . $tanggal . ' --sampai=' . $tanggal);
        $tanggalMessage = Carbon::parse($tanggal)->locale('id_ID')->translatedFormat('d F Y');
        return redirect()->route('presensi.index', ['tanggal' => $tanggal])->with('success', 'Data presensi tanggal ' . $tanggalMessage . ' berhasil diambil.');
    }

    public function view(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $kd_karyawan = $request->input('kd_karyawan');
        $karyawan = Karyawan::where('kd_karyawan', $kd_karyawan)->first();
        $karyawans = Karyawan::all();
        $rekapData = Presensi::whereYear('tanggal', $tahun)
                            ->whereMonth('tanggal', $bulan)
                            ->where('kd_karyawan', $kd_karyawan)
                            ->orderBy('tanggal', 'asc')
                            ->get();
        return view('karyawan.presensi.presensi', [
            'rekapData' => $rekapData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kd_karyawan' => $kd_karyawan,
            'karyawan' => $karyawan,
            'karyawans' => $karyawans
        ]);
    }
}