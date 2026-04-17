<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Event' : 'Create Event' }}
    </h1>
    
    <form @submit.prevent="submitForm" class="max-w-2xl">
      <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
          <input 
            v-model="form.title" 
            type="text" 
            required
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea 
            v-model="form.description" 
            rows="4"
            required
            class="w-full p-2 border rounded"
          ></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select 
              v-model="form.type" 
              required
              class="w-full p-2 border rounded"
            >
              <option value="academic">Academic</option>
              <option value="sports">Sports</option>
              <option value="cultural">Cultural</option>
              <option value="seminar">Seminar</option>
              <option value="workshop">Workshop</option>
              <option value="curricular">Curricular</option>
              <option value="extra_curricular">Extra Curricular</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              v-model="form.status" 
              required
              class="w-full p-2 border rounded"
            >
              <option value="upcoming">Upcoming</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
            <input 
              v-model="form.start_datetime" 
              type="datetime-local" 
              required
              class="w-full p-2 border rounded"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
            <input 
              v-model="form.end_datetime" 
              type="datetime-local" 
              required
              class="w-full p-2 border rounded"
            >
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
          <input 
            v-model="form.venue" 
            type="text" 
            required
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Organizer</label>
          <input 
            v-model="form.organizer" 
            type="text" 
            required
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
            <select 
              v-model="form.target_audience" 
              required
              class="w-full p-2 border rounded"
            >
              <option value="all_students">All Students</option>
              <option value="specific_year">Specific Year</option>
              <option value="specific_course">Specific Course</option>
              <option value="faculty">Faculty</option>
              <option value="all">All</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Max Participants</label>
            <input 
              v-model.number="form.max_participants" 
              type="number" 
              min="1"
              class="w-full p-2 border rounded"
            >
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Registration Fee</label>
          <input 
            v-model.number="form.registration_fee" 
            type="number" 
            min="0" 
            step="0.01"
            required
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience Specification</label>
          <input 
            v-model="form.target_audience_specification" 
            type="text"
            placeholder="e.g., 3rd Year Students Only"
            class="w-full p-2 border rounded"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
          <textarea 
            v-model="form.requirements" 
            rows="2"
            placeholder="e.g., Bring laptop, registration form, etc."
            class="w-full p-2 border rounded"
          ></textarea>
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
            to="/events" 
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
  title: '',
  description: '',
  type: 'academic',
  start_datetime: '',
  end_datetime: '',
  venue: '',
  organizer: '',
  target_audience: 'all_students',
  target_audience_specification: '',
  max_participants: null,
  registration_fee: 0,
  status: 'upcoming',
  poster_image: '',
  requirements: ''
})

const fetchEvent = async () => {
  try {
    const response = await api.get(`/events/${route.params.id}`)
    form.value = response.data
    // Format datetime for input fields
    form.value.start_datetime = new Date(response.data.start_datetime).toISOString().slice(0, 16)
    form.value.end_datetime = new Date(response.data.end_datetime).toISOString().slice(0, 16)
  } catch (error) {
    console.error('Error fetching event:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  try {
    if (isEdit) {
      await api.put(`/events/${route.params.id}`, form.value)
    } else {
      await api.post('/events', form.value)
    }
    router.push('/events')
  } catch (error) {
    console.error('Error saving event:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (isEdit) {
    fetchEvent()
  }
})
</script>
