<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewController extends Controller
{
    /**
     * Beralih ke mode tampilan sebagai Karyawan (non-superadmin).
     */
    public function switchToUserView()
    {
        // Pastikan hanya superadmin asli yang bisa menggunakan fitur ini
        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->role == 'superadmin') {
            session(['view_as_karyawan' => true]);
            return redirect()->route('dashboard')->with('success', 'Anda sekarang melihat sebagai Karyawan.');
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
    }

    /**
     * Kembali ke mode tampilan sebagai Superadmin.
     */
    public function switchToAdminView()
    {
        session()->forget('view_as_karyawan');
        return redirect()->route('dashboard')->with('success', 'Selamat datang kembali, Superadmin!');
    }
}

