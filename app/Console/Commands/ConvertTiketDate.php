<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tiket;
use Carbon\Carbon;

class ConvertTiketDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tiket:convert-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert tiket.tanggal from DD/MM/YYYY to YYYY-MM-DD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting date conversion for table: tiket');

        // Fetch all tickets where tanggal is not null
        $tikets = Tiket::whereNotNull('tanggal')->get();
        $count = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($tikets));
        $bar->start();

        foreach ($tikets as $tiket) {
            $originalDate = $tiket->tanggal;

            // Skip if already in standard format (contains '-') or empty
            if (empty($originalDate) || strpos($originalDate, '-') !== false) {
                $bar->advance();
                continue;
            }

            try {
                // Attempt to parse from Indonesian format DD/MM/YYYY
                $date = Carbon::createFromFormat('d/m/Y', trim($originalDate));
                
                if ($date) {
                    $tiket->tanggal = $date->format('Y-m-d');
                    $tiket->save();
                    $count++;
                }
            } catch (\Exception $e) {
                // Log error but continue
                $errors++;
                $this->newLine();
                $this->error("Failed to parse ID {$tiket->kd_tiket}: '{$originalDate}'");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Success! Converted {$count} records to YYYY-MM-DD.");
        
        if ($errors > 0) {
            $this->warn("Skipped {$errors} records due to invalid format.");
        }
    }
}