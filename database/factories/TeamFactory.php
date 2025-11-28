<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $teams = ['Manchester United', 'Real Madrid', 'Barcelona', 'Bayern Munich', 'PSG', 'Juventus', 'AC Milan', 'Inter Milan'];
        
        return [
            'name' => fake()->unique()->randomElement($teams) . ' ' . fake()->numberBetween(1, 99),
            'logo' => fake()->slug(2) . '.png',
            'power' => fake()->numberBetween(60, 95),
            'home_advantage' => fake()->randomFloat(2, 1.05, 1.20),
            'goalkeeper_factor' => fake()->randomFloat(2, 0.75, 0.95),
        ];
    }

    /**
     * Create a strong team.
     */
    public function strong(): static
    {
        return $this->state(fn (array $attributes) => [
            'power' => fake()->numberBetween(85, 95),
            'home_advantage' => 1.15,
            'goalkeeper_factor' => 0.80,
        ]);
    }

    /**
     * Create a weak team.
     */
    public function weak(): static
    {
        return $this->state(fn (array $attributes) => [
            'power' => fake()->numberBetween(50, 65),
            'home_advantage' => 1.08,
            'goalkeeper_factor' => 0.95,
        ]);
    }
}

