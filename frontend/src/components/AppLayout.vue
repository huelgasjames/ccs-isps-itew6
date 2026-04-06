<template>
  <div class="app-layout">
    <!-- Header -->
    <AppHeader />

    <!-- Navigation Bar -->
    <AppNavBar />

    <!-- Main Content -->
    <main class="main-content">
      <slot></slot>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
import AppHeader from './layout/AppHeader.vue'
import AppNavBar from './layout/AppNavBar.vue'
import AppFooter from './layout/AppFooter.vue'
</script>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #ffffff;
  transition: background-color 0.3s;
}

.dark .app-layout {
  background: #0f172a;
}

.main-content {
  flex: 1;
  margin-left: 280px;
  margin-top: 64px;
  padding: 1.5rem;
  min-height: calc(100vh - 64px);
  transition: margin-left 0.3s ease, padding 0.3s ease;
}

/* Responsive */
@media (max-width: 1024px) {
  .main-content {
    margin-left: 260px;
    padding: 1.25rem;
  }
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 1rem;
    min-height: calc(100vh - 64px);
  }
}

@media (max-width: 640px) {
  .main-content {
    margin-top: 60px;
    padding: 0.75rem;
    min-height: calc(100vh - 60px);
  }
}

@media (max-width: 480px) {
  .main-content {
    padding: 0.5rem;
  }
}
</style>
