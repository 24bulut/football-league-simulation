<?php

use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\SimulationController;
use App\Http\Controllers\Api\StandingController;
use App\Http\Controllers\Api\PredictionController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;


// League Management
Route::prefix('league')->group(function () {
    Route::post('/start', [LeagueController::class, 'start']);
    Route::get('/status', [LeagueController::class, 'status']);
    Route::post('/reset', [LeagueController::class, 'reset']);
});

// Simulation
Route::prefix('simulation')->group(function () {
    Route::post('/next-week', [SimulationController::class, 'nextWeek']);
    Route::post('/play-all', [SimulationController::class, 'playAll']);
});

// Matches
Route::prefix('matches')->group(function () {
    Route::get('/', [MatchController::class, 'index']);
    Route::get('/week/{week}', [MatchController::class, 'byWeek']);
    Route::put('/{match}', [MatchController::class, 'update']);
});

// Standings
Route::prefix('standings')->group(function () {
    Route::get('/', [StandingController::class, 'index']);
});

// Predictions
Route::prefix('predictions')->group(function () {
    Route::get('/', [PredictionController::class, 'index']);
});

// Teams
Route::prefix('teams')->group(function () {
    Route::get('/', [TeamController::class, 'index']);
});