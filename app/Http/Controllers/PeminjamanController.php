<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index()
    {
        $barangs = Barang::get()->all();
        $dipinjam = Peminjaman::get()->all();
        return view('karyawan.barang.peminjaman', compact('barangs', 'dipinjam'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_barang' => 'required|exists:barang,kd_barang',
            'jumlah' => 'required',
        ]);
        
        $barang = Barang::find($request->kd_barang);
        if ($barang->stok < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi.');
        }
        
        $dipinjam = new Peminjaman();
        $dipinjam->kd_barang = $request->kd_barang;
        $dipinjam->jumlah = $request->jumlah;
        $dipinjam->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $dipinjam->save();
        
        return redirect()->back()->with('success', 'Barang berhasil dipinjam.');
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $peminjaman->status = $request->status;
        $peminjaman->save();
        return redirect()->back()->with('success', 'Barang berhasil dikembalikan.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        $peminjaman->delete();
        return redirect()->back()->with('success', 'Barang berhasil dibatalkan.');
    }
}
