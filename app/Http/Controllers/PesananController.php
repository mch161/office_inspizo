<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with(['pelanggan'])->latest()->get();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $barangs = Barang::orderBy('nama_barang')->get();
        
        return view('karyawan.agenda.pesanan', compact('pesanans', 'pelanggans', 'barangs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pelanggan' => 'required|exists:pelanggan,kd_pelanggan',
            'deskripsi_pesanan' => 'required|string',
            'status' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.nama_barang' => 'required|string',
            'details.*.jumlah' => 'required|numeric|min:1',
            'details.*.harga_jual' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use a database transaction to ensure all data is saved or none at all
        DB::transaction(function () use ($request) {
            $pesanan = Pesanan::create([
                'kd_karyawan' => Auth::guard('karyawan')->id(),
                'kd_pelanggan' => $request->kd_pelanggan,
                'deskripsi_pesanan' => $request->deskripsi_pesanan,
                'status' => $request->status,
                'progres' => 'Baru', // Set a default progress
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama,
            ]);

            foreach ($request->details as $detail) {
                $pesanan->details()->create([
                    'kd_karyawan' => Auth::guard('karyawan')->id(),
                    'keterangan' => $detail['keterangan'] ?? '',
                    'kd_barang' => $detail['kd_barang'] ?? null,
                    'nama_barang' => $detail['nama_barang'],
                    'jenis' => 'Barang', // Or determine this dynamically if needed
                    'hpp' => $detail['hpp'] ?? 0,
                    'laba' => $detail['laba'] ?? 0,
                    'harga_jual' => $detail['harga_jual'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['harga_jual'] * $detail['jumlah'],
                    'dibuat_oleh' => Auth::guard('karyawan')->user()->nama,
                ]);
            }
        });

        return response()->json(['success' => 'Order created successfully.']);
    }

    public function show(Pesanan $pesanan)
    {
        // Load the order with its details to show in the modal
        $pesanan->load(['pelanggan', 'details']);
        return response()->json($pesanan);
    }
}