<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\Keuangan_Kategori;
use App\Models\Keuangan_Kotak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
public function index()
{
    $keuangans = Keuangan::with('karyawan')->get();
    $kotak = Keuangan_Kotak::all();
    $kategori = Keuangan_Kategori::all();
    return view('karyawan.keuangan.keuangan', [
        "keuangans" => $keuangans,
        "kotak" => $kotak,
        "kategori" => $kategori
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required',
            'kotak' => 'required',
            'kategori' => 'required',
            'masuk' => 'numeric',
            'keluar' => 'numeric',
            'keterangan' => 'required|string',
        ]);

        $keuangan = new Keuangan();
        $keuangan->kd_karyawan = Auth::id();
        $keuangan->tanggal = $request->tanggal;
        $keuangan->jenis = $request->jenis;
        $keuangan->kd_kotak = $request->kotak;
        $keuangan->kd_kategori = $request->kategori;
        $keuangan->masuk = $request->input('masuk', 0);
        $keuangan->keluar = $request->input('keluar', 0);
        $keuangan->keterangan = $request->keterangan;
        $keuangan->dibuat_oleh = Auth::user()->nama;
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

    public function destroy(Keuangan $keuangan)
    {
        $keuangan->delete();

        if ($keuangan) {
            return redirect()->route('keuangan.index')
                ->with('success', 'Keuangan berhasil dihapus.');
        }
        else {
            return redirect()->route('keuangan.index')
                ->with('error', 'Keuangan gagal dihapus.');
        }
    }
}

