<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->kd_pelanggan.'" data-original-title="Edit" class="edit btn btn-warning btn-sm editPelanggan">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->kd_pelanggan.'" data-original-title="Delete" class="btn btn-danger btn-sm deletePelanggan">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('karyawan.pelanggan.datapelanggan');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pelanggan' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'email' => 'required|email|unique:pelanggan,email,' . $request->pelanggan_id . ',kd_pelanggan',
            'telp_pelanggan' => 'required|string|max:20',
            'alamat_pelanggan' => 'nullable|string',
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('pelanggan_id', 'password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        Pelanggan::updateOrCreate(['kd_pelanggan' => $request->pelanggan_id], $data);        

        return response()->json(['success'=>'Customer saved successfully.']);
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::find($id);
        return response()->json($pelanggan);
    }

    public function destroy($id)
    {
        Pelanggan::find($id)->delete();
        return response()->json(['success'=>'Customer deleted successfully.']);
    }
}