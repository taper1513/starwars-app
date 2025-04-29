<?php

namespace App\Http\Controllers;

use App\Events\SearchPerformed;
use App\Services\SwapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Search",
 *     description="Search operations for Star Wars data"
 * )
 */
class SearchController extends Controller
{
    public function __construct(private SwapiService $swapiService)
    {}

    /**
     * @OA\Get(
     *     path="/api/search",
     *     summary="Search for Star Wars data",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Type of data to search (people or movies)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"people", "movies"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="results",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="url", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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