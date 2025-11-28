<?php

namespace App\DTOs;

readonly class GameMatchDTO
{
    public function __construct(
        public int $id,
        public int $leagueId,
        public int $week,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'league_id' => $this->leagueId,
            'week' => $this->week,
        ];
    }
}