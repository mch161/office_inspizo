<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JasaController extends Controller
{
    public function index()
    {
        $jasa = Jasa::all();
        return view('karyawan.jasa.jasa', compact('jasa'));
    }

    public function store(Request $request)
    {
        $validaror = Validator::make($request->all(), [
            'nama_jasa' => 'required',
            'tarif' => 'required|numeric',
        ]);

        if ($validaror->fails()) {
            return redirect()->back()->with('error', $validaror->errors()->first());
        }

        Jasa::create([
            'nama_jasa' => $request->nama_jasa,
            'tarif' => $request->tarif,
        ]);

        return redirect()->back()->with('success', 'Jasa berhasil ditambahkan.');
    }

    public function update(Request $request, Jasa $jasa)
    {
        $validaror = Validator::make($request->all(), [
            'nama_jasa' => 'required',
            'tarif' => 'required|numeric',
        ]);

        if ($validaror->fails()) {
            return redirect()->back()->with('error', $validaror->errors()->first());
        }

        $jasa->update([
            'nama_jasa' => $request->nama_jasa,
            'tarif' => $request->tarif,
        ]);

        return redirect()->back()->with('success', 'Jasa berhasil diubah.');
    }

    public function destroy(Jasa $jasa)
    {
        $jasa->delete();
        return redirect()->back()->with('success', 'Jasa berhasil dihapus.');
    }
}
