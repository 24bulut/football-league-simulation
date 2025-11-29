<template>
  <div class="min-h-screen p-8 bg-gradient-to-br from-dark-900 via-dark-800 to-dark-700">
    <!-- Header -->
    <header class="text-center mb-12">
      <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
        ⚽ Premier League Simulation
      </h1>
      <p class="text-gray-400 text-lg">Welcome to the Football League Simulator</p>
    </header>

    <!-- Teams Section -->
    <section class="max-w-4xl mx-auto mb-8">
      <h2 class="text-xl font-semibold mb-6 text-primary">Teams</h2>

      <AppLoading v-if="loading" message="Loading teams..." />

      <div v-else-if="teams.length === 0" class="text-center py-12 text-gray-500">
        <p>No teams available. Start a league to begin!</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <TeamCard v-for="team in teams" :key="team.id" :team="team" />
      </div>
    </section>

    <!-- Action Button -->
    <div class="text-center mt-8">
      <AppButton
        variant="primary"
        :loading="creatingLeague"
        loading-text="Creating..."
        class="text-xl py-4 px-10 rounded-full"
        @click="createLeague"
      >
        🏆 Create League
      </AppButton>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'
import AppButton from '../components/ui/AppButton.vue'
import AppLoading from '../components/ui/AppLoading.vue'
import TeamCard from '../components/league/TeamCard.vue'

const router = useRouter()
const teams = ref([])
const loading = ref(true)
const creatingLeague = ref(false)

const fetchTeams = async () => {
  try {
    loading.value = true
    const response = await api.getTeams()
    teams.value = response.data?.data || []
  } catch (error) {
    console.error('Failed to fetch teams:', error)
    teams.value = []
  } finally {
    loading.value = false
  }
}

const createLeague = async () => {
  try {
    creatingLeague.value = true
    await api.startLeague()
    router.push('/league')
  } catch (error) {
    console.error('Failed to create league:', error)
    alert('Failed to create league')
  } finally {
    creatingLeague.value = false
  }
}

onMounted(fetchTeams)
</script>
