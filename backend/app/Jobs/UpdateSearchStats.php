<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateSearchStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {

        Log::info('Refreshing search stats:', [
            'query' => $this->query,
            'type' => $this->type,
            'execution_time' => $this->executionTime,
        ]);


        $searchCounts = Cache::get('search_counts', []);
        $requestTimes = Cache::get('request_times', []);
        $hourCounts = Cache::get('hour_counts', array_fill(0, 24, 0));

        // Calculate top searches
        arsort($searchCounts);
        $total = array_sum($searchCounts);
        $topSearches = array_slice($searchCounts, 0, 5, true);
        $topSearchesWithPercentage = array_map(function ($count) use ($total) {
            return [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0
            ];
        }, $topSearches);

        // Calculate average response time
        $avgTime = count($requestTimes) > 0 ? array_sum($requestTimes) / count($requestTimes) : 0;

        // Find busiest hour
        $busiestHour = array_search(max($hourCounts), $hourCounts);

        // Store computed stats
        Cache::put('search_stats', [
            'top_searches' => $topSearchesWithPercentage,
            'avg_response_time' => round($avgTime, 2),
            'busiest_hour' => $busiestHour
        ], now()->addHours(1));
    }
} 