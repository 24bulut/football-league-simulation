<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetWeekRequest;
use App\Http\Requests\Api\SimulateAllWeeksRequest;
use App\Http\Requests\Api\SimulateNextWeekRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\LeagueResource;
use App\Http\Resources\MatchResource;
use App\Http\Resources\StandingResource;
use App\Services\LeagueService;
use Illuminate\Http\JsonResponse;

class SimulationController extends Controller
{
    public function __construct(
        private LeagueService $leagueService
    ) {}

    /**
     * Simulate next week's matches.
     * POST /api/simulation/next-week
     */
    public function nextWeek(SimulateNextWeekRequest $request): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();

        if ($league->isCompleted()) {
            return ApiResponse::badRequest('League is already completed.');
        }

        $result = $this->leagueService->playNextWeek($league);

        return ApiResponse::success([
            'matches' => MatchResource::collection($result['matches']),
            'status' => $result['status']->value,
        ], "Week {$league->current_week} simulated successfully");
    }

    /**
     * Simulate all remaining weeks.
     * POST /api/simulation/play-all
     */
    public function playAll(SimulateAllWeeksRequest $request): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();

        if ($league->isCompleted()) {
            return ApiResponse::badRequest('League is already completed.');
        }

        $league = $this->leagueService->playAllWeeks($league);

        return ApiResponse::success([
            'league' => new LeagueResource($league),
        ], 'All weeks simulated successfully. League completed!');
    }

    /**
     * Get results for a specific week.
     * GET /api/simulation/week/{week}
     */
    public function week(GetWeekRequest $request): JsonResponse
    {
        if (!$request->hasLeague()) {
            return ApiResponse::notFound('No league found.');
        }

        $week = $request->getWeek();
        $matches = $this->leagueService->getWeekMatches($request->getLeague(), $week);

        if ($matches->isEmpty()) {
            return ApiResponse::notFound("No matches found for week {$week}.");
        }

        return ApiResponse::success([
            'week' => $week,
            'matches' => MatchResource::collection($matches),
        ]);
    }
}
