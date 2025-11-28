<?php

namespace App\Repositories\Contracts;

use App\Models\League;
use App\Models\LeagueStanding;
use Illuminate\Support\Collection;
use App\DTOs\LeagueStandingDTO;
interface LeagueStandingRepositoryInterface
{
    public function create(LeagueStandingDTO $data): LeagueStanding;

    public function getByLeague(League $league): Collection;

    public function getByLeagueOrdered(League $league): Collection;

    public function findByLeagueAndTeam(int $leagueId, int $teamId): ?LeagueStanding;

    public function update(LeagueStanding $standing, LeagueStandingDTO $data): LeagueStanding;

    public function resetAll(League $league): bool;

    public function deleteByLeague(League $league): bool;
}

