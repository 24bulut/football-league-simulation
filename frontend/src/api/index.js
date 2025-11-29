import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

export default {
  getTeams: () => api.get('/teams'),

  // League
  startLeague: () => api.post('/league/start'),
  getLeagueStatus: () => api.get('/league/status'),
  resetLeague: () => api.post('/league/reset'),
  getStandings: () => api.get('/standings'),
  getPredictions: () => api.get('/predictions'),

  // Simulation
  playNextWeek: () => api.post('/simulation/next-week'),
  playAllWeeks: () => api.post('/simulation/play-all'),
  getWeekResults: (week) => api.get(`/simulation/week/${week}`),

  // Matches
  getAllMatches: () => api.get('/matches'),
  getMatchesByWeek: (week) => api.get(`/matches/week/${week}`),
  updateMatch: (matchId, data) => api.put(`/matches/${matchId}`, data),
}

