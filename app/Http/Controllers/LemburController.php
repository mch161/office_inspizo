<?php

namespace App\Http\Controllers;

use App\Models\PresensiLembur;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class LemburController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'superadmin') {
            $lemburs = PresensiLembur::all();
        }
        else {
            $lemburs = PresensiLembur::where('kd_karyawan', auth()->user()->id)->get();
        }

        return view('karyawan.presensi.lembur', compact('lemburs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'required',
        ], [
            'jam_mulai.required' => 'Jam mulai harus diisi.',
            'jam_selesai.required' => 'Jam selesai harus diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = Carbon::parse($request->jam_selesai);
        $jumlah_jam = $mulai->diff($selesai)->format('%H:%I');

        PresensiLembur::create([
            'kd_karyawan' => auth()->user()->id,
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d'),
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jumlah_jam' => $jumlah_jam,
            'keterangan' => $request->keterangan,
            'verifikasi' => '0',
            'dibuat_oleh' => auth()->user()->nama
        ]);

        return redirect()->back()->with('success', 'Lembur berhasil dikirim.');
    }

    public function approve(Request $request)
    {
        $lembur = PresensiLembur::find($request->kd_lembur);
        $lembur->verifikasi = '1';
        $lembur->save();

        return redirect()->back()->with('success', 'Lembur berhasil disetujui.');
    }

    public function destroy($id)
    {
        PresensiLembur::find($id)->delete();
        return redirect()->back()->with('success', 'Lembur berhasil dihapus.');
    }
}
