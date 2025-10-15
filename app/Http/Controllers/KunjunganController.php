<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\KunjunganKaryawan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;


class KunjunganController extends Controller
{
    public function index()
    {
        $kunjungan = Kunjungan::latest()->get();
        return view('karyawan.kunjungan.index', compact('kunjungan'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $pesanans = Pesanan::all();
        return view('karyawan.kunjungan.create', compact('pelanggans', 'pesanans'));
    }

    public function store(Request $request)
    {
        $kunjungan = Kunjungan::create([
            'kd_pelanggan' => $request->kd_pelanggan,
            'kd_pesanan' => $request->kd_pesanan,
            'kd_karyawan' => auth()->user()->kd_karyawan,
            'tanggal' => Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d'),
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'dibuat_oleh' => auth()->user()->nama
        ]);

        foreach ($request->kd_karyawan as $kd_karyawan) {
            KunjunganKaryawan::create([
                'kd_kunjungan' => $kunjungan->kd_kunjungan,
                'kd_karyawan' => $kd_karyawan,
                'dibuat_oleh' => auth()->user()->nama
            ]);
        }
        return redirect()->route('kunjungan.index')->with('success', 'Data kunjungan berhasil ditambahkan');
    }

    public function edit(Kunjungan $kunjungan)
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $pesanans = Pesanan::orderBy('deskripsi_pesanan')->get();

        return view('karyawan.kunjungan.edit', compact('kunjungan', 'pelanggans', 'pesanans'));
    }

    public function update(Request $request, Kunjungan $kunjungan)
    {
        $kunjungan->update([
            'kd_pelanggan' => $request->kd_pelanggan,
            'kd_pesanan' => $request->kd_pesanan,
            'tanggal' => Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d'),
            'keterangan' => $request->keterangan,
            'status' => $request->status
        ]);

        KunjunganKaryawan::where('kd_kunjungan', $kunjungan->kd_kunjungan)->delete();
        foreach ($request->kd_karyawan as $kd_karyawan) {
            KunjunganKaryawan::create([
                'kd_kunjungan' => $kunjungan->kd_kunjungan,
                'kd_karyawan' => $kd_karyawan,
                'dibuat_oleh' => auth()->user()->nama
            ]);
        }

        return redirect()->route('kunjungan.index')->with('success', 'Data kunjungan berhasil diperbarui');
    }

    public function destroy(Kunjungan $kunjungan)
    {
        try {
            KunjunganKaryawan::where('kd_kunjungan', $kunjungan->kd_kunjungan)->delete();
            Kunjungan::destroy($kunjungan->kd_kunjungan);
            return response()->json(['success' => 'Data kunjungan berhasil dihapus.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data.'], 500);
        }
    }

    public function getByPelanggan($kd_pelanggan)
    {
        $pesanan = Pesanan::where('kd_pelanggan', $kd_pelanggan)
            ->select('kd_pesanan', 'deskripsi_pesanan')
            ->get();

        return response()->json($pesanan);
    }

    public function updateStatus(Kunjungan $kunjungan)
    {
        try {

            $kunjungan->status = '1';
            $kunjungan->save();

            return response()->json(['success' => 'Status kunjungan berhasil diperbarui.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui status.'], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageFile = $request->file('image');

        $fileName = uniqid() . '.' . $imageFile->getClientOriginalExtension();
        $path = 'kunjungan_images/' . $fileName;

        $resizedImage = Image::read($imageFile)
            ->scale(width: 480)
            ->save();

        Storage::disk('public')->put($path, (string) $resizedImage->encode());

        return response()->json(['url' => asset('storage/' . $path)]);
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'src' => 'required|url',
        ]);

        $path = str_replace(asset('storage/'), '', $request->src);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['success' => 'Gambar berhasil dihapus.']);
        }

        return response()->json(['error' => 'File tidak ditemukan.'], 404);
    }

}
