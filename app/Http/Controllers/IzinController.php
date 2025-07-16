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
            'foto' => 'string',
            'keterangan' => 'required|string'

        ]);

        $izin = new Izin();
        $izin->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $izin->tanggal = $request->tanggal;
        $izin->jam = $request->jam;
        $izin->isi_izin = $request->isi_izin;
        $izin->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $izin->save();
        return redirect()->route('izin')->with('success', 'izin berhasil dibuat.');
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
