<?php

namespace App\Http\Controllers;

use App\Models\PesananJasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananJasaController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan_detail' => 'required|exists:pesanan_detail,kd_pesanan_detail',
            'nama_jasa' => 'required|string',
            'harga_jasa' => 'required|numeric',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Jasa gagal ditambahkan: ' . $validator->errors()->first());
        }

        $pesanan = PesananJasa::create([
            'kd_pesanan_detail' => $request->kd_pesanan_detail,
            'nama_jasa' => $request->nama_jasa,
            'harga_jasa' => $request->harga_jasa,
            'jumlah' => $request->jumlah,
            'subtotal' => $request->harga_jasa * $request->jumlah
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
