<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user = Karyawan::all();
        return view('auth.users', ['user' => $user]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:20|unique:karyawan,username',
            'telp' => 'nullable|string|max:20|unique:karyawan,telp',
            'alamat' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'role' => 'required|string',
            'finger_id' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:karyawan,email',
            'password' => 'required|string|min:8|confirmed',
            'jam_masuk' => 'nullable|date_format:H:i',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'telp.unique' => 'Nomor telepon sudah terdaftar pada akun lain.',
            'role.required' => 'Role wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email yang Anda masukkan tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus terdiri dari :min karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'jam_masuk.date_format' => 'Format waktu harus dalam format HH:mm.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator)
                ->withInput($request->all())
                ->with('invalid', 'User gagal ditambahkan.');
        }

        $user = Karyawan::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'telp' => $request->telp,
            'alamat' => $request->alamat,
            'nip' => $request->nip,
            'nik' => $request->nik,
            'role' => $request->role,
            'id_finger' => $request->finger_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jam_masuk' => $request->jam_masuk,
            'status' => 1,
        ]);

        if ($user) {
            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } else {
            return redirect()->route('users.index')
                ->with('error', 'User gagal ditambahkan.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Cari user secara manual berdasarkan ID
        $user = Karyawan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'edit-nama' => 'required|string|max:255',
            'edit-username' => ['required', 'string', 'max:20', Rule::unique('karyawan', 'username')->ignore($user->kd_karyawan, 'kd_karyawan')],
            'edit-telp' => ['nullable', 'string', 'max:20', Rule::unique('karyawan', 'telp')->ignore($user->kd_karyawan, 'kd_karyawan')],
            'edit-alamat' => 'nullable|string|max:255',
            'edit-nip' => 'nullable|string|max:20',
            'edit-nik' => 'nullable|string|max:20',
            'edit-role' => 'required|string',
            'edit-status' => 'required|string',
            'edit-finger_id' => 'nullable|string',
            'edit-email' => ['required', 'string', 'email', 'max:255', Rule::unique('karyawan', 'email')->ignore($user->kd_karyawan, 'kd_karyawan')],
            'edit-password' => 'nullable|string|min:8|confirmed',
            'edit-jam_masuk' => 'nullable|date_format:H:i',
        ], [
            'edit-nama.required' => 'Nama lengkap wajib diisi.',
            'edit-username.required' => 'Username wajib diisi.',
            'edit-username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'edit-telp.unique' => 'Nomor telepon sudah terdaftar pada akun lain.',
            'edit-role.required' => 'Role wajib diisi.',
            'edit-email.required' => 'Email wajib diisi.',
            'edit-email.email' => 'Format email yang Anda masukkan tidak valid.',
            'edit-email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'edit-password.min' => 'Password minimal harus terdiri dari :min karakter.',
            'edit-password.confirmed' => 'Konfirmasi password tidak cocok.',
            'edit-jam_masuk.date_format' => 'Format waktu harus dalam format HH:mm.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator)
                ->withInput($request->all())
                ->with('invalid', 'User gagal diubah.')
                ->with('error_user_id', $id);
            ;
        }

        $user = Karyawan::findOrFail($id);
        $user->nama = $request->input('edit-nama');
        $user->username = $request->input('edit-username');
        $user->telp = $request->input('edit-telp');
        $user->alamat = $request->input('edit-alamat');
        $user->nip = $request->input('edit-nip');
        $user->nik = $request->input('edit-nik');
        $user->role = $request->input('edit-role');
        $user->status = $request->input('edit-status');
        $user->finger_id = $request->input('edit-finger_id');
        $user->email = $request->input('edit-email');
        $user->jam_masuk = $request->input('edit-jam_masuk');
        if ($request->filled('edit-password')) {
            $user->password = Hash::make($request->input('edit-password'));
        }

        $isSuccess = $user->save();

        if ($isSuccess) {
            return redirect()->route('users.index')
                ->with('success', 'User berhasil diubah.');
        } else {
            return redirect()->route('users.index')
                ->with('error', 'User gagal diubah.');
        }
    }

    /**
     * Toggle the status of a user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        $user = Karyawan::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        $message = $user->status ? 'User berhasil diaktifkan.' : 'User berhasil dinonaktifkan.';

        return redirect()->route('users.index')->with('success', $message);
    }
}
