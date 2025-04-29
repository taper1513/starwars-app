<?php

namespace App\Listeners;

use App\Events\SearchPerformed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateRawSearchStats implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * Handle the event.
     */
    public function handle(SearchPerformed $event)
    {

        Log::info('Event triggered! ', [
            'query' => $event->query,
            'type' => $event->type,
            'execution_time' => $event->executionTime,
        ]);

        // Create a unique lock key for this search event
        $lockKey = "search_stats_lock:{$event->query}:{$event->type}:" . floor($event->executionTime * 1000);
        
        // Prevent duplicate processing with a cache lock
        if (!Cache::add($lockKey, true, 60)) {
            Log::info('Skipping duplicate search stats update');

            
            return;
        }

        Log::info('Event Not Duplicate');

        try {
            // Update search count statistics
            $searchCounts = Cache::get('search_counts', []);
            $searchCounts[$event->query] = ($searchCounts[$event->query] ?? 0) + 1;
            Cache::put('search_counts', $searchCounts);

            // Record request execution time
            $requestTimes = Cache::get('request_times', []);
            $requestTimes[] = $event->executionTime;
            Cache::put('request_times', $requestTimes);

            // Update hourly search statistics
            $hourCounts = Cache::get('hour_counts', array_fill(0, 24, 0));
            $currentHour = now()->hour;
            $hourCounts[$currentHour]++;
            Cache::put('hour_counts', $hourCounts);
        } catch (\Exception $e) {
            Log::error('Error updating search stats: ' . $e->getMessage());
            Cache::forget($lockKey);
            throw $e;
        }
    }
} 