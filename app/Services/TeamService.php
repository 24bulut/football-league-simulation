<?php

namespace App\Services;

use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TeamService {
    public function __construct(
        private TeamRepositoryInterface $teamRepository
    ) {}

    public function getAllTeams(): Collection
    {
        return $this->teamRepository->all();
    }
}