<?php

namespace Tests\Unit\Services;

use App\Enums\LeagueStatus;
use App\Enums\MatchStatus;
use App\Services\LeagueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class LeagueServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private LeagueService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LeagueService::class);
    }

    #[Test]
    public function it_starts_a_new_league(): void
    {
        $this->createTeams(4);

        $league = $this->service->startLeague();

        $this->assertNotNull($league);
        $this->assertEquals(0, $league->current_week);
        $this->assertEquals(LeagueStatus::NOT_STARTED, $league->status);
    }

    #[Test]
    public function starting_a_league_creates_fixtures(): void
    {
        $this->createTeams(4);

        $league = $this->service->startLeague();

        $this->assertCount(12, $league->matches);
    }

    #[Test]
    public function starting_a_league_initializes_standings(): void
    {
        $this->createTeams(4);

        $league = $this->service->startLeague();

        $this->assertCount(4, $league->standings);
        foreach ($league->standings as $standing) {
            $this->assertEquals(0, $standing->points);
            $this->assertEquals(0, $standing->played);
        }
    }

    #[Test]
    public function it_plays_next_week_and_simulates_matches(): void
    {
        $this->createTeams(4);
        $league = $this->service->startLeague();

        $result = $this->service->playNextWeek($league);

        $this->assertEquals(LeagueStatus::IN_PROGRESS, $result['status']);

        $week1Matches = $result['matches'];
        foreach ($week1Matches as $match) {
            $this->assertEquals(MatchStatus::PLAYED, $match->status);
            $this->assertNotNull($match->home_score);
            $this->assertNotNull($match->away_score);
        }
    }

    #[Test]
    public function it_completes_league_after_all_weeks(): void
    {
        $this->createTeams(4);
        $league = $this->service->startLeague();

        for ($i = 0; $i < 6; $i++) {
            $result = $this->service->playNextWeek($league);
        }

        $this->assertEquals(LeagueStatus::COMPLETED, $result['status']);
    }

    #[Test]
    public function it_returns_null_when_no_league_exists(): void
    {
        $currentLeague = $this->service->getCurrentLeague();

        $this->assertNull($currentLeague);
    }

    #[Test]
    public function it_gets_matches_for_specific_week(): void
    {
        $this->createTeams(4);
        $league = $this->service->startLeague();

        $week1Matches = $this->service->getWeekMatches($league, 1);
        $week3Matches = $this->service->getWeekMatches($league, 3);

        $this->assertCount(2, $week1Matches);
        $this->assertCount(2, $week3Matches);

        foreach ($week1Matches as $match) {
            $this->assertEquals(1, $match->week);
        }
    }
}
