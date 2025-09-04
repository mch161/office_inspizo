<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PesananGaleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class GaleriController extends Controller
{
    public function index($id)
    {
        $pesanan = Pesanan::find($id);
        $galeri = PesananGaleri::where('kd_pesanan', $id)->get();
        return view('karyawan.pesanan.galeri', compact('pesanan', 'galeri'));
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|array',
            'foto.*' => 'image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        foreach ($request->file('foto') as $file) {
            $imageName = $file->getClientOriginalName();
            $i = 1;
            while (file_exists(public_path('storage/images/pesanan/' . $id . '/' . $imageName))) {
                $imageName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "({$i})" . "." . $file->getClientOriginalExtension();
                $i++;
            }
            $path = public_path('storage/images/pesanan/' . $id);
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $img = Image::read($file->path());
            $img->scale(width: 480)->save($path . '/' . $imageName);

            $galeri = PesananGaleri::create([
                'kd_pesanan' => $id,
                'kd_karyawan' => Auth::guard('karyawan')->id(),
                'foto' => $imageName,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
        }

        if ($galeri) {
            return redirect()->back()->with('success', 'Foto berhasil diunggah.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function destroy($id, $kd_galeri)
    {
        $galeri = PesananGaleri::find($kd_galeri);
        Storage::disk('public')->delete('images/pesanan/' . $id . '/' . $galeri->foto);

        if ($galeri) {
            $galeri->delete();
            return redirect()->back()->with('success', 'Foto berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }
}
