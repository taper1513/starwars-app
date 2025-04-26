<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json(
            Cache::get('search_stats', [
                'top_searches' => [],
                'avg_response_time' => 0,
                'busiest_hour' => null
            ])
        );
    }
} 