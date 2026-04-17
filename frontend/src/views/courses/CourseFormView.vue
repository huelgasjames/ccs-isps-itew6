<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Course' : 'Create Course' }}
    </h1>
    
    <form @submit.prevent="submitForm" class="max-w-2xl">
      <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Course Code</label>
          <input 
            v-model="form.course_code" 
            type="text" 
            required
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Course Name</label>
          <input 
            v-model="form.course_name" 
            type="text" 
            required
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea 
            v-model="form.description" 
            rows="3"
            class="w-full p-2 border rounded"
          ></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Credits</label>
            <input 
              v-model.number="form.credits" 
              type="number" 
              min="1" 
              max="12"
              required
              class="w-full p-2 border rounded"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <input 
              v-model="form.department" 
              type="text" 
              required
              class="w-full p-2 border rounded"
            >
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
            <select 
              v-model="form.level" 
              required
              class="w-full p-2 border rounded"
            >
              <option value="1st">1st Year</option>
              <option value="2nd">2nd Year</option>
              <option value="3rd">3rd Year</option>
              <option value="4th">4th Year</option>
              <option value="5th">5th Year</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
            <select 
              v-model="form.semester" 
              required
              class="w-full p-2 border rounded"
            >
              <option value="1st">1st Semester</option>
              <option value="2nd">2nd Semester</option>
              <option value="summer">Summer</option>
            </select>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Prerequisites</label>
          <input 
            v-model="form.prerequisites" 
            type="text"
            placeholder="e.g., CS101, MATH201"
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="flex items-center">
            <input 
              v-model="form.active" 
              type="checkbox"
              class="mr-2"
            >
            Active
          </label>
        </div>
        
        <div class="flex gap-3">
          <button 
            type="submit" 
            :disabled="loading"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
          >
            {{ loading ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
          </button>
          <router-link 
            to="/courses" 
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
          >
            Cancel
          </router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const isEdit = !!route.params.id
const loading = ref(false)

const form = ref({
  course_code: '',
  course_name: '',
  description: '',
  credits: 3,
  department: '',
  level: '1st',
  semester: '1st',
  prerequisites: '',
  active: true
})

const fetchCourse = async () => {
  try {
    const response = await api.get(`/courses/${route.params.id}`)
    form.value = response.data
  } catch (error) {
    console.error('Error fetching course:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  try {
    if (isEdit) {
      await api.put(`/courses/${route.params.id}`, form.value)
    } else {
      await api.post('/courses', form.value)
    }
    router.push('/courses')
  } catch (error) {
    console.error('Error saving course:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (isEdit) {
    fetchCourse()
  }
})
</script>
