<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Syllabi</h1>
      <router-link 
        v-if="authStore.isAdmin || authStore.isProfessor" 
        to="/syllabi/create" 
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Create Syllabus
      </router-link>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
      <p class="text-gray-600">Syllabi management coming soon...</p>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
</script>
