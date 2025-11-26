<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PesananProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananProgressController extends Controller
{
    public function index($pesanan)
    {
        $pesanan = Pesanan::find($pesanan);
        $progress = PesananProgress::where('kd_pesanan', $pesanan->kd_pesanan)->get();
        return view('karyawan.tiket.progress', compact('pesanan', 'progress'));
    }

    public function store(Request $request, $pesanan)
    {
        $request->validate([
            'keterangan' => 'required|string',
        ]);
        $progress = PesananProgress::create([
            'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
            'kd_pesanan' => $pesanan,
            'keterangan' => $request->keterangan,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        if ($progress) {
            return redirect()->route('progress.index', $pesanan)->with('success', 'Progress berhasil ditambahkan.');
        } else {
            return redirect()->route('progress.index', $pesanan)->with('error', 'Progress gagal ditambahkan.');
        }
    }

    public function update(Request $request, $pesanan, $progress)
    {
        $request->validate([
            'keterangan' => 'required|string',
        ]);
        $progress = PesananProgress::find($progress);
        $progress->keterangan = $request->keterangan;
        $progress->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $progress->save();
        return redirect()->route('progress.index', $pesanan)->with('success', 'Progress berhasil diubah.');
    }

    public function destroy($pesanan, $progress)
    {
        $progress = PesananProgress::find($progress);
        $progress->delete();
        return redirect()->route('progress.index', $pesanan)->with('success', 'Progress berhasil dihapus.');
    }
}
