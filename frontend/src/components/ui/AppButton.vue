<template>
  <button
    :class="buttonClasses"
    :disabled="disabled || loading"
    @click="$emit('click')"
  >
    <span v-if="loading">{{ loadingText }}</span>
    <slot v-else />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'danger'].includes(v)
  },
  disabled: Boolean,
  loading: Boolean,
  loadingText: {
    type: String,
    default: 'Loading...'
  }
})

defineEmits(['click'])

const buttonClasses = computed(() => {
  const base = 'font-bold py-2.5 px-5 rounded-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed'

  const variants = {
    primary: 'bg-gradient-to-r from-primary to-accent text-white hover:scale-105 hover:shadow-lg hover:shadow-accent/40 disabled:hover:scale-100',
    secondary: 'bg-white/10 text-white border border-white/20 hover:-translate-y-0.5',
    danger: 'bg-danger/20 text-danger border border-danger/30 hover:-translate-y-0.5'
  }

  return `${base} ${variants[props.variant]}`
})
</script>

