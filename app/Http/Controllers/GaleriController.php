<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pekerjaan; 
use App\Models\PesananGaleri;
use App\Models\PekerjaanGaleri;
use App\Models\Tiket; // Diasumsikan model Tiket tersedia
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
    public function index($type, $id)
    {
        if (!isset($this->modelMap[$type])) {
            abort(404, 'Tipe galeri tidak valid.');
        }

        $map = $this->modelMap[$type];
        $mainModel = $map[0];
        $galleryModel = $map[1];
        $keyName = $map[2];
        $folder = $map[4];

        $item = $mainModel::find($id);

        if (!$item) {
            abort(404, ucfirst($type) . ' tidak ditemukan.');
        }

        $galeri = new Collection();
        $kd_pesanan = null; // Digunakan untuk penyimpanan path folder gambar
        $is_combined_view = false;

        if ($type === 'pesanan') {
            // Logika Gabungan untuk Pesanan:

            // 1. Dapatkan KD Tiket dari Pesanan (Asumsi Pesanan memiliki relasi/kolom ke Tiket)
            // Kebutuhan: Pesanan -> kd_tiket
            $kd_tiket = $item->kd_tiket; 
            $kd_pesanan = $id;
            $is_combined_view = true;

            // 2. Ambil galeri dari tabel pesanan_galeri (miliknya sendiri)
            $galeri_pesanan = PesananGaleri::where('kd_pesanan', $id)->get();
            
            // 3. Cari semua Pekerjaan yang memiliki KD Tiket yang sama
            $kd_pekerjaan_list = Pekerjaan::where('kd_tiket', $kd_tiket)->pluck('kd_pekerjaan');
            
            // 4. Ambil galeri dari tabel pekerjaan_galeri berdasarkan list kd_pekerjaan
            if ($kd_pekerjaan_list->isNotEmpty()) {
                $galeri_pekerjaan_terkait = PekerjaanGaleri::whereIn('kd_pekerjaan', $kd_pekerjaan_list)->get();
            } else {
                $galeri_pekerjaan_terkait = new Collection();
            }

            // 5. Gabungkan kedua koleksi
            $galeri = $galeri_pesanan->merge($galeri_pekerjaan_terkait)->sortByDesc('created_at');
            
        } elseif ($type === 'pekerjaan') {
            // Logika Normal untuk Pekerjaan: Hanya ambil dari pekerjaan_galeri berdasarkan kd_pekerjaan
            $galeri = PekerjaanGaleri::where('kd_pekerjaan', $id)->get();
            
            // Dapatkan kd_pesanan untuk path folder jika dibutuhkan (Asumsi Pekerjaan->kd_tiket->kd_pesanan)
            $kd_tiket = $item->kd_tiket;
            // Jika Anda memiliki relasi Pekerjaan -> Tiket -> Pesanan, Anda bisa mendapatkan kd_pesanan di sini
            // Untuk kesederhanaan, saya akan mengabaikan path dinamis gambar dari pekerjaan_galeri, 
            // karena galeri pekerjaan akan menggunakan folder 'pekerjaan'.
        }
        
        // Pass item generic name, type, storage folder, dan status gabungan
        return view('karyawan.tiket.galeri', compact('item', 'galeri', 'type', 'folder', 'is_combined_view'));
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
            // Folder penyimpanan menggunakan nama folder dinamis
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

            // Buat data dinamis untuk penyimpanan
            $data = [
                $keyName => $id, // kd_pesanan atau kd_pekerjaan
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

        // Hapus file dari storage
        Storage::disk('public')->delete('images/' . $folder . '/' . $id . '/' . $galeri->foto);

        if ($galeri->delete()) {
            return redirect()->back()->with('success', 'Foto berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}