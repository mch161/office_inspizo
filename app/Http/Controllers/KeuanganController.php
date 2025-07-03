<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    public function index()
    {
        $keuangans = Keuangan::with('karyawan')->get();
        return view('karyawan.keuangan', [
            "keuangans" => $keuangans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'required|string',
        ]);

        $keuangan = new Keuangan();
        $keuangan->kd_karyawan = Auth::id();
        $keuangan->tanggal = $request->tanggal;
        $keuangan->jumlah = $request->jumlah;
        $keuangan->keterangan = $request->keterangan;
        $keuangan->save();

        if ($keuangan) {
            return redirect()->route('keuangan.index')
                ->with('success', 'Keuangan berhasil dibuat.');
        }
        else {
            return redirect()->route('keuangan.index')
                ->with('error', 'Keuangan gagal dibuat.');
        }
    }
}

