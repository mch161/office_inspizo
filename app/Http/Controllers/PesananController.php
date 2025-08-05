<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
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
        $pesanan = Pesanan::where('progres', '>=', '2')->get();
        $pelanggan = Pelanggan::get()->all();
        return view('karyawan.pesanan.pesanan', [
            "pesanan" => $pesanan,
            "pelanggan" => $pelanggan,
        ]);
    }

    public function permintaan()
    {
        $pesanan = Pesanan::where('progres', '1')->get();

        return view('karyawan.pesanan.permintaan', [
            "pesanan" => $pesanan
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
        $validator = Validator::make($request->all(), [
            'deskripsi_pesanan' => 'required',
            'tanggal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $appointmentDate = Carbon::createFromFormat('d/m/Y', $request->tanggal);
        if ($appointmentDate->isPast() && !$appointmentDate->isToday()) {
            return redirect()->back()
                ->with('error', 'Tanggal temu tidak boleh kurang dari hari ini.')
                ->withInput();
        }

        $pesanan = Pesanan::create([
            'kd_pelanggan' => $request->kd_pelanggan,
            'deskripsi_pesanan' => $request->deskripsi_pesanan,
            'tanggal' => $request->tanggal,
            'progres' => '2',
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        if ($pesanan) {
            PesananDetail::create([
                'kd_pelanggan' => $request->kd_pelanggan,
                'kd_pesanan' => $pesanan->kd_pesanan,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
        }

        if ($pesanan) {
            return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dibuat.');
        }
        return redirect()->route('pesanan.index')->with('error', 'Pesanan gagal dibuat.');
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

        $pesanan = Pesanan::find($pesanan);
        $pesanan_detail = Pesanan::where('kd_pesanan', $pesanan->kd_pesanan)->first();
        $pesanan_barang = PesananBarang::where('kd_pesanan_detail', $pesanan_detail->kd_pesanan_detail)->get();
        $pesanan_jasa = PesananJasa::where('kd_pesanan_detail', $pesanan_detail->kd_pesanan_detail)->get();
        $barang = Barang::get()->all();

        return view('karyawan.pesanan.detail', compact('pesanan', 'pesanan_barang','barang','pesanan_jasa'));
    }

    public function agenda(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan' => 'required|exists:pesanan,kd_pesanan',
            'title' => 'required|string',
            'tanggal' => 'required|string',
            'jam' => 'required|string',
        ]);

        $date = $request->tanggal . ' ' . $request->jam;

        $startDateTime = Carbon::createFromFormat('d/m/Y H:i', $date);

        $agendaExists = Agenda::where('start', $startDateTime)->exists();

        if ($agendaExists){
            return redirect()->back()->with('error', 'Agenda sudah ada.');
        }

        Agenda::create([
            'kd_pesanan' => $request->kd_pesanan,
            'title' => $request->title,
            'start' => $startDateTime,
            'end' => $startDateTime->addMinutes(60),
            'color' => '#007bff',
            'kd_karyawan' => Auth::guard('karyawan')->id(),
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        Pesanan::find($request->kd_pesanan)->update([
            'progres' => '3'
        ]);

        return redirect()->back()->with('success', 'Agenda berhasil dibuat.');
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
