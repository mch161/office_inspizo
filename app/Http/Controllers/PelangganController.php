<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = Pelanggan::all()->sortBy('nama_pelanggan');
        return view('karyawan.pelanggan.pelanggan', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'nama_perusahaan' => ['nullable', 'string', 'max:255'],
            'alamat_pelanggan' => ['nullable', 'string', 'max:255'],
            'telp_pelanggan' => ['nullable', 'string', 'max:255', 'unique:pelanggan'],
            'username' => ['required', 'string', 'max:255', 'unique:pelanggan'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pelanggan'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'telp_pelanggan.unique' => 'Nomor telepon sudah terdaftar.',
            'username.unique' => 'Username sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Password tidak cocok.',
            'email.email' => 'Format email salah.',
            'username.required' => 'Username harus diisi.',
            'email.required' => 'Email harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        $user = Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'telp_pelanggan' => $request->telp_pelanggan,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
        }
        else {
            return redirect()->route('pelanggan.index')->with('error', 'Pelanggan gagal ditambahkan.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_pelanggan_edit' => ['required', 'string', 'max:255'],
            'nama_perusahaan_edit' => ['nullable', 'string', 'max:255'],
            'alamat_pelanggan_edit' => ['nullable', 'string', 'max:255'],
            'telp_pelanggan_edit' => ['nullable', 'string', 'max:255'],
            'username_edit' => ['nullable', 'string', 'max:255'],
            'email_edit' => ['nullable', 'string', 'email', 'max:255'],
            'password_edit' => ['nullable', 'string', 'min:8'],
        ]);

        $user = Pelanggan::find($id);
        $data = [
            'nama_pelanggan' => $request->nama_pelanggan_edit,
            'nama_perusahaan' => $request->nama_perusahaan_edit,
            'alamat_pelanggan' => $request->alamat_pelanggan_edit,
            'telp_pelanggan' => $request->telp_pelanggan_edit,
            'username' => $request->username_edit,
            'email' => $request->email_edit,
        ];
            if ($request->password_edit != null) {
                $data['password'] = Hash::make($request->password_edit);
            }
        
        $user->update($data);

        if ($user) {
            return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diubah.');
        }
        else {
            return redirect()->route('pelanggan.index')->with('error', 'Pelanggan gagal diubah.');
        }
    }

    public function show($id)
    {
        $pelanggan = Pelanggan::find($id);
        $pesanan = Pesanan::where('kd_pelanggan', $id)->get();
        $kunjungan = Kunjungan::where('kd_pelanggan', $id)->get();
        return view('karyawan.pelanggan.show', compact('pelanggan', 'pesanan', 'kunjungan'));
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::find($id);
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}