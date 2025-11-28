<?php

namespace App\Services;

use App\Models\League;
use App\Repositories\Contracts\LeagueStandingRepositoryInterface;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use Illuminate\Support\Collection;

class PredictionService
{
    private const PREDICTION_START_WEEK = 4;

    public function __construct(
        private LeagueStandingRepositoryInterface $leagueStandingRepository,
        private GameMatchRepositoryInterface $gameMatchRepository
    ) {}

    public function shouldShowPredictions(League $league): bool
    {
        return $league->current_week >= self::PREDICTION_START_WEEK;
    }

    /**
     * Calculate championship predictions.
     * Formula: Score = (Points × 3) + MaxPossiblePoints + GoalDifference
     */
    public function calculatePredictions(League $league): Collection
    {
        $standings = $this->leagueStandingRepository->getByLeague($league);
        $remainingCount = $this->countRemainingMatchesPerTeam($league);

        // Calculate scores
        $predictions = $standings->map(fn($s) => [
            'team_id' => $s->team_id,
            'team_name' => $s->team->name,
            'current_points' => $s->points,
            'score' => $this->calculateScore($s, $remainingCount[$s->team_id] ?? 0),
        ]);

        return $this->convertToPercentages($predictions);
    }

    /*
     * Calculate score for a team based on current points, remaining matches and goal difference.
     * Score = (Points × 3)     +  MaxPossible  +  GoalDiff
     *       ↓                    ↓                 ↓
     *      "What I have"      "What I could"  "Tiebreaker"
     *      (most important)    (less certain)  (bonus)
     */
    private function calculateScore($standing, int $remainingMatches): float
    {
        $maxPoints = $standing->points + ($remainingMatches * 3);
        
        return (($standing->points * 3) + $maxPoints + $standing->goal_difference);
    }

    /*
     * Count remaining matches per team
     * her takımın kalan maç sayısını hesaplar.
     */
    private function countRemainingMatchesPerTeam(League $league): array
    {
        $counts = [];
        
        foreach ($this->gameMatchRepository->getPending($league) as $match) {
            $counts[$match->home_team_id] = ($counts[$match->home_team_id] ?? 0) + 1;
            $counts[$match->away_team_id] = ($counts[$match->away_team_id] ?? 0) + 1;
        }

        return $counts;
    }

    private function convertToPercentages(Collection $predictions): Collection
    {
        $totalScore = $predictions->sum('score');

        if ($totalScore <= 0) {
            $equal = (int) round(100 / max($predictions->count(), 1));
            return $predictions->map(fn($p) => [
                'team_id' => $p['team_id'],
                'team_name' => $p['team_name'],
                'current_points' => $p['current_points'],
                'prediction_percentage' => $equal,
            ])->values();
        }

        return $predictions->map(fn($p) => [
            'team_id' => $p['team_id'],
            'team_name' => $p['team_name'],
            'current_points' => $p['current_points'],
            'prediction_percentage' => (int) round(($p['score'] / $totalScore) * 100),
        ])->sortByDesc('prediction_percentage')->values();
    }
}
