<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Barang;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Jasa;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananBarang;
use App\Models\PesananDetail;
use App\Models\PesananJasa;
use App\Models\PesananProgress;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SuratPerintahKerja;
use App\Models\Tiket;
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
                ->addColumn('prioritas', function ($row) {
                    if ($row->tiket == null) {
                        return '<span class="badge bg-info">Normal</span>';
                    }
                    if ($row->tiket->prioritas == 1) {
                        return '<span class="badge bg-info">Normal</span>';
                    }
                    if ($row->tiket->prioritas == 2) {
                        return '<span class="badge bg-warning">Segera</span>';
                    }
                    if ($row->tiket->prioritas == 3) {
                        return '<span class="badge bg-danger">Penting</span>';
                    }
                })
                ->addColumn('jenis', function ($row) {
                    if ($row->tiket == null) {
                        return "-";
                    }
                    return $row->tiket->jenis;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal;
                })
                ->addColumn('via', function ($row) {
                    if ($row->tiket == null) {
                        return "-";
                    }
                    return $row->tiket->via;
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
                    $btn = '<a href="' . route('tiket.show', $row->kd_pesanan) . '" class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>';
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
                ->rawColumns(['prioritas', 'status', 'action'])
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
        return view('karyawan.tiket.index', [
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
                ->addColumn('prioritas', function ($row) {
                    if ($row->tiket->prioritas == 1) {
                        return '<span class="badge bg-info">Normal</span>';
                    }
                    if ($row->tiket->prioritas == 2) {
                        return '<span class="badge bg-warning">Segera</span>';
                    }
                    if ($row->tiket->prioritas == 3) {
                        return '<span class="badge bg-danger">Penting</span>';
                    }
                })
                ->addColumn('jenis', function ($row) {
                    return $row->tiket->jenis;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal;
                })
                ->addColumn('via', function ($row) {
                    return $row->tiket->via;
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
            Tiket::where('kd_tiket', $pesanan->kd_tiket)->update([
                'prioritas' => $request->prioritas,
                'jenis' => $request->jenis,
                'deskripsi' => $request->deskripsi_pesanan,
                'via' => $request->via
            ]);
        } else {
            $tiket = Tiket::create([
                'prioritas' => $request->prioritas,
                'jenis' => $request->jenis,
                'deskripsi' => $request->deskripsi_pesanan,
                'kd_pelanggan' => $request->kd_pelanggan,
                'via' => $request->via,
            ]);

            $pesanan = Pesanan::create([
                'kd_pelanggan' => $request->kd_pelanggan,
                'deskripsi_pesanan' => $request->deskripsi_pesanan,
                'tanggal' => $request->tanggal,
                'progres' => '2',
                'jenis' => 'Quotation',
                'kd_tiket' => $tiket->kd_tiket,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);

            PesananDetail::create([
                'kd_pelanggan' => $request->kd_pelanggan,
                'kd_pesanan' => $pesanan->kd_pesanan,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);

            Quotation::create([
                'kd_tiket' => $tiket->kd_tiket,
                'kd_pelanggan' => $request->kd_pelanggan,
                'tanggal' => $request->tanggal,
                'dibuat_oleh' => Auth::guard('karyawan')->user()->nama
            ]);
        }

        return response()->json(['success' => 'Data pekerjaan berhasil disimpan.']);
    }

    public function show($tiket)
    {
        $pesanan = Pesanan::find($tiket);
        $surat_perintah = SuratPerintahKerja::where('kd_pesanan', $pesanan->kd_pesanan)->get();

        $previousUrl = URL::previous();
        $pesananId = $pesanan->kd_pesanan;

        $pattern = '/\/pesanan\/' . $pesananId . '\/.+/';

        if (preg_match($pattern, $previousUrl) || $previousUrl == route('login')) {
            $backUrl = route('tiket.index');
        } else {
            $backUrl = $previousUrl;
        }

        if ($pesanan->jenis == 'Quotation') {
            $billing = Quotation::where('kd_tiket', $pesanan->kd_tiket)->first();
            $billing_barang = QuotationItem::where('kd_quotation', $billing->kd_quotation)->where('kd_barang', '!=', null)->get();
            $billing_jasa = QuotationItem::where('kd_quotation', $billing->kd_quotation)->where('kd_jasa', '!=', null)->get();
        } elseif ($pesanan->jenis == 'Invoice') {
            $quotation = Quotation::where('kd_tiket', $pesanan->kd_tiket)->first();
            $billing = Invoice::where('kd_quotation', $quotation->kd_quotation)->first();
            $billing_barang = InvoiceItem::where('kd_invoice', $billing->kd_invoice)->where('kd_barang', '!=', null)->get();
            $billing_jasa = InvoiceItem::where('kd_invoice', $billing->kd_invoice)->where('kd_jasa', '!=', null)->get();
        }

        return view('karyawan.tiket.show', compact('pesanan', 'billing', 'billing_barang', 'billing_jasa', 'surat_perintah', 'backUrl'));
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

    public function complete($tiket)
    {
        $p = Pesanan::find($tiket);
        $p->status = '1';
        $p->progres = '4';
        $p->save();

        return redirect()->route('tiket.index')->with('success', 'Pesanan terselesaikan.');
    }

    public function edit(string $id)
    {
        $pesanan = Pesanan::find($id);
        if (is_null($pesanan->kd_tiket)) {
            $tiket = Tiket::create([
                'prioritas' => 1,
                'jenis' => 'Pesanan',
                'deskripsi' => $pesanan->deskripsi_pesanan,
                'kd_pelanggan' => $pesanan->kd_pelanggan,
                'via' => 'web',
            ]);
            $pesanan->kd_tiket = $tiket->kd_tiket;
            $pesanan->save();
        }

        $pesanan->tiket = Tiket::where('kd_tiket', $pesanan->kd_tiket)->first();

        return response()->json($pesanan);
    }

    public function update(Request $request, Pesanan $tiket)
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
            $tiket->progres = 2;
            $message = 'Pesanan diterima.';
        }

        if ($request->has('status') && $request->status == 2) {
            $tiket->status = 2;
            $message = 'Pesanan dibatalkan.';
        }

        $tiket->save();

        return response()->json(['success' => $message]);
    }

    public function destroy(Pesanan $id)
    {
        $pesananDetail = PesananDetail::where('kd_pesanan', $id->kd_pesanan)->first();

        if ($pesananDetail) {
            PesananBarang::where('kd_pesanan_detail', $pesananDetail->kd_pesanan_detail)->delete();
            PesananJasa::where('kd_pesanan_detail', $pesananDetail->kd_pesanan_detail)->delete();
        }

        if (PesananProgress::where('kd_pesanan', $id->kd_pesanan)->exists()) {
            PesananProgress::where('kd_pesanan', $id->kd_pesanan)
                ->update(['kd_pesanan' => null]);
        }

        if ($pesananDetail) {
            $pesananDetail->delete();
        }

        $id->delete();

        return response()->json(['success' => 'Pesanan berhasil dihapus.']);
    }

    public function migrateToInvoice($kd_pesanan)
    {
        $pesanan = Pesanan::findOrFail($kd_pesanan);

        $quotation = Quotation::where('kd_tiket', $pesanan->kd_tiket)->first();

        if (!$quotation) {
            return redirect()->back()->with('error', 'Quotation tidak ditemukan untuk pesanan ini.');
        }

        if (Invoice::where('kd_quotation', $quotation->kd_quotation)->exists()) {
            return redirect()->back()->with('error', 'Invoice sudah dibuat untuk Quotation ini.');
        }

        $invoice = Invoice::create([
            'kd_quotation' => $quotation->kd_quotation,
            'kd_pelanggan' => $quotation->kd_pelanggan,
            'kd_karyawan' => Auth::guard('karyawan')->user()->kd_karyawan,
            'tanggal' => Carbon::now()->format('d/m/Y'),
            'dibuat_oleh' => Auth::guard('karyawan')->user()->nama,
        ]);

        $quotationItems = QuotationItem::where('kd_quotation', $quotation->kd_quotation)->get();

        foreach ($quotationItems as $item) {
            InvoiceItem::create([
                'kd_invoice' => $invoice->kd_invoice,
                'kd_barang' => $item->kd_barang,
                'kd_jasa' => $item->kd_jasa,
                'harga' => $item->harga,
                'jumlah' => $item->jumlah,
                'subtotal' => $item->subtotal ?? ($item->harga * $item->jumlah),
            ]);
        }

        $pesanan->jenis = 'Invoice';
        $pesanan->save();

        return redirect()->back()->with('success', 'Quotation berhasil dimigrasi menjadi Invoice.');
    }

}