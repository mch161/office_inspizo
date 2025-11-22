<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Jasa;
use App\Models\Pesanan;
use App\Models\PesananJasa;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananJasaController extends Controller
{
    public function store(Request $request, $jenis, $id)
    {
        $validator = Validator::make($request->all(), [
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

        if ($jenis == 'Quotation') {
            if (QuotationItem::where('kd_quotation', $request->quotation_item)->where('kd_jasa', $request->kd_jasa)->exists()) {
                QuotationItem::where('kd_jasa', $request->kd_jasa)->update([
                    'jumlah' => QuotationItem::where('kd_quotation', $request->quotation_item)->where('kd_jasa', $request->kd_jasa)->first()->jumlah + $request->jumlah,
                    'subtotal' => QuotationItem::where('kd_quotation', $request->quotation_item)->where('kd_jasa', $request->kd_jasa)->first()->subtotal + ($jasa->tarif * $request->jumlah)
                ]);
                return redirect()->back()->with('success', 'Jasa berhasil ditambahkan.');
            }

            QuotationItem::create([
                'kd_quotation' => $id,
                'kd_jasa' => $request->kd_jasa,
                'harga' => $jasa->tarif,
                'jumlah' => $request->jumlah,
                'subtotal' => $jasa->tarif * $request->jumlah
            ]);
        } elseif ($jenis == 'Invoice') {
            if (InvoiceItem::where('kd_invoice', $request->invoice_item)->where('kd_jasa', $request->kd_jasa)->exists()) {
                InvoiceItem::where('kd_jasa', $request->kd_jasa)->update([
                    'jumlah' => InvoiceItem::where('kd_invoice', $request->invoice_item)->where('kd_jasa', $request->kd_jasa)->first()->jumlah + $request->jumlah,
                    'subtotal' => InvoiceItem::where('kd_invoice', $request->invoice_item)->where('kd_jasa', $request->kd_jasa)->first()->subtotal + ($jasa->tarif * $request->jumlah)
                ]);
                return redirect()->back()->with('success', 'Jasa berhasil ditambahkan.');
            }

            InvoiceItem::create([
                'kd_invoice' => $id,
                'kd_jasa' => $request->kd_jasa,
                'harga' => $jasa->tarif,
                'jumlah' => $request->jumlah,
                'subtotal' => $jasa->tarif * $request->jumlah
            ]);
        }


        return redirect()->back()->with('success', 'Jasa berhasil ditambahkan');
    }

    public function update(Request $request, $jenis, $id)
    {
        if ($jenis == 'Quotation') {
            $pesanan = QuotationItem::find($id);
            $pesanan->update([
                'harga' => $request->harga_jasa,
                'jumlah' => $request->jumlah,
                'subtotal' => $request->harga_jasa * $request->jumlah
            ]);
            return redirect()->back()->with('success', 'Jasa berhasil diubah.');
        } elseif ($jenis == 'Invoice') {
            $pesanan = InvoiceItem::find($id);
            $pesanan->update([
                'harga' => $request->harga_jasa,
                'jumlah' => $request->jumlah,
                'subtotal' => $request->harga_jasa * $request->jumlah
            ]);
            return redirect()->back()->with('success', 'Jasa berhasil diubah.');
        }
    }
    public function destroy($jenis, $id)
    {
        if ($jenis == 'Quotation') {
            $pesanan = QuotationItem::find($id);
            $pesanan->delete();
            return redirect()->back()->with('success', 'Jasa berhasil dihapus.');
        } elseif ($jenis == 'Invoice') {
            $pesanan = InvoiceItem::find($id);
            $pesanan->delete();
            return redirect()->back()->with('success', 'Jasa berhasil dihapus.');
        }
    }
}
