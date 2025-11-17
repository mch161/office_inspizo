<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PesananBarang;
use App\Models\QuotationItem;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PesananBarangController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_quotation' => 'required|exists:quotation,kd_quotation',
            'kd_barang' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Barang gagal ditambahkan: ' . $validator->errors()->first());
        }

        $barang = Barang::find($request->kd_barang);

        if ($barang){
            $kd_barang = $barang->kd_barang;
        } else {
            $newBarang = Barang::firstOrCreate([
                'nama_barang' => $request->kd_barang,
            ], [
                'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
                'stok' => $request->jumlah,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
            $kd_barang = $newBarang->kd_barang;
        }

        $barang = Barang::find($kd_barang);

        $request->merge([
            'kd_barang' => $kd_barang
        ]);

        if ($request->jumlah > $barang->stok) {
            return redirect()->back()->with('error', 'Stok barang tidak cukup. Stok tersedia: ' . $barang->stok);
        }

        if (QuotationItem::where('kd_quotation', $request->kd_quotation)->where('kd_barang', $request->kd_barang)->exists()) {
            Stok::where('created_at', QuotationItem::where('kd_quotation', $request->kd_quotation)->where('kd_barang', $request->kd_barang)->first()->created_at)->update([
                'stok_keluar' => Stok::where('created_at', QuotationItem::where('kd_quotation', $request->kd_quotation)->where('kd_barang', $request->kd_barang)->first()->created_at)->first()->stok_keluar + $request->jumlah
            ]);
            Barang::where('kd_barang', $request->kd_barang)->update([
                'stok' => Barang::where('kd_barang', $request->kd_barang)->first()->stok - $request->jumlah
            ]);
            QuotationItem::where('kd_quotation', $request->kd_quotation)->where('kd_barang', $request->kd_barang)->update([
                'jumlah' => QuotationItem::where('kd_quotation', $request->kd_quotation)->where('kd_barang', $request->kd_barang)->first()->jumlah + $request->jumlah
            ]);
            return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
        }

        QuotationItem::create([
            'kd_quotation' => $request->kd_quotation,
            'kd_barang' => $request->kd_barang,
            'jumlah' => $request->jumlah,
            'harga'=> $barang->harga_jual,
            'subtotal' => $barang->harga_jual * $request->jumlah
        ]);

        Stok::create([
            'kd_karyawan' => Auth::id(),
            'kd_barang' => $request->kd_barang,
            'stok_keluar' => $request->jumlah,
            'stok_masuk' => 0,
            'klasifikasi' => 'Stok Keluar',
            'keterangan' => 'Penjualan barang ' . $barang->nama_barang,
            'dibuat_oleh' => Auth::user()->nama
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $barang_stok = Barang::find($request->kd_barang);


        $barang = QuotationItem::find($id);
        $barang->harga = $request->harga;
        $barang->jumlah = $request->jumlah;

        if ($barang->isDirty('jumlah') && $request->jumlah > $barang_stok->stok) {
            return redirect()->back()->with('error', 'Stok barang tidak cukup. Stok tersedia: ' . $barang_stok->stok);
        }
        Barang::where('kd_barang', $request->kd_barang)->update([
            'stok' => Barang::where('kd_barang', $request->kd_barang)->first()->stok + (QuotationItem::find($id)->jumlah - $request->jumlah)
        ]);

        $barang->subtotal = $request->harga_jual * $request->jumlah;
        $barang->save();

        Stok::where('created_at', $barang->created_at)->update([
            'stok_keluar' => $barang->jumlah
        ]);

        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Stok::where('created_at', QuotationItem::find($id)->created_at)->delete();
        Barang::where('kd_barang', QuotationItem::find($id)->kd_barang)->update([
            'stok' => Barang::where('kd_barang', QuotationItem::find($id)->kd_barang)->first()->stok + QuotationItem::find($id)->jumlah
        ]);
        QuotationItem::find($id)->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
}
