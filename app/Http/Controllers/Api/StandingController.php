<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetStandingsRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\StandingResource;
use App\Services\LeagueService;
use App\Services\StandingsCalculatorService;
use Illuminate\Http\JsonResponse;

class StandingController extends Controller
{
    public function __construct(
        private LeagueService $leagueService,
        private StandingsCalculatorService $standingsCalculator
    ) {}

    public function index(GetStandingsRequest $request): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();
        $standings = $this->standingsCalculator->getStandings($league);

        return ApiResponse::success(StandingResource::collection($standings));
    }
}