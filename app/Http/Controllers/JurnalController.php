<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;


class JurnalController extends Controller
{
    public function index()
    {
        $jurnals = Jurnal::with('karyawan')->get();
        return view('karyawan.jurnal', [
            "jurnals" => $jurnals
        ]);
    }
    public function destroy(Jurnal $jurnal)
    {
        // Delete the database record
        $jurnal->delete();

        return redirect()->route('jurnal.index')
                         ->with('success', 'Jurnal entry deleted successfully.');
    }
}
