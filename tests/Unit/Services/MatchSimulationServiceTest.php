<?php

namespace Tests\Unit\Services;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\Team;
use App\Services\MatchSimulationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class MatchSimulationServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private MatchSimulationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MatchSimulationService::class);
    }

    #[Test]
    public function it_simulates_a_match_and_sets_scores(): void
    {
        $match = $this->createPendingMatchWithPower(80, 75);

        $result = $this->service->simulate($match);

        $this->assertNotNull($result->home_score);
        $this->assertNotNull($result->away_score);
        $this->assertGreaterThanOrEqual(0, $result->home_score);
        $this->assertGreaterThanOrEqual(0, $result->away_score);
    }

    #[Test]
    public function it_sets_match_status_to_played_after_simulation(): void
    {
        $match = $this->createPendingMatchWithPower(80, 75);

        $result = $this->service->simulate($match);

        $this->assertEquals(MatchStatus::PLAYED, $result->status);
    }

    #[Test]
    public function it_does_not_re_simulate_already_played_matches(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $match = $this->createPlayedMatch($league, $teams[0], $teams[1], 2, 1);

        $result = $this->service->simulate($match);

        $this->assertEquals(2, $result->home_score);
        $this->assertEquals(1, $result->away_score);
    }

    #[Test]
    public function stronger_team_wins_more_often(): void
    {
        $strongTeam = Team::factory()->create([
            'power' => 95,
            'home_advantage' => 1.15,
            'goalkeeper_factor' => 0.80
        ]);
        $weakTeam = Team::factory()->create([
            'power' => 50,
            'home_advantage' => 1.10,
            'goalkeeper_factor' => 0.95
        ]);

        $strongWins = 0;
        $simulations = 50;

        for ($i = 0; $i < $simulations; $i++) {
            $league = $this->createLeague();
            $match = $this->createMatch($league, $strongTeam, $weakTeam);

            $result = $this->service->simulate($match);
            if ($result->home_score > $result->away_score) {
                $strongWins++;
            }
        }

        $this->assertGreaterThan($simulations * 0.4, $strongWins);
    }

    #[Test]
    public function scores_are_within_realistic_range(): void
    {
        $match = $this->createPendingMatchWithPower(80, 75);

        $result = $this->service->simulate($match);

        $this->assertLessThanOrEqual(7, $result->home_score);
        $this->assertLessThanOrEqual(7, $result->away_score);
    }

    private function createPendingMatchWithPower(int $homePower, int $awayPower): GameMatch
    {
        $homeTeam = Team::factory()->create([
            'power' => $homePower,
            'home_advantage' => 1.15,
            'goalkeeper_factor' => 0.85
        ]);
        $awayTeam = Team::factory()->create([
            'power' => $awayPower,
            'home_advantage' => 1.10,
            'goalkeeper_factor' => 0.90
        ]);
        $league = $this->createLeague();

        return $this->createMatch($league, $homeTeam, $awayTeam);
    }
}
