<?php

namespace App\Http\Controllers;

use App\Models\Keuangan_Kotak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KotakKeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kotak = Keuangan_Kotak::with('karyawan')->get();
        return view('karyawan.keuangan.kotak', [
            "kotak" => $kotak
        ]);
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

        $kotak = new Keuangan_Kotak();
        $kotak->nama = $request->nama;
        $kotak->dibuat_oleh = Auth::user()->nama;
        $kotak->save();
        return redirect()->route('kotak.index')->with('success', 'Kotak keuangan berhasil dibuat.');
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
    public function destroy(Keuangan_Kotak $kotak)
    {
        $kotak->delete();
        
        if ($kotak) {
            return redirect()->route('kotak.index')
                ->with('success', 'Kotak keuangan berhasil dihapus.');
        }
        else {
            return redirect()->route('kotak.index')
                ->with('error', 'Kotak keuangan gagal dihapus.');
        }
    }
}
