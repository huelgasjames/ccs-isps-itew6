import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import type { Room, RoomFormData } from '@/types/room'

export const useRoomStore = defineStore('room', () => {
  const rooms = ref<Room[]>([])
  const loading = ref(false)
  const error = ref('')

  const fetchRooms = async () => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.get('/rooms')
      rooms.value = Array.isArray(response.data) ? response.data : []
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch rooms'
    } finally {
      loading.value = false
    }
  }

  const fetchRoom = async (id: number): Promise<Room | null> => {
    try {
      const response = await api.get(`/rooms/${id}`)
      return response.data
    } catch {
      return null
    }
  }

  const createRoom = async (payload: RoomFormData): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.post('/rooms', payload)
      rooms.value.push(response.data)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create room'
      return false
    } finally {
      loading.value = false
    }
  }

  const updateRoom = async (id: number, payload: RoomFormData): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.put(`/rooms/${id}`, payload)
      const idx = rooms.value.findIndex(r => r.id === id)
      if (idx !== -1) rooms.value[idx] = response.data
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update room'
      return false
    } finally {
      loading.value = false
    }
  }

  const deleteRoom = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      await api.delete(`/rooms/${id}`)
      rooms.value = rooms.value.filter(r => r.id !== id)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete room'
      return false
    } finally {
      loading.value = false
    }
  }

  return { rooms, loading, error, fetchRooms, fetchRoom, createRoom, updateRoom, deleteRoom }
})
