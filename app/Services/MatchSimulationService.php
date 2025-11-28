<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\Team;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use App\DTOs\GameMatchDTO;

class MatchSimulationService
{

    /* Base goals per match
     * ortalama gol beklentisi bu değeri yukarı veya aşağı çekmeye çalışıyoruz aslında.
    */
    private const BASE_GOALS = 1.5;

    /*
     * Normalize the power to a value between 0 and 1
     * For example, if the power is 100, the normalized power is 1.15
    */
    private const POWER_FOR_NORMALIZATION = 85;
    private const MAX_GOALS = 7;

    public function __construct(
        private GameMatchRepositoryInterface $gameMatchRepository
    ) {}

    /**
     * Simulate a single match and return the result.
     */
    public function simulate(GameMatch $match): GameMatch
    {
        if ($match->isPlayed()) {
            return $match;
        }

        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;
        // Generate scores
        $scores = $this->generateScores($homeTeam, $awayTeam);

        return $this->gameMatchRepository->update($match, new GameMatchDTO(
            id: $match->id,
            leagueId: $match->league_id,
            week: $match->week,
            homeScore: $scores['home'],
            awayScore: $scores['away'],
            status: MatchStatus::PLAYED,
        ));
    }

    /**
     * Generate realistic scores based on team powers and goalkeeper factors.
     */
    private function generateScores(Team $homeTeam, Team $awayTeam): array
    {

        $homePower = $homeTeam->power * $homeTeam->home_advantage; // Ev avantajı ile güç çarpılıyor.
        $awayPower = $awayTeam->power;

        $homeExpected = self::BASE_GOALS * $homePower / self::POWER_FOR_NORMALIZATION * $awayTeam->goalkeeper_factor;
        $awayExpected = self::BASE_GOALS * $awayPower / self::POWER_FOR_NORMALIZATION * $homeTeam->goalkeeper_factor;

        $homeGoals = $this->generateGoals($homeExpected);
        $awayGoals = $this->generateGoals($awayExpected);

        return ['home' => $homeGoals, 'away' => $awayGoals];
    }

    /**
     * Generate goals using Poisson distribution.
     * 
     * Beklenen golleri Poisson dağılımı kullanarak hesaplar.
     */
    private function generateGoals(float $expected): int
    {
        $L = exp(-$expected);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= mt_rand(0, 10000) / 10000;
        } while ($p > $L && $k < self::MAX_GOALS);

        return min($k - 1, self::MAX_GOALS);
    }
}
