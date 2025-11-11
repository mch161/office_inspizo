<?php

namespace App\Http\Controllers;

use App\Models\PresensiLembur;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->role == 'superadmin') {
                $data = PresensiLembur::all();
            } else {
                $data = PresensiLembur::where('kd_karyawan', auth()->user()->id)->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('karyawan', function ($row) {
                    return $row->karyawans->nama;
                })
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d-m-Y');
                })
                ->addColumn('jam', function ($row) {
                    return $row->jam_mulai . ' - ' . $row->jam_selesai . ' (' . $row->jumlah_jam . ')';
                })
                ->addColumn('keterangan', function ($row) {
                    return $row->keterangan;
                })
                ->addColumn('status', function ($row) {
                    if ($row->verifikasi == 1) {
                        return '<div class="badge badge-success">Disetujui</div>';
                    } else {
                        return '<div class="badge badge-warning">Menunggu</div>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->verifikasi == 0 && auth()->user()->role == 'superadmin') {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_lembur . '" data-original-title="Approve" class="btn btn-success btn-sm approve-btn"><i class="fas fa-check"></i></a>';
                    }
                    if ($row->verifikasi == 0 && $row->kd_karyawan == auth()->user()->id) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_lembur . '" data-original-title="Hapus" class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('karyawan.presensi.lembur');
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
            return redirect()->json(['errors' => $validator->errors()->first()]);
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
