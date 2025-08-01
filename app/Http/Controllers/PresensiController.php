<?php

namespace App\Http\Controllers;

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

        return view('karyawan.presensi.presensi', [
            'rekapData' => $rekapData,
            'tanggal' => $tanggal,
        ]);
    }

    public function fetch(Request $request)
    {
        $tanggal = $request->input('tanggal');
        Artisan::call('presensi:fetch --dari=' . $tanggal . ' --sampai=' . $tanggal);
        $tanggalMessage = Carbon::parse($tanggal)->locale('id_ID')->translatedFormat('d F Y');
        return redirect()->route('presensi.index', ['tanggal' => $tanggal])->with('success', 'Data presensi tanggal ' . $tanggalMessage . ' berhasil diambil.');
    }
}