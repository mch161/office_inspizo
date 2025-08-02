<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananBarang;
use App\Models\PesananDetail;
use App\Models\PesananJasa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pesanan = Pesanan::get()->all();
        $pelanggan = Pelanggan::get()->all();
        $barang = Barang::get()->all();
        return view('karyawan.agenda.pesanan', [
            "pesanan" => $pesanan,
            "pelanggan" => $pelanggan,
            "barang" => $barang
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate( [
            'deskripsi_pesanan' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'string'],
        ]);

        $appointmentDate = Carbon::createFromFormat('d/m/Y', $request->tanggal);
        if ($appointmentDate->isPast() && !$appointmentDate->isToday()) {
            return redirect()->back()
                ->with('error', 'Tanggal temu tidak boleh kurang dari hari ini.')
                ->withInput();
        }

        $pesanan = Pesanan::create([
            'kd_pelanggan' => Auth::guard('pelanggan')->user()->kd_pelanggan,
            'deskripsi_pesanan' => $request->deskripsi_pesanan,
            'tanggal' => $request->tanggal,
            'dibuat_oleh' => Auth::guard('pelanggan')->user()->nama_pelanggan
        ]);

        if ($pesanan) {
            PesananDetail::create([
                'kd_pelanggan' => Auth::guard('pelanggan')->user()->kd_pelanggan,
                'kd_pesanan' => $pesanan->kd_pesanan,
                'dibuat_oleh' => Auth::guard('pelanggan')->user()->nama_pelanggan
            ]);
        }

        if ($pesanan) {
            return redirect()->route('dashboard')->with('success', 'Pesanan berhasil dibuat.');
        }
        return redirect()->route('ticket.create')->with('error', 'Pesanan gagal dibuat.');
    }


    public function accept($pesanan)
    {
        $p = Pesanan::find($pesanan);
        $p->progres = '2';
        $p->save();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diterima.');
    }

    public function detail($pesanan)
    {

        $p = Pesanan::find($pesanan);
        $pesanan_detail = Pesanan::where('kd_pesanan', $p->kd_pesanan)->first();
        $pesanan_barang = PesananBarang::where('kd_pesanan_detail', $pesanan_detail->kd_pesanan_detail)->get();
        $pesanan_jasa = PesananJasa::where('kd_pesanan_detail', $pesanan_detail->kd_pesanan_detail)->get();
        foreach ($pesanan_barang as $barang) {
            $barang->barang = Barang::find($barang->kd_barang);
        }

        return view('pesanan.detail', compact('p', 'pesanan_barang', 'pesanan_jasa'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
