<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pesanan;
use App\Models\Project;
use App\Models\SuratPerintahKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class SuratPerintahController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'superadmin') {
            $surat_perintah = SuratPerintahKerja::all();
        } else {
            $surat_perintah = SuratPerintahKerja::where('kd_karyawan', Auth::user()->kd_karyawan)->get();
        }

        return view('karyawan.surat-perintah.index', compact('surat_perintah'));
    }

    public function create(Request $request)
    {
        if (Auth::user()->role != 'superadmin') {
            abort(403, 'Access denied');
        }
        $pesanan = Pesanan::get()->all();
        $project = Project::get()->all();
        $karyawan = Karyawan::get()->all();
        $kd_pesanan = $request->pesanan;
        $deskripsi_pesanan = Pesanan::where('kd_pesanan', $kd_pesanan)->first()->deskripsi_pesanan ?? null;

        $previousUrl = URL::previous();

        if ($previousUrl == route('login') || $previousUrl == route('surat-perintah.create', ['pesanan' => $kd_pesanan])) {
            $backUrl = route('surat-perintah.index');
        } else {
            $backUrl = $previousUrl;
        }

        return view('karyawan.surat-perintah.create', compact('pesanan', 'project', 'karyawan', 'kd_pesanan', 'deskripsi_pesanan', 'backUrl'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan' => 'nullable|exists:pesanan,kd_pesanan',
            'kd_project' => 'nullable|exists:project,kd_project',
            'kd_karyawan' => 'required|exists:karyawan,kd_karyawan',
            'tanggal_mulai' => 'required|date',
            'keterangan' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        foreach ($request->kd_karyawan as $karyawan) {
            SuratPerintahKerja::create([
                'kd_pesanan' => $request->kd_pesanan,
                'kd_project' => $request->kd_project,
                'kd_karyawan' => $karyawan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'keterangan' => $request->keterangan,
                'status' => '0',
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
        }

        return redirect()->route('surat-perintah.index')->with('success', 'Surat perintah berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_selesai' => 'required|date|after_or_equal:today',
        ], [
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        $surat_perintah = SuratPerintahKerja::find($id);
        $surat_perintah->tanggal_selesai = Carbon::parse($request->tanggal_selesai)->format('Y-m-d');
        $surat_perintah->status = '1';
        $surat_perintah->save();
        return redirect()->route('surat-perintah.index')->with('success', 'Surat perintah berhasil selesai.');
    }

    public function destroy($id)
    {
        $surat_perintah = SuratPerintahKerja::find($id);
        $surat_perintah->delete();
        return redirect()->route('surat-perintah.index')->with('success', 'Surat perintah berhasil dihapus.');
    }
}
