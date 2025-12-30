<?php

namespace App\Http\Controllers;


use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('karyawan')->user()->role !== 'superadmin') {
            $izin = Izin::where('kd_karyawan', Auth::guard('karyawan')->user()->kd_karyawan)->get();
        } else {
            $izin = Izin::latest()->get();
        }
        return view('karyawan.presensi.izin', [
            "izin" => $izin
        ]);
    }

    /**
     * Show the form for creating a new izin request.
     */
    public function izinForm()
    {
        return view('karyawan.forms.izin');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|string',
            'tanggal' => 'required|string',
            'jam' => 'nullable|string',
            'jam2' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5048',
            'keterangan' => 'required|string',
        ]);

        if ($request->jenis === 'Izin Terlambat' || $request->jenis === 'Izin Keluar Kantor') {
            $validator->after(function ($validator) use ($request) {
                if (empty($request->jam2)) {
                    $validator->errors()->add('jam2', 'Jam harus diisi.');
                }
            });
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($validator->validated());
        }

        $imageName = null;
        if ($request->hasFile('foto')) {
            $imageName = time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(public_path('storage/images/izin'), $imageName);
        }

        $dates = explode(' - ', $request->tanggal);

        if (count($dates) == 2) {
            $startDate = Carbon::createFromFormat('d-m-Y', $dates[0]);
            $endDate = Carbon::createFromFormat('d-m-Y', $dates[1]);
        } else {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->tanggal);
            $endDate = $startDate->copy();
        }

        $jumlahHari = 0;
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isSunday()) {
                continue;
            }
            $jumlahHari++;
        }

        $jam_masuk = !is_null(Auth::guard('karyawan')->user()->jam_masuk) ? Carbon::parse(Auth::guard('karyawan')->user()->jam_masuk)->format('H:i') : '08:00';
        $jam_pulang = '17:00';

        if ($request->jenis === 'Izin Terlambat') {
            $jam = $jam_masuk . ' - ' . $request->jam2;
        } elseif ($request->jenis === 'Izin Keluar Kantor') {
            $jam = $request->jam . ' - ' . $request->jam2;
        } else {
            $jam = $jam_masuk . ' - ' . $jam_pulang;
        }

        Izin::create([
            'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
            'jenis' => $request->jenis,
            'tanggal' => $startDate->format('Y-m-d'),
            'tanggal_selesai' => $endDate->format('Y-m-d'),
            'jumlah_hari' => $jumlahHari,
            'jam' => $jam,
            'keterangan' => $request->keterangan,
            'foto' => $imageName,
            'status' => 0,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil dibuat.');
    }

    public function upload(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($validator->validated());
        }

        $izin = Izin::findOrFail($id);

        $imageName = time() . '.' . $request->file('foto')->extension();
        $request->file('foto')->move(public_path('storage/images/izin'), $imageName);

        $izin->foto = $imageName;
        $izin->save();
        return redirect()->route('izin.index')->with('success', 'Gambar berhasil diupload.');
    }

    /**
     * Update the specified resource in storage.
     * This is typically used by an admin to approve or reject a request.
     */
    public function update(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);
        $izin->status = $request->status;
        $izin->save();
        return redirect()->route('izin.index')->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $izin = Izin::findOrFail($id);
        if ($izin->foto && file_exists(public_path('storage/images/izin/' . $izin->foto))) {
            unlink(public_path('storage/images/izin/' . $izin->foto));
        }
        $izin->delete();
        return redirect()->route('izin.index')->with('success', 'Data berhasil dihapus.');
    }
}
