<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSchedule extends Command
{
    protected $signature = 'test:schedule';
    protected $description = 'A simple command to test if the scheduler is running.';

    public function handle()
    {
        Log::info('Scheduler is working!');
        return 0;
    }
}