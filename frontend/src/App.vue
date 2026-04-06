<script setup lang="ts">
<<<<<<< HEAD
import { RouterView, useRoute } from 'vue-router'
import { computed } from 'vue'
import AppLayout from '@/components/AppLayout.vue'

const route = useRoute()

// Check if the current route should use the layout (exclude login and other auth pages)
const shouldUseLayout = computed(() => {
  const noLayoutRoutes = ['/login', '/register', '/forgot-password']
  return !noLayoutRoutes.includes(route.path)
=======
import { RouterView } from 'vue-router'
import AppLayout from './components/AppLayout.vue'
import { useAuthStore } from '@/stores/auth'
import { onMounted } from 'vue'

const authStore = useAuthStore()

onMounted(() => {
  // Initialize auth state
  authStore.initialize()
>>>>>>> 25048deddd9a824e336580a4aceee5bd8dd08608
})
</script>

<template>
<<<<<<< HEAD
  <AppLayout v-if="shouldUseLayout">
=======
  <AppLayout v-if="authStore.isAuthenticated">
>>>>>>> 25048deddd9a824e336580a4aceee5bd8dd08608
    <RouterView />
  </AppLayout>
  <RouterView v-else />
</template>

<<<<<<< HEAD
<style scoped>
/* No styles needed - using component styles */
=======
<style>
/* Global styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Rajdhani', sans-serif;
  background: #ffffff;
  transition: background-color 0.3s;
}

.dark body {
  background: #0f172a;
}
>>>>>>> 25048deddd9a824e336580a4aceee5bd8dd08608
</style>
