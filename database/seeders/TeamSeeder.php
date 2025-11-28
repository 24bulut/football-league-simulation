<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Manchester City',
                'logo' => 'manchester-city.png',
                'power' => 90,
                'home_advantage' => 1.15,
                'goalkeeper_factor' => 0.85,
            ],
            [
                'name' => 'Liverpool',
                'logo' => 'liverpool.png',
                'power' => 88,
                'home_advantage' => 1.18,
                'goalkeeper_factor' => 0.82,
            ],
            [
                'name' => 'Chelsea',
                'logo' => 'chelsea.png',
                'power' => 85,
                'home_advantage' => 1.12,
                'goalkeeper_factor' => 0.88,
            ],
            [
                'name' => 'Arsenal',
                'logo' => 'arsenal.png',
                'power' => 82,
                'home_advantage' => 1.10,
                'goalkeeper_factor' => 0.90,
            ],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}

