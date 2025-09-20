<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SuratPerintahKerja;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Agenda;
use App\Models\Keuangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPelanggan = Pelanggan::count();
        $totalPesananAktif = Pesanan::where('progres', '>=', 2)->where('status', 0)->count();

        $totalAgendaHariIni = Agenda::whereDate('start', Carbon::today())->count();
        $pendapatanBulanIni = Keuangan::where('jenis', 'Masuk')
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('masuk');

        $agendaTerdekat = Agenda::where('start', '>=', Carbon::now())
            ->orderBy('start', 'asc')
            ->limit(5)
            ->get();

        $tugas = SuratPerintahKerja::where('kd_karyawan', Auth::guard('karyawan')->user()->kd_karyawan)->where('status', 0)->count();

        $pesananStatus = Pesanan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $pesananProgres = Pesanan::select('progres', DB::raw('count(*) as total'))
            ->where('status', 0)
            ->groupBy('progres')
            ->pluck('total', 'progres')
            ->all();


        $pesananStatusLabels = [];
        $pesananStatusData = [];
        $pesananStatusColors = ['#007bff', '#ffc107', '#28a745', '#17a2b8', '#dc3545'];

        $statusLabels = [
            0 => 'Baru',
            1 => 'Dikerjakan',
            2 => 'Selesai',
            3 => 'Diambil',
            4 => 'Dibatalkan',
        ];

        foreach ($statusLabels as $status => $label) {
            $pesananStatusLabels[] = $label;
        }

        $pesananStatusData = array_merge([
            0 => ($pesananProgres[1] ?? 0),
            1 => ($pesananProgres[3] ?? 0),
            2 => ($pesananStatus[1] ?? 0),
            3 => ($pesananProgres[2] ?? 0),
            4 => ($pesananStatus[2] ?? 0),
        ], array_fill_keys(range(0, 4), 0));

        $pesananData = Pesanan::select(
            DB::raw('count(kd_pesanan) as total'),
            DB::raw("SUBSTR(tanggal, 4, 7) as bulan")
        )
            ->where(DB::raw("SUBSTR(tanggal, 4, 7)"), '>=', Carbon::now()->subMonths(5)->startOfMonth()->format('m/Y'))
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('total', 'bulan');

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $carbonDate = Carbon::now()->subMonths($i);
            $monthKey = $carbonDate->format('m/Y');

            $labels[] = $carbonDate->format('M Y');

            $data[] = $pesananData[$monthKey] ?? 0;
        }

        return view('dashboard', compact(
            'totalPelanggan',
            'totalPesananAktif',
            'totalAgendaHariIni',
            'pendapatanBulanIni',
            'agendaTerdekat',
            'tugas',
            'labels',
            'data',
            'pesananStatusLabels',
            'pesananStatusData',
            'pesananStatusColors'
        ));
    }
}