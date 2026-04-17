import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { 
  Student, 
  StudentFilter, 
  StudentListResponse, 
  CreateStudentRequest, 
  UpdateStudentRequest,
  DisplayMode,
  StudentStatistics
} from '@/types/student'
import api, { apiWithFallback } from '@/services/api'
import { offlineService } from '@/services/offline'

export const useStudentStore = defineStore('student', () => {
  // State
  const students = ref<Student[]>([])
  const currentStudent = ref<Student | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const filter = ref<StudentFilter>({})
  const displayMode = ref<DisplayMode>('table')
  const currentPage = ref(1)
  const pageSize = ref(10)
  const totalStudents = ref(0)
  const totalPages = ref(0)
  const statistics = ref<StudentStatistics | null>(null)

  // Computed
  const filteredStudents = computed(() => {
    let filtered = [...students.value]

    // Text search
    if (filter.value.search) {
      const search = filter.value.search.toLowerCase()
      filtered = filtered.filter(student => 
        student.personalInfo.firstName.toLowerCase().includes(search) ||
        student.personalInfo.lastName.toLowerCase().includes(search) ||
        student.personalInfo.studentId.toLowerCase().includes(search) ||
        student.personalInfo.email.toLowerCase().includes(search)
      )
    }

    // Skills filter
    if (filter.value.skills && filter.value.skills.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.skills!.some(skillName =>
          student.skills.some(skill =>
            skill.name.toLowerCase().includes(skillName.toLowerCase())
          )
        )
      )
    }

    // Activities filter
    if (filter.value.activities && filter.value.activities.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.activities!.some(activityName =>
          student.activities.some(activity =>
            activity.name.toLowerCase().includes(activityName.toLowerCase())
          )
        )
      )
    }

    // Academic standing filter
    if (filter.value.academicStanding) {
      filtered = filtered.filter(student =>
        filter.value.academicStanding!.some(standing =>
          student.academicStanding.standing === standing
        )
      )
    }

    // Year level filter
    if (filter.value.yearLevel) {
      filtered = filtered.filter(student =>
        filter.value.yearLevel!.some(year =>
          student.academicStanding.currentYear === year
        )
      )
    }

    // GPA range filter
    if (filter.value.gpaRange) {
      filtered = filtered.filter(student =>
        student.academicStanding.currentGPA >= filter.value.gpaRange!.min &&
        student.academicStanding.currentGPA <= filter.value.gpaRange!.max
      )
    }

    return filtered
  })

  const paginatedStudents = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value
    const end = start + pageSize.value
    return filteredStudents.value.slice(start, end)
  })

  // Actions
  const fetchStudents = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get<StudentListResponse>('/students')
      students.value = response.data.students
      totalStudents.value = response.data.total
      totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
    } catch (err) {
      // If API is not available, return empty array for demo purposes
      if (err instanceof Error && (err.message.includes('Network Error') || err.message.includes('ERR_CONNECTION_REFUSED'))) {
        console.warn('Backend API not available. No students to display.')
        students.value = []
        totalStudents.value = 0
        totalPages.value = 0
        return
      }
      
      error.value = err instanceof Error ? err.message : 'Failed to fetch students'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Offline-enabled methods
  const fetchStudentsOffline = async () => {
    loading.value = true
    error.value = null
    
    try {
      // First try to get from localStorage
      let localStudents = offlineService.getLocalStudents()
      
      // If no local students exist, initialize dummy data
      if (localStudents.length === 0) {
        offlineService.initializeDummyData()
        localStudents = offlineService.getLocalStudents()
      }
      
      // Try API first, fallback to localStorage
      try {
        const response = await apiWithFallback.get('/students')
        const data = response.data
        students.value = data.students || data
        totalStudents.value = data.total || students.value.length
        totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      } catch (apiErr) {
        // API failed, use localStorage data
        console.log('API unavailable, using offline data:', apiErr)
        students.value = localStudents
        totalStudents.value = localStudents.length
        totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch students'
      // Still try to load local data as fallback
      const localStudents = offlineService.getLocalStudents()
      if (localStudents.length > 0) {
        students.value = localStudents
        totalStudents.value = localStudents.length
        totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
        error.value = null // Clear error since we have offline data
      }
    } finally {
      loading.value = false
    }
  }

  const fetchStudentById = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get<Student>(`/students/${id}`)
      currentStudent.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createStudent = async (studentData: CreateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post<Student>('/students', studentData)
      students.value.push(response.data)
      totalStudents.value += 1
      totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      return response.data
    } catch (err) {
      // If API is not available, create a mock student for demo purposes
      if (err instanceof Error && (err.message.includes('Network Error') || err.message.includes('ERR_CONNECTION_REFUSED'))) {
        console.warn('Backend API not available. Creating mock student for demo.')
        
        // Create a mock student with the provided data
        const mockStudent: Student = {
          id: Date.now(), // Use timestamp as ID
          ...studentData,
          academicHistory: [],
          activities: [],
          violations: [],
          skills: [],
          affiliations: [],
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
          isActive: true
        }
        
        students.value.push(mockStudent)
        totalStudents.value += 1
        totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
        
        return mockStudent
      }
      
      error.value = err instanceof Error ? err.message : 'Failed to create student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createStudentOffline = async (studentData: CreateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiWithFallback.post('/students', studentData)
      students.value.push(response.data)
      totalStudents.value += 1
      totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to create student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStudent = async (id: number, studentData: UpdateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.put<Student>(`/students/${id}`, studentData)
      const index = students.value.findIndex(student => student.id === id)
      if (index !== -1) {
        students.value[index] = response.data
      }
      if (currentStudent.value?.id === id) {
        currentStudent.value = response.data
      }
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to update student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteStudent = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await api.delete(`/students/${id}`)
      students.value = students.value.filter(student => student.id !== id)
      totalStudents.value -= 1
      totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      if (currentStudent.value?.id === id) {
        currentStudent.value = null
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to delete student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStudentOffline = async (id: number, studentData: UpdateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiWithFallback.put(`/students/${id}`, studentData)
      const index = students.value.findIndex(student => student.id === id)
      if (index !== -1) {
        students.value[index] = response.data
      }
      if (currentStudent.value?.id === id) {
        currentStudent.value = response.data
      }
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to update student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteStudentOffline = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await apiWithFallback.delete(`/students/${id}`)
      students.value = students.value.filter(student => student.id !== id)
      totalStudents.value -= 1
      totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      if (currentStudent.value?.id === id) {
        currentStudent.value = null
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to delete student'
      throw err
    } finally {
      loading.value = false
    }
  }

  const addSkill = async (studentId: number, skillData: any) => {
    try {
      const response = await api.post(`/students/${studentId}/skills`, skillData)
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills.push(response.data)
      }
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to add skill'
      throw err
    }
  }

  const removeSkill = async (studentId: number, skillId: number) => {
    try {
      await api.delete(`/students/${studentId}/skills/${skillId}`)
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills = student.skills.filter(skill => skill.id !== skillId)
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to remove skill'
      throw err
    }
  }

  const addActivity = async (studentId: number, activityData: any) => {
    try {
      const response = await api.post(`/students/${studentId}/activities`, activityData)
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.activities.push(response.data)
      }
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to add activity'
      throw err
    }
  }

  // Offline versions of skill and activity operations
  const addSkillOffline = async (studentId: number, skillData: any) => {
    try {
      const response = await apiWithFallback.post(`/students/${studentId}/skills`, skillData)
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills.push(response.data)
      }
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to add skill'
      throw err
    }
  }

  const removeSkillOffline = async (studentId: number, skillId: number) => {
    try {
      await apiWithFallback.delete(`/students/${studentId}/skills/${skillId}`)
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills = student.skills.filter(skill => skill.id !== skillId)
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to remove skill'
      throw err
    }
  }

  const addActivityOffline = async (studentId: number, activityData: any) => {
    try {
      const response = await apiWithFallback.post(`/students/${studentId}/activities`, activityData)
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.activities.push(response.data)
      }
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to add activity'
      throw err
    }
  }

  const fetchStatistics = async () => {
    try {
      const response = await api.get<StudentStatistics>('/students/statistics')
      statistics.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch statistics'
      throw err
    }
  }

  const fetchStatisticsOffline = async () => {
    try {
      const response = await apiWithFallback.get('/students/statistics')
      statistics.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch statistics'
      throw err
    }
  }

  const setFilter = (newFilter: Partial<StudentFilter>) => {
    filter.value = { ...filter.value, ...newFilter }
    currentPage.value = 1 // Reset to first page when filter changes
  }

  const clearFilter = () => {
    filter.value = {}
    currentPage.value = 1
  }

  const setDisplayMode = (mode: DisplayMode) => {
    displayMode.value = mode
  }

  const setCurrentPage = (page: number) => {
    currentPage.value = page
  }

  const clearCurrentStudent = () => {
    currentStudent.value = null
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    students,
    currentStudent,
    loading,
    error,
    filter,
    displayMode,
    currentPage,
    pageSize,
    totalStudents,
    totalPages,
    statistics,
    
    // Computed
    filteredStudents,
    paginatedStudents,
    
    // Actions
    fetchStudents,
    fetchStudentById,
    createStudent,
    updateStudent,
    deleteStudent,
    fetchStudentsOffline,
    createStudentOffline,
    updateStudentOffline,
    deleteStudentOffline,
    addSkill,
    removeSkill,
    addActivity,
    addSkillOffline,
    removeSkillOffline,
    addActivityOffline,
    fetchStatistics,
    fetchStatisticsOffline,
    setFilter,
    clearFilter,
    setDisplayMode,
    setCurrentPage,
    clearCurrentStudent,
    clearError
  }
})
