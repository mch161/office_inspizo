<?php

namespace App\Http\Controllers;

use App\Models\Reimburse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimburseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reimburse = Reimburse::all();
        return view('karyawan.keuangan.reimburse', [
            "reimburse" => $reimburse
        ]);
    }

    public function reimburseForm()
    {
        return view('karyawan.forms.reimburse');
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
        // 1. Validate ALL the input first.
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required|string',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'keterangan' => 'required|string',
            'nominal' => 'required|numeric',
        ]);

        // 2. If validation passes, handle the file upload.
        $imageName = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('storage/images/reimburse'), $imageName);

        // 3. Create and save the new Reimburse model.
        $reimburse = new Reimburse();
        $reimburse->kd_karyawan = Auth::guard('karyawan')->user()->kd_karyawan;
        $reimburse->tanggal = $validatedData['tanggal'];
        $reimburse->jam = $validatedData['jam'];
        $reimburse->nominal = $validatedData['nominal'];
        $reimburse->foto = $imageName;
        $reimburse->keterangan = $validatedData['keterangan'];
        $reimburse->dibuat_oleh = Auth::guard('karyawan')->user()->nama;
        $reimburse->save();

        return redirect()->route('reimburse.form')->with('success', 'Reimburse berhasil dibuat.');
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
public function update(Request $request, $id)
{
    // 1. Validate that the 'status' field is present and is either 0 or 1.
    $request->validate([
        'status' => 'required|in:0,1',
    ]);

    // 2. Find the existing record.
    $reimburse = Reimburse::findOrFail($id);

    // 3. Update the status with the validated value.
    $reimburse->status = $request->status;
    
    // 4. Save the change and check if it was successful.
    if ($reimburse->save()) {
        return redirect()->route('reimburse.index')->with('success', 'Status reimburse berhasil diupdate.');
    } else {
        return redirect()->route('reimburse.index')->with('error', 'Gagal mengupdate status reimburse.');
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
