<script setup lang="ts">
import { RouterView, useRoute } from 'vue-router'
import { computed } from 'vue'
import AppLayout from '@/components/AppLayout.vue'

const route = useRoute()

// Check if the current route should use the layout (exclude login and other auth pages)
const shouldUseLayout = computed(() => {
  const noLayoutRoutes = ['/login', '/register', '/forgot-password']
  return !noLayoutRoutes.includes(route.path)
})
</script>

<template>
  <AppLayout v-if="shouldUseLayout">
    <RouterView />
  </AppLayout>
  <RouterView v-else />
</template>

<style scoped>
/* No styles needed - using component styles */
</style>
