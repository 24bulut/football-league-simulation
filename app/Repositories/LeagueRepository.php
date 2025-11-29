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
        return $this->model->find($id);
    }

    public function findWithAllDetails(int $id): ?League
    {
        return $this->model->withAllDetails()->find($id);
    }

    public function getLatest(): ?League
    {
        return $this->model->latest()->first();
    }

    public function getLatestWithAllDetails(): ?League
    {
        return $this->model->withAllDetails()->latest()->first();
    }

    public function update(League $league, LeagueDTO $data): League
    {
        $league->update($data->toArray());
        return $league->fresh();
    }

    public function updateStatus(League $league, LeagueStatus $status, int $week): League
    {
        $league->update([
            'status' => $status,
            'current_week' => $week,
        ]);
        return $this->find($league->id);
    }

    public function moveOnToNextWeek(League $league): League
    {
        $nextWeek = $league->current_week + 1;
        $league->update(['current_week' => $nextWeek, 'status' => $nextWeek >= $league->getTotalWeeks() ? LeagueStatus::COMPLETED : LeagueStatus::IN_PROGRESS]);
        return $league->fresh();
    }
}
