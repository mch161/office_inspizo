<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PresensiBulanan;
use App\Models\PresensiLembur;
use App\Models\PresensiLibur;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PresensiBulananController extends Controller
{
    public function index(Request $request)
    {
        $query = PresensiBulanan::with('karyawan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');

        if (Auth::guard('karyawan')->user()->role !== 'superadmin') {
            $query->where('kd_karyawan', Auth::user()->kd_karyawan);
        }

        $rekapBulanan = $query->get();
        $karyawans = Karyawan::orderBy('nama', 'asc')->get();

        $rekapData = collect();
        $dataBulanan = null;

        if ($request->has('kd_presensi_bulanan')) {
            $dataBulanan = PresensiBulanan::find($request->kd_presensi_bulanan);

            if ($dataBulanan) {

                $startOfMonth = Carbon::createFromDate($dataBulanan->tahun, $dataBulanan->bulan, 1);
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
                $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

                $presensi = Presensi::where('kd_karyawan', $dataBulanan->kd_karyawan)
                    ->whereYear('tanggal', $dataBulanan->tahun)
                    ->whereMonth('tanggal', $dataBulanan->bulan)
                    ->get()
                    ->keyBy(function ($item) {
                        return Carbon::parse($item->tanggal)->format('Y-m-d');
                    });

                $rawIzin = Izin::where('kd_karyawan', $dataBulanan->kd_karyawan)
                    ->where(function ($query) use ($dataBulanan) {
                        $query->whereYear('tanggal', $dataBulanan->tahun)
                            ->whereMonth('tanggal', $dataBulanan->bulan)
                            ->orWhere(function ($query) use ($dataBulanan) {
                                $query->whereYear('tanggal_selesai', $dataBulanan->tahun)
                                    ->whereMonth('tanggal_selesai', $dataBulanan->bulan);
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
            }
        }

        return view('karyawan.presensi.bulanan', compact('rekapBulanan', 'rekapData', 'karyawans', 'dataBulanan'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah_tanggal' => 'required|numeric',
            'jumlah_libur' => 'required|numeric',
            'jumlah_hari_cuti' => 'required|numeric',
            'jumlah_hari_kerja_normal' => 'required|numeric',
            'jumlah_hari_minggu' => 'required|numeric',
            'jumlah_hari_sakit' => 'required|numeric',
            'jumlah_hari_izin' => 'required|numeric',
            'jumlah_fingerprint' => 'required|numeric',
            'jumlah_alpha' => 'required|numeric',
            'jumlah_terlambat' => 'required|numeric',
            'jumlah_jam_izin' => 'required|date_format:H:i',
            'jumlah_hari_lembur' => 'required|numeric',
            'jumlah_jam_lembur' => 'required|date_format:H:i',
            'kd_presensi_bulanan' => 'required|exists:presensi_bulanan,kd_presensi_bulanan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            $rekapBulanan = PresensiBulanan::findOrFail($request->kd_presensi_bulanan);
            $rekapBulanan->fill($request->all());

            if ($rekapBulanan->isDirty()) {
                $rekapBulanan->save();
                return redirect()->route('presensi.bulanan', ['kd_presensi_bulanan' => $request->kd_presensi_bulanan])
                    ->with('success', 'Rekap Bulanan berhasil diperbarui.');
            }

            return redirect()->back()->with('info', 'Tidak ada perubahan pada data.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|numeric|min:1|max:12',
            'tahun' => 'required|numeric|min:2000',
            'kd_karyawan' => 'nullable|exists:karyawan,kd_karyawan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $targetKaryawan = $request->kd_karyawan;

        $karyawans = Karyawan::query()
            ->when($targetKaryawan, fn($q) => $q->where('kd_karyawan', $targetKaryawan))
            ->get();

        if ($karyawans->isEmpty()) {
            dd("No employees found for ID: " . $targetKaryawan);
        }

        $holidays = PresensiLibur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->pluck('keterangan', 'tanggal')
            ->toArray();

        $allPresensi = Presensi::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->groupBy('kd_karyawan');

        $allLembur = PresensiLembur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->groupBy('kd_karyawan');

        $allIzin = Izin::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->groupBy('kd_karyawan');

        DB::beginTransaction();
        try {
            foreach ($karyawans as $karyawan) {
                $empPresensi = $allPresensi->get($karyawan->kd_karyawan, collect());
                $empLembur = $allLembur->get($karyawan->kd_karyawan, collect());
                $empIzin = $allIzin->get($karyawan->kd_karyawan, collect());

                $totalDays = Carbon::createFromDate($tahun, $bulan)->daysInMonth;
                $jumlahMinggu = 0;
                $jumlahLibur = 0;
                $jumlahKerjaNormal = 0;

                // Hitung Hari Kerja & Libur
                for ($day = 1; $day <= $totalDays; $day++) {
                    $date = Carbon::createFromDate($tahun, $bulan, $day)->format('Y-m-d');
                    $isSunday = Carbon::parse($date)->isSunday();
                    $isHoliday = array_key_exists($date, $holidays);

                    if ($isSunday) {
                        $jumlahMinggu++;
                    } elseif ($isHoliday) {
                        $jumlahLibur++;
                    } else {
                        $jumlahKerjaNormal++;
                    }
                }

                $totalSakit = $empIzin->where('jenis', 'Izin Sakit')->sum('jumlah_hari');
                $totalIzin = $empIzin->where('jenis', 'Izin Keperluan Pribadi')->sum('jumlah_hari');
                $totalCuti = $empIzin->where('jenis', 'Cuti')->sum('jumlah_hari');

                // Helper sum time
                $sumTime = function ($items, $column) {
                    $totalMinutes = 0;
                    foreach ($items as $item) {
                        if ($item->$column) {
                            $parts = explode(':', $item->$column);
                            $totalMinutes += ($parts[0] * 60) + ($parts[1] ?? 0);
                        }
                    }
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    return sprintf('%02d:%02d', $hours, $minutes);
                };

                $total_menit_izin = $empIzin
                    ->where('jenis', '!=', 'Izin Sakit')
                    ->where('status', '1')
                    ->sum(function ($izin) {
                        $times = explode(' - ', $izin->jam);
                        $waktu_mulai = Carbon::createFromFormat('H:i', trim($times[0]));
                        $waktu_selesai = Carbon::createFromFormat('H:i', trim($times[1]));
                        return $waktu_mulai->diffInMinutes($waktu_selesai);
                    });

                $jam = floor($total_menit_izin / 60);
                $menit = $total_menit_izin % 60;

                $jumlah_jam_izin = sprintf('%02d:%02d', $jam, $menit);
                $data = [
                    'kd_karyawan' => $karyawan->kd_karyawan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'jumlah_tanggal' => $totalDays,
                    'jumlah_libur' => $jumlahLibur,
                    'jumlah_hari_minggu' => $jumlahMinggu,
                    'jumlah_hari_kerja_normal' => $jumlahKerjaNormal,

                    // Data dari tabel Presensi (Fingerprint)
                    'jumlah_fingerprint' => $empPresensi->count(),
                    'jumlah_alpha' => $jumlahKerjaNormal - $empPresensi->count() - $empIzin->sum('jumlah_hari'),
                    'jumlah_terlambat' => $empPresensi->where('terlambat', '!=', 'Tidak')->count(),

                    // Data dari tabel Izin (dihitung dari kolom jumlah_hari)
                    'jumlah_hari_sakit' => $totalSakit,
                    'jumlah_hari_izin' => $totalIzin,
                    'jumlah_hari_cuti' => $totalCuti,

                    'jumlah_jam_izin' => $jumlah_jam_izin,

                    // Data dari tabel Lembur
                    'jumlah_hari_lembur' => $empLembur->count(),
                    'jumlah_jam_lembur' => $sumTime($empLembur, 'total_jam'),
                ];

                PresensiBulanan::updateOrCreate(
                    [
                        'kd_karyawan' => $karyawan->kd_karyawan,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'verifikasi' => $request->verifikasi ?? 0,
                    ],
                    $data
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Rekap bulanan berhasil digenerate.');

        } catch (\Exception $e) {
            DB::rollBack();
            dd('Error caught: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate rekap: ' . $e->getMessage());
        }
    }
}