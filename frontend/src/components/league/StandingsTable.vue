<template>
  <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
    <h2 class="text-lg font-semibold mb-4 text-primary">📊 Standings</h2>

    <div class="space-y-2">
      <!-- Header -->
      <div class="grid grid-cols-[30px_24px_1fr_repeat(5,24px)_32px] gap-1 items-center px-2 pb-2 border-b border-white/10 text-[10px] text-gray-500 uppercase font-bold">
        <span>#</span>
        <span></span>
        <span>Team</span>
        <span class="text-center">P</span>
        <span class="text-center">W</span>
        <span class="text-center">D</span>
        <span class="text-center">L</span>
        <span class="text-center">GD</span>
        <span class="text-center">Pts</span>
      </div>

      <!-- Rows -->
      <div
        v-for="standing in standings"
        :key="standing.team.id"
        class="grid grid-cols-[30px_24px_1fr_repeat(5,24px)_32px] gap-1 items-center px-2 py-2 bg-black/20 rounded-lg text-sm transition-all hover:bg-black/30"
        :class="{ 'bg-gradient-to-r from-primary/15 to-accent/15 border border-primary/30': standing.position === 1 }"
      >
        <span class="font-bold text-primary">{{ standing.position }}</span>
        <TeamLogo :name="standing.team.name" size="sm" />
        <span class="font-medium truncate">{{ standing.team.name }}</span>
        <span class="text-center text-gray-400 text-xs">{{ standing.played }}</span>
        <span class="text-center text-gray-400 text-xs">{{ standing.won }}</span>
        <span class="text-center text-gray-400 text-xs">{{ standing.drawn }}</span>
        <span class="text-center text-gray-400 text-xs">{{ standing.lost }}</span>
        <span
          class="text-center text-xs"
          :class="gdClass(standing.goal_difference)"
        >
          {{ standing.goal_difference > 0 ? '+' : '' }}{{ standing.goal_difference }}
        </span>
        <span class="text-center font-bold">{{ standing.points }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import TeamLogo from './TeamLogo.vue'

defineProps({
  standings: {
    type: Array,
    required: true
  }
})

const gdClass = (gd) => {
  if (gd > 0) return 'text-success'
  if (gd < 0) return 'text-danger'
  return 'text-gray-400'
}
</script>
