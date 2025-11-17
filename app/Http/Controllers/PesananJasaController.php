<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use App\Models\PesananJasa;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananJasaController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_quotation' => 'required|exists:quotation,kd_quotation',
            'kd_jasa' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Jasa gagal ditambahkan: ' . $validator->errors()->first());
        }

        $jasa = Jasa::find($request->kd_jasa);
        
        if (!$jasa) {
            $newJasa = Jasa::firstOrCreate([
                'nama_jasa' => $request->kd_jasa,
            ], [
                'tarif' => 0
            ]);
            $jasa = $newJasa;
            $request->merge(['kd_jasa' => $newJasa->kd_jasa]);
        }

        if (QuotationItem::where('kd_quotation', $request->quotation_item)->where('kd_jasa', $request->kd_jasa)->exists()) {
            QuotationItem::where('kd_jasa', $request->kd_jasa)->update([
                'jumlah' => QuotationItem::where('kd_quotation', $request->quotation_item)->where('kd_jasa', $request->kd_jasa)->first()->jumlah + $request->jumlah,
                'subtotal' => QuotationItem::where('kd_quotation', $request->quotation_item)->where('kd_jasa', $request->kd_jasa)->first()->subtotal + ($jasa->tarif * $request->jumlah)
            ]);
            return redirect()->back()->with('success', 'Jasa berhasil ditambahkan.');
        }

        QuotationItem::create([
            'kd_quotation' => $request->kd_quotation,
            'kd_jasa' => $request->kd_jasa,
            'harga' => $jasa->tarif,
            'jumlah' => $request->jumlah,
            'subtotal' => $jasa->tarif * $request->jumlah
        ]);

        return redirect()->back()->with('success', 'Jasa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pesanan = QuotationItem::find($id);
        $pesanan->update([
            'harga' => $request->harga_jasa,
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
