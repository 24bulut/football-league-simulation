<?php

namespace App\DTOs;

use App\Enums\LeagueStatus;

readonly class LeagueDTO
{
    public function __construct(
        public int $id,
        public int $currentWeek,
        public LeagueStatus $status,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'current_week' => $this->currentWeek,
            'status' => $this->status,
        ];
    }
}