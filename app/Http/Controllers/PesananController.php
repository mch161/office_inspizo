<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananBarang;
use App\Models\PesananDetail;
use App\Models\PesananJasa;
use App\Models\PesananProgress;
use App\Models\SuratPerintahKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pesanan = Pesanan::where('progres', '>=', 2)
                ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') DESC")
                ->get();

            return DataTables::of($pesanan)
                ->addIndexColumn()
                ->addColumn('nama_pelanggan', function ($row) {
                    return $row->pelanggan->nama_pelanggan;
                })
                ->addColumn('deskripsi_pesanan', function ($row) {
                    return $row->deskripsi_pesanan;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">Selesai</span>';
                    } elseif ($row->status == 0 && $row->progres == 1) {
                        return '<span class="badge bg-info">Pesanan Dibuat</span>';
                    } elseif ($row->status == 0 && $row->progres == 2) {
                        return '<span class="badge bg-warning">Pesanan Diterima</span>';
                    } elseif ($row->status == 0 && $row->progres == 3) {
                        return '<span class="badge bg-secondary">Pesanan Diproses</span>';
                    } elseif ($row->status == 2) {
                        return '<span class="badge bg-danger">Pesanan Dibatalkan</span>';
                    } else {
                        return '<span class="badge bg-primary">Belum Selesai</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('pesanan.detail', $row->kd_pesanan) . '" class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editBtn"><i class="fas fa-edit"></i></a>';
                    if ($row->status == 0) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Batalkan" class="btn btn-danger btn-sm batalkan-btn"><i class="fas fa-times"></i></a>';
                    }
                    if ($row->status == 2) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Hapus" class="btn btn-danger btn-sm deleteBtn"><i class="fas fa-trash"></i></a>';
                    }
                    if ($row->status == 0 && $row->progres == 2) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Agendakan" class="btn btn-warning btn-sm agendaBtn"><i class="fas fa-calendar-day"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $pesanan = Pesanan::where('progres', '>=', 2)
            ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') DESC")
            ->get();

        $permintaan = Pesanan::where('progres', '1')
            ->where('status', '!=', '1')
            ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') DESC")
            ->get();

        $pelanggan = Pelanggan::get()->all();
        return view('karyawan.pesanan.pesanan', [
            "pesanan" => $pesanan,
            "pelanggan" => $pelanggan,
            "permintaan" => $permintaan
        ]);
    }

    public function permintaan(Request $request)
    {
        if ($request->ajax()) {
            $permintaan = Pesanan::where('progres', '1')
                ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') DESC")
                ->get();

            return DataTables::of($permintaan)
                ->addIndexColumn()
                ->addColumn('nama_pelanggan', function ($row) {
                    return $row->pelanggan->nama_pelanggan;
                })
                ->addColumn('deskripsi_pesanan', function ($row) {
                    return $row->deskripsi_pesanan;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 0 && $row->progres == 1) {
                        return '<span class="badge bg-info">Pesanan Dibuat</span>';
                    } elseif ($row->status == 2) {
                        return '<span class="badge bg-danger">Pesanan Dibatalkan</span>';
                    } else {
                        return '<span class="badge bg-light">Unknown</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    if ($row->status == 0) {
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Terima" class="btn btn-success btn-sm accept-btn"><i class="fas fa-check"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Batalkan" class="btn btn-danger btn-sm batalkan-btn"><i class="fas fa-times"></i></a>';
                    }

                    if ($row->status == 2) {
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->kd_pesanan . '" data-original-title="Hapus" class="btn btn-danger btn-sm deleteBtn"><i class="fas fa-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deskripsi_pesanan' => 'required',
            'tanggal' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        if ($request->kd_pesanan) {
            $pesanan = Pesanan::find($request->kd_pesanan);
            $pesanan->update($request->all());
        } else {
            $pesanan = Pesanan::create([
                'kd_pelanggan' => $request->kd_pelanggan,
                'deskripsi_pesanan' => $request->deskripsi_pesanan,
                'tanggal' => $request->tanggal,
                'progres' => '2',
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
            
            PesananDetail::create([
                'kd_pelanggan' => $request->kd_pelanggan,
                'kd_pesanan' => $pesanan->kd_pesanan,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
        }

        return response()->json(['success' => 'Data pekerjaan berhasil disimpan.']);
    }

    public function detail($pesanan)
    {
        $pesanan = Pesanan::find($pesanan);
        $pesanan_detail = PesananDetail::where('kd_pesanan', $pesanan->kd_pesanan)->first();
        $pesanan_barang = PesananBarang::where('kd_pesanan_detail', $pesanan_detail->kd_pesanan_detail)->get();
        $pesanan_jasa = PesananJasa::where('kd_pesanan_detail', $pesanan_detail->kd_pesanan_detail)->get();
        $barang = Barang::where('status', '1')->get();
        $jasa = Jasa::get()->all();
        $surat_perintah = SuratPerintahKerja::where('kd_pesanan', $pesanan->kd_pesanan)->get();

        $previousUrl = URL::previous();
        $pesananId = $pesanan->kd_pesanan;

        $pattern = '/\/pesanan\/' . $pesananId . '\/.+/';

        if (preg_match($pattern, $previousUrl) || $previousUrl == route('login')) {
            $backUrl = route('pesanan.index');
        } else {
            $backUrl = $previousUrl;
        }

        return view('karyawan.pesanan.detail', compact('pesanan', 'pesanan_detail', 'pesanan_barang', 'barang', 'jasa', 'pesanan_jasa', 'surat_perintah', 'backUrl'));
    }

    public function agenda(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_pesanan' => 'required|exists:pesanan,kd_pesanan',
            'title' => 'required|string',
            'tanggal' => 'required|string',
            'jam' => 'required|string',
        ]);

        $date = $request->tanggal . ' ' . $request->jam;

        $startDateTime = Carbon::createFromFormat('d/m/Y H:i', $date);

        $agendaExists = Agenda::where('start', $startDateTime)->exists();

        if ($agendaExists) {
            return redirect()->back()->with('error', 'Agenda sudah ada.');
        }

        Agenda::create([
            'kd_pesanan' => $request->kd_pesanan,
            'title' => $request->title,
            'start' => $startDateTime,
            'end' => $startDateTime->addMinutes(60),
            'color' => '#007bff',
            'kd_karyawan' => Auth::guard('karyawan')->id(),
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        PesananProgress::create([
            'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
            'kd_pesanan' => $request->kd_pesanan,
            'keterangan' => 'Petugas akan melakukan kunjungan ke pelanggan tanggal ' . $request->tanggal,
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
        ]);

        Pesanan::find($request->kd_pesanan)->update([
            'progres' => '3'
        ]);

        return redirect()->back()->with('success', 'Agenda berhasil dibuat.');
    }

    public function complete($pesanan)
    {
        $p = Pesanan::find($pesanan);
        $p->status = '1';
        $p->progres = '4';
        $p->save();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan terselesaikan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $pesanan = Pesanan::find($id);

        return response()->json($pesanan);
    }

    public function update(Request $request, Pesanan $pesanan)
    {
        $validator = Validator::make($request->all(), [
            'progres' => 'sometimes|in:2',
            'status' => 'sometimes|in:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $message = 'Pesanan diperbarui.';

        if ($request->has('progres') && $request->progres == 2) {
            $pesanan->progres = 2;
            $message = 'Pesanan diterima.';
        }

        if ($request->has('status') && $request->status == 2) {
            $pesanan->status = 2;
            $message = 'Pesanan dibatalkan.';
        }

        $pesanan->save();

        return response()->json(['success' => $message]);
    }

    public function destroy(Pesanan $pesanan)
    {
        $pesananDetail = PesananDetail::where('kd_pesanan', $pesanan->kd_pesanan)->first();

        if ($pesananDetail) {
            PesananBarang::where('kd_pesanan_detail', $pesananDetail->kd_pesanan_detail)->delete();
            PesananJasa::where('kd_pesanan_detail', $pesananDetail->kd_pesanan_detail)->delete();
        }

        if (PesananProgress::where('kd_pesanan', $pesanan->kd_pesanan)->exists()) {
            PesananProgress::where('kd_pesanan', $pesanan->kd_pesanan)
                ->update(['kd_pesanan' => null]);
        }

        if ($pesananDetail) {
            $pesananDetail->delete();
        }

        $pesanan->delete();

        return response()->json(['success' => 'Pesanan berhasil dihapus.']);
    }
}