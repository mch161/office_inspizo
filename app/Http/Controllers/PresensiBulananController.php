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
        if (Auth::guard('karyawan')->user()->role !== 'superadmin') {
            $rekapBulanan = PresensiBulanan::where('kd_karyawan', Auth::user()->kd_karyawan)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        } else {
            $rekapBulanan = PresensiBulanan::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        }
        $rekapData = Presensi::get();
        $karyawans = Karyawan::all();

        if ($request->has('kd_presensi_bulanan')) {
            $dataBulanan = PresensiBulanan::where('kd_presensi_bulanan', $request->kd_presensi_bulanan)->first();
            $rekapData = Presensi::where('kd_karyawan', $dataBulanan->kd_karyawan)
                ->whereYear('tanggal', $dataBulanan->tahun)
                ->whereMonth('tanggal', $dataBulanan->bulan)
                ->get();

            $karyawan = Karyawan::where('kd_karyawan', $dataBulanan->kd_karyawan)->first();

            return view('karyawan.presensi.bulanan', compact(
                'rekapBulanan',
                'rekapData',
                'karyawans',
                'dataBulanan',
                'karyawan'
            ));
        }

        return view('karyawan.presensi.bulanan', compact(
            'rekapBulanan',
            'rekapData',
            'karyawans'
        ));
    }

    public function check(Request $request)
    {
        $jumlah_tanggal = Carbon::createFromDate($request->tahun, $request->bulan, 1)->daysInMonth;

        $startDate = Carbon::createFromDate($request->tahun, $request->bulan, 1);
        $endDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)->endOfMonth();

        $jumlahMinggu = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return $date->isSunday();
        }, $endDate);

        $jumlah_libur = PresensiLibur::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->count() ?? 0;

        $jumlah_hari_kerja_normal = $jumlah_tanggal - $jumlah_libur - $jumlahMinggu;

        $jumlahCuti = Izin::whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('jenis', 'Cuti')
            ->where('status', '1')
            ->count() ?? 0;

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
                $waktu_mulai = Carbon::createFromFormat('H:i', trim($times[0]));
                $waktu_selesai = Carbon::createFromFormat('H:i', trim($times[1]));
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
            'jumlah_hari_cuti' => $jumlahCuti,
            'jumlah_hari_kerja_normal' => $jumlah_hari_kerja_normal,
            'jumlah_hari_minggu' => $jumlahMinggu,
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
        if ($request->kd_karyawan == null) {
            return redirect()->back()->with('error', 'Karyawan tidak ditemukan.');
        }

        if (PresensiBulanan::where('kd_karyawan', $request->kd_karyawan)->where('bulan', $request->bulan)->where('tahun', $request->tahun)->first() != null) {
            return redirect()->back()->with('error', 'Rekap Bulanan sudah ada.')->withInput();
        }

        if ($request->bulan > 12 || $request->bulan < 1) {
            return redirect()->route('presensi.bulanan')->with('error', 'Mohon pilih bulan yang valid')->withInput();
        }

        if ($request->bulan > Carbon::now()->month && $request->tahun >= Carbon::now()->year) {
            return redirect()->route('presensi.bulanan')->with('error', 'Bulan tidak boleh melebihi bulan sekarang')->withInput();
        }

        if ($request->tahun > Carbon::now()->year) {
            return redirect()->route('presensi.bulanan')->with('error', 'Tahun tidak boleh melebihi tahun sekarang')->withInput();
        }

        $data = $this->check($request);
        $data['kd_karyawan'] = $request->kd_karyawan;
        $data['bulan'] = $request->bulan;
        $data['tahun'] = $request->tahun;
        $data['verifikasi'] = '0';
        PresensiBulanan::create($data);

        return redirect()->back()->with('success', 'Rekap Bulanan berhasil dibuat.');
    }

    public function sync(Request $request, $id)
    {
        $data = $this->check($request);
        $data['kd_presensi_bulanan'] = $request->kd_presensi_bulanan;
        $data['kd_karyawan'] = $request->kd_karyawan;
        $data['bulan'] = $request->bulan;
        $data['tahun'] = $request->tahun;
        $presensiBulanan = PresensiBulanan::find($request->kd_presensi_bulanan);
        $presensiBulanan->fill($data);
        if (!$presensiBulanan->isDirty()) {
            return redirect()->route('presensi.bulanan')->with('success', 'Tidak ada perubahan pada rekap bulanan.');
        }
        $presensiBulanan->save();

        return redirect()->back()->with('success', 'Auto Sync Rekap Bulanan berhasil.');
    }

    public function verify($id)
    {
        PresensiBulanan::where('kd_presensi_bulanan', $id)->update([
            'verifikasi' => '1'
        ]);

        return redirect()->route('presensi-bulanan')->with('success', 'Rekap Bulanan berhasil diverifikasi.');
    }

    public function update(Request $request)
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        if ($request->kd_presensi_bulanan == null) {
            return redirect()->back()->with('error', 'Rekap Bulanan tidak ditemukan.');
        }

        $rekapBulanan = PresensiBulanan::find($request->kd_presensi_bulanan);
        $rekapBulanan->fill($request->all());
        if (!$rekapBulanan->isDirty()) {
            return redirect()->route('presensi.bulanan')->with('success', 'Tidak ada perubahan pada rekap bulanan.');
        }
        $rekapBulanan->save();

        return redirect()->route('presensi.bulanan')->with('success', 'Rekap Bulanan berhasil diupdate.');
    }
}
