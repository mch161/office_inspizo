<?php

namespace App\Http\Controllers\Auth;

use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = Pelanggan::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::guard('pelanggan')->login($user);
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

        public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request) : RedirectResponse
    {
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.Pelanggan::class,
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Pelanggan::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::guard('pelanggan')->login($user);

        return to_route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
    