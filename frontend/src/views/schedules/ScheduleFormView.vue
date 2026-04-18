<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Schedule' : 'Create Schedule' }}
    </h1>
    
    <div class="bg-white rounded-lg shadow p-6">
      <p class="text-gray-600">Schedule form coming soon...</p>
      <router-link to="/schedules" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
        Back to Schedules
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const isEdit = !!route.params.id
</script>
