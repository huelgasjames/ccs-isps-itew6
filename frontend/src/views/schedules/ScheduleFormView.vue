<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Schedule' : 'Create Schedule' }}
    </h1>
    
    <div class="bg-white rounded-lg shadow p-6">
      <form class="space-y-3" @submit.prevent="submit">
        <input v-model.number="form.course_id" type="number" min="1" class="w-full border rounded px-3 py-2" placeholder="Course ID" required />
        <input v-model.number="form.professor_id" type="number" min="1" class="w-full border rounded px-3 py-2" placeholder="Professor ID" required />
        <input v-model="form.section" class="w-full border rounded px-3 py-2" placeholder="Section" required />
        <input v-model="form.room" class="w-full border rounded px-3 py-2" placeholder="Room" required />
        <select v-model="form.room_type" class="w-full border rounded px-3 py-2" required>
          <option value="lecture">lecture</option><option value="lab">lab</option><option value="computer_lab">computer_lab</option><option value="multimedia">multimedia</option>
        </select>
        <select v-model="form.day_of_week" class="w-full border rounded px-3 py-2" required>
          <option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option>
        </select>
        <div class="grid grid-cols-2 gap-2">
          <input v-model="form.start_time" type="time" class="border rounded px-3 py-2" required />
          <input v-model="form.end_time" type="time" class="border rounded px-3 py-2" required />
        </div>
        <input v-model="form.academic_year" class="w-full border rounded px-3 py-2" placeholder="Academic Year" required />
        <select v-model="form.semester" class="w-full border rounded px-3 py-2" required>
          <option>First Semester</option><option>Second Semester</option><option>Summer</option>
        </select>
        <input v-model.number="form.max_capacity" type="number" min="1" class="w-full border rounded px-3 py-2" placeholder="Max Capacity" required />
        <input v-model.number="form.current_enrollment" type="number" min="0" class="w-full border rounded px-3 py-2" placeholder="Current Enrollment" />
        <select v-model="form.status" class="w-full border rounded px-3 py-2" required>
          <option>active</option><option>inactive</option><option>cancelled</option>
        </select>
        <textarea v-model="form.notes" class="w-full border rounded px-3 py-2" placeholder="Notes"></textarea>
        <div class="space-x-2">
          <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" type="submit">{{ isEdit ? 'Update' : 'Create' }}</button>
          <router-link to="/schedules" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 inline-block">Back to Schedules</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useScheduleStore } from '@/stores/schedule'
import type { ScheduleFormData } from '@/types/schedule'

const route = useRoute()
const router = useRouter()
const scheduleStore = useScheduleStore()
const isEdit = computed(() => !!route.params.id)

const form = ref<ScheduleFormData>({
  course_id: 1,
  professor_id: 1,
  section: '',
  room: '',
  room_type: 'lecture',
  day_of_week: 'Monday',
  start_time: '08:00',
  end_time: '09:00',
  academic_year: '2025-2026',
  semester: 'First Semester',
  max_capacity: 40,
  current_enrollment: 0,
  status: 'active',
  notes: ''
})

const submit = async () => {
  const ok = isEdit.value
    ? await scheduleStore.updateSchedule(Number(route.params.id), form.value)
    : await scheduleStore.createSchedule(form.value)
  if (ok) router.push('/schedules')
}

onMounted(async () => {
  if (!isEdit.value) return
  const data = await scheduleStore.fetchSchedule(Number(route.params.id))
  if (data) form.value = { ...data }
})
</script>
