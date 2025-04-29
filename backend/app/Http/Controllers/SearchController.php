<?php

namespace App\Http\Controllers;

use App\Events\SearchPerformed;
use App\Services\SwapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function __construct(private SwapiService $swapiService)
    {}

    public function search(Request $request)
    {
        $request->validate([
            'type' => 'required|in:people,movies',
            'query' => 'required|string|min:1'
        ]);
    
        $query = $request->query('query');
        $start = microtime(true);
    
        $results = match($request->type) {
            'people' => $this->swapiService->searchPeople($query),
            'movies' => $this->swapiService->searchMovies($query),
        };
    
        $executionTime = microtime(true) - $start;


        Log::info('SearchPerformed triggering event', [
            'query' => $query,
            'type' => $request->type,
            'execution_time' => $executionTime
        ]);

        SearchPerformed::dispatch(
            $query,
            $request->type,
            $executionTime
        );

        Log::info('Event dispatched', [
            'query' => $query,
            'type' => $request->type,
            'execution_time' => $executionTime
        ]);

        return response()->json($results);
    }
} 