<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Schedules</h1>
      <router-link 
        v-if="authStore.isAdmin" 
        to="/schedules/create" 
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Create Schedule
      </router-link>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
      <p v-if="scheduleStore.loading" class="text-gray-600">Loading schedules...</p>
      <p v-else-if="scheduleStore.error" class="text-red-600">{{ scheduleStore.error }}</p>
      <table v-else class="w-full text-left">
        <thead>
          <tr class="border-b">
            <th class="py-2">Course</th>
            <th class="py-2">Section</th>
            <th class="py-2">Room</th>
            <th class="py-2">Day</th>
            <th class="py-2">Time</th>
            <th class="py-2">Status</th>
            <th v-if="authStore.isAdmin" class="py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="schedule in scheduleStore.schedules" :key="schedule.id" class="border-b">
            <td class="py-2">{{ schedule.course?.course_code || schedule.course_id }}</td>
            <td class="py-2">{{ schedule.section }}</td>
            <td class="py-2">{{ schedule.room }}</td>
            <td class="py-2">{{ schedule.day_of_week }}</td>
            <td class="py-2">{{ schedule.start_time }} - {{ schedule.end_time }}</td>
            <td class="py-2">{{ schedule.status }}</td>
            <td v-if="authStore.isAdmin" class="py-2 space-x-2">
              <router-link :to="`/schedules/${schedule.id}/edit`" class="text-blue-600">Edit</router-link>
              <button class="text-red-600" @click="removeSchedule(schedule.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useScheduleStore } from '@/stores/schedule'

const authStore = useAuthStore()
const scheduleStore = useScheduleStore()

const removeSchedule = async (id: number) => {
  if (!confirm('Delete this schedule?')) return
  await scheduleStore.deleteSchedule(id)
}

onMounted(() => {
  scheduleStore.fetchSchedules()
})
</script>
