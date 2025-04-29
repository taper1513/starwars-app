<?php

namespace App\Http\Controllers;

use App\Services\SwapiService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Movies",
 *     description="Star Wars movie operations"
 * )
 */
class MovieController extends Controller
{
    public function __construct(private SwapiService $swapiService)
    {}

    /**
     * @OA\Get(
     *     path="/api/movies/{id}",
     *     summary="Get a Star Wars movie by ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movie ID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="episode_id", type="integer"),
     *             @OA\Property(property="opening_crawl", type="string"),
     *             @OA\Property(property="director", type="string"),
     *             @OA\Property(property="producer", type="string"),
     *             @OA\Property(property="release_date", type="string"),
     *             @OA\Property(property="characters", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="planets", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="starships", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="vehicles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="species", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="created", type="string"),
     *             @OA\Property(property="edited", type="string"),
     *             @OA\Property(property="url", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        return response()->json(
            $this->swapiService->getMovie($id)
        );
    }
} 