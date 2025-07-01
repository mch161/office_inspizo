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
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'isi_jurnal' => 'required|string',
        ]);

        $jurnal = new Jurnal();
        $jurnal->kd_karyawan = Auth::id();
        $jurnal->tanggal = $request->tanggal;
        $jurnal->jam = $request->jam;
        $jurnal->isi_jurnal = $request->isi_jurnal;
        $jurnal->dibuat_oleh = Auth::user()->nama;
        $jurnal->save();

        return redirect()->route('jurnal.index')
            ->with('success', 'Jurnal entry created successfully.');
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $request->validate([
            'tanggal_edit' => 'required|date',
            'jam_edit' => 'required',
            'isi_jurnal_edit' => 'required|string',
        ]);

        $jurnal->tanggal = $request->tanggal_edit;
        $jurnal->jam = $request->jam_edit;
        $jurnal->isi_jurnal = $request->isi_jurnal_edit;
        $jurnal->save();

        return redirect()->route('jurnal.index')
                         ->with('success', 'Jurnal entry updated successfully.');
    }
    public function destroy(Jurnal $jurnal)
    {
        $jurnal->delete();

        return redirect()->route('jurnal.index')
            ->with('success', 'Jurnal entry deleted successfully.');
    }
}
