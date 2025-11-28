<?php

namespace Database\Factories;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameMatch>
 */
class GameMatchFactory extends Factory
{
    protected $model = GameMatch::class;

    public function definition(): array
    {
        return [
            'league_id' => League::factory(),
            'week' => fake()->numberBetween(1, 6),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'home_score' => null,
            'away_score' => null,
            'status' => MatchStatus::PENDING,
        ];
    }

    /**
     * Match has been played.
     */
    public function played(int $homeScore = null, int $awayScore = null): static
    {
        return $this->state(fn (array $attributes) => [
            'home_score' => $homeScore ?? fake()->numberBetween(0, 4),
            'away_score' => $awayScore ?? fake()->numberBetween(0, 4),
            'status' => MatchStatus::PLAYED,
        ]);
    }

    /**
     * Match is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'home_score' => null,
            'away_score' => null,
            'status' => MatchStatus::PENDING,
        ]);
    }

    /**
     * Home team wins.
     */
    public function homeWin(): static
    {
        return $this->state(fn (array $attributes) => [
            'home_score' => fake()->numberBetween(2, 4),
            'away_score' => fake()->numberBetween(0, 1),
            'status' => MatchStatus::PLAYED,
        ]);
    }

    /**
     * Away team wins.
     */
    public function awayWin(): static
    {
        return $this->state(fn (array $attributes) => [
            'home_score' => fake()->numberBetween(0, 1),
            'away_score' => fake()->numberBetween(2, 4),
            'status' => MatchStatus::PLAYED,
        ]);
    }

    /**
     * Match is a draw.
     */
    public function draw(): static
    {
        $score = fake()->numberBetween(0, 3);
        return $this->state(fn (array $attributes) => [
            'home_score' => $score,
            'away_score' => $score,
            'status' => MatchStatus::PLAYED,
        ]);
    }
}

