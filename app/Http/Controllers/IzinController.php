<?php

namespace App\Http\Controllers;


use App\Models\Izin;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
            'tanggal' => 'required|string', // Changed to string to accept "DD-MM-YYYY - DD-MM-YYYY"
            'jam' => 'nullable|string',
            'jam2' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5048',
            'keterangan' => 'required|string',
        ], [
            'jenis.required' => 'Jenis izin harus diisi.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
            'keterangan.required' => 'Keterangan harus diisi.',
        ]);

        if ($request->jenis === 'Izin Terlambat' || $request->jenis === 'Izin Keluar Kantor') {
            $validator->after(function ($validator) use ($request) {
                if (empty($request->jam)) {
                    $validator->errors()->add('jam', 'Jam harus diisi.');
                }
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

        // Handle single date selection vs range selection
        if (count($dates) == 2) {
            $startDate = Carbon::createFromFormat('d-m-Y', $dates[0]);
            $endDate = Carbon::createFromFormat('d-m-Y', $dates[1]);
        } else {
            // Fallback if formatting fails or single date provided without separator
            $startDate = Carbon::createFromFormat('d-m-Y', $request->tanggal);
            $endDate = $startDate->copy();
        }

        $period = CarbonPeriod::create($startDate, $endDate);

        $jam_masuk = !is_null(Auth::guard('karyawan')->user()->jam_masuk) ? Carbon::parse(Auth::guard('karyawan')->user()->jam_masuk)->format('H:i') : '08:00';

        foreach ($period as $date) {
            $jam_pulang = $date->isSaturday() ? '16:00' : '17:00';

            // Logic for 'Jam' based on specific date in the loop
            if ($request->jenis === 'Izin Terlambat') {
                $jamField = $jam_masuk . ' - ' . $request->jam2; // Only override end time usually? Or user input?
                // Logic from original code:
                // if ($request->jenis === 'Izin Terlambat') { $request->jam = $jam_masuk; }
                // $jam = $request->jam . ' - ' . $request->jam2;

                // Re-applying original logic properly inside loop:
                $currentJam = $jam_masuk;
                $jam = $currentJam . ' - ' . $request->jam2;
            } elseif ($request->jenis === 'Izin Keluar Kantor') {
                $jam = $request->jam . ' - ' . $request->jam2;
            } else {
                $jam = $jam_masuk . ' - ' . $jam_pulang;
            }

            Izin::create([
                'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
                'jenis' => $request->jenis,
                'tanggal' => $date->format('Y-m-d'),
                'jam' => $jam,
                'keterangan' => $request->keterangan,
                'foto' => $imageName,
                'status' => 0,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
        }

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil dibuat.');
    }

    /**
     * Update the specified resource in storage.
     * This is typically used by an admin to approve or reject a request.
     */
    public function update(Request $request, $id)
    {
        // Validate that the status is present and is one of the allowed values
        // 1 = Disetujui (Approved), 2 = Ditolak (Rejected)
        $validated = $request->validate([
            'status' => 'required|in:1,2',
        ]);

        $izin = Izin::findOrFail($id);
        $izin->status = $validated['status'];

        if ($izin->save()) {
            return redirect()->route('izin.index')->with('success', 'Status izin berhasil diupdate.');
        } else {
            return redirect()->route('izin.index')->with('error', 'Gagal mengupdate status izin.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $izin = Izin::findOrFail($id);

        // Delete the associated image file from storage if it exists
        if ($izin->foto) {
            $imagePath = public_path('storage/images/izin/' . $izin->foto);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($izin->delete()) {
            return redirect()->route('izin.index')->with('success', 'Data izin berhasil dihapus.');
        } else {
            return redirect()->route('izin.index')->with('error', 'Gagal menghapus data izin.');
        }
    }
}
