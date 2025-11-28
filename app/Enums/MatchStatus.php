<?php

namespace App\Enums;

enum MatchStatus: string
{
    case PENDING = 'pending';
    case PLAYED = 'played';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PLAYED => 'Played',
        };
    }
}

