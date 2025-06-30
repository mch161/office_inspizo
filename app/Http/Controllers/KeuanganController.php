<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $keuangans = Keuangan::with('karyawan')->get();
        return view('karyawan.keuangan', [
            "keuangans" => $keuangans
        ]);
    }
}
