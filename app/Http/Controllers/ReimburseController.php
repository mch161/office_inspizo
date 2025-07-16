<?php

namespace App\Http\Controllers;

use App\Models\Reimburse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimburseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reimburse = Reimburse::all();
        return view('karyawan.keuangan.reimburse', [
            "reimburse" => $reimburse
        ]);
    }

    public function reimburseForm()
    {
        return view('karyawan.forms.reimburse');
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
            'tanggal' => 'required|date',
            'jam' => 'required',
            'keterangan' => 'required|string',
            'nominal' => 'required|numeric'
        ]);

        $reimburse = new Reimburse();
        $reimburse->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $reimburse->tanggal = $request->tanggal;
        $reimburse->jam = $request->jam;
        $reimburse->nominal = $request->nominal;
        $reimburse->isi_reimburse = $request->isi_reimburse;
        $reimburse->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $reimburse->save();

        return redirect()->route('reimburseku')->with('success', 'reimburse berhasil dibuat.');
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
