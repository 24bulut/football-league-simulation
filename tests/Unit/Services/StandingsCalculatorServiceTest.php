<?php

namespace Tests\Unit\Services;

use App\Enums\MatchStatus;
use App\Models\LeagueStanding;
use App\Services\StandingsCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class StandingsCalculatorServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private StandingsCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StandingsCalculatorService::class);
    }

    #[Test]
    public function it_initializes_standings_for_all_teams(): void
    {
        $this->createTeams(4);
        $league = $this->createLeague();

        $this->service->initializeStandings($league);

        $standings = LeagueStanding::where('league_id', $league->id)->get();
        $this->assertCount(4, $standings);

        foreach ($standings as $standing) {
            $this->assertEquals(0, $standing->played);
            $this->assertEquals(0, $standing->won);
            $this->assertEquals(0, $standing->drawn);
            $this->assertEquals(0, $standing->lost);
            $this->assertEquals(0, $standing->points);
            $this->assertEquals(0, $standing->goals_for);
            $this->assertEquals(0, $standing->goals_against);
        }
    }

    #[Test]
    public function it_awards_three_points_for_a_win(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        $this->createPlayedMatch($league, $teams[0], $teams[1], 2, 0);

        $this->service->recalculate($league);

        $winnerStanding = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[0]->id)
            ->first();
        $loserStanding = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[1]->id)
            ->first();

        $this->assertEquals(3, $winnerStanding->points);
        $this->assertEquals(1, $winnerStanding->won);
        $this->assertEquals(0, $loserStanding->points);
        $this->assertEquals(1, $loserStanding->lost);
    }

    #[Test]
    public function it_awards_one_point_each_for_a_draw(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        $this->createPlayedMatch($league, $teams[0], $teams[1], 1, 1);

        $this->service->recalculate($league);

        $homeStanding = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[0]->id)
            ->first();
        $awayStanding = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[1]->id)
            ->first();

        $this->assertEquals(1, $homeStanding->points);
        $this->assertEquals(1, $homeStanding->drawn);
        $this->assertEquals(1, $awayStanding->points);
        $this->assertEquals(1, $awayStanding->drawn);
    }

    #[Test]
    public function it_calculates_goal_difference_correctly(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        $this->createPlayedMatch($league, $teams[0], $teams[1], 3, 1);

        $this->service->recalculate($league);

        $homeStanding = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[0]->id)
            ->first();
        $awayStanding = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[1]->id)
            ->first();

        $this->assertEquals(3, $homeStanding->goals_for);
        $this->assertEquals(1, $homeStanding->goals_against);
        $this->assertEquals(2, $homeStanding->goal_difference);

        $this->assertEquals(1, $awayStanding->goals_for);
        $this->assertEquals(3, $awayStanding->goals_against);
        $this->assertEquals(-2, $awayStanding->goal_difference);
    }

    #[Test]
    public function it_sorts_standings_by_points_first(): void
    {
        $teams = $this->createTeams(3);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        // Team 0 beats Team 1 (3 points)
        $this->createPlayedMatch($league, $teams[0], $teams[1], 1, 0);

        // Team 2 draws with Team 1 (1 point each)
        $this->createPlayedMatch($league, $teams[2], $teams[1], 1, 1);

        $this->service->recalculate($league);
        $standings = $this->service->getStandings($league);

        // Team 0 (3 pts) should be first
        $this->assertEquals($teams[0]->id, $standings->first()->team_id);
        $this->assertEquals(1, $standings->first()->position);
    }

    #[Test]
    public function it_sorts_by_goal_difference_when_points_are_equal(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        // Team 0 wins 3-0 (GD +3)
        $this->createPlayedMatch($league, $teams[0], $teams[1], 3, 0);

        // Team 1 wins 1-0 in return (now: Team 0 GD = +2, Team 1 GD = -2)
        $this->createPlayedMatch($league, $teams[1], $teams[0], 1, 0, 2);

        $this->service->recalculate($league);
        $standings = $this->service->getStandings($league);

        // Both have 3 points, Team 0 has better GD
        $this->assertEquals($teams[0]->id, $standings->first()->team_id);
    }

    #[Test]
    public function it_updates_played_matches_count(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        $this->createPlayedMatch($league, $teams[0], $teams[1], 1, 0);
        $this->createPlayedMatch($league, $teams[1], $teams[0], 2, 2, 2);

        $this->service->recalculate($league);

        $standing = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[0]->id)
            ->first();
        $this->assertEquals(2, $standing->played);
    }

    #[Test]
    public function it_ignores_pending_matches(): void
    {
        $teams = $this->createTeams(2);
        $league = $this->createLeague();
        $this->service->initializeStandings($league);

        // Create a pending match
        $this->createMatch($league, $teams[0], $teams[1]);

        $this->service->recalculate($league);

        $standing = LeagueStanding::where('league_id', $league->id)
            ->where('team_id', $teams[0]->id)
            ->first();
        $this->assertEquals(0, $standing->played);
        $this->assertEquals(0, $standing->points);
    }
}
