<?php

namespace Database\Factories;

use App\Models\League;
use App\Models\LeagueStanding;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeagueStanding>
 */
class LeagueStandingFactory extends Factory
{
    protected $model = LeagueStanding::class;

    public function definition(): array
    {
        $won = fake()->numberBetween(0, 6);
        $drawn = fake()->numberBetween(0, 6 - $won);
        $lost = 6 - $won - $drawn;
        $goalsFor = fake()->numberBetween($won, $won * 3 + $drawn);
        $goalsAgainst = fake()->numberBetween($lost, $lost * 3 + $drawn);

        return [
            'league_id' => League::factory(),
            'team_id' => Team::factory(),
            'played' => $won + $drawn + $lost,
            'won' => $won,
            'drawn' => $drawn,
            'lost' => $lost,
            'goals_for' => $goalsFor,
            'goals_against' => $goalsAgainst,
            'goal_difference' => $goalsFor - $goalsAgainst,
            'points' => ($won * 3) + $drawn,
            'position' => fake()->numberBetween(1, 4),
        ];
    }

    /**
     * Fresh standing with no matches played.
     */
    public function fresh(): static
    {
        return $this->state(fn (array $attributes) => [
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'position' => 0,
        ]);
    }

    /**
     * Leader position.
     */
    public function leader(): static
    {
        return $this->state(fn (array $attributes) => [
            'played' => 6,
            'won' => 5,
            'drawn' => 1,
            'lost' => 0,
            'goals_for' => 15,
            'goals_against' => 3,
            'goal_difference' => 12,
            'points' => 16,
            'position' => 1,
        ]);
    }
}

