<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');

        if ($tanggal) {
            $rekapData = Presensi::whereDate('tanggal', $tanggal)
                            ->orderBy('nama', 'asc')
                            ->get();
        } else {
            $rekapData = Presensi::orderBy('tanggal', 'desc')
                            ->orderBy('nama', 'asc')
                            ->get();
        }


        return view('karyawan.presensi.presensi', [
            'rekapData' => $rekapData,
            'tanggal' => $tanggal,
        ]);
    }
}