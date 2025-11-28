<?php

namespace App\Repositories;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\League;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use Illuminate\Support\Collection;
use App\DTOs\GameMatchDTO;

class GameMatchRepository implements GameMatchRepositoryInterface
{
    public function __construct(
        private GameMatch $model
    ) {}

    public function create(GameMatchDTO $data): GameMatch
    {
        return $this->model->create($data->toArray());
    }

    public function bulkInsert(array $matches): bool
    {
        return $this->model->insert($matches);
    }

    public function find(int $id): ?GameMatch
    {
        return $this->model
            ->with(['homeTeam', 'awayTeam', 'league'])
            ->find($id);
    }

    public function getByLeague(League $league): Collection
    {
        return $league->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('week')
            ->get();
    }

    public function getByWeek(League $league, int $week): Collection
    {
        return $league->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->where('week', $week)
            ->get();
    }

    public function getPendingByWeek(League $league, int $week): Collection
    {
        return $league->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->where('week', $week)
            ->where('status', MatchStatus::PENDING)
            ->get();
    }

    public function getPending(League $league): Collection
    {
        return $league->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->where('status', MatchStatus::PENDING)
            ->get();
    }

    public function update(GameMatch $match, GameMatchDTO $data): GameMatch
    {
        $match->update($data->toArray());
        return $match->fresh(['homeTeam', 'awayTeam']);
    }

    public function deleteByLeague(League $league): bool
    {
        return $league->matches()->delete() >= 0;
    }
}

