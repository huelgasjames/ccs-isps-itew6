<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Syllabus' : 'Create Syllabus' }}
    </h1>
    
    <div class="bg-white rounded-lg shadow p-6">
      <p class="text-gray-600">Syllabus form coming soon...</p>
      <router-link to="/syllabi" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
        Back to Syllabi
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const isEdit = !!route.params.id
</script>
