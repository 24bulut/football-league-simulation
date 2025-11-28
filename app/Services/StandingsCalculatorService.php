<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\League;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use App\Repositories\Contracts\LeagueStandingRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Collection;
use App\DTOs\LeagueStandingDTO;
class StandingsCalculatorService
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private LeagueStandingRepositoryInterface $leagueStandingRepository,
        private GameMatchRepositoryInterface $gameMatchRepository
    ) {}

    /**
     * Initialize standings for all teams in a league.
     * Tüm takımların durumlarını başlatır.
     */
    public function initializeStandings(League $league): void
    {
        $teams = $this->teamRepository->all();

        if ($teams->isEmpty()) {
            throw new \Exception('No teams found');
        }

        foreach ($teams as $team) {
            $this->leagueStandingRepository->create(new LeagueStandingDTO(
                id: 0,
                leagueId: $league->id,
                teamId: $team->id,
                played: 0,
                won: 0,
                drawn: 0,
                lost: 0,
                goalsFor: 0,
                goalsAgainst: 0,
                points: 0,
                position: 0
            ));
        }

        $this->updatePositions($league);
    }

    /**
     * Recalculate all standings from scratch based on played matches.
     */
    public function recalculate(League $league): void
    {
        // Reset all standings
        $this->leagueStandingRepository->resetAll($league);

        // Get all played matches
        $playedMatches = $this->gameMatchRepository->getByLeague($league)
            ->filter(fn(GameMatch $match) => $match->status === MatchStatus::PLAYED);

        // Process each match
        foreach ($playedMatches as $match) {
            $this->processMatch($match);
        }

        $this->updatePositions($league);
    }

    /**
     * Update standings after a single match.
     */
    public function updateAfterMatch(GameMatch $match): void
    {
        if (!$match->isPlayed()) {
            return;
        }

        $this->processMatch($match);
        $this->updatePositions($match->league);
    }

    /**
     * Process a single match and update team standings.
     */
    private function processMatch(GameMatch $match): void
    {
        $homeStanding = $this->leagueStandingRepository->findByLeagueAndTeam(
            $match->league_id,
            $match->home_team_id
        );

        $awayStanding = $this->leagueStandingRepository->findByLeagueAndTeam(
            $match->league_id,
            $match->away_team_id
        );

        if (!$homeStanding || !$awayStanding) {
            return;
        }

        $homeStanding->played++;
        $awayStanding->played++;

        $homeStanding->goals_for += $match->home_score;
        $homeStanding->goals_against += $match->away_score;
        $awayStanding->goals_for += $match->away_score;
        $awayStanding->goals_against += $match->home_score;

        if ($match->home_score > $match->away_score) {
            $homeStanding->won++;
            $homeStanding->points += 3;
            $awayStanding->lost++;
        } elseif ($match->away_score > $match->home_score) {
            $awayStanding->won++;
            $awayStanding->points += 3;
            $homeStanding->lost++;
        } else {
            $homeStanding->drawn++;
            $awayStanding->drawn++;
            $homeStanding->points += 1;
            $awayStanding->points += 1;
        }

        $homeStanding->goal_difference = $homeStanding->goals_for - $homeStanding->goals_against;
        $awayStanding->goal_difference = $awayStanding->goals_for - $awayStanding->goals_against;

        $homeStanding->save();
        $awayStanding->save();
    }

    /**
     * Update positions based on Premier League sorting rules.
     * Priority: Points > Goal Difference > Goals For > Head-to-head > Alphabetical
     * 
     * Takımların durumlarını günceller.
     * Puan > Gol Farkı > Gol Atışları > Başlık Başlık > Alfabetik
     */
    public function updatePositions(League $league): void
    {
        $standings = $this->leagueStandingRepository->getByLeague($league)
            ->sortBy([
                ['points', 'desc'],
                ['goal_difference', 'desc'],
                ['goals_for', 'desc'],
                fn($a, $b) => strcmp($a->team->name, $b->team->name),
            ])
            ->values();

        foreach ($standings as $index => $standing) {
            $standing->position = $index + 1;
            $standing->save();
        }
    }

    /**
     * Get sorted standings for a league.
     */
    public function getStandings(League $league): Collection
    {
        return $this->leagueStandingRepository->getByLeagueOrdered($league);
    }
}
