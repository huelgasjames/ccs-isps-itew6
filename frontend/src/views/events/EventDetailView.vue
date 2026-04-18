<template>
  <div class="p-6">
    <div v-if="loading" class="text-center">Loading...</div>
    
    <div v-else-if="event" class="max-w-4xl">
      <div class="flex justify-between items-start mb-6">
        <div>
          <h1 class="text-3xl font-bold">{{ event.title }}</h1>
          <p class="text-xl text-gray-600">{{ event.type }}</p>
        </div>
        <router-link 
          v-if="authStore.isAdmin"
          :to="`/events/${event.id}/edit`" 
          class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
        >
          Edit
        </router-link>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Event Details</h3>
            <div class="space-y-2">
              <p><strong>Type:</strong> {{ event.type }}</p>
              <p><strong>Status:</strong> 
                <span :class="getStatusClass(event.status)">
                  {{ event.status }}
                </span>
              </p>
              <p><strong>Start:</strong> {{ formatDateTime(event.start_datetime) }}</p>
              <p><strong>End:</strong> {{ formatDateTime(event.end_datetime) }}</p>
              <p><strong>Venue:</strong> {{ event.venue }}</p>
              <p><strong>Organizer:</strong> {{ event.organizer }}</p>
            </div>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Registration Info</h3>
            <div class="space-y-2">
              <p><strong>Target Audience:</strong> {{ event.target_audience }}</p>
              <p v-if="event.target_audience_specification">
                <strong>Specification:</strong> {{ event.target_audience_specification }}
              </p>
              <p><strong>Max Participants:</strong> {{ event.max_participants || 'Unlimited' }}</p>
              <p><strong>Current Participants:</strong> {{ event.current_participants }}</p>
              <p><strong>Registration Fee:</strong> ₱{{ event.registration_fee }}</p>
              <p v-if="authStore.isStudent && event.status === 'upcoming'" class="mt-3">
                <button 
                  @click="handleRegistration" 
                  :disabled="isRegistering"
                  class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50"
                >
                  {{ isRegistering ? 'Processing...' : 'Register' }}
                </button>
              </p>
            </div>
          </div>
        </div>
        
        <div v-if="event.description" class="mb-6">
          <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
          <p class="text-gray-600">{{ event.description }}</p>
        </div>
        
        <div v-if="event.requirements" class="mb-6">
          <h3 class="font-semibold text-gray-700 mb-2">Requirements</h3>
          <p class="text-gray-600">{{ event.requirements }}</p>
        </div>
        
        <div v-if="event.registrations && event.registrations.length > 0" class="mb-6">
          <h3 class="font-semibold text-gray-700 mb-3">Registrations</h3>
          <div class="space-y-2">
            <div 
              v-for="registration in event.registrations" 
              :key="registration.id"
              class="border rounded p-3"
            >
              <p class="font-medium">{{ registration.student?.name }}</p>
              <p class="text-sm text-gray-600">
                Registered: {{ formatDate(registration.registration_time) }} | 
                Payment: {{ registration.payment_completed ? 'Paid' : 'Pending' }}
              </p>
            </div>
          </div>
        </div>
        
        <div class="mt-6">
          <router-link 
            to="/events" 
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
          >
            Back to Events
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route = useRoute()
const authStore = useAuthStore()
const event = ref(null)
const loading = ref(true)
const isRegistering = ref(false)

const fetchEvent = async () => {
  try {
    const response = await api.get(`/events/${route.params.id}`)
    event.value = response.data
  } catch (error) {
    console.error('Error fetching event:', error)
  } finally {
    loading.value = false
  }
}

const handleRegistration = async () => {
  isRegistering.value = true
  try {
    await api.post(`/events/${event.value.id}/register`)
    await fetchEvent() // Refresh event data
  } catch (error) {
    console.error('Error registering for event:', error)
  } finally {
    isRegistering.value = false
  }
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const getStatusClass = (status) => {
  const classes = {
    upcoming: 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800',
    ongoing: 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800',
    completed: 'px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800',
    cancelled: 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800'
  }
  return classes[status] || ''
}

onMounted(fetchEvent)
</script>
