<?php

namespace App\Repositories\Contracts;

use App\Enums\LeagueStatus;
use App\Models\League;
use App\DTOs\LeagueDTO;

interface LeagueRepositoryInterface
{
    public function create(LeagueDTO $data): League;

    public function find(int $id): ?League;

    public function getLatest(): ?League;

    public function update(League $league, LeagueDTO $data): League;

    public function updateStatus(League $league, LeagueStatus $status, int $week): League;
}

