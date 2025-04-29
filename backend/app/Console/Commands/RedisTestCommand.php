<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisTestCommand extends Command
{
    protected $signature = 'redis:test';
    protected $description = 'Test Redis connection';

    public function handle()
    {
        $this->info('Testing Redis connection...');
        
        try {
            Redis::ping();
            $this->info('Successfully connected to Redis!');
            
            // Test queue connection
            $this->info('Testing queue connection...');
            Redis::connection('queue')->ping();
            $this->info('Successfully connected to Redis queue!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Redis connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 