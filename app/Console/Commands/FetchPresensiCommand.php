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
        if (!$this->syncronizeRawLogs()) {
            return 1;
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
                $this->error('Gagal terhubung ke mesin.'); return false;
            }

            preg_match_all('/<Row>(.*?)<\\/Row>/s', $response->body(), $matches);
            if (empty($matches[1])) {
                $this->info('Tidak ada log baru.'); return true;
            }

            foreach ($matches[1] as $rowXml) {
                $pin = $this->parseTag($rowXml, 'PIN');
                $dateTime = $this->parseTag($rowXml, 'DateTime');
                if (!$pin || !$dateTime) continue;

                Fingerprint::updateOrCreate(
                    ['user_id' => $pin, 'timestamp' => Carbon::parse($dateTime)],
                    ['verified' => (bool)$this->parseTag($rowXml, 'Verified'), 'status' => (int)$this->parseTag($rowXml, 'Status')]
                );
            }
            $this->info('Sinkronisasi log mentah berhasil!');
            return true;
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
            
            $jamMasuk = Carbon::parse($logs->where('status', 0)->min('timestamp'));
            $jamKeluar = $logs->where('status', 1)->max('timestamp') ? Carbon::parse($logs->where('status', 1)->max('timestamp')) : null;

            if ($jamKeluar && $jamKeluar->isBefore($jamMasuk)) {
                $jamKeluar = null;
            }

            $jamMasukStandar = $date->copy()->setTimeFromTimeString($karyawan->jam_masuk ?? '08:00:00');
            $jamKeluarStandar = $date->copy()->setTime($date->isSaturday() ? 16 : 17, 0);

            $terlambat = $jamMasuk->isAfter($jamMasukStandar) ? $jamMasukStandar->diff($jamMasuk)->format('%H jam %i mnt') : 'Tidak';
            $pulangCepat = $jamKeluar && $jamKeluar->isBefore($jamKeluarStandar) ? $jamKeluar->diff($jamKeluarStandar)->format('%H jam %i mnt') : 'Tidak';
            
            $rekapToInsert[] = [
                'kd_karyawan'  => $karyawan->kd_karyawan ?? null,
                'nama'         => $karyawan->nama ?? 'Karyawan Tidak Ditemukan ID: ' . $fingerId,
                'tanggal'      => $date->format('Y-m-d'),
                'jam_masuk'    => $jamMasuk->format('H:i:s'),
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

