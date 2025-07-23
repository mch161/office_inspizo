<?php

namespace App\Http\Controllers;


use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // 1. Validate all the input first.
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
        ]);

        $imageName = null;

        // 2. If validation passes and a file was uploaded, handle the file.
        if ($request->hasFile('foto')) {
            $imageName = time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(public_path('storage/images/izin'), $imageName);
        }

        // 3. Create and save the new Izin model using validated data.
        $izin = new Izin();
        $izin->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $izin->tanggal = $validatedData['tanggal'];
        $izin->jam = $validatedData['jam'];
        $izin->foto = $imageName; // Assign the image name (or null if no file)
        $izin->keterangan = $validatedData['keterangan'];
        $izin->status = '0'; // 0 = Menunggu (Pending)
        $izin->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $izin->save();

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
