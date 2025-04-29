<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{       
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('dispatch:update-search-stats')
        //     ->everyFiveMinutes()
        //     ->before(function () {
        //         Log::info('About to run dispatch:update-search-stats');
        //     })
        //     ->after(function () {
        //         Log::info('Finished running dispatch:update-search-stats');
        //     });; 
    }

    protected function commands()
    {
        $this->load(__DIR__ . "/Commands");

        require base_path("routes/console.php");
    }
} 