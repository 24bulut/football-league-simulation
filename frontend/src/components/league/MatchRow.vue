<template>
  <div
    class="flex items-center bg-black/20 rounded-lg p-2 text-sm transition-all"
    :class="{ 'bg-primary/10 border border-primary/30': isEditing }"
  >
    <!-- Normal View -->
    <template v-if="!isEditing">
      <div class="flex-1 flex items-center justify-end gap-2">
        <span class="text-white truncate">{{ match.home_team.name }}</span>
        <TeamLogo :name="match.home_team.name" size="sm" />
      </div>

      <div
        class="flex items-center justify-center min-w-[70px] gap-1 cursor-pointer px-2 py-1 rounded hover:bg-white/10 transition-colors group relative"
        @click="$emit('edit', match)"
      >
        <template v-if="match.status === 'played'">
          <span class="text-base font-bold text-primary">{{ match.home_score }}</span>
          <span class="text-gray-600">-</span>
          <span class="text-base font-bold text-primary">{{ match.away_score }}</span>
        </template>
        <template v-else>
          <span class="text-gray-600 text-xs">vs</span>
        </template>
        <span class="absolute -right-4 text-xs opacity-0 group-hover:opacity-100 transition-opacity">✏️</span>
      </div>

      <div class="flex-1 flex items-center gap-2">
        <TeamLogo :name="match.away_team.name" size="sm" />
        <span class="text-white truncate">{{ match.away_team.name }}</span>
      </div>
    </template>

    <!-- Edit Mode -->
    <template v-else>
      <div class="flex-1 flex items-center justify-end gap-2">
        <span class="text-white truncate">{{ match.home_team.name }}</span>
        <TeamLogo :name="match.home_team.name" size="sm" />
      </div>

      <div class="flex items-center gap-1 mx-2">
        <input
          type="number"
          :value="homeScore"
          @input="$emit('update:homeScore', Number($event.target.value))"
          min="0"
          max="20"
          class="w-10 p-1 text-center bg-black/30 border border-primary/50 rounded text-primary font-bold focus:outline-none focus:border-primary"
          @keyup.enter="$emit('save')"
          @keyup.escape="$emit('cancel')"
        />
        <span class="text-gray-600">-</span>
        <input
          type="number"
          :value="awayScore"
          @input="$emit('update:awayScore', Number($event.target.value))"
          min="0"
          max="20"
          class="w-10 p-1 text-center bg-black/30 border border-primary/50 rounded text-primary font-bold focus:outline-none focus:border-primary"
          @keyup.enter="$emit('save')"
          @keyup.escape="$emit('cancel')"
        />
      </div>

      <div class="flex-1 flex items-center gap-2">
        <TeamLogo :name="match.away_team.name" size="sm" />
        <span class="text-white truncate">{{ match.away_team.name }}</span>
      </div>

      <div class="flex gap-1 ml-2">
        <button
          @click="$emit('save')"
          class="w-7 h-7 bg-success text-white rounded flex items-center justify-center hover:bg-success/80 disabled:opacity-50"
          :disabled="saving"
        >
          {{ saving ? '...' : '✓' }}
        </button>
        <button
          @click="$emit('cancel')"
          class="w-7 h-7 bg-danger/30 text-danger rounded flex items-center justify-center hover:bg-danger/50"
        >
          ✕
        </button>
      </div>
    </template>
  </div>
</template>

<script setup>
import TeamLogo from './TeamLogo.vue'

defineProps({
  match: {
    type: Object,
    required: true
  },
  isEditing: Boolean,
  homeScore: Number,
  awayScore: Number,
  saving: Boolean
})

defineEmits(['edit', 'save', 'cancel', 'update:homeScore', 'update:awayScore'])
</script>
