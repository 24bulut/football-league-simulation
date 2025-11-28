<?php

namespace App\Repositories\Contracts;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\League;
use Illuminate\Support\Collection;
use App\DTOs\GameMatchDTO;

interface GameMatchRepositoryInterface
{
    public function create(GameMatchDTO $data): GameMatch;

    public function bulkInsert(array $matches): bool;

    public function find(int $id): ?GameMatch;

    public function getByLeague(League $league): Collection;

    public function getByWeek(League $league, int $week): Collection;

    public function getPendingByWeek(League $league, int $week): Collection;

    public function getPending(League $league): Collection;

    public function update(GameMatch $match, GameMatchDTO $data): GameMatch;

    public function deleteByLeague(League $league): bool;
}

