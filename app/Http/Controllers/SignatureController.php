<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignatureController extends Controller
{
    public function index($id)
    {
        $pesanan = Pesanan::find($id);
        $signature = Signature::where('kd_pesanan', $id)->first();
        return view('karyawan.pesanan.signature', compact('pesanan', 'signature'));
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signed' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        Signature::where('kd_pesanan', $id)->delete();

        $signature = new Signature();
        $signature->kd_pesanan = $id;
        $signature->signature = $request->signed;
        $signature->save();

        return redirect()->route('pesanan.detail', ['pesanan' => $id])->with('success', 'Signature berhasil disimpan.');
    }
}
