<?php

namespace App\Http\Controllers;

use App\Services\SwapiService;

class PersonController extends Controller
{
    public function __construct(private SwapiService $swapiService)
    {}

    public function show(string $id)
    {
        return response()->json(
            $this->swapiService->getPerson($id)
        );
    }
} 