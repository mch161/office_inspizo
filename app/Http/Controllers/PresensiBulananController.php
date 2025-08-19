<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PresensiBulanan;
use App\Models\PresensiLembur;
use App\Models\PresensiLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PresensiBulananController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan') ?? date('m');
        $tahun = $request->input('tahun') ?? date('Y');
        $kd_karyawan = $request->input('kd_karyawan') ?? Auth::guard('karyawan')->user()->kd_karyawan;
        $karyawan = Karyawan::where('kd_karyawan', $kd_karyawan)->first();

        if ($bulan > 12 || $bulan < 1) {
            return redirect()->route('presensi.bulanan')->with('error', 'Mohon pilih bulan yang valid');
        }

        if ($bulan > Carbon::now()->month && $tahun >= Carbon::now()->year) {
            return redirect()->route('presensi.bulanan')->with('error', 'Bulan tidak boleh melebihi bulan sekarang');
        }

        if ($tahun > Carbon::now()->year) {
            return redirect()->route('presensi.bulanan')->with('error', 'Tahun tidak boleh melebihi tahun sekarang');
        }

        $rekapBulanan = PresensiBulanan::where('kd_karyawan', $kd_karyawan)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        $rekapData = null;
        if (!$rekapBulanan) {
            $request['kd_karyawan'] = $kd_karyawan;
            $request['bulan'] = $bulan;
            $request['tahun'] = $tahun;
            $this->create($request);
            $rekapBulanan = PresensiBulanan::where('kd_karyawan', $kd_karyawan)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();

            $rekapData = Presensi::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('kd_karyawan', $kd_karyawan)
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        if ($rekapBulanan) {
            $rekapData = Presensi::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('kd_karyawan', $kd_karyawan)
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        $karyawans = Karyawan::all();
        return view('karyawan.presensi.bulanan', compact(
            'rekapBulanan',
            'rekapData',
            'bulan',
            'tahun',
            'kd_karyawan',
            'karyawan',
            'karyawans'
        ));
    }

    public function check(Request $request)
    {
        $jumlah_tanggal = Carbon::createFromDate($request->tahun, $request->bulan, 1)->daysInMonth;

        $jumlah_libur = PresensiLibur::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->count() ?? 0;
        $jumlah_hari_kerja_normal = $jumlah_tanggal - $jumlah_libur;

        $jumlah_hari_sakit = Izin::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('jenis', 'Izin Sakit')
            ->where('status', '1')
            ->count() ?? 0;

        $jumlah_hari_izin = Izin::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('jenis', '!=', 'Izin Sakit')
            ->where('status', '1')
            ->count() ?? 0;

        $jumlah_fingerprint = Presensi::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->count() ?? 0;

        $jumlah_alpha = $jumlah_hari_kerja_normal - ($jumlah_fingerprint + $jumlah_hari_sakit + $jumlah_hari_izin);

        $jumlah_terlambat = Presensi::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('Terlambat', '!=', 'Tidak')
            ->count() ?? 0;

        $total_menit_izin = Izin::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('jenis', '!=', 'Izin Sakit')
            ->where('status', '1')
            ->get()
            ->sum(function ($izin) {
                $times = explode(' - ', $izin->jam);
                if (count($times) !== 2 || strtolower($times[0]) === 'Full Day') {
                    $date = Carbon::createFromFormat('Y-m-d', $izin->tanggal);
                    if ($date->isSaturday()) {
                        $waktu_mulai = Carbon::createFromFormat('H:i', '08:00');
                        $waktu_selesai = Carbon::createFromFormat('H:i', '16:00');
                    } else {
                        $waktu_mulai = Carbon::createFromFormat('H:i', '08:00');
                        $waktu_selesai = Carbon::createFromFormat('H:i', '17:00');
                    }
                } else {
                    $waktu_mulai = Carbon::createFromFormat('H:i', trim($times[0]));
                    $waktu_selesai = Carbon::createFromFormat('H:i', trim($times[1]));
                }
                return $waktu_mulai->diffInMinutes($waktu_selesai);
            });

        $jam = floor($total_menit_izin / 60);
        $menit = $total_menit_izin % 60;

        $jumlah_jam_izin = sprintf('%02d:%02d', $jam, $menit);

        $jumlah_hari_lembur = PresensiLembur::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('verifikasi', '1')
            ->count() ?? 0;

        $jumlah_menit_lembur = PresensiLembur::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('verifikasi', '1')
            ->get()
            ->sum(function ($lembur) {
                $mulai = Carbon::parse($lembur->jam_mulai);
                $selesai = Carbon::parse($lembur->jam_selesai);
                return $mulai->diffInMinutes($selesai);
            });

        $jam = floor($jumlah_menit_lembur / 60);
        $menit = $jumlah_menit_lembur % 60;

        $jumlah_jam_lembur = sprintf('%02d:%02d', $jam, $menit);

        return [
            'jumlah_tanggal' => $jumlah_tanggal,
            'jumlah_libur' => $jumlah_libur,
            'jumlah_hari_kerja_normal' => $jumlah_hari_kerja_normal,
            'jumlah_hari_sakit' => $jumlah_hari_sakit,
            'jumlah_hari_izin' => $jumlah_hari_izin,
            'jumlah_fingerprint' => $jumlah_fingerprint,
            'jumlah_alpha' => $jumlah_alpha,
            'jumlah_terlambat' => $jumlah_terlambat,
            'jumlah_jam_izin' => $jumlah_jam_izin,
            'jumlah_hari_lembur' => $jumlah_hari_lembur,
            'jumlah_jam_lembur' => $jumlah_jam_lembur,
        ];
    }

    public function create(Request $request)
    {
        $data = $this->check($request);
        $data['kd_karyawan'] = $request->kd_karyawan;
        $data['bulan'] = $request->bulan;
        $data['tahun'] = $request->tahun;
        $data['verifikasi'] = '0';
        PresensiBulanan::create($data);

    }

    public function sync(Request $request)
    {
        $data = $this->check($request);
        $data['kd_presensi_bulanan'] = $request->kd_presensi_bulanan;
        $data['bulan'] = $request->bulan;
        $data['tahun'] = $request->tahun;
        PresensiBulanan::where('kd_presensi_bulanan', $request->kd_presensi_bulanan)->update($data);

        return redirect()->back()->with('success', 'Rekap Bulanan berhasil diupdate.');
    }

    public function verify(Request $request)
    {
        $rekapBulanan = PresensiBulanan::find($request->kd_presensi_bulanan);
        $rekapBulanan->verifikasi = '1';
        $rekapBulanan->save();

        return redirect()->back()->with('success', 'Rekap Bulanan berhasil diverifikasi.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah_tanggal' => 'required|numeric',
            'jumlah_libur' => 'required|numeric',
            'jumlah_hari_kerja_normal' => 'required|numeric',
            'jumlah_hari_sakit' => 'required|numeric',
            'jumlah_hari_izin' => 'required|numeric',
            'jumlah_fingerprint' => 'required|numeric',
            'jumlah_alpha' => 'required|numeric',
            'jumlah_terlambat' => 'required|numeric',
            'jumlah_jam_izin' => 'required|date_format:H:i',
            'jumlah_hari_lembur' => 'required|numeric',
            'jumlah_jam_lembur' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $rekapBulanan = PresensiBulanan::find($request->kd_presensi_bulanan);
        $rekapBulanan->jumlah_tanggal = $request->jumlah_tanggal;
        $rekapBulanan->jumlah_libur = $request->jumlah_libur;
        $rekapBulanan->jumlah_hari_kerja_normal = $request->jumlah_hari_kerja_normal;
        $rekapBulanan->jumlah_hari_sakit = $request->jumlah_hari_sakit;
        $rekapBulanan->jumlah_hari_izin = $request->jumlah_hari_izin;
        $rekapBulanan->jumlah_fingerprint = $request->jumlah_fingerprint;
        $rekapBulanan->jumlah_alpha = $request->jumlah_alpha;
        $rekapBulanan->jumlah_terlambat = $request->jumlah_terlambat;
        $rekapBulanan->jumlah_jam_izin = $request->jumlah_jam_izin;
        $rekapBulanan->jumlah_hari_lembur = $request->jumlah_hari_lembur;
        $rekapBulanan->jumlah_jam_lembur = $request->jumlah_jam_lembur;
        $rekapBulanan->save();

        return redirect()->back()->with('success', 'Rekap Bulanan berhasil diupdate.');
    }
}
