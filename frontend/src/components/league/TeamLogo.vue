<template>
  <img
    :src="logoSrc"
    :alt="name"
    :class="sizeClasses"
    class="object-contain"
  />
</template>

<script setup>
import { computed } from 'vue'

// Import logos
import arsenalLogo from '@/assets/arsenal.png'
import chelseaLogo from '@/assets/chelsea.png'
import liverpoolLogo from '@/assets/liverpool.png'
import manchesterCityLogo from '@/assets/manchester-city.png'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  }
})

const logoMap = {
  'Arsenal': arsenalLogo,
  'Chelsea': chelseaLogo,
  'Liverpool': liverpoolLogo,
  'Manchester City': manchesterCityLogo
}

const logoSrc = computed(() => logoMap[props.name] || '')

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'w-5 h-5',
    md: 'w-8 h-8',
    lg: 'w-20 h-20'
  }
  return sizes[props.size]
})
</script>

