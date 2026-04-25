import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import type { Schedule, ScheduleFormData } from '@/types/schedule'

export const useScheduleStore = defineStore('schedule', () => {
  const schedules = ref<Schedule[]>([])
  const loading = ref(false)
  const error = ref('')

  const fetchSchedules = async () => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.get('/schedules')
      schedules.value = Array.isArray(response.data) ? response.data : []
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch schedules'
    } finally {
      loading.value = false
    }
  }

  const fetchSchedule = async (id: number): Promise<Schedule | null> => {
    try {
      const response = await api.get(`/schedules/${id}`)
      return response.data
    } catch {
      return null
    }
  }

  const createSchedule = async (payload: ScheduleFormData): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.post('/schedules', payload)
      schedules.value.push(response.data)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create schedule'
      return false
    } finally {
      loading.value = false
    }
  }

  const updateSchedule = async (id: number, payload: ScheduleFormData): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.put(`/schedules/${id}`, payload)
      const idx = schedules.value.findIndex(s => s.id === id)
      if (idx !== -1) schedules.value[idx] = response.data
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update schedule'
      return false
    } finally {
      loading.value = false
    }
  }

  const deleteSchedule = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      await api.delete(`/schedules/${id}`)
      schedules.value = schedules.value.filter(s => s.id !== id)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete schedule'
      return false
    } finally {
      loading.value = false
    }
  }

  return { schedules, loading, error, fetchSchedules, fetchSchedule, createSchedule, updateSchedule, deleteSchedule }
})
