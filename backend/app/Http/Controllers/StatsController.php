<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Stats",
 *     description="Search statistics operations"
 * )
 */
class StatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/stats",
     *     summary="Get search statistics",
     *     tags={"Stats"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="top_searches",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="query", type="string"),
     *                     @OA\Property(property="count", type="integer")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="avg_response_time",
     *                 type="number",
     *                 format="float"
     *             ),
     *             @OA\Property(
     *                 property="busiest_hour",
     *                 type="integer",
     *                 description="Hour of the day (0-23) with most searches"
     *             )
     *         )
     *     )
     * )
     */
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