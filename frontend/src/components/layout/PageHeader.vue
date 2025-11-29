<template>
  <header class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
      {{ title }}
    </h1>
    <router-link v-if="backLink" :to="backLink" class="text-primary hover:opacity-80 transition-opacity text-sm">
      ← {{ backText }}
    </router-link>
  </header>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  backLink: String,
  backText: {
    type: String,
    default: 'Back'
  }
})
</script>
