<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember'); // Get the "remember me" value

        // Attempt to log in as a Pelanggan
        $pelanggan = Pelanggan::where('email', $login)
                              ->orWhere('username', $login)
                              ->orWhere('telp_pelanggan', $login)
                              ->first();

        if ($pelanggan && Hash::check($password, $pelanggan->password)) {
            Auth::guard('pelanggan')->login($pelanggan, $remember); // Pass the remember value
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // If not a Pelanggan, attempt to log in as a Karyawan
        $karyawan = Karyawan::where('email', $login)
                            ->orWhere('username', $login)
                            ->orWhere('telp', $login)
                            ->first();

        if ($karyawan && Hash::check($password, $karyawan->password)) {
            Auth::guard('karyawan')->login($karyawan, $remember); // Pass the remember value
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // If authentication fails for both, redirect back with an error
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pelanggan',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('pelanggan')->login($user);

        return redirect('/dashboard');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('pelanggan')->logout();
        Auth::guard('karyawan')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
