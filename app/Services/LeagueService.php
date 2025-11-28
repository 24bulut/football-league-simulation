<?php

namespace App\Services;

use App\Enums\LeagueStatus;
use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\League;
use App\Repositories\Contracts\LeagueRepositoryInterface;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use App\Repositories\Contracts\LeagueStandingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\DTOs\LeagueDTO;
use App\DTOs\GameMatchDTO;
class LeagueService
{
    public function __construct(
        private LeagueRepositoryInterface $leagueRepository,
        private GameMatchRepositoryInterface $gameMatchRepository,
        private LeagueStandingRepositoryInterface $leagueStandingRepository,
        private FixtureGeneratorService $fixtureGenerator,
        private MatchSimulationService $matchSimulation,
        private StandingsCalculatorService $standingsCalculator,
        private PredictionService $predictionService
    ) {}

    /**
     * Start a new league - creates fixtures and initializes standings.
     * Yeni bir lig oluşturur ve ilk haftanın maçlarını oluşturur.
     */
    public function startLeague(): League
    {
        return DB::transaction(function () {
            $league = $this->leagueRepository->create(new LeagueDTO(
                id: 0,
                currentWeek: 0,
                status: LeagueStatus::NOT_STARTED,
            ));

            $this->standingsCalculator->initializeStandings($league);

            $this->fixtureGenerator->generate($league);

            return $this->leagueRepository->find($league->id);
        });
    }

    /**
     * Get the current active league or null.
     */
    public function getCurrentLeague(): ?League
    {
        return $this->leagueRepository->getLatest();
    }

    /**
     * Simulate the next week's matches.
     * 
     * Bir sonraki haftanın maçlarını simüle eder.
     */
    public function playNextWeek(League $league): League
    {
        if ($league->isCompleted()) {
            return $league;
        }

        return DB::transaction(function () use ($league) {
            $nextWeek = $league->current_week + 1;

            $matches = $this->gameMatchRepository->getPendingByWeek($league, $nextWeek);

            foreach ($matches as $match) {
                $this->matchSimulation->simulate($match);
            }

            // Recalculate standings
            $this->standingsCalculator->recalculate($league);

            // Update league status
            $status = $nextWeek >= $league->getTotalWeeks()
                ? LeagueStatus::COMPLETED
                : LeagueStatus::IN_PROGRESS;

            return $this->leagueRepository->updateStatus($league, $status, $nextWeek);
        });
    }

    /**
     * Play all remaining weeks at once.
     */
    public function playAllWeeks(League $league): League
    {
        while (!$league->isCompleted()) {
            $league = $this->playNextWeek($league);
        }

        return $league;
    }

    /**
     * Reset the league to initial state.
     */
    public function resetLeague(League $league): League
    {
        return DB::transaction(function () use ($league) {
            $this->gameMatchRepository->deleteByLeague($league);
            $this->leagueStandingRepository->deleteByLeague($league);

            $this->standingsCalculator->initializeStandings($league);
            $this->fixtureGenerator->generate($league);

            return $this->leagueRepository->updateStatus($league, LeagueStatus::NOT_STARTED, 0);
        });
    }

    /**
     * Get current league status with all data.
     */
    public function getLeagueStatus(League $league): array
    {
        $standings = $this->standingsCalculator->getStandings($league);
        
        $currentWeekMatches = $this->gameMatchRepository->getByWeek($league, $league->current_week);

        $predictions = null;
        if ($this->predictionService->shouldShowPredictions($league)) {
            $predictions = $this->predictionService->calculatePredictions($league);
        }

        return [
            'league' => $league,
            'current_week' => $league->current_week,
            'total_weeks' => $league->getTotalWeeks(),
            'status' => $league->status,
            'standings' => $standings,
            'current_week_matches' => $currentWeekMatches,
            'predictions' => $predictions,
            'is_completed' => $league->isCompleted(),
        ];
    }

    /**
     * Get matches for a specific week.
     */
    public function getWeekMatches(League $league, int $week): Collection
    {
        return $this->gameMatchRepository->getByWeek($league, $week);
    }

}
