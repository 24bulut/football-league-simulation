<?php

namespace App\Models;

use App\Enums\LeagueStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_week',
        'status',
    ];

    protected $casts = [
        'current_week' => 'integer',
        'status' => LeagueStatus::class,
    ];

    // Relationships
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class);
    }

    // Scopes
    public function scopeWithStandings(Builder $query): Builder
    {
        return $query->with('standings.team');
    }

    public function scopeWithMatches(Builder $query): Builder
    {
        return $query->with(['matches.homeTeam', 'matches.awayTeam']);
    }

    public function scopeWithAllDetails(Builder $query): Builder
    {
        return $query->with(['standings.team', 'matches.homeTeam', 'matches.awayTeam']);
    }

    // Helpers
    public function isCompleted(): bool
    {
        return $this->status === LeagueStatus::COMPLETED;
    }

    public function isInProgress(): bool
    {
        return $this->status === LeagueStatus::IN_PROGRESS;
    }

    public function hasNotStarted(): bool
    {
        return $this->status === LeagueStatus::NOT_STARTED;
    }

    public function getTotalWeeks(): int
    {
        $teamCount = $this->standings()->count();
        if ($teamCount < 2) {
            return 0;
        }
        return ($teamCount - 1) * 2;
    }

    public function getRemainingWeeks(): int
    {
        return $this->getTotalWeeks() - $this->current_week;
    }
}
