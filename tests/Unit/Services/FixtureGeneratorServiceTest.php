<?php

namespace Tests\Unit\Services;

use App\Enums\MatchStatus;
use App\Models\League;
use App\Services\FixtureGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class FixtureGeneratorServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private FixtureGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FixtureGeneratorService::class);
    }

    #[Test]
    public function it_generates_correct_number_of_fixtures_for_four_teams(): void
    {
        $this->createTeams(4);
        $league = $this->createLeague();

        $fixtures = $this->service->generate($league);

        $this->assertCount(12, $fixtures);
    }

    #[Test]
    public function it_generates_two_matches_per_week(): void
    {
        $this->createTeams(4);
        $league = $this->createLeague();

        $fixtures = $this->service->generate($league);

        for ($week = 1; $week <= 6; $week++) {
            $matchesInWeek = $fixtures->where('week', $week);
            $this->assertCount(2, $matchesInWeek, "Week $week should have 2 matches");
        }
    }

    #[Test]
    public function each_team_plays_every_other_team_twice(): void
    {
        $teams = $this->createTeams(4);
        $league = $this->createLeague();

        $fixtures = $this->service->generate($league);

        foreach ($teams as $teamA) {
            foreach ($teams as $teamB) {
                if ($teamA->id === $teamB->id) continue;

                $matchesPlayed = $fixtures->filter(function ($match) use ($teamA, $teamB) {
                    return ($match->home_team_id === $teamA->id && $match->away_team_id === $teamB->id) ||
                           ($match->home_team_id === $teamB->id && $match->away_team_id === $teamA->id);
                });

                $this->assertCount(2, $matchesPlayed,
                    "Team {$teamA->name} and {$teamB->name} should play exactly 2 matches");
            }
        }
    }

    #[Test]
    public function all_fixtures_start_with_pending_status(): void
    {
        $this->createTeams(4);
        $league = $this->createLeague();

        $fixtures = $this->service->generate($league);

        foreach ($fixtures as $fixture) {
            $this->assertEquals(MatchStatus::PENDING, $fixture->status);
        }
    }

    #[Test]
    public function it_returns_empty_collection_with_less_than_two_teams(): void
    {
        $this->createTeams(1);
        $league = $this->createLeague();

        $fixtures = $this->service->generate($league);

        $this->assertTrue($fixtures->isEmpty());
    }

    #[Test]
    public function no_team_plays_against_itself(): void
    {
        $this->createTeams(4);
        $league = $this->createLeague();

        $fixtures = $this->service->generate($league);

        foreach ($fixtures as $fixture) {
            $this->assertNotEquals($fixture->home_team_id, $fixture->away_team_id,
                "A team should never play against itself");
        }
    }
}
