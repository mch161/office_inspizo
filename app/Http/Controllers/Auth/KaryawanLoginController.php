<?php
namespace App\Http\Controllers\Auth;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.karyawan.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = Karyawan::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::guard('karyawan')->login($user);
            return redirect()->intended('karyawan/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::guard('karyawan')->logout();
        return redirect('/');
    }
}