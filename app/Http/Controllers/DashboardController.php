<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Agenda;
use App\Models\Keuangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPelanggan = Pelanggan::count();
        $totalPesananAktif = Pesanan::where('status', 0)->count();
        $totalAgendaHariIni = Agenda::whereDate('start', Carbon::today())->count();
        $pendapatanBulanIni = Keuangan::where('jenis', 'Masuk')
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('masuk');

        $agendaTerdekat = Agenda::where('start', '>=', Carbon::now())
                                ->orderBy('start', 'asc')
                                ->limit(5)
                                ->get();

        $pesananData = Pesanan::select(
            DB::raw('count(kd_pesanan) as total'),
            DB::raw("SUBSTR(tanggal, 7, 4) || '-' || SUBSTR(tanggal, 4, 2) as bulan")
        )
        ->where(DB::raw("SUBSTR(tanggal, 7, 4) || '-' || SUBSTR(tanggal, 4, 2) || '-' || SUBSTR(tanggal, 1, 2)"), '>=', Carbon::now()->subMonths(5)->startOfMonth()->toDateString())
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $carbonDate = Carbon::now()->subMonths($i);
            $monthKey = $carbonDate->format('Y-m');
            
            $labels[] = $carbonDate->format('M Y');
            
            $data[] = $pesananData[$monthKey] ?? 0;
        }

        return view('dashboard', compact(
            'totalPelanggan',
            'totalPesananAktif',
            'totalAgendaHariIni',
            'pendapatanBulanIni',
            'agendaTerdekat',
            'labels',
            'data'
        ));
    }
}