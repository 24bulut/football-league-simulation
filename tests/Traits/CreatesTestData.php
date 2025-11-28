<?php

namespace Tests\Traits;

use App\Enums\LeagueStatus;
use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\LeagueStanding;
use App\Models\Team;
use Illuminate\Support\Collection;

trait CreatesTestData
{
    protected function createTeams(int $count = 4): Collection
    {
        return Team::factory()->count($count)->create();
    }

    protected function createLeague(array $attributes = []): League
    {
        return League::factory()->create($attributes);
    }

    protected function createLeagueInProgress(int $week = 3): League
    {
        return League::factory()->create([
            'current_week' => $week,
            'status' => LeagueStatus::IN_PROGRESS,
        ]);
    }

    protected function createCompletedLeague(): League
    {
        return League::factory()->create([
            'current_week' => 6,
            'status' => LeagueStatus::COMPLETED,
        ]);
    }

    protected function createMatch(League $league, Team $homeTeam, Team $awayTeam, int $week = 1): GameMatch
    {
        return GameMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => $week,
            'status' => MatchStatus::PENDING,
        ]);
    }

    protected function createPlayedMatch(
        League $league,
        Team $homeTeam,
        Team $awayTeam,
        int $homeScore,
        int $awayScore,
        int $week = 1
    ): GameMatch {
        return GameMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'week' => $week,
            'status' => MatchStatus::PLAYED,
        ]);
    }

    protected function createStanding(League $league, Team $team, array $attributes = []): LeagueStanding
    {
        return LeagueStanding::factory()->create(array_merge([
            'league_id' => $league->id,
            'team_id' => $team->id,
        ], $attributes));
    }

    protected function createPendingMatchesForWeek(League $league, Collection $teams, int $week): Collection
    {
        $matches = collect();

        // Create 2 matches per week for 4 teams
        $matches->push($this->createMatch($league, $teams[0], $teams[1], $week));
        $matches->push($this->createMatch($league, $teams[2], $teams[3], $week));

        return $matches;
    }
}

