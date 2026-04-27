import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Room } from '@/types/room'
import api from '@/services/api'

export const useRoomStore = defineStore('room', () => {
  // State
  const rooms = ref<Room[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref({
    currentPage: 1,
    lastPage: 1,
    perPage: 15,
    total: 0,
    from: 0,
    to: 0
  })

  // Filter state
  const filter = ref({
    search: '',
    type: '',
    building: '',
    status: '',
    min_capacity: '',
    max_capacity: ''
  })

  // Computed properties
  const availableTypes = computed(() => [
    { value: 'lecture', label: 'Lecture Hall' },
    { value: 'lab', label: 'Laboratory' },
    { value: 'computer_lab', label: 'Computer Lab' },
    { value: 'multimedia', label: 'Multimedia Room' },
    { value: 'conference', label: 'Conference Room' }
  ])

  const availableStatuses = computed(() => [
    { value: 'available', label: 'Available' },
    { value: 'maintenance', label: 'Under Maintenance' },
    { value: 'occupied', label: 'Occupied' },
    { value: 'unavailable', label: 'Unavailable' }
  ])

  const availableBuildings = computed(() => {
    const buildings = [...new Set(rooms.value.map(room => room.building).filter(Boolean))]
    return buildings.sort()
  })

  const filteredRooms = computed(() => {
    let filtered = rooms.value

    if (filter.value.search) {
      const search = filter.value.search.toLowerCase()
      filtered = filtered.filter(room => 
        room.name.toLowerCase().includes(search) ||
        room.room_code.toLowerCase().includes(search) ||
        room.building?.toLowerCase().includes(search)
      )
    }

    if (filter.value.type) {
      filtered = filtered.filter(room => room.type === filter.value.type)
    }

    if (filter.value.building) {
      filtered = filtered.filter(room => room.building === filter.value.building)
    }

    if (filter.value.status) {
      filtered = filtered.filter(room => room.status === filter.value.status)
    }

    if (filter.value.min_capacity) {
      filtered = filtered.filter(room => room.capacity >= parseInt(filter.value.min_capacity))
    }

    if (filter.value.max_capacity) {
      filtered = filtered.filter(room => room.capacity <= parseInt(filter.value.max_capacity))
    }

    return filtered
  })

  const statistics = computed(() => {
    const totalRooms = rooms.value.length
    const availableRooms = rooms.value.filter(room => room.status === 'available').length
    const maintenanceRooms = rooms.value.filter(room => room.status === 'maintenance').length
    const occupiedRooms = rooms.value.filter(room => room.status === 'occupied').length
    
    const roomsByType = availableTypes.value.map(type => ({
      type: type.value,
      label: type.label,
      count: rooms.value.filter(room => room.type === type.value).length
    }))

    const roomsByBuilding = availableBuildings.value.map(building => ({
      building,
      count: rooms.value.filter(room => room.building === building).length
    }))

    const totalCapacity = rooms.value.reduce((sum, room) => sum + room.capacity, 0)
    const avgCapacity = totalCapacity / totalRooms || 0

    return {
      totalRooms,
      availableRooms,
      maintenanceRooms,
      occupiedRooms,
      roomsByType,
      roomsByBuilding,
      totalCapacity,
      averageCapacity: Math.round(avgCapacity)
    }
  })

  // Actions
  const fetchRooms = async (page = 1) => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: pagination.value.perPage.toString(),
        ...Object.fromEntries(Object.entries(filter.value).filter(([_, value]) => value))
      })

      const response = await api.get(`/rooms?${params}`)
      
      rooms.value = response.data.data
      pagination.value = response.data.pagination
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch rooms'
      console.error('Fetch rooms error:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchRoom = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get(`/rooms/${id}`)
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch room'
      console.error('Fetch room error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const createRoom = async (roomData: Partial<Room>) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post('/rooms', roomData)
      
      // Add new room to the list
      if (response.data.success) {
        rooms.value.unshift(response.data.data)
      }
      
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create room'
      console.error('Create room error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateRoom = async (id: number, roomData: Partial<Room>) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.put(`/rooms/${id}`, roomData)
      
      // Update room in the list
      if (response.data.success) {
        const index = rooms.value.findIndex(room => room.id === id)
        if (index !== -1) {
          rooms.value[index] = { ...rooms.value[index], ...response.data.data }
        }
      }
      
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update room'
      console.error('Update room error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteRoom = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.delete(`/rooms/${id}`)
      
      // Remove room from the list
      if (response.data.success) {
        rooms.value = rooms.value.filter(room => room.id !== id)
      }
      
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete room'
      console.error('Delete room error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchStatistics = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get('/rooms/statistics')
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch statistics'
      console.error('Fetch statistics error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchAvailableRooms = async (filters: any = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams(filters)
      const response = await api.get(`/rooms/available?${params}`)
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch available rooms'
      console.error('Fetch available rooms error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const resetFilter = () => {
    filter.value = {
      search: '',
      type: '',
      building: '',
      status: '',
      min_capacity: '',
      max_capacity: ''
    }
  }

  const resetPagination = () => {
    pagination.value = {
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0,
      from: 0,
      to: 0
    }
  }

  return {
    // State
    rooms,
    loading,
    error,
    pagination,
    filter,
    
    // Computed
    availableTypes,
    availableStatuses,
    availableBuildings,
    filteredRooms,
    statistics,
    
    // Actions
    fetchRooms,
    fetchRoom,
    createRoom,
    updateRoom,
    deleteRoom,
    fetchStatistics,
    fetchAvailableRooms,
    resetFilter,
    resetPagination
  }
})
