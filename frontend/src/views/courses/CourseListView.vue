<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Courses</h1>
      <router-link 
        v-if="authStore.isAdmin" 
        to="/courses/create" 
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Create Course
      </router-link>
    </div>
    
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b">
        <input 
          v-model="search" 
          type="text" 
          placeholder="Search courses..." 
          class="w-full p-2 border rounded"
        >
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Code</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="course in courses" :key="course.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ course.course_code }}</td>
              <td class="px-6 py-4">{{ course.course_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ course.credits }}</td>
              <td class="px-6 py-4">{{ course.department }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ course.level }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link 
                  :to="`/courses/${course.id}`" 
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  View
                </router-link>
                <router-link 
                  v-if="authStore.isAdmin"
                  :to="`/courses/${course.id}/edit`" 
                  class="text-yellow-600 hover:text-yellow-900"
                >
                  Edit
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()
const courses = ref([])
const search = ref('')

const fetchCourses = async () => {
  try {
    const response = await api.get('/courses', {
      params: { search: search.value }
    })
    courses.value = response.data
  } catch (error) {
    console.error('Error fetching courses:', error)
  }
}

onMounted(fetchCourses)

// Watch search changes
const searchCourses = () => {
  fetchCourses()
}
</script>
