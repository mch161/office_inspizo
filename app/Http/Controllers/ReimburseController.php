<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\Keuangan_Kategori;
use App\Models\Reimburse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class ReimburseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('karyawan')->user()->role == 'superadmin') {
            $reimburse = Reimburse::all();
        } else {
            $reimburse = Reimburse::where('kd_karyawan', Auth::guard('karyawan')->user()->kd_karyawan)->get();
        }

        return view('karyawan.keuangan.reimburse', compact('reimburse'));
    }

    public function reimburseForm()
    {
        $kategori = Keuangan_Kategori::get();
        return view('karyawan.forms.reimburse', compact('kategori'));
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
        $validate = Validator::make($request->all(), ([
            'tanggal' => 'required|date',
            'jam' => 'required|string',
            'foto' => 'image|mimes:jpg,jpeg,png|max:2048',
            'keterangan' => 'required|string',
            'nominal' => 'required|numeric',
            'kategori' => 'nullable|string'
        ]));

        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first())->withInput();
        }

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $imageName = time() . '.' . $request->foto->extension();
            $img = Image::read($image->path());
            $img->scale(width: 480)->save(public_path('storage/images/reimburse') . '/' . $imageName);
        } else {
            $imageName = null;
        }

        $reimburse = new Reimburse([
            'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'nominal' => $request->nominal,
            'foto' => $imageName,
            'keterangan' => $request->keterangan,
            'kategori' => $request->kategori,
            'status' => 0,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        if ($reimburse->save()) {
            return redirect()->route('reimburse.index')->with('success', 'Reimburse berhasil ditambahkan.');
        } else {
            return redirect()->route('reimburse.index')->with('error', 'Gagal menambahkan reimburse.');
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1',
            'kotak' => 'nullable|string|exists:keuangan_kotak,kd_kotak',
        ]);

        // if (Reimburse::findOrFail($id)->status == 0) {
        //     $reimburse = Reimburse::findOrFail($id);
        //     Keuangan::create([
        //         'kd_karyawan' => $reimburse->kd_karyawan,
        //         'jenis' => 'Keluar',
        //         'keluar' => $reimburse->nominal,
        //         'kd_kotak' => $request->kotak,
        //         'kd_kategori' => $reimburse->kategori,
        //         'keterangan' => 'Reimburse: ' . $reimburse->keterangan,
        //         'tanggal' => $reimburse->tanggal,
        //         'dibuat_oleh' => $reimburse->dibuat_oleh
        //     ]);
        // }

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $imageName = time() . '.' . $request->foto->extension();

            $reimburse = Reimburse::findOrFail($id);
            if (Storage::disk('public')->exists('images/reimburse/' . $reimburse->bukti_transfer)) {
                Storage::disk('public')->delete('images/reimburse/' . $reimburse->bukti_transfer);
            }

            $img = Image::read($image->path());
            $img->scale(width: 480)->save(public_path('storage/images/reimburse') . '/' . $imageName);
        }

        $reimburse = Reimburse::findOrFail($id);
        $reimburse->status = $request->status;

        if ($request->kotak) {
            $reimburse->kotak = $request->kotak;
        }

        if ($request->hasFile('foto')) {
            $reimburse->bukti_transfer = $imageName;
        }

        if ($reimburse->save()) {
            return redirect()->route('reimburse.index')->with('success', 'Status reimburse berhasil diupdate.');
        } else {
            return redirect()->route('reimburse.index')->with('error', 'Gagal mengupdate status reimburse.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
