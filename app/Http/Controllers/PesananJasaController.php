<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use App\Models\PesananJasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananJasaController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan_detail' => 'required|exists:pesanan_detail,kd_pesanan_detail',
            'kd_jasa' => 'required|exists:jasa,kd_jasa',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Jasa gagal ditambahkan: ' . $validator->errors()->first());
        }

        $jasa = Jasa::find($request->kd_jasa);

        if (PesananJasa::where('kd_pesanan_detail', $request->kd_pesanan_detail)->where('kd_jasa', $request->kd_jasa)->exists()) {
            PesananJasa::where('kd_jasa', $request->kd_jasa)->update([
                'jumlah' => PesananJasa::where('kd_pesanan_detail', $request->kd_pesanan_detail)->where('kd_jasa', $request->kd_jasa)->first()->jumlah + $request->jumlah,
                'subtotal' => PesananJasa::where('kd_pesanan_detail', $request->kd_pesanan_detail)->where('kd_jasa', $request->kd_jasa)->first()->subtotal + ($jasa->tarif * $request->jumlah)
            ]);
            return redirect()->back()->with('success', 'Jasa berhasil ditambahkan.');
        }

        $pesanan = PesananJasa::create([
            'kd_pesanan_detail' => $request->kd_pesanan_detail,
            'kd_jasa' => $request->kd_jasa,
            'nama_jasa' => $jasa->nama_jasa,
            'harga_jasa' => $jasa->tarif,
            'jumlah' => $request->jumlah,
            'subtotal' => $jasa->tarif * $request->jumlah
        ]);

        return redirect()->back()->with('success', 'Jasa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pesanan = PesananJasa::find($id);
        $pesanan->update([
            'harga_jasa' => $request->harga_jasa,
            'jumlah' => $request->jumlah,
            'subtotal' => $request->harga_jasa * $request->jumlah
        ]);
        return redirect()->back()->with('success', 'Jasa berhasil diubah.');
    }
    public function destroy($id)
    {
        PesananJasa::find($id)->delete();
        return redirect()->back()->with('success', 'Jasa berhasil dihapus.');
    }
}
