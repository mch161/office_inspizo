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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), ([
            'tanggal' => 'required|date',
            'jam' => 'required|string',
            'foto' => 'image|mimes:jpg,jpeg,png|max:2048',
            'keterangan' => 'required|string',
            'nominal' => 'required|numeric',
            'kategori' => 'required|string'
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

        $kategori = Keuangan_Kategori::find($request->kategori);
        $kategori_id = null;

        if ($kategori) {
            $kategori_id = $kategori->kd_kategori;
        } else {
            $newKategori = Keuangan_Kategori::firstOrCreate(
                [
                    'nama' => $request->kategori
                ],
                [
                    'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
                ]
            );
            $kategori_id = $newKategori->kd_kategori;
        }

        $reimburse = new Reimburse([
            'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'nominal' => $request->nominal,
            'foto' => $imageName,
            'keterangan' => $request->keterangan,
            'kategori' => $kategori_id,
            'status' => 0,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        if ($reimburse->save()) {
            return redirect()->route('reimburse.index')->with('success', 'Reimburse berhasil ditambahkan.');
        } else {
            return redirect()->route('reimburse.index')->with('error', 'Gagal menambahkan reimburse.');
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1',
            'kotak' => 'nullable|string|exists:keuangan_kotak,kd_kotak',
        ]);

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

    public function destroy(string $id)
    {
        //
    }
}