<?php

namespace App\Http\Controllers;

use App\Models\PresensiLibur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LiburController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal') ?? Carbon::now()->format('Y-m');
        $liburs = PresensiLibur::whereMonth('tanggal', Carbon::parse($tanggal)->month)
            ->whereYear('tanggal', Carbon::parse($tanggal)->year)
            ->get();
        return view('karyawan.presensi.libur', compact('liburs', 'tanggal'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'jenis_libur' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        PresensiLibur::create([
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d'),
            'jenis_libur' => $request->jenis_libur,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Libur berhasil disimpan.');
    }

    public function destroy($id)
    {
        PresensiLibur::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Libur berhasil dihapus.');
    }

    public function importFromApi(Request $request)
    {
        $response = Http::get('https://api-harilibur.vercel.app/api');

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data dari API libur.');
        }

        $holidays = $response->json();

        if (!is_array($holidays)) {
            return redirect()->back()->with('error', 'Format data API tidak terduga.');
        }

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($holidays as $holiday) {

            if (isset($holiday['is_national_holiday']) && $holiday['is_national_holiday'] == true && !empty($holiday['holiday_date'])) {

                $holidayDate = Carbon::parse($holiday['holiday_date'])->format('Y-m-d');

                $libur = PresensiLibur::firstOrCreate(
                    [
                        'tanggal' => $holidayDate
                    ],
                    [
                        'jenis_libur' => 'Nasional',
                        'keterangan' => $holiday['holiday_name'] ?? 'Libur Nasional'
                    ]
                );

                if ($libur->wasRecentlyCreated) {
                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        return redirect()->route('libur.index')
            ->with('success', "Import selesai. $importedCount libur baru ditambahkan. $skippedCount data yang sudah ada dilewati.");
    }
}