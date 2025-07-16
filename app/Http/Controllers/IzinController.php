<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('karyawan.forms.izin');
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
        $request->validate( [
            'tanggal' => 'required|date',
            'jam' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
        ]);

        if ($request->hasFile('foto')) {
            $imageName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('storage/images/izin'), $imageName);
        }

        $izin = new Izin();
        $izin->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $izin->tanggal = $request->tanggal;
        $izin->jam = $request->jam;
        $izin->foto = $imageName ?? null;
        $izin->keterangan = $request->keterangan;
        $izin->status = '0';
        $izin->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $izin->save();
        return redirect()->route('izin.index')->with('success', 'izin berhasil dibuat.');

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
