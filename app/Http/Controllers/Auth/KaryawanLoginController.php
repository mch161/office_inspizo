<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanLoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['login', 'password']);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : (preg_match('/^\d{10,15}$/', $credentials['login']) ? 'telp' : 'username');

        $credentials = [
            $field => $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (Auth::guard('karyawan')->attempt($credentials)) {
            // Login successful
        } else {
            // Login failed
        }
    }
}
