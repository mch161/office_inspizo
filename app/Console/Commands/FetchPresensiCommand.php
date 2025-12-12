<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Fingerprint;
use App\Models\Presensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class FetchPresensiCommand extends Command
{
    protected $signature = 'presensi:fetch {--dari=} {--sampai=} {--all}';
    protected $description = 'Sinkronisasi log mentah, lalu proses dan simpan rekap harian';

    public function handle()
    {
        // 1. Capture the status of the sync
        // Returns: 1 (New data found), 0 (No new data), false (Connection error)
        $syncStatus = $this->syncronizeRawLogs();

        // If connection failed
        if ($syncStatus === false) {
            return 1;
        }

        // 2. LOGIC FIX: If sync returned 0 (Meaning no NEW entries were created in DB)
        // AND the user is not forcing a specific date range, STOP here.
        if ($syncStatus === 0 && !$this->option('dari') && !$this->option('sampai') && !$this->option('all')) {
            $this->info('Proses dihentikan karena tidak ada data baru yang masuk ke database.');
            return 0;
        }

        $period = $this->getDateRange();
        $startDate = $period->getStartDate()->format('Y-m-d');
        $endDate = $period->getEndDate()->format('Y-m-d');

        $this->info("Menghapus rekap lama dari tanggal {$startDate} hingga {$endDate}...");
        Presensi::whereBetween('tanggal', [$startDate, $endDate])->delete();
        $this->info("Rekap lama berhasil dihapus.");

        foreach ($period as $date) {
            $this->processAndStoreReport($date);
        }

        $this->info('Semua proses selesai!');
        return 0;
    }

    private function syncronizeRawLogs()
    {
        $this->info('Menyinkronkan log mentah dari mesin...');
        $deviceIp = '192.168.88.9';
        $deviceKey = '0';
        $soapRequest = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">{$deviceKey}</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
        
        try {
            $response = Http::withBody($soapRequest, 'text/xml')->post("http://{$deviceIp}/iWsService");
            if (!$response->successful()) {
                $this->error('Gagal terhubung ke mesin.'); 
                return false;
            }

            preg_match_all('/<Row>(.*?)<\\/Row>/s', $response->body(), $matches);
            
            if (empty($matches[1])) {
                $this->info('Mesin tidak mengirimkan data log.'); 
                return 0; 
            }

            // CHECK 1: Get the latest timestamp currently in the database
            $lastDbTimestamp = Fingerprint::max('timestamp');
            $lastDbTime = $lastDbTimestamp ? Carbon::parse($lastDbTimestamp) : null;

            $newLogsCount = 0;

            foreach ($matches[1] as $rowXml) {
                $pin = $this->parseTag($rowXml, 'PIN');
                $dateTime = $this->parseTag($rowXml, 'DateTime');
                
                if (!$pin || !$dateTime) continue;

                $xmlLogTime = Carbon::parse($dateTime);

                // CHECK 2: Compare XML DateTime with Database Timestamp
                // If the XML log is older or equal to the last DB log, skip it immediately.
                if ($lastDbTime && $xmlLogTime->lte($lastDbTime)) {
                    continue; 
                }

                // If we are here, it is a NEW log. Create it.
                Fingerprint::create([
                    'user_id' => $pin,
                    'timestamp' => $xmlLogTime,
                    'verified' => (bool)$this->parseTag($rowXml, 'Verified'),
                    'status' => (int)$this->parseTag($rowXml, 'Status')
                ]);

                $newLogsCount++;
            }

            if ($newLogsCount > 0) {
                $this->info("Sinkronisasi berhasil! Ditemukan {$newLogsCount} log baru.");
                return 1; // Return 1 because we found new data
            } else {
                $this->info('Sinkronisasi selesai. Tidak ada log baru (semua log dari mesin sudah ada di database).');
                return 0; // Return 0 to tell handle() to stop
            }

        } catch (\Exception $e) {
            $this->error('Error sinkronisasi: ' . $e->getMessage());
            return false;
        }
    }

    private function processAndStoreReport(Carbon $date)
    {
        $this->info("Memproses rekap untuk tanggal: " . $date->format('d-m-Y'));
        
        $rawLogs = Fingerprint::whereDate('timestamp', $date)->get();
        if ($rawLogs->isEmpty()) {
            $this->warn("Tidak ada log mentah untuk tanggal " . $date->format('d-m-Y') . ", dilewati.");
            return;
        }

        $logsByUser = $rawLogs->groupBy('user_id');
        $karyawanList = Karyawan::whereIn('finger_id', $logsByUser->keys())->get()->keyBy('finger_id');
        $rekapToInsert = [];

        foreach ($logsByUser as $fingerId => $logs) {
            $karyawan = $karyawanList[$fingerId] ?? null;
            
            $rawMasuk = $logs->where('status', 0)->min('timestamp');
            $jamMasuk = $rawMasuk ? Carbon::parse($rawMasuk) : null;

            $rawKeluar = $logs->where('status', 1)->max('timestamp');
            $jamKeluar = $rawKeluar ? Carbon::parse($rawKeluar) : null;

            $jamMasukStandar = $date->copy()->setTimeFromTimeString($karyawan->jam_masuk ?? '08:00:00');
            $jamKeluarStandar = $date->copy()->setTime($date->isSaturday() ? 16 : 17, 0);

            if ($jamKeluar && $jamKeluar->isBefore($jamMasukStandar)) {
                $jamKeluar = null;
            }

            $terlambat = ($jamMasuk && $jamMasuk->isAfter($jamMasukStandar)) 
                ? $jamMasukStandar->diff($jamMasuk)->format('%H jam %i mnt') 
                : 'Tidak';
            
            $pulangCepat = ($jamKeluar && $jamKeluar->isBefore($jamKeluarStandar)) 
                ? $jamKeluar->diff($jamKeluarStandar)->format('%H jam %i mnt') 
                : 'Tidak';
            
            $rekapToInsert[] = [
                'kd_karyawan'  => $karyawan->kd_karyawan ?? null,
                'nama'         => $karyawan->nama ?? 'Karyawan Tidak Ditemukan ID: ' . $fingerId,
                'tanggal'      => $date->format('Y-m-d'),
                'jam_masuk'    => $jamMasuk ? $jamMasuk->format('H:i:s') : null,
                'jam_keluar'   => $jamKeluar ? $jamKeluar->format('H:i:s') : null,
                'terlambat'    => $terlambat,
                'pulang_cepat' => $pulangCepat,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        if (!empty($rekapToInsert)) {
            Presensi::insert($rekapToInsert);
        }
    }

    private function getDateRange(): CarbonPeriod
    {
        if ($this->option('dari') && $this->option('sampai')) {
            return CarbonPeriod::create($this->option('dari'), $this->option('sampai'));
        }
        if ($this->option('dari')) {
            return CarbonPeriod::create($this->option('dari'), Carbon::now());
        }
        if ($this->option('sampai')) {
            return CarbonPeriod::create(Carbon::createFromFormat('Y-m-d', '2025-04-05'), $this->option('sampai'));
        }
        if ($this->option('all')) {
            return CarbonPeriod::create(Carbon::createFromFormat('Y-m-d', '2025-04-05'), Carbon::now());
        }
        return CarbonPeriod::create(Carbon::today(), Carbon::today());
    }

    private function parseTag(string $string, string $tag): ?string
    {
        if (preg_match("/<{$tag}>(.*?)<\\/{$tag}>/s", $string, $matches)) {
            return $matches[1];
        }
        return null;
    }
}