<template>
  <div class="min-h-screen px-4 py-6 bg-gradient-to-br from-dark-900 via-dark-800 to-dark-700">
    <!-- Header -->
    <PageHeader
      title="🏆 League Simulation"
      back-link="/"
      back-text="Back to Home"
    />

    <AppLoading v-if="loading" message="Loading matches..." />

    <div v-else class="grid grid-cols-1 xl:grid-cols-[1fr_380px] gap-6">
      <!-- Left: Fixtures -->
      <div class="min-w-0">
        <!-- Action Buttons -->
        <ActionButtons
          :simulating="simulating"
          @play-next="playNextWeek"
          @play-all="playAllWeeks"
          @reset="resetLeague"
        />

        <!-- Weeks Grid -->
        <div class="grid grid-cols-3 gap-4">
          <WeekCard
            v-for="weekData in weeks"
            :key="weekData.week"
            :week="weekData"
            :editing-match-id="editingMatchId"
            :home-score="editHomeScore"
            :away-score="editAwayScore"
            :saving="saving"
            @edit-match="startEditing"
            @save-match="saveMatch"
            @cancel-edit="cancelEditing"
            @update:home-score="editHomeScore = $event"
            @update:away-score="editAwayScore = $event"
          />
        </div>
      </div>

      <!-- Right: Sidebar -->
      <div class="space-y-6 xl:sticky xl:top-6 xl:h-fit">
        <StandingsTable :standings="standings" />
        <PredictionsPanel :predictions="predictions" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api'

// Components
import AppLoading from '../components/ui/AppLoading.vue'
import PageHeader from '../components/layout/PageHeader.vue'
import ActionButtons from '../components/league/ActionButtons.vue'
import WeekCard from '../components/league/WeekCard.vue'
import StandingsTable from '../components/league/StandingsTable.vue'
import PredictionsPanel from '../components/league/PredictionsPanel.vue'

// State
const loading = ref(true)
const simulating = ref(false)
const saving = ref(false)
const weeks = ref([])
const standings = ref([])
const predictions = ref([])

// Edit state
const editingMatchId = ref(null)
const editHomeScore = ref(0)
const editAwayScore = ref(0)

// Methods
const startEditing = (match) => {
  editingMatchId.value = match.id
  editHomeScore.value = match.home_score ?? 0
  editAwayScore.value = match.away_score ?? 0
}

const cancelEditing = () => {
  editingMatchId.value = null
  editHomeScore.value = 0
  editAwayScore.value = 0
}

const saveMatch = async (matchId) => {
  try {
    saving.value = true
    await api.updateMatch(matchId, {
      home_score: editHomeScore.value,
      away_score: editAwayScore.value
    })
    cancelEditing()
    await fetchData()
  } catch (error) {
    console.error('Failed to update match:', error)
    alert(error.response?.data?.message || 'Failed to update match')
  } finally {
    saving.value = false
  }
}

const fetchData = async () => {
  try {
    loading.value = true
    const [matchesRes, standingsRes, predictionsRes] = await Promise.all([
      api.getAllMatches(),
      api.getStandings(),
      api.getPredictions().catch(() => ({ data: { data: [] } }))
    ])
    weeks.value = matchesRes.data?.data || []
    standings.value = standingsRes.data?.data || []
    predictions.value = predictionsRes.data?.data || []
  } catch (error) {
    console.error('Failed to fetch data:', error)
  } finally {
    loading.value = false
  }
}

const playNextWeek = async () => {
  try {
    simulating.value = true
    await api.playNextWeek()
    await fetchData()
  } catch (error) {
    console.error('Failed to play next week:', error)
    alert(error.response?.data?.message || 'Failed to simulate')
  } finally {
    simulating.value = false
  }
}

const playAllWeeks = async () => {
  try {
    simulating.value = true
    await api.playAllWeeks()
    await fetchData()
  } catch (error) {
    console.error('Failed to play all weeks:', error)
    alert(error.response?.data?.message || 'Failed to simulate')
  } finally {
    simulating.value = false
  }
}

const resetLeague = async () => {
  if (!confirm('Are you sure you want to reset the league?')) return

  try {
    await api.resetLeague()
    await fetchData()
  } catch (error) {
    console.error('Failed to reset league:', error)
    alert('Failed to reset league')
  }
}

onMounted(fetchData)
</script>
