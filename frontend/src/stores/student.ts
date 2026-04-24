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
      const response = await api.get('/students')
      const data = response.data
      
      // Handle both paginated response and simple array
      if (data && Array.isArray(data)) {
        // Simple array response from backend
        students.value = data
        totalStudents.value = data.length
      } else if (data && data.students && Array.isArray(data.students)) {
        // Paginated response
        students.value = data.students
        totalStudents.value = data.total || data.students.length
      } else {
        // Empty or invalid response
        students.value = []
        totalStudents.value = 0
      }
      
      totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
      
      // Transform backend data to frontend format if needed
      if (students.value.length > 0 && students.value[0] && 'first_name' in students.value[0]) {
        students.value = students.value.map(transformBackendStudent)
      }
      
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

  // Helper function to transform backend student data to frontend format
  const transformBackendStudent = (backendStudent: any): Student => {
    return {
      id: backendStudent.id,
      personalInfo: {
        firstName: backendStudent.first_name || '',
        lastName: backendStudent.last_name || '',
        middleName: backendStudent.middle_name,
        studentId: backendStudent.student_number || '',
        email: backendStudent.email || `${backendStudent.student_number}@ccs.edu`,
        phone: backendStudent.phone || '',
        dateOfBirth: backendStudent.date_of_birth || '',
        age: backendStudent.age || 18,
        gender: backendStudent.gender || 'other',
        address: backendStudent.address || '',
        city: backendStudent.city || '',
        province: backendStudent.province || '',
        postalCode: backendStudent.postal_code || '',
        emergencyContact: {
          name: backendStudent.emergency_contact_name || '',
          relationship: 'Parent',
          phone: backendStudent.emergency_contact_phone || ''
        }
      },
      academicHistory: [],
      academicStanding: {
        currentYear: backendStudent.year_level || backendStudent.current_year || 1,
        currentSemester: 'first',
        currentGPA: backendStudent.current_gpa || backendStudent.gpa || 0,
        totalUnits: 0,
        standing: backendStudent.academic_standing || 'good',
        advisor: 'TBD'
      },
      activities: [],
      violations: [],
      skills: [],
      affiliations: [],
      createdAt: backendStudent.created_at || new Date().toISOString(),
      updatedAt: backendStudent.updated_at || new Date().toISOString(),
      isActive: true
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

  const generateSampleData = () => {
    const sampleStudents: Student[] = [
      {
        id: 1,
        personalInfo: {
          firstName: 'John',
          lastName: 'Doe',
          studentId: '2021001',
          email: 'john.doe@ccs.edu',
          phone: '09123456789',
          dateOfBirth: '2000-01-15',
          age: 23,
          gender: 'male',
          address: '123 Main St',
          city: 'Quezon City',
          province: 'Metro Manila',
          postalCode: '1100',
          emergencyContact: {
            name: 'Jane Doe',
            relationship: 'Mother',
            phone: '09123456788'
          }
        },
        academicHistory: [],
        academicStanding: {
          currentYear: 3,
          currentSemester: 'first',
          currentGPA: 3.5,
          totalUnits: 120,
          standing: 'good',
          advisor: 'Dr. Smith'
        },
        activities: [],
        violations: [],
        skills: [
          { id: 1, name: 'JavaScript', category: 'technical', proficiency: 'advanced' },
          { id: 2, name: 'Leadership', category: 'soft', proficiency: 'intermediate' }
        ],
        affiliations: [],
        createdAt: '2023-01-01',
        updatedAt: '2023-01-01',
        isActive: true
      },
      {
        id: 2,
        personalInfo: {
          firstName: 'Jane',
          lastName: 'Smith',
          studentId: '2021002',
          email: 'jane.smith@ccs.edu',
          phone: '09123456787',
          dateOfBirth: '2000-05-20',
          age: 23,
          gender: 'female',
          address: '456 Oak Ave',
          city: 'Manila',
          province: 'Metro Manila',
          postalCode: '1000',
          emergencyContact: {
            name: 'Bob Smith',
            relationship: 'Father',
            phone: '09123456786'
          }
        },
        academicHistory: [],
        academicStanding: {
          currentYear: 2,
          currentSemester: 'second',
          currentGPA: 3.8,
          totalUnits: 80,
          standing: 'good',
          advisor: 'Dr. Johnson'
        },
        activities: [],
        violations: [],
        skills: [
          { id: 3, name: 'Python', category: 'technical', proficiency: 'advanced' },
          { id: 4, name: 'Communication', category: 'soft', proficiency: 'advanced' }
        ],
        affiliations: [],
        createdAt: '2023-01-01',
        updatedAt: '2023-01-01',
        isActive: true
      },
      {
        id: 3,
        personalInfo: {
          firstName: 'Mike',
          lastName: 'Wilson',
          studentId: '2021003',
          email: 'mike.wilson@ccs.edu',
          phone: '09123456785',
          dateOfBirth: '2001-03-10',
          age: 22,
          gender: 'male',
          address: '789 Pine Rd',
          city: 'Pasig',
          province: 'Metro Manila',
          postalCode: '1600',
          emergencyContact: {
            name: 'Sarah Wilson',
            relationship: 'Sister',
            phone: '09123456784'
          }
        },
        academicHistory: [],
        academicStanding: {
          currentYear: 1,
          currentSemester: 'first',
          currentGPA: 2.8,
          totalUnits: 40,
          standing: 'warning',
          advisor: 'Dr. Brown'
        },
        activities: [],
        violations: [],
        skills: [
          { id: 5, name: 'Basketball', category: 'sports', proficiency: 'intermediate' },
          { id: 6, name: 'Programming', category: 'technical', proficiency: 'beginner' }
        ],
        affiliations: [],
        createdAt: '2023-01-01',
        updatedAt: '2023-01-01',
        isActive: true
      }
    ]
    
    students.value = sampleStudents
    totalStudents.value = sampleStudents.length
    totalPages.value = Math.ceil(totalStudents.value / pageSize.value)
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
    clearError,
    generateSampleData
  }
})
