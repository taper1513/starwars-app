<?php

namespace App\Http\Controllers;

use App\Services\SwapiService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="People",
 *     description="Star Wars character operations"
 * )
 */
class PersonController extends Controller
{
    public function __construct(private SwapiService $swapiService)
    {}

    /**
     * @OA\Get(
     *     path="/api/people/{id}",
     *     summary="Get a Star Wars character by ID",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Character ID",
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
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="height", type="string"),
     *             @OA\Property(property="mass", type="string"),
     *             @OA\Property(property="hair_color", type="string"),
     *             @OA\Property(property="skin_color", type="string"),
     *             @OA\Property(property="eye_color", type="string"),
     *             @OA\Property(property="birth_year", type="string"),
     *             @OA\Property(property="gender", type="string"),
     *             @OA\Property(property="homeworld", type="string"),
     *             @OA\Property(property="films", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="species", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="vehicles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="starships", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="created", type="string"),
     *             @OA\Property(property="edited", type="string"),
     *             @OA\Property(property="url", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Character not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        return response()->json(
            $this->swapiService->getPerson($id)
        );
    }
} 