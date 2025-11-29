<template>
  <div v-if="predictions.length > 0" class="bg-white/5 border border-white/10 rounded-2xl p-6">
    <h2 class="text-lg font-semibold mb-4 text-primary">🔮 Championship Predictions</h2>

    <div class="space-y-4">
      <div
        v-for="prediction in predictions"
        :key="prediction.team_id"
        class="space-y-2"
      >
        <div class="flex items-center gap-2">
          <TeamLogo :name="prediction.team_name" size="sm" />
          <span class="font-medium text-sm flex-1">{{ prediction.team_name }}</span>
          <span class="text-xs text-gray-500">{{ prediction.current_points }} pts</span>
        </div>
        <div class="relative h-6 bg-black/30 rounded-full overflow-hidden">
          <div
            class="h-full bg-gradient-to-r from-primary to-accent rounded-full transition-all duration-500"
            :style="{ width: prediction.prediction_percentage + '%' }"
          ></div>
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-white drop-shadow">
            {{ prediction.prediction_percentage }}%
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import TeamLogo from './TeamLogo.vue'

defineProps({
  predictions: {
    type: Array,
    required: true
  }
})
</script>
