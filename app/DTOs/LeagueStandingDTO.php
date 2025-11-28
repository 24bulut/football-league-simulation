<?php

namespace App\DTOs;

readonly class LeagueStandingDTO
{
    public function __construct(
        public int $id,
        public int $leagueId,
        public int $teamId,
        public int $played,
        public int $won,
        public int $drawn,
        public int $lost,
        public int $goalsFor,
        public int $goalsAgainst,
        public int $points,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'league_id' => $this->leagueId,
            'team_id' => $this->teamId,
            'played' => $this->played,
            'won' => $this->won,
            'drawn' => $this->drawn,
            'lost' => $this->lost,
            'goals_for' => $this->goalsFor,
            'goals_against' => $this->goalsAgainst,
            'points' => $this->points,
            'position' => $this->position,
        ];
    }
}