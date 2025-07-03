<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang = Barang::with('karyawan')->get();
        return view('karyawan.barang', [
            "barang" => $barang
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
            'nama_barang' => 'required|string',
            'harga' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('storage/images/barang'), $imageName);

        $barang = new Barang();
        $barang->kd_karyawan = Auth::id();
        $barang->nama_barang = $request->nama_barang;
        $barang->harga = $request->harga;
        $barang->foto = $imageName;
        $barang->dibuat_oleh = Auth::user()->nama;
        $barang->save();
        
        if ($barang) {
            return redirect()->route('barang.index')
                ->with('success', 'Barang berhasil dibuat.');
        }
        else {
            return redirect()->route('barang.index')
                ->with('error', 'Barang gagal dibuat.');
        }
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
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string',
            'harga' => 'required|numeric',
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            File::delete(public_path('storage/images/barang/' . $barang->foto));
            $imageName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('storage/images/barang'), $imageName);
            $barang->foto = $imageName;
        }

        $barang->kd_karyawan = Auth::id();
        $barang->nama_barang = $request->nama_barang;
        $barang->harga = $request->harga;
        $barang->dibuat_oleh = Auth::user()->nama;
        $barang->save();

        if ($barang) {
            return redirect()->route('barang.index')
                ->with('success', 'Barang berhasil diupdate.');
        }
        else {
            return redirect()->route('barang.index')
                ->with('error', 'Barang gagal diupdate.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();
        File::delete(public_path('storage/images/barang/' . $barang->foto));

        if ($barang) {
            return redirect()->route('barang.index')
                ->with('success', 'Barang berhasil dihapus.');
        }
        else {
            return redirect()->route('barang.index')
                ->with('error', 'Barang gagal dihapus.');
        }
    }
}
