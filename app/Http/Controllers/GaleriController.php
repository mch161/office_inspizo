<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pekerjaan;
use App\Models\PesananGaleri;
use App\Models\PekerjaanGaleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Collection;

class GaleriController extends Controller
{
    private $modelMap = [
        // [Model Utama, Model Galeri, 'Primary Key', 'Redirect Route', 'Storage Folder']
        'pesanan' => [Pesanan::class, PesananGaleri::class, 'kd_pesanan', 'tiket.show', 'pesanan'],
        'pekerjaan' => [Pekerjaan::class, PekerjaanGaleri::class, 'kd_pekerjaan', 'pekerjaan.show', 'pekerjaan'],
    ];

    /**
     * Menampilkan galeri berdasarkan tipe dan ID dengan logika penggabungan.
     */
    public function index($type, $id, $id2 = null)
    {
        if ($id2) {
            $type = $id;
            $id = $id2;
        }
        if (!isset($this->modelMap[$type])) {
            abort(404, 'Tipe galeri tidak valid.');
        }

        $map = $this->modelMap[$type];
        $mainModel = $map[0];
        $galleryModel = $map[1];
        $keyName = $map[2];
        $folder = $map[4];

        $item = $mainModel::find($id);

        $currentUrl = url()->current();
        $normalizedUrl = rtrim($currentUrl, '/');

        $backUrl = dirname($normalizedUrl);

        if (!$item) {
            abort(404, ucfirst($type) . ' tidak ditemukan.');
        }

        $galeri = new Collection();
        $kd_pesanan = null;
        $is_combined_view = false;

        if ($type === 'pesanan') {
            $kd_tiket = $item->kd_tiket;
            $kd_pesanan = $id;
            $is_combined_view = true;

            $galeri_pesanan = PesananGaleri::where('kd_pesanan', $id)->get();

            $kd_pekerjaan_list = Pekerjaan::where('kd_tiket', $kd_tiket)->pluck('kd_pekerjaan');

            if ($kd_pekerjaan_list->isNotEmpty()) {
                $galeri_pekerjaan_terkait = PekerjaanGaleri::whereIn('kd_pekerjaan', $kd_pekerjaan_list)->get();
            } else {
                $galeri_pekerjaan_terkait = new Collection();
            }

            $galeri = $galeri_pesanan->merge($galeri_pekerjaan_terkait)->sortByDesc('created_at');

        } elseif ($type === 'pekerjaan') {
            $galeri = PekerjaanGaleri::where('kd_pekerjaan', $id)->get();

            $kd_tiket = $item->kd_tiket;
        }

        return view('karyawan.tiket.galeri', compact('item', 'galeri', 'type', 'folder', 'is_combined_view', 'backUrl'));
    }

    /**
     * Menyimpan gambar ke galeri.
     */
    public function store(Request $request, $type, $id)
    {
        if (!isset($this->modelMap[$type])) {
            return redirect()->back()->with('error', 'Tipe galeri tidak valid.');
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|array',
            'foto.*' => 'image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        $map = $this->modelMap[$type];
        $mainModel = $map[0];
        $galleryModel = $map[1];
        $keyName = $map[2];
        $folder = $map[4];

        $item = $mainModel::find($id);
        if (!$item) {
            return redirect()->back()->with('error', ucfirst($type) . ' tidak ditemukan.');
        }

        $galeri = null;

        foreach ($request->file('foto') as $file) {
            $imageName = $file->getClientOriginalName();
            $i = 1;
            $storage_path = 'storage/images/' . $folder . '/' . $id;

            while (file_exists(public_path($storage_path . '/' . $imageName))) {
                $imageName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "({$i})" . "." . $file->getClientOriginalExtension();
                $i++;
            }

            $path = public_path($storage_path);
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $img = Image::read($file->path());
            $img->scale(width: 480)->save($path . '/' . $imageName);

            $data = [
                $keyName => $id,
                'kd_karyawan' => Auth::guard('karyawan')->id(),
                'foto' => $imageName,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ];

            $galeri = $galleryModel::create($data);
        }

        if ($galeri) {
            return redirect()->back()->with('success', 'Foto berhasil diunggah.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Menghapus gambar dari galeri.
     */
    public function destroy($type, $id, $kd_galeri)
    {
        if (!isset($this->modelMap[$type])) {
            return redirect()->back()->with('error', 'Tipe galeri tidak valid.');
        }

        $map = $this->modelMap[$type];
        $galleryModel = $map[1];
        $folder = $map[4];

        $galeri = $galleryModel::find($kd_galeri);

        if (!$galeri) {
            return redirect()->back()->with('error', 'Foto tidak ditemukan.');
        }

        Storage::disk('public')->delete('images/' . $folder . '/' . $id . '/' . $galeri->foto);

        if ($galeri->delete()) {
            return redirect()->back()->with('success', 'Foto berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}