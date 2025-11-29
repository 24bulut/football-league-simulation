<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetLeagueStatusRequest;
use App\Http\Requests\Api\ResetLeagueRequest;
use App\Http\Requests\Api\StartLeagueRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\LeagueResource;
use App\Http\Resources\MatchResource;
use App\Http\Resources\StandingResource;
use App\Services\LeagueService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\GetStandingsRequest;

class LeagueController extends Controller
{
    public function __construct(
        private LeagueService $leagueService
    ) {}

    /**
     * Start a new league.
     * POST /api/league/start
     */
    public function start(StartLeagueRequest $request): JsonResponse
    {
        $league = $this->leagueService->startLeague();

        return ApiResponse::created(
            new LeagueResource($league),
            'League started successfully'
        );
    }

    /**
     * Get current league status.
     * GET /api/league/status
     */
    public function status(GetLeagueStatusRequest $request): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();

        return ApiResponse::success([
            'league' => new LeagueResource($league),
        ]);
    }

    /**
     * Reset the current league.
     * POST /api/league/reset
     */
    public function reset(ResetLeagueRequest $request): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();

        $league = $this->leagueService->resetLeague($league);

        return ApiResponse::success(
            new LeagueResource($league),
            'League reset successfully'
        );
    }


}
