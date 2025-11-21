<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pekerjaan::with(['tiket', 'pelanggan', 'karyawan'])->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('pelanggan', function ($row) {
                    return $row->pelanggan->nama_pelanggan;
                })
                ->addColumn('karyawan', function ($row) {
                    return $row->karyawans->pluck('nama')->implode(', ');
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal;
                })
                ->addColumn('jenis', function ($row) {
                    return $row->jenis;
                })
                ->addColumn('keterangan', function ($row) {
                    return $row->keterangan_pekerjaan;
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        'Akan Dikerjakan' => 'info',
                        'Dalam Proses' => 'primary',
                        'Ditunda' => 'secondary',
                        'Dilanjutkan' => 'primary',
                        'Selesai' => 'success',
                    ];
                    $badgeColor = $statusMap[$row->status] ?? 'warning';
                    return '<span class="badge badge-' . $badgeColor . '">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('pekerjaan.show', $row->kd_pekerjaan) . '" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="View" class="btn btn-info btn-sm viewPekerjaan"><i class="fa fa-eye"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editPekerjaan"><i class="fa fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="Delete" class="btn btn-danger btn-sm deletePekerjaan"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // Pass data needed for dropdowns in the modal
        $tikets = Tiket::all();
        $karyawans = Karyawan::all();
        return view('karyawan.manajemen-pekerjaan.pekerjaan.index', compact('tikets', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_tiket' => 'required|exists:tiket,kd_tiket',
            'kd_karyawan' => 'required|array',
            'kd_karyawan.*' => 'exists:karyawan,kd_karyawan',
            'tanggal' => 'required',
            'jenis' => 'required',
            'keterangan_pekerjaan' => 'required|string',
            'status' => 'required',
        ], [
            'kd_tiket.required' => 'Pesanan harus diisi.',
            'kd_tiket.exists' => 'Pesanan tidak ditemukan.',
            'kd_karyawan.required' => 'Karyawan harus diisi.',
            'kd_karyawan.array' => 'Kode karyawan harus berupa array.',
            'kd_karyawan.*.exists' => 'Karyawan tidak ditemukan.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'jenis.required' => 'Jenis harus diisi.',
            'keterangan_pekerjaan.required' => 'Keterangan pekerjaan harus diisi.',
            'keterangan_pekerjaan.string' => 'Keterangan pekerjaan harus berupa string.',
        ]);

        $request->merge([
            'kd_pelanggan' => Tiket::where('kd_tiket', $request->kd_tiket)->value('kd_pelanggan'),
        ]);

        if ($request->kd_pekerjaan) {
            $pekerjaan = Pekerjaan::findOrFail($request->kd_pekerjaan);
            $pekerjaan->update($request->except('kd_karyawan'));
        } else {
            $pekerjaan = Pekerjaan::create($request->except('kd_karyawan'));
        }

        $pekerjaan->karyawans()->sync($request->kd_karyawan);

        return response()->json(['success' => 'Data pekerjaan berhasil disimpan.']);
    }

    public function edit($id)
    {
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);
        return response()->json($pekerjaan);
    }

    public function show($id)
    {
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);
        return view('karyawan.manajemen-pekerjaan.pekerjaan.show', compact('pekerjaan'));
    }

    public function signature($id)
    {
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);
        return view('karyawan.manajemen-pekerjaan.pekerjaan.signature', compact('pekerjaan'));
    }

    public function destroy($id)
    {
        Pekerjaan::find($id)->delete();
        return response()->json(['success' => 'Data pekerjaan berhasil dihapus.']);
    }
}