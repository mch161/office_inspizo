<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Auth;


class JurnalController extends Controller
{
    public function index()
    {
        $jurnals = Jurnal::with('karyawan')->get();
        return view('karyawan.jurnal', [
            "jurnals" => $jurnals
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'isi_jurnal' => 'required|string',
        ]);

        // 2. Create and save the new Jurnal entry
        $jurnal = new Jurnal();
        $jurnal->kd_karyawan = Auth::id();
        $jurnal->tanggal = $request->tanggal;
        $jurnal->jam = $request->jam;
        $jurnal->isi_jurnal = $request->isi_jurnal;
        $jurnal->dibuat_oleh = Auth::user()->nama;
        $jurnal->save();

        // 3. Redirect back to the index page with a success message
        return redirect()->route('jurnal.index')
            ->with('success', 'Jurnal entry created successfully.');
    }
    public function destroy(Jurnal $jurnal)
    {
        // Delete the database record
        $jurnal->delete();

        return redirect()->route('jurnal.index')
            ->with('success', 'Jurnal entry deleted successfully.');
    }
}
