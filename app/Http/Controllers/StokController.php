<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { {
            $stok = stok::with('karyawan')->get();
            return view('karyawan.barang.stok', [
                "stok" => $stok
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_barang' => 'required|exists:barang,kd_barang',
            'jumlah' => 'required|numeric|min:1',
            'klasifikasi' => 'required|in:Stok Masuk,Stok Keluar',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barang.index')
                ->with('error', 'Gagal memperbarui stok: ' . $validator->errors()->first());
        }

        $klasifikasi = $request->klasifikasi;
        $jumlah = $request->jumlah;

        if ($klasifikasi == 'Stok Keluar') {
            $barang = Barang::findOrFail($request->kd_barang);
            if ($barang->stok < $jumlah) {
                return redirect()->route('barang.index')->with('error', 'Stok tidak mencukupi untuk dikurangi.');
            }
        }

        Stok::create([
            'kd_karyawan' => Auth::id(),
            'kd_barang' => $request->kd_barang,
            'stok_masuk' => ($klasifikasi == 'Stok Masuk') ? $jumlah : 0,
            'stok_keluar' => ($klasifikasi == 'Stok Keluar') ? $jumlah : 0,
            'klasifikasi' => $klasifikasi,
            'keterangan' => $request->keterangan,
            'dibuat_oleh' => Auth::user()->nama,
        ]);

        $message = ($klasifikasi == 'Stok Masuk') ? 'Stok berhasil ditambahkan.' : 'Stok berhasil dikurangi.';
        return redirect()->route('barang.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
