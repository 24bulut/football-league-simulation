<template>
  <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center hover:-translate-y-1 hover:border-primary hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 cursor-pointer">
    <img
      :src="logoSrc"
      :alt="team.name"
      class="w-20 h-20 mx-auto mb-3 object-contain"
    />
    <h3 class="text-lg font-medium text-white">{{ team.name }}</h3>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Import logos
import arsenalLogo from '@/assets/arsenal.png'
import chelseaLogo from '@/assets/chelsea.png'
import liverpoolLogo from '@/assets/liverpool.png'
import manchesterCityLogo from '@/assets/manchester-city.png'

const props = defineProps({
  team: {
    type: Object,
    required: true
  }
})

const logoMap = {
  'Arsenal': arsenalLogo,
  'Chelsea': chelseaLogo,
  'Liverpool': liverpoolLogo,
  'Manchester City': manchesterCityLogo
}

const logoSrc = computed(() => logoMap[props.team.name] || '')
</script>
