<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'game_matches';

    protected $fillable = [
        'league_id',
        'week',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'status',
    ];

    protected $casts = [
        'week' => 'integer',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'status' => MatchStatus::class,
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function isPlayed(): bool
    {
        return $this->status === MatchStatus::PLAYED;
    }

    public function isPending(): bool
    {
        return $this->status === MatchStatus::PENDING;
    }

    public function getWinner(): ?Team
    {
        if (!$this->isPlayed()) {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return $this->homeTeam;
        }

        if ($this->away_score > $this->home_score) {
            return $this->awayTeam;
        }

        return null; // Draw
    }

    public function isDraw(): bool
    {
        return $this->isPlayed() && $this->home_score === $this->away_score;
    }
}

