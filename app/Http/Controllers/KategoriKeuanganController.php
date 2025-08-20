<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\Keuangan_Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriKeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keuangan = Keuangan::get();
        $kategori = Keuangan_Kategori::with('karyawan')->get();
        return view('karyawan.keuangan.kategori', compact('kategori', 'keuangan'));
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
        $request->validate([
            'nama' => 'required|string',
        ]);

        $kategori = new Keuangan_Kategori();
        $kategori->nama = $request->nama;
        $kategori->dibuat_oleh = Auth::user()->nama;
        $kategori->save();
        return redirect()->route('kategori.index')->with('success', 'kategori keuangan berhasil dibuat.');
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
    public function update(Request $request, $id)
    {
        $kategori = Keuangan_Kategori::findOrFail($id);

        $request->validate([
            'nama' => 'required|string',
        ]);

        $kategori->nama = $request->nama;
        $kategori->dibuat_oleh = Auth::user()->nama;
        $kategori->save();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keuangan_Kategori $kategori)
    {
        $kategori->delete();
        
        if ($kategori) {
            return redirect()->route('kategori.index')
                ->with('success', 'kategori keuangan berhasil dihapus.');
        }
        else {
            return redirect()->route('kategori.index')
                ->with('error', 'kategori keuangan gagal dihapus.');
        }
    }
}
