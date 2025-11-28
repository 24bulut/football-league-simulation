<?php

namespace App\Repositories;

use App\Models\Team;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Collection;

class TeamRepository implements TeamRepositoryInterface
{
    public function __construct(
        private Team $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Team
    {
        return $this->model->find($id);
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function getIds(): array
    {
        return $this->model->pluck('id')->toArray();
    }
}

