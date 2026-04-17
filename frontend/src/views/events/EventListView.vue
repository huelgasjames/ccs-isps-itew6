<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Events</h1>
      <router-link 
        v-if="authStore.isAdmin" 
        to="/events/create" 
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Create Event
      </router-link>
    </div>
    
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b">
        <div class="grid grid-cols-3 gap-4">
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search events..." 
            class="p-2 border rounded"
          >
          <select v-model="typeFilter" class="p-2 border rounded">
            <option value="">All Types</option>
            <option value="academic">Academic</option>
            <option value="sports">Sports</option>
            <option value="cultural">Cultural</option>
            <option value="seminar">Seminar</option>
            <option value="workshop">Workshop</option>
          </select>
          <select v-model="statusFilter" class="p-2 border rounded">
            <option value="">All Status</option>
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="event in events" :key="event.id">
              <td class="px-6 py-4 font-medium">{{ event.title }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                  {{ event.type }}
                </span>
              </td>
              <td class="px-6 py-4">{{ formatDate(event.start_datetime) }}</td>
              <td class="px-6 py-4">{{ event.venue }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(event.status)">
                  {{ event.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link 
                  :to="`/events/${event.id}`" 
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  View
                </router-link>
                <router-link 
                  v-if="authStore.isAdmin"
                  :to="`/events/${event.id}/edit`" 
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
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()
const events = ref([])
const search = ref('')
const typeFilter = ref('')
const statusFilter = ref('')

const fetchEvents = async () => {
  try {
    const params = {}
    if (search.value) params.search = search.value
    if (typeFilter.value) params.type = typeFilter.value
    if (statusFilter.value) params.status = statusFilter.value
    
    const response = await api.get('/events', { params })
    events.value = response.data
  } catch (error) {
    console.error('Error fetching events:', error)
  }
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

onMounted(fetchEvents)

watch([search, typeFilter, statusFilter], fetchEvents)
</script>
