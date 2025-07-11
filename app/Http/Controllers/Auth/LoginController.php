<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            Auth::guard('karyawan')->login($karyawan, $remember);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

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
        $validator = Validator::make($request->all(), [
            'nama_pelanggan' => 'required|string|max:255',
            'telp_pelanggan' => 'nullable|string|max:20|unique:pelanggan,telp_pelanggan|unique:karyawan,telp',
            'alamat_pelanggan' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:pelanggan,username|unique:karyawan,username',            
            'email' => 'required|string|email|max:255|unique:pelanggan,email|unique:karyawan,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama_pelanggan.required' => 'Nama lengkap wajib diisi.',
            'telp_pelanggan.unique' => 'Nomor telepon sudah terdaftar pada akun lain.',
            'alamat_pelanggan.required' => 'Alamat wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email yang Anda masukkan tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus terdiri dari :min karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect('register')
                        ->withErrors($validator)
                        ->withInput($request->all());
        }


        $user = Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'telp_pelanggan' => $request->telp_pelanggan,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'nama_perusahaan' => $request->nama_perusahaan,
            'nik' => $request->nik,
            'username' => $request->username,
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
