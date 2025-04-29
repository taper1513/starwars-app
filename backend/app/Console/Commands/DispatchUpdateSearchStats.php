<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateSearchStats;

class DispatchUpdateSearchStats extends Command
{
    protected $signature = 'dispatch:update-search-stats';
    protected $description = 'Dispatch the UpdateSearchStats job to the queue';

    public function handle()
    {
        UpdateSearchStats::dispatch();
        $this->info('UpdateSearchStats job dispatched!');
    }
}
