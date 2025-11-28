<?php

namespace Database\Factories;

use App\Enums\LeagueStatus;
use App\Models\League;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\League>
 */
class LeagueFactory extends Factory
{
    protected $model = League::class;

    public function definition(): array
    {
        return [
            'current_week' => 0,
            'status' => LeagueStatus::NOT_STARTED,
        ];
    }

    /**
     * League in progress.
     */
    public function inProgress(int $week = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'current_week' => $week,
            'status' => LeagueStatus::IN_PROGRESS,
        ]);
    }

    /**
     * Completed league.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_week' => 6,
            'status' => LeagueStatus::COMPLETED,
        ]);
    }
}

