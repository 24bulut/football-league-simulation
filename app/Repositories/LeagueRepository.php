<?php

namespace App\Repositories;

use App\Enums\LeagueStatus;
use App\Models\League;
use App\Repositories\Contracts\LeagueRepositoryInterface;
use App\DTOs\LeagueDTO;

class LeagueRepository implements LeagueRepositoryInterface
{
    public function __construct(
        private League $model
    ) {}

    public function create(LeagueDTO $data): League
    {
        return $this->model->create($data->toArray());
    }

    public function find(int $id): ?League
    {
        return $this->model
            ->with(['standings.team', 'matches.homeTeam', 'matches.awayTeam'])
            ->find($id);
    }

    public function getLatest(): ?League
    {
        return $this->model
            ->with(['standings.team', 'matches.homeTeam', 'matches.awayTeam'])
            ->latest()
            ->first();
    }

    public function update(League $league, LeagueDTO $data): League
    {
        $league->update($data->toArray());
        return $league->fresh(['standings.team', 'matches.homeTeam', 'matches.awayTeam']);
    }

    public function updateStatus(League $league, LeagueStatus $status, int $week): League
    {
        $league->update([
            'status' => $status,
            'current_week' => $week,
        ]);
        return $league->fresh(['standings.team', 'matches.homeTeam', 'matches.awayTeam']);
    }
}

