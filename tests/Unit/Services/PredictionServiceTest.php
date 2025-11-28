<?php

namespace Tests\Unit\Services;

use App\Models\LeagueStanding;
use App\Services\PredictionService;
use App\Services\StandingsCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class PredictionServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private PredictionService $service;
    private StandingsCalculatorService $standingsCalculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PredictionService::class);
        $this->standingsCalculator = app(StandingsCalculatorService::class);
    }

    #[Test]
    public function predictions_are_not_shown_before_week_four(): void
    {
        $league = $this->createLeagueInProgress(3);

        $this->assertFalse($this->service->shouldShowPredictions($league));
    }

    #[Test]
    public function predictions_are_shown_from_week_four(): void
    {
        $league = $this->createLeagueInProgress(4);

        $this->assertTrue($this->service->shouldShowPredictions($league));
    }

    #[Test]
    public function predictions_sum_to_approximately_100_percent(): void
    {
        $teams = $this->createTeams(4);
        $league = $this->createLeagueInProgress(4);
        $this->standingsCalculator->initializeStandings($league);
        $this->createPendingMatchesForWeek($league, $teams, 5);

        $predictions = $this->service->calculatePredictions($league);

        $totalPercentage = $predictions->sum('prediction_percentage');
        $this->assertGreaterThanOrEqual(98, $totalPercentage);
        $this->assertLessThanOrEqual(102, $totalPercentage);
    }

    #[Test]
    public function leader_has_higher_prediction_percentage(): void
    {
        $teams = $this->createTeams(4);
        $league = $this->createLeagueInProgress(4);
        $this->standingsCalculator->initializeStandings($league);

        // Give team 0 a significant lead
        LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[0]->id)
            ->update(['points' => 12, 'goal_difference' => 10]);

        LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[1]->id)
            ->update(['points' => 6, 'goal_difference' => 2]);

        $this->standingsCalculator->updatePositions($league);
        $this->createPendingMatchesForWeek($league, $teams, 5);

        $predictions = $this->service->calculatePredictions($league);

        $topPrediction = $predictions->first();
        $this->assertEquals($teams[0]->id, $topPrediction['team_id']);
    }

    #[Test]
    public function predictions_include_team_names_and_points(): void
    {
        $teams = $this->createTeams(4);
        $league = $this->createLeagueInProgress(4);
        $this->standingsCalculator->initializeStandings($league);
        $this->createPendingMatchesForWeek($league, $teams, 5);

        $predictions = $this->service->calculatePredictions($league);

        foreach ($predictions as $prediction) {
            $this->assertArrayHasKey('team_id', $prediction);
            $this->assertArrayHasKey('team_name', $prediction);
            $this->assertArrayHasKey('current_points', $prediction);
            $this->assertArrayHasKey('prediction_percentage', $prediction);
        }
    }
}
