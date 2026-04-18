<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Room' : 'Create Room' }}
    </h1>
    
    <div class="bg-white rounded-lg shadow p-6">
      <p class="text-gray-600">Room form coming soon...</p>
      <router-link to="/rooms" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
        Back to Rooms
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const isEdit = !!route.params.id
</script>
