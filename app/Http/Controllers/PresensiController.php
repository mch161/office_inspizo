<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

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
}