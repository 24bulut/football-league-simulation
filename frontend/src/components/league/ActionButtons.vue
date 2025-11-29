<template>
  <div class="flex flex-wrap gap-3 mb-6">
    <AppButton
      variant="primary"
      :loading="simulating"
      loading-text="Simulating..."
      @click="$emit('playNext')"
    >
      ▶️ Play Next Week
    </AppButton>

    <AppButton
      variant="secondary"
      :loading="simulating"
      loading-text="Simulating..."
      @click="$emit('playAll')"
    >
      ⏩ Play All
    </AppButton>

    <AppButton
      variant="danger"
      @click="$emit('reset')"
    >
      🔄 Reset
    </AppButton>
  </div>
</template>

<script setup>
import AppButton from '../ui/AppButton.vue'

defineProps({
  simulating: Boolean
})

defineEmits(['playNext', 'playAll', 'reset'])
</script>

