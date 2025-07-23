<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('s');

        $query = Barang::query();

        if ($search) {
            $query->where('nama_barang', 'like', '%' . $search . '%');
        }

        $barang = $query->get();

        return view('karyawan.barang.barang', compact('barang'));
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
            'stok' => 'nullable|numeric|min:0',
        ]);

        $imageName = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('storage/images/barang'), $imageName);

        $barang = new Barang();
        $barang->kd_karyawan = Auth::id();
        $barang->nama_barang = $request->nama_barang;
        $barang->harga = $request->harga;
        $barang->stok = 0;
        $barang->foto = $imageName;
        $barang->dibuat_oleh = Auth::user()->nama;
        $barang->save();

        $stok = new Stok();
        $stok->kd_barang = $barang->kd_barang;
        $stok->kd_karyawan = Auth::id();
        $stok->stok_masuk = $request->stok;
        $stok->stok_keluar = 0;
        $stok->klasifikasi = 'Stok Awal';
        $stok->keterangan = 'Stok awal saat barang dibuat.';
        $stok->dibuat_oleh = Auth::user()->nama;
        $stok->save();

        if ($barang && $stok) {
            return redirect()->route('barang.index')
                ->with('success', 'Barang berhasil dibuat.');
        } else {
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
        } else {
            return redirect()->route('barang.index')
                ->with('error', 'Barang gagal diupdate.');
        }
    }

    public function updateStok(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'tambah_stok' => 'nullable|numeric|min:0',
            'kurangi_stok' => 'nullable|numeric|min:0',
        ]);

        if ($request->has('tambah_stok') && $request->tambah_stok > 0) {
            $barang->stok += $request->tambah_stok;
            $barang->save();
            return redirect()->route('barang.index')->with('success', 'Stok berhasil ditambahkan.');
        }

        if ($request->has('kurangi_stok') && $request->kurangi_stok > 0) {
            if ($barang->stok < $request->kurangi_stok) {
                return redirect()->route('barang.index')->with('error', 'Stok tidak mencukupi untuk dikurangi.');
            }
            $barang->stok -= $request->kurangi_stok;
            $barang->save();
            return redirect()->route('barang.index')->with('success', 'Stok berhasil dikurangi.');
        }

        return redirect()->route('barang.index')->with('error', 'Tidak ada perubahan stok yang dilakukan.');
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
        } else {
            return redirect()->route('barang.index')
                ->with('error', 'Barang gagal dihapus.');
        }
    }
}
