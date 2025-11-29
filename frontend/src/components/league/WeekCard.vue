<template>
  <div class="bg-white/5 border border-white/10 rounded-xl p-4">
    <h3 class="flex items-center gap-2 text-base font-semibold mb-3 text-primary">
      Week {{ week.week }}
      <span v-if="isPlayed" class="text-success text-sm">✓</span>
      <span v-else class="text-warning text-sm">⏳</span>
    </h3>

    <div class="space-y-2">
      <MatchRow
        v-for="match in week.matches"
        :key="match.id"
        :match="match"
        :is-editing="editingMatchId === match.id"
        :home-score="homeScore"
        :away-score="awayScore"
        :saving="saving"
        @edit="$emit('editMatch', $event)"
        @save="$emit('saveMatch', match.id)"
        @cancel="$emit('cancelEdit')"
        @update:home-score="$emit('update:homeScore', $event)"
        @update:away-score="$emit('update:awayScore', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import MatchRow from './MatchRow.vue'

const props = defineProps({
  week: {
    type: Object,
    required: true
  },
  editingMatchId: Number,
  homeScore: Number,
  awayScore: Number,
  saving: Boolean
})

defineEmits(['editMatch', 'saveMatch', 'cancelEdit', 'update:homeScore', 'update:awayScore'])

const isPlayed = computed(() => props.week.matches.every(m => m.status === 'played'))
</script>

