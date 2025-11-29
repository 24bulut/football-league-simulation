# Football League Simulation

A full-stack application that simulates a football league with 4 teams playing a double round-robin tournament (6 weeks, 12 matches total).

## Tech Stack

**Backend:** Laravel 12, PHP 8.4, SQLite  
**Frontend:** Vue 3, Tailwind CSS, Vite

## Features

- Generate fixtures using Round-Robin algorithm (Circle Method)
- Simulate matches with realistic scores (Poisson distribution)
- Calculate standings with Premier League rules
- Predict championship probabilities (0% for mathematically eliminated teams)
- Edit match scores manually

## Quick Start

```bash
# Backend
composer install
cp .env.example .env
php artisan migrate --seed
php artisan serve

# Frontend
cd frontend
npm install
npm run dev
```

## Backend Architecture

```
app/
├── Http/
│   ├── Controllers/Api/    # REST API controllers
│   ├── Requests/Api/       # Form request validation
│   └── Resources/          # API response formatting
├── Services/               # Business logic
│   ├── FixtureGeneratorService    # Round-robin scheduling
│   ├── MatchSimulationService     # Score generation (Poisson)
│   ├── StandingsCalculatorService # Points & rankings
│   ├── PredictionService          # Championship odds
│   └── LeagueService              # Orchestrator
├── Repositories/           # Data access layer
├── Models/                 # Eloquent models
├── Enums/                  # Status enums
└── DTOs/                   # Data transfer objects
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/teams | List teams |
| POST | /api/league/start | Start new league |
| POST | /api/league/reset | Reset league |
| GET | /api/matches | Get all matches |
| PUT | /api/matches/{id} | Update match score |
| GET | /api/standings | Get standings |
| GET | /api/predictions | Get predictions |
| POST | /api/simulation/next-week | Play next week |
| POST | /api/simulation/play-all | Play all remaining |

## Algorithms

**Fixture Generation:** Circle Method - fixes one team, rotates others to create balanced schedule.

**Match Simulation:** 
```
Expected Goals = 1.5 × (team_power / 85) × opponent_goalkeeper_factor
Actual Goals = Poisson(expected)
```

**Predictions:** Teams with `max_possible_points < leader_current_points` get 0%.

## Running Tests

```bash
php artisan test
```
