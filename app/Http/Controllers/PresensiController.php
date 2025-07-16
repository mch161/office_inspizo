<?php

namespace App\Http\Controllers;

use App\Models\presensi; // <-- Use your Presensi model
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        // Get all logs, with the newest ones first
        $logs = Presensi::latest('timestamp')->get(); // <-- Use your Presensi model

        // Return the view, passing the logs data to it
        return view('karyawan.presensi.presensi', compact('logs'));
    }
}