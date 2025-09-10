<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pesanan;
use App\Models\SuratPerintahKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SuratPerintahController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'superadmin') {
            $surat_perintah = SuratPerintahKerja::all();
        }
        else {
            $surat_perintah = SuratPerintahKerja::where('kd_karyawan', Auth::user()->kd_karyawan)->get();
        }
        
        return view('karyawan.surat-perintah.index', compact('surat_perintah'));
    }

    public function create()
    {
        $pesanan = Pesanan::get()->all();
        $karyawan = Karyawan::get()->all();
        return view('karyawan.surat-perintah.create', compact('pesanan', 'karyawan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan' => 'nullable|exists:pesanan,kd_pesanan',
            'kd_karyawan' => 'required|exists:karyawan,kd_karyawan',
            'tanggal_mulai' => 'required|date',
            'keterangan' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        SuratPerintahKerja::create([
            'kd_pesanan' => $request->kd_pesanan,
            'kd_karyawan' => $request->kd_karyawan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'keterangan' => $request->keterangan,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        return redirect()->route('surat-perintah.index')->with('success', 'Surat perintah berhasil dibuat.');
    }

    public function destroy($id)
    {
        $surat_perintah = SuratPerintahKerja::find($id);
        $surat_perintah->delete();
        return redirect()->route('surat-perintah.index')->with('success', 'Surat perintah berhasil dihapus.');
    }
}
