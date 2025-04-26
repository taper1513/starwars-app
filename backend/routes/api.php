<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\StatsController;

Route::get('/search', [SearchController::class, 'search']);
Route::get('/people/{id}', [PersonController::class, 'show']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/stats', [StatsController::class, 'index']); 