<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Karyawan;
use App\Models\Pekerjaan;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $pekerjaans = Pekerjaan::with('project')->get();
            $karyawans = Karyawan::all();
            return view('karyawan.manajemen-pekerjaan.tugas.index', compact('pekerjaans', 'karyawans'));
        }

        $data = Tugas::with(['pekerjaan.project', 'karyawan'])->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('project', function ($row) {
                return $row->pekerjaan && $row->pekerjaan->project ? $row->pekerjaan->project->nama_project : 'N/A';
            })
            ->addColumn('pekerjaan_info', function ($row) {
                return $row->pekerjaan ? $row->pekerjaan->pekerjaan : 'N/A';
            })
            ->editColumn('tanggal', function ($row) {
                return Carbon::parse($row->tanggal)->format('d-m-Y');
            })
            ->editColumn('status', function($row){
                $statusMap = [
                    'Akan Dikerjakan' => 'info',
                    'Dalam Proses'    => 'primary',
                    'Ditunda'         => 'secondary',
                    'Dilanjutkan'     => 'primary',
                    'Selesai'         => 'success',
                ];
                $badgeColor = $statusMap[$row->status] ?? 'warning';
                return '<span class="badge badge-'.$badgeColor.'">'.$row->status.'</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<a href="javascript:void(0)" data-id="' . $row->kd_tugas . '" class="edit btn btn-primary btn-sm editTugas">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" data-id="' . $row->kd_tugas . '" class="btn btn-danger btn-sm deleteTugas">Delete</a>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_pekerjaan' => 'required|exists:pekerjaan,kd_pekerjaan',
            'kd_tugas' => 'nullable',
            'tanggal' => 'nullable|date_format:d-m-Y',
        ], [
            'kd_pekerjaan.required' => 'Pekerjaan harus diisi.',
            'kd_pekerjaan.exists' => 'Pekerjaan tidak ditemukan.',
        ]);

        if ($request->tanggal == null) {
            $tanggal = date('Y-m-d');
            $request->merge(['tanggal' => $tanggal]);
        }

        $tugas = Tugas::updateOrCreate([
            'kd_tugas' => $request->kd_tugas,
        ], [
            'kd_pekerjaan' => $request->kd_pekerjaan,
            'kd_karyawan' => Auth::user()->kd_karyawan,
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d'),
            'status' => $request->status
        ]);


        return response()->json(['success' => 'Data tugas berhasil disimpan.']);
    }

    public function edit($id)
    {
        $tugas = Tugas::find($id);
        $tugas->tanggal = Carbon::parse($tugas->tanggal)->format('d-m-Y');
        return response()->json($tugas);
    }

    public function destroy($id)
    {
        Tugas::find($id)->delete();
        return response()->json(['success' => 'Data tugas berhasil dihapus.']);
    }
}
