<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Project;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pekerjaan::with(['project', 'karyawan'])->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project', function ($row) {
                    if ($row->project) {
                        $url = route('project.detail', $row->project->kd_project);
                        return '<a href="' . $url . '">' . $row->project->nama_project . '</a>';
                    }
                    return 'N/A';
                })
                ->addColumn('karyawan', function ($row) {
                    return $row->karyawans->pluck('nama')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editPekerjaan">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="Delete" class="btn btn-danger btn-sm deletePekerjaan">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['project', 'action'])
                ->make(true);
        }

        // Pass data needed for dropdowns in the modal
        $projects = Project::all();
        $karyawans = Karyawan::all();
        return view('karyawan.manajemen-pekerjaan.pekerjaan.index', compact('projects', 'karyawans'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'kd_project' => 'required|exists:project,kd_project',
            'kd_karyawan' => 'required|array',
            'kd_karyawan.*' => 'exists:karyawan,kd_karyawan',
            'pekerjaan' => 'required|string',
        ], [
            'kd_project.required' => 'Project harus diisi.',
            'kd_project.exists' => 'Project tidak ditemukan.',
            'kd_karyawan.required' => 'Karyawan harus diisi.',
            'kd_karyawan.array' => 'Kode karyawan harus berupa array.',
            'kd_karyawan.*.exists' => 'Karyawan tidak ditemukan.',
            'pekerjaan.required' => 'Deskripsi pekerjaan harus diisi.',
            'pekerjaan.string' => 'Deskripsi pekerjaan harus berupa string.',
        ]);

        if ($request->kd_pekerjaan) {
            // Update
            $pekerjaan = Pekerjaan::findOrFail($request->kd_pekerjaan);
            $pekerjaan->update($request->only(['kd_project', 'pekerjaan']));
        } else {
            // Create
            $pekerjaan = Pekerjaan::create($request->only(['kd_project', 'pekerjaan']));
        }

        // Gunakan sync() untuk menautkan karyawan. Sangat efisien!
        $pekerjaan->karyawans()->sync($request->kd_karyawan);

        return response()->json(['success' => 'Data pekerjaan berhasil disimpan.']);
    }

    public function edit($id)
    {
        // PERUBAHAN: Eager load karyawans
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);
        return response()->json($pekerjaan);
    }

    public function destroy($id)
    {
        Pekerjaan::find($id)->delete();
        return response()->json(['success' => 'Data pekerjaan berhasil dihapus.']);
    }
}