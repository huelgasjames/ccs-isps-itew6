<template>
  <div class="p-6">
    <div v-if="loading" class="text-center">Loading...</div>
    
    <div v-else-if="course" class="max-w-4xl">
      <div class="flex justify-between items-start mb-6">
        <div>
          <h1 class="text-3xl font-bold">{{ course.course_code }}</h1>
          <p class="text-xl text-gray-600">{{ course.course_name }}</p>
        </div>
        <router-link 
          v-if="authStore.isAdmin"
          :to="`/courses/${course.id}/edit`" 
          class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
        >
          Edit
        </router-link>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-2 gap-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Course Information</h3>
            <div class="space-y-2">
              <p><strong>Credits:</strong> {{ course.credits }}</p>
              <p><strong>Department:</strong> {{ course.department }}</p>
              <p><strong>Level:</strong> {{ course.level }}</p>
              <p><strong>Semester:</strong> {{ course.semester }}</p>
              <p><strong>Status:</strong> 
                <span :class="course.active ? 'text-green-600' : 'text-red-600'">
                  {{ course.active ? 'Active' : 'Inactive' }}
                </span>
              </p>
            </div>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Additional Details</h3>
            <div class="space-y-2">
              <div v-if="course.prerequisites">
                <p><strong>Prerequisites:</strong></p>
                <p class="text-gray-600">{{ course.prerequisites }}</p>
              </div>
              <div v-if="course.description">
                <p><strong>Description:</strong></p>
                <p class="text-gray-600">{{ course.description }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div v-if="course.syllabi && course.syllabi.length > 0" class="mt-6">
          <h3 class="font-semibold text-gray-700 mb-3">Current Syllabi</h3>
          <div class="space-y-2">
            <div 
              v-for="syllabus in course.syllabi" 
              :key="syllabus.id"
              class="border rounded p-3"
            >
              <p class="font-medium">{{ syllabus.title }}</p>
              <p class="text-sm text-gray-600">
                Academic Year: {{ syllabus.academic_year }} | 
                Professor: {{ syllabus.professor?.name }}
              </p>
            </div>
          </div>
        </div>
        
        <div class="mt-6">
          <router-link 
            to="/courses" 
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
          >
            Back to Courses
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

interface Course {
  id?: number
  course_code?: string
  course_name?: string
  description?: string
  credits?: number
  department?: string
  level?: string
  semester?: string
  prerequisites?: string
  active?: boolean
  syllabi?: any[]
}

const route = useRoute()
const authStore = useAuthStore()
const course = ref<Course | null>(null)
const loading = ref(true)

const fetchCourse = async () => {
  try {
    const response = await api.get(`/courses/${route.params.id}`)
    course.value = response.data
  } catch (error) {
    console.error('Error fetching course:', error)
  } finally {
    loading.value = false
  }
}

onMounted(fetchCourse)
</script>
