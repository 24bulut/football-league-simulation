<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'power',
        'home_advantage',
        'goalkeeper_factor',
    ];

    protected $casts = [
        'power' => 'integer',
        'home_advantage' => 'decimal:2',
        'goalkeeper_factor' => 'decimal:2',
    ];

    public function homeMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'away_team_id');
    }

    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class);
    }
}

