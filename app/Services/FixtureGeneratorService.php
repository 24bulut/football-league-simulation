<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Models\League;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Collection;

class FixtureGeneratorService
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private GameMatchRepositoryInterface $gameMatchRepository
    ) {}

    /**
     * Generate round-robin fixtures for a league.
     * Each team plays every other team twice (home & away).
     */
    public function generate(League $league): Collection
    {
        $teamCount = $this->teamRepository->count();

        if ($teamCount < 2) {
            return collect();
        }

        $fixtures = collect();
        $teamIds = $this->teamRepository->getIds();

        $firstHalfFixtures = $this->generateRoundRobin($teamIds);

        // Create matches for first half
        foreach ($firstHalfFixtures as $week => $weekMatches) {
            foreach ($weekMatches as $match) {
                $fixtures->push([
                    'league_id' => $league->id,
                    'week' => $week + 1,
                    'home_team_id' => $match['home'],
                    'away_team_id' => $match['away'],
                    'status' => MatchStatus::PENDING->value,
                ]);
            }
        }

        // Generate second half (reverse fixtures)
        $weeksInFirstHalf = count($firstHalfFixtures);
        foreach ($firstHalfFixtures as $week => $weekMatches) {
            foreach ($weekMatches as $match) {
                $fixtures->push([
                    'league_id' => $league->id,
                    'week' => $weeksInFirstHalf + $week + 1,
                    'home_team_id' => $match['away'], // Swap home/away
                    'away_team_id' => $match['home'],
                    'status' => MatchStatus::PENDING->value,
                ]);
            }
        }

        $this->gameMatchRepository->bulkInsert($fixtures->toArray());

        return $this->gameMatchRepository->getByLeague($league);
    }

    /**
     * Generate round-robin schedule using the circle method.
     * Returns array of weeks, each containing matches for that week.
     */
    private function generateRoundRobin(array $teamIds): array
    {
        $teamCount = count($teamIds);
        $weeks = [];

        // If odd number of teams, add a "bye" team
        if ($teamCount % 2 !== 0) {
            $teamIds[] = null;
            $teamCount++;
        }

        $totalRounds = $teamCount - 1;
        $matchesPerRound = $teamCount / 2;

        // Circle method: fix first team, rotate others
        // Döndürme metodu: sabit takım, diğer takımları döndür
        $fixed = $teamIds[0];
        $rotating = array_slice($teamIds, 1); // sabit takım dışındaki takımlar

        for ($round = 0; $round < $totalRounds; $round++) {
            $weekMatches = [];

            // First match: fixed team vs first rotating team
            // Sabit takım vs ilk döndürülen takım
            $opponent = $rotating[0];
            if ($fixed !== null && $opponent !== null) {
                // Alternate home/away for fixed team
                if ($round % 2 === 0) {
                    $weekMatches[] = ['home' => $fixed, 'away' => $opponent];
                } else {
                    $weekMatches[] = ['home' => $opponent, 'away' => $fixed];
                }
            }

            // Remaining matches: pair from ends of rotating array
            for ($i = 1; $i < $matchesPerRound; $i++) {
                $home = $rotating[$i];
                $away = $rotating[$teamCount - 1 - $i];

                if ($home !== null && $away !== null) {
                    $weekMatches[] = ['home' => $home, 'away' => $away];
                }
            }

            $weeks[] = $weekMatches;

            // Rotate: move last element to second position - Son elemanı ikinci pozisyona taşı
            $last = array_pop($rotating);
            array_splice($rotating, 0, 0, [$last]);
        }

        return $weeks;
    }
}
