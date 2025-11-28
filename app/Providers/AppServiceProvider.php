<?php

namespace App\Providers;

use App\Repositories\Contracts\LeagueRepositoryInterface;
use App\Repositories\Contracts\GameMatchRepositoryInterface;
use App\Repositories\Contracts\LeagueStandingRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\LeagueRepository;
use App\Repositories\GameMatchRepository;
use App\Repositories\LeagueStandingRepository;
use App\Repositories\TeamRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings.
     */
    public array $bindings = [
        TeamRepositoryInterface::class => TeamRepository::class,
        LeagueRepositoryInterface::class => LeagueRepository::class,
        GameMatchRepositoryInterface::class => GameMatchRepository::class,
        LeagueStandingRepositoryInterface::class => LeagueStandingRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
