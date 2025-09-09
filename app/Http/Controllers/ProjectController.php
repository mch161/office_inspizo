<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::get();
        $karyawans = Karyawan::get();
        return view('karyawan.project.project', compact('projects', 'karyawans'));
    }

    public function search(Request $request)
    {
        $s = $request->s;
        $projects = Project::where('nama_project', 'like', '%' . $s . '%')->get();
        return view('karyawan.project.project', compact('projects'));
    }

    public function detail($kd_project)
    {
        $project = Project::where('kd_project', $kd_project)->first();
        return view('karyawan.project.detail', compact('project'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_project' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif',
            'lokasi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        if ($request->foto) {
            $image = $request->foto;
            $imageName = time() . '.' . $request->foto->extension();

            $path = public_path('storage/images/project');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $img = Image::read($image->path());
            $img->scale(width: 480)->save($path . '/' . $imageName);
        } else {
            $imageName = null;
        }

        $project = new Project();
        $project->nama_project = $request->nama_project;
        $project->kd_karyawan = Auth::id();
        $project->foto = $imageName;
        $project->tanggal_mulai = $request->tanggal_mulai;
        $project->lokasi = $request->lokasi;
        $project->deskripsi = $request->deskripsi;
        $project->status = 'Belum Selesai';
        $project->dibuat_oleh = Auth::user()->nama;
        $project->save();

        return redirect()->back()->with('success', 'Project berhasil ditambahkan.');
    }

    public function update(Request $request, $kd_project)
    {
        $validator = Validator::make($request->all(), [
            'nama_project' => 'required|string',
            'foto' => 'image|mimes:jpeg,png,jpg,gif',
            'lokasi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $project = Project::find($kd_project);


        if ($request->foto) {
            if (Storage::disk('public')->exists('images/project/' . $project->foto)) {
                Storage::disk('public')->delete('images/project/' . $project->foto);
            }
            
            $image = $request->foto;
            $imageName = time() . '.' . $request->foto->extension();

            $path = public_path('storage/images/project');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $img = Image::read($image->path());
            $img->scale(width: 480)->save($path . '/' . $imageName);

            $project->foto = $imageName;
        }

        $project->nama_project = $request->nama_project;
        $project->tanggal_mulai = $request->tanggal_mulai;
        $project->lokasi = $request->lokasi;
        $project->deskripsi = $request->deskripsi;
        $project->save();

        return redirect()->back()->with('success', 'Project berhasil diupdate.');
    }

    public function destroy($kd_project)
    {
        $project = Project::find($kd_project);
        Storage::disk('public')->delete('images/project/' . $project->foto);
        $project->delete();
        return redirect()->back()->with('success', 'Project berhasil dihapus.');
    }
}
