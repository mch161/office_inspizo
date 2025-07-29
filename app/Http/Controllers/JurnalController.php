<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // <-- Tambahkan ini

class JurnalController extends Controller
{
    public function jurnalku()
    {

        $jurnals = Jurnal::where('kd_karyawan', Auth::guard('karyawan')->user()->kd_karyawan)->orderBy('tangal', 'asc')->get();

        return view('karyawan.jurnal.jurnalku', [
            "jurnals" => $jurnals
        ]);
    }

    public function jurnal_kita()
    {
        $jurnals = Jurnal::all();
        return view('karyawan.jurnal.jurnal_kita', [
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
        $jurnal->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $jurnal->tanggal = $request->tanggal;
        $jurnal->jam = $request->jam;
        $jurnal->isi_jurnal = $request->isi_jurnal;
        $jurnal->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $jurnal->save();

        return redirect()->route('jurnalku')->with('success', 'Jurnal berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_edit' => 'required|date',
            'jam_edit' => 'required',
            'isi_jurnal_edit' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jurnalku')
                ->withErrors($validator)
                ->withInput($request->all())
                ->with('invalid_jurnal', 'Jurnal gagal diupdate.')
                ->with('error_jurnal_id', $id);
        }

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->tanggal = $request->input('tanggal_edit');
        $jurnal->jam = $request->input('jam_edit');
        $jurnal->isi_jurnal = $request->input('isi_jurnal_edit');
        $jurnal->save();

        return redirect()->route('jurnalku')->with('success', 'Jurnal berhasil diupdate.');
    }

    public function destroy($id)
    {
        $jurnal = Jurnal::findOrFail($id);
        $jurnal->delete();

        return redirect()->route('jurnalku')->with('success', 'Jurnal berhasil dihapus.');
    }
}
