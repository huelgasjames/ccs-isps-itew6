<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Rooms</h1>
      <router-link 
        v-if="authStore.isAdmin" 
        to="/rooms/create" 
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Create Room
      </router-link>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <p v-if="roomStore.loading" class="text-gray-600">Loading rooms...</p>
      <p v-else-if="roomStore.error" class="text-red-600">{{ roomStore.error }}</p>
      <table v-else class="w-full text-left">
        <thead>
          <tr class="border-b">
            <th class="py-2">Name</th>
            <th class="py-2">Building</th>
            <th class="py-2">Type</th>
            <th class="py-2">Capacity</th>
            <th class="py-2">Status</th>
            <th v-if="authStore.isAdmin" class="py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="room in roomStore.rooms" :key="room.id" class="border-b">
            <td class="py-2">{{ room.name }}</td>
            <td class="py-2">{{ room.building || '-' }}</td>
            <td class="py-2">{{ room.room_type }}</td>
            <td class="py-2">{{ room.capacity }}</td>
            <td class="py-2">{{ room.status }}</td>
            <td v-if="authStore.isAdmin" class="py-2 space-x-2">
              <router-link :to="`/rooms/${room.id}/edit`" class="text-blue-600">Edit</router-link>
              <button class="text-red-600" @click="removeRoom(room.id)">Delete</button>
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
import { useRoomStore } from '@/stores/room'

const authStore = useAuthStore()
const roomStore = useRoomStore()

const removeRoom = async (id: number) => {
  if (!confirm('Delete this room?')) return
  await roomStore.deleteRoom(id)
}

onMounted(() => {
  roomStore.fetchRooms()
})
</script>
