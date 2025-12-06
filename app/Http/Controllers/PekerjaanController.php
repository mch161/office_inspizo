<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pekerjaan;
use App\Models\PekerjaanBarang;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pekerjaan::with(['tiket', 'pelanggan', 'karyawan'])->latest()->get();
            if ($request->has('kd_tiket')) {
                $data = $data->where('kd_tiket', $request->kd_tiket);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('pelanggan', function ($row) {
                    return $row->pelanggan->nama_pelanggan;
                })
                ->addColumn('karyawan', function ($row) {
                    return $row->karyawans->pluck('nama')->implode(', ');
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal;
                })
                ->addColumn('jenis', function ($row) {
                    return $row->jenis;
                })
                ->addColumn('keterangan', function ($row) {
                    return $row->keterangan_pekerjaan;
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        'Akan Dikerjakan' => 'info',
                        'Dalam Proses' => 'primary',
                        'Ditunda' => 'secondary',
                        'Dilanjutkan' => 'primary',
                        'Selesai' => 'success',
                    ];
                    $badgeColor = $statusMap[$row->status] ?? 'warning';
                    return '<span class="badge badge-' . $badgeColor . '">' . $row->status . '</span>';
                })
                ->addColumn('signature_status', function ($row) {
                    $url = route('signature.index', ['type' => 'pekerjaan', 'id' => $row->kd_pekerjaan]);

                    if ($row->ttd_pelanggan) {
                        return '<a href="' . $url . '" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Ada</a>';
                    } else {
                        return '<a href="' . $url . '" class="btn btn-warning btn-sm"><i class="fas fa-file-signature"></i> Minta</a>';
                    }
                })
                ->addColumn('action', function ($row) {
                    if (url()->previous() == route('pekerjaan.index')) {
                        $btn = '<a href="' . route('pekerjaan.show', $row->kd_pekerjaan) . '" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="View" class="btn btn-info btn-sm viewPekerjaan"><i class="fa fa-eye"></i></a>';
                    } elseif (url()->previous() == route('tiket.show', request()->kd_tiket)) {
                        $btn = '<a href="' . route('tiket.pekerjaan.show', [request()->kd_tiket, $row->kd_pekerjaan]) . '" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="View" class="btn btn-info btn-sm viewPekerjaan"><i class="fa fa-eye"></i></a>';
                    }
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editPekerjaan"><i class="fa fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan . '" data-original-title="Delete" class="btn btn-danger btn-sm deletePekerjaan"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['signature_status', 'status', 'action'])
                ->make(true);
        }

        // Pass data needed for dropdowns in the modal
        $tikets = Tiket::all();
        $karyawans = Karyawan::all();
        return view('karyawan.manajemen-pekerjaan.pekerjaan.index', compact('tikets', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_tiket' => 'required|exists:tiket,kd_tiket',
            'kd_karyawan' => 'required|array',
            'kd_karyawan.*' => 'exists:karyawan,kd_karyawan',
            'tanggal' => 'required',
            'jenis' => 'required',
            'keterangan_pekerjaan' => 'required|string',
            'status' => 'required',
        ], [
            'kd_tiket.required' => 'Pesanan harus diisi.',
            'kd_tiket.exists' => 'Pesanan tidak ditemukan.',
            'kd_karyawan.required' => 'Karyawan harus diisi.',
            'kd_karyawan.array' => 'Kode karyawan harus berupa array.',
            'kd_karyawan.*.exists' => 'Karyawan tidak ditemukan.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'jenis.required' => 'Jenis harus diisi.',
            'keterangan_pekerjaan.required' => 'Keterangan pekerjaan harus diisi.',
            'keterangan_pekerjaan.string' => 'Keterangan pekerjaan harus berupa string.',
        ]);

        $request->merge([
            'kd_pelanggan' => Tiket::where('kd_tiket', $request->kd_tiket)->value('kd_pelanggan'),
        ]);

        if ($request->kd_pekerjaan) {
            $pekerjaan = Pekerjaan::findOrFail($request->kd_pekerjaan);
            $pekerjaan->update($request->except('kd_karyawan'));
        } else {
            $pekerjaan = Pekerjaan::create($request->except('kd_karyawan'));
        }

        $pekerjaan->karyawans()->sync($request->kd_karyawan);

        return response()->json(['success' => 'Data pekerjaan berhasil disimpan.']);
    }

    public function edit($id)
    {
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);
        return response()->json($pekerjaan);
    }

    public function show(Request $request, $id, $id2 = null)
    {
        if (request()->ajax()) {
            $data = PekerjaanBarang::with('pekerjaan', 'barang')->where('kd_pekerjaan', $id)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_barang', function ($row) {
                    return $row->barang->nama_barang;
                })
                ->addColumn('jumlah', function ($row) {
                    return $row->jumlah;
                })
                ->addColumn('aksi', function ($row) {
                    $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pekerjaan_barang . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteBtn"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        if ($request->routeIs('tiket.pekerjaan.show')) {
            $id = $id2;
        }
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);

        $currentUrl = url()->current();
        if ($currentUrl = "/tiket/pekerjaan/$id") {
            $backUrl = route('tiket.index');
        } else {
            $normalizedUrl = rtrim($currentUrl, '/');

            $backUrl = dirname($normalizedUrl);
        }

        return view('karyawan.manajemen-pekerjaan.pekerjaan.show', compact('pekerjaan', 'backUrl'));
    }

    public function destroyBarang($id)
    {
        $barang = PekerjaanBarang::find($id);
        $barang->delete();
        return response()->json(['success' => 'Barang berhasil dihapus.']);
    }

    public function signature($id)
    {
        $pekerjaan = Pekerjaan::with('karyawans')->find($id);
        return view('karyawan.manajemen-pekerjaan.pekerjaan.signature', compact('pekerjaan'));
    }

    public function storeBarang(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kd_barang' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Barang gagal ditambahkan: ' . $validator->errors()->first());
        }

        $barang = Barang::find($request->kd_barang);

        if ($barang) {
            $kd_barang = $barang->kd_barang;
        } else {
            $newBarang = Barang::firstOrCreate([
                'nama_barang' => $request->kd_barang,
            ], [
                'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
                'stok' => $request->jumlah,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
            $kd_barang = $newBarang->kd_barang;
        }

        $barang = Barang::find($kd_barang);

        $request->merge([
            'kd_barang' => $kd_barang
        ]);


        if (PekerjaanBarang::where('kd_pekerjaan', $id)->where('kd_barang', $request->kd_barang)->exists()) {
            PekerjaanBarang::where('kd_pekerjaan', $id)->where('kd_barang', $request->kd_barang)->update([
                'jumlah' => PekerjaanBarang::where('kd_pekerjaan', $id)->where('kd_barang', $request->kd_barang)->first()->jumlah + $request->jumlah
            ]);
            return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
        }

        PekerjaanBarang::create([
            'kd_pekerjaan' => $id,
            'kd_barang' => $request->kd_barang,
            'jumlah' => $request->jumlah,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);
        return response()->json(['success' => 'Data barang berhasil disimpan.']);
    }

    public function destroy($id)
    {
        Pekerjaan::find($id)->delete();
        return response()->json(['success' => 'Data pekerjaan berhasil dihapus.']);
    }
}