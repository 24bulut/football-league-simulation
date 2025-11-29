<?php

namespace App\Http\Controllers\Api;

use App\Enums\MatchStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetMatchesRequest;
use App\Http\Requests\Api\GetWeekRequest;
use App\Http\Requests\Api\UpdateMatchRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\MatchResource;
use App\Http\Resources\StandingResource;
use App\Models\GameMatch;
use App\Services\LeagueService;
use App\Services\StandingsCalculatorService;
use Illuminate\Http\JsonResponse;

class MatchController extends Controller
{
    public function __construct(
        private LeagueService $leagueService,
        private StandingsCalculatorService $standingsCalculator
    ) {}

    /**
     * Get all matches.
     * GET /api/matches
     */
    public function index(GetMatchesRequest $request): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();

        $matches = $league
            ->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('week')
            ->get();

        $matchesByWeek = $matches->groupBy('week')->map(fn($weekMatches, $week) => [
            'week' => $week,
            'matches' => MatchResource::collection($weekMatches),
        ])->values();

        return ApiResponse::success($matchesByWeek);
    }

    /**
     * Get matches for a specific week.
     * GET /api/matches/week/{week}
     */
    public function byWeek(GetWeekRequest $request): JsonResponse
    {
        $week = $request->getWeek();
        $league = $this->leagueService->getCurrentLeague();
        $matches = $this->leagueService->getWeekMatches($league, $week);

        return ApiResponse::success([
            'week' => $week,
            'matches' => MatchResource::collection($matches),
        ]);
    }

    /**
     * Update match scores manually.
     * PUT /api/matches/{match}
     */
    public function update(UpdateMatchRequest $request, GameMatch $match): JsonResponse
    {
        $match->update([
            'home_score' => $request->getHomeScore(),
            'away_score' => $request->getAwayScore(),
            'status' => MatchStatus::PLAYED,
        ]);

        $this->standingsCalculator->recalculate($match->league);
        return ApiResponse::success([
            'match' => new MatchResource($match->loadMissing(['homeTeam', 'awayTeam']))
        ], 'Match updated successfully');
    }
}
