<?php

namespace App\DTOs;

use App\Enums\MatchStatus;

readonly class GameMatchDTO
{
    public function __construct(
        public int $id,
        public int $leagueId,
        public int $week,
        public ?int $homeTeamId = null,
        public ?int $awayTeamId = null,
        public ?int $homeScore = null,
        public ?int $awayScore = null,
        public ?MatchStatus $status = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'league_id' => $this->leagueId,
            'week' => $this->week,
            'home_team_id' => $this->homeTeamId,
            'away_team_id' => $this->awayTeamId,
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
            'status' => $this->status?->value,
        ], fn($value) => $value !== null);
    }
}
