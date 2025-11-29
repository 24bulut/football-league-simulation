# Football League Simulation - Frontend

A Vue 3 app that simulates a football league with 4 teams playing a double round-robin tournament.

## Tech Stack

- Vue 3 (Composition API)
- Vue Router
- Axios
- Tailwind CSS
- Vite

## Features

- View all teams and start a new league
- Simulate matches week by week or all at once
- Live standings table with points, wins, draws, losses, and goal difference
- Championship predictions (shows 0% for teams that mathematically can't win)
- Edit match scores manually
- Reset league to start over

## Setup

```bash
npm install
npm run dev
```

Create `.env` file:
```
VITE_API_URL=http://localhost:8000/api
```

## Project Structure

```
src/
├── api/           # Axios API client
├── components/
│   ├── ui/        # Reusable UI (Button, Card, Loading)
│   ├── layout/    # Layout components (PageHeader)
│   └── league/    # Feature components (TeamCard, MatchRow, etc.)
├── pages/         # Page views (Home, League)
└── router/        # Vue Router config
```

## API Endpoints Used

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /teams | Get all teams |
| POST | /league/start | Start new league |
| POST | /league/reset | Reset league |
| GET | /matches | Get all matches |
| PUT | /matches/:id | Update match score |
| GET | /standings | Get standings |
| GET | /predictions | Get predictions |
| POST | /simulation/next-week | Play next week |
| POST | /simulation/play-all | Play all weeks |
