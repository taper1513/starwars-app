<?php

namespace App\Http\Controllers;

use App\Events\SearchPerformed;
use App\Services\SwapiService;
use Illuminate\Http\Request;

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
    
        SearchPerformed::dispatch(
            $query,
            $request->type,
            microtime(true) - $start
        );
    
        return response()->json($results);
    }
} 