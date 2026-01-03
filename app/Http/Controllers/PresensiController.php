<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\PresensiBulanan;
use App\Models\PresensiLibur;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = ($request->input('tanggal') ?? Carbon::now()->format('Y-m-d'));

        $rekapData = Presensi::whereDate('tanggal', $tanggal)
            ->orderBy('nama', 'asc')
            ->get();

        $karyawans = Karyawan::all();

        return view('karyawan.presensi.presensi', [
            'rekapData' => $rekapData,
            'tanggal' => $tanggal,
            'karyawans' => $karyawans
        ]);
    }

    public function fetch(Request $request)
    {
        if ($request->has('all')) {
            Artisan::call('presensi:fetch --all');
            return redirect()->route('presensi.index')->with('success', 'Semua data presensi berhasil diambil.');
        }
        $tanggal = $request->input('tanggal');
        Artisan::call('presensi:fetch --dari=' . $tanggal . ' --sampai=' . $tanggal);
        $tanggalMessage = Carbon::parse($tanggal)->locale('id_ID')->translatedFormat('d F Y');
        return redirect()->route('presensi.index', ['tanggal' => $tanggal])->with('success', 'Data presensi tanggal ' . $tanggalMessage . ' berhasil diambil.');
    }

    public function view(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $kd_karyawan = $request->input('kd_karyawan');
        $karyawan = Karyawan::where('kd_karyawan', $kd_karyawan)->first();
        $karyawans = Karyawan::all();

        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $presensi = Presensi::where('kd_karyawan', $kd_karyawan)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $rawIzin = Izin::where('kd_karyawan', $kd_karyawan)
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->orWhere(function ($query) use ($bulan, $tahun) {
                        $query->whereYear('tanggal_selesai', $tahun)
                            ->whereMonth('tanggal_selesai', $bulan);
                    });
            })
            ->where('status', '1')
            ->get();

        $izin = collect();

        foreach ($rawIzin as $item) {
            $start = Carbon::parse($item->tanggal);
            $end = $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai) : $start->copy();

            $izinPeriod = CarbonPeriod::create($start, $end);

            foreach ($izinPeriod as $date) {
                $izin->put($date->format('Y-m-d'), $item);
            }
        }

        $hariLibur = PresensiLibur::get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $finalLog = collect();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $isSunday = $date->isSunday();

            $status = 'A';
            $jam_masuk = '--:--:--';
            $jam_keluar = '--:--:--';
            $keterangan = 'Tidak Hadir';
            $jenis_izin = null;
            if ($hariLibur->has($dateStr)) {
                $data = $hariLibur[$dateStr];
                $status = 'L';
                $keterangan = $data->keterangan ?? 'Libur';
            } elseif ($isSunday) {
                $status = 'M';
                $keterangan = 'Hari Minggu';
            } elseif ($izin->has($dateStr)) {
                $data = $izin[$dateStr];
                $status = 'I';
                $keterangan = $data->keterangan ?? $data->jenis;
                $jenis_izin = $data->jenis;
            } elseif ($presensi->has($dateStr)) {
                $data = $presensi[$dateStr];
                $status = 'H';
                $jam_masuk = $data->jam_masuk;
                $jam_keluar = $data->jam_keluar;
                $keterangan = 'Hadir';
            }

            $finalLog->push((object) [
                'tanggal' => $dateStr,
                'jam_masuk' => $jam_masuk,
                'jam_keluar' => $jam_keluar,
                'status' => $status,
                'keterangan' => $keterangan,
                'jenis' => $jenis_izin
            ]);
        }

        $rekapData = $finalLog;

        return view('karyawan.presensi.presensi', [
            'rekapData' => $rekapData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kd_karyawan' => $kd_karyawan,
            'karyawan' => $karyawan,
            'karyawans' => $karyawans
        ]);
    }
}