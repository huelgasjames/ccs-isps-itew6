<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">
      {{ isEdit ? 'Edit Room' : 'Create Room' }}
    </h1>
    
    <div class="bg-white rounded-lg shadow p-6">
      <form class="space-y-4" @submit.prevent="submit">
        <input v-model="form.name" class="w-full border rounded px-3 py-2" placeholder="Room Name" required />
        <input v-model="form.building" class="w-full border rounded px-3 py-2" placeholder="Building" />
        <input v-model.number="form.floor" type="number" min="0" class="w-full border rounded px-3 py-2" placeholder="Floor" />
        <select v-model="form.room_type" class="w-full border rounded px-3 py-2" required>
          <option value="lecture">lecture</option>
          <option value="lab">lab</option>
          <option value="computer_lab">computer_lab</option>
          <option value="multimedia">multimedia</option>
          <option value="other">other</option>
        </select>
        <input v-model.number="form.capacity" type="number" min="1" class="w-full border rounded px-3 py-2" placeholder="Capacity" required />
        <select v-model="form.status" class="w-full border rounded px-3 py-2">
          <option value="active">active</option>
          <option value="inactive">inactive</option>
        </select>
        <textarea v-model="form.notes" class="w-full border rounded px-3 py-2" placeholder="Notes"></textarea>
        <div class="space-x-2">
          <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" type="submit">
            {{ isEdit ? 'Update' : 'Create' }}
          </button>
          <router-link to="/rooms" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 inline-block">
            Back to Rooms
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRoomStore } from '@/stores/room'
import type { RoomFormData } from '@/types/room'

const route = useRoute()
const router = useRouter()
const roomStore = useRoomStore()
const isEdit = computed(() => !!route.params.id)

const form = ref<RoomFormData>({
  name: '',
  building: '',
  floor: null,
  room_type: 'lecture',
  capacity: 40,
  status: 'active',
  notes: ''
})

const submit = async () => {
  const ok = isEdit.value
    ? await roomStore.updateRoom(Number(route.params.id), form.value)
    : await roomStore.createRoom(form.value)
  if (ok) router.push('/rooms')
}

onMounted(async () => {
  if (!isEdit.value) return
  const room = await roomStore.fetchRoom(Number(route.params.id))
  if (room) {
    form.value = {
      name: room.name,
      building: room.building || '',
      floor: room.floor ?? null,
      room_type: room.room_type,
      capacity: room.capacity,
      status: room.status,
      notes: room.notes || ''
    }
  }
})
</script>
