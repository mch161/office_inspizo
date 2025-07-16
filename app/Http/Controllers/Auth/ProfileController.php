<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page.
     */
    public function index()
    {
        $user = Auth::guard('karyawan')->user();
        return view('auth.profile', ['user' => $user]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        if ($request->hasFile('foto')) {
            $validator = Validator::make($request->all(), [
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->store('profile', 'public');
            $user->foto = $path;
            $user->save();

            return response()->json([
                'success' => 'Foto sukses diupload.',
                'path' => Storage::url($path)
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('karyawan')->ignore($user->kd_karyawan, 'kd_karyawan')],
            'nip' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->all());

        return response()->json([
            'success' => 'Profil sukses diupdate!',
            'user' => $user->fresh()
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $validator = Validator::make($request->all(), [
            'password_lama' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            $validator->errors()->add('password_lama', 'Password lama salah.');
        }

        if ($validator->fails()) {
            return redirect()->route('profile')->with('errors', $validator->errors());
        }

        $user->password = Hash::make($request->password);
        $user->save();

        if ($user->wasChanged()) {
            return redirect()->route('profile')->with('success', 'Password berhasil diubah.');
        } else {
            return redirect()->route('profile')->with('error', 'Password gagal diubah.');
        }
    }
}
