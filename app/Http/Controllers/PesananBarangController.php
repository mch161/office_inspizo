<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PesananBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananBarangController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan_detail' => 'required|exists:pesanan_detail,kd_pesanan_detail',
            'kd_barang' => 'required|exists:barang,kd_barang',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Barang gagal ditambahkan: ' . $validator->errors()->first());
        }

        $barang = Barang::find($request->kd_barang);
        if ($request->jumlah > $barang->stok) {
            return redirect()->back()->with('error', 'Stok barang tidak cukup. Stok tersedia: ' . $barang->stok);
        }

        if (PesananBarang::where('kd_pesanan_detail', $request->kd_pesanan_detail)->where('kd_barang', $request->kd_barang)->exists()) {
            PesananBarang::where('kd_pesanan_detail', $request->kd_pesanan_detail)->where('kd_barang', $request->kd_barang)->update([
                'jumlah' => PesananBarang::where('kd_pesanan_detail', $request->kd_pesanan_detail)->where('kd_barang', $request->kd_barang)->first()->jumlah + $request->jumlah
            ]);
            return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
        }

        PesananBarang::create([
            'kd_pesanan_detail' => $request->kd_pesanan_detail,
            'kd_barang' => $request->kd_barang,
            'nama_barang' => $barang->nama_barang,
            'hpp' => $barang->hpp,
            'laba' => $barang->harga_jual - $barang->hpp,
            'harga_jual' => $barang->harga_jual,
            'jumlah' => $request->jumlah,
            'subtotal' => $barang->harga_jual * $request->jumlah
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $barang_stok = Barang::find($request->kd_barang);
        if ($request->jumlah > $barang_stok->stok) {
            return redirect()->back()->with('error', 'Stok barang tidak cukup. Stok tersedia: ' . $barang_stok->stok);
        }
        $barang = PesananBarang::find($id);
        $barang->laba = $request->harga_jual - $barang->hpp;
        $barang->harga_jual = $request->harga_jual;
        $barang->jumlah = $request->jumlah;
        $barang->subtotal = $request->harga_jual * $request->jumlah;
        $barang->save();
        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PesananBarang::find($id)->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
}
