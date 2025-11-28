<?php

namespace App\Repositories;

use App\Models\League;
use App\Models\LeagueStanding;
use App\Repositories\Contracts\LeagueStandingRepositoryInterface;
use Illuminate\Support\Collection;
use App\DTOs\LeagueStandingDTO;

class LeagueStandingRepository implements LeagueStandingRepositoryInterface
{
    public function __construct(
        private LeagueStanding $model
    ) {}

    public function create(LeagueStandingDTO $data): LeagueStanding
    {
        return $this->model->create($data->toArray());
    }

    public function getByLeague(League $league): Collection
    {
        return $league->standings()
            ->with('team')
            ->get();
    }

    public function getByLeagueOrdered(League $league): Collection
    {
        return $league->standings()
            ->with('team')
            ->orderBy('position')
            ->get();
    }

    public function findByLeagueAndTeam(int $leagueId, int $teamId): ?LeagueStanding
    {
        return $this->model
            ->where('league_id', $leagueId)
            ->where('team_id', $teamId)
            ->first();
    }

    public function update(LeagueStanding $standing, LeagueStandingDTO $data): LeagueStanding
    {
        $standing->update($data->toArray());
        return $standing->fresh(['team']);
    }

    public function resetAll(League $league): bool
    {
        return $league->standings()->update([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
        ]) >= 0;
    }

    public function deleteByLeague(League $league): bool
    {
        return $league->standings()->delete() >= 0;
    }
}

