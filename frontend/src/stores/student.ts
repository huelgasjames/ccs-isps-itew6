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
import api from '@/services/api'

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

    // Affiliations filter
    if (filter.value.affiliations && filter.value.affiliations.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.affiliations!.some(affiliationName =>
          student.affiliations.some(affiliation =>
            affiliation.name.toLowerCase().includes(affiliationName.toLowerCase())
          )
        )
      )
    }

    // Academic standing filter
    if (filter.value.academicStanding && filter.value.academicStanding.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.academicStanding!.includes(student.academicStanding.standing)
      )
    }

    // Year level filter
    if (filter.value.yearLevel && filter.value.yearLevel.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.yearLevel!.includes(student.academicStanding.currentYear)
      )
    }

    // GPA range filter
    if (filter.value.gpaRange) {
      filtered = filtered.filter(student =>
        student.academicStanding.currentGPA >= filter.value.gpaRange!.min &&
        student.academicStanding.currentGPA <= filter.value.gpaRange!.max
      )
    }

    // Age range filter
    if (filter.value.ageRange) {
      filtered = filtered.filter(student =>
        student.personalInfo.age >= filter.value.ageRange!.min &&
        student.personalInfo.age <= filter.value.ageRange!.max
      )
    }

    // Violation status filter
    if (filter.value.violationStatus && filter.value.violationStatus.length > 0) {
      filtered = filtered.filter(student =>
        student.violations.some(violation =>
          filter.value.violationStatus!.includes(violation.status)
        )
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
  const fetchStudents = async (page = 1, limit = 10) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get<StudentListResponse>('/students', {
        params: { page, limit }
      })
      
      students.value = response.data.students
      totalStudents.value = response.data.total
      totalPages.value = response.data.totalPages
      currentPage.value = response.data.page
    } catch (err) {
      error.value = 'Failed to fetch students'
      console.error('Error fetching students:', err)
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
      error.value = 'Failed to fetch student'
      console.error('Error fetching student:', err)
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
      students.value.unshift(response.data)
      totalStudents.value += 1
      return response.data
    } catch (err) {
      error.value = 'Failed to create student'
      console.error('Error creating student:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStudent = async (id: number, updates: UpdateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.put<Student>(`/students/${id}`, updates)
      
      // Update in array
      const index = students.value.findIndex(s => s.id === id)
      if (index !== -1) {
        students.value[index] = response.data
      }
      
      // Update current student if it's the same
      if (currentStudent.value?.id === id) {
        currentStudent.value = response.data
      }
      
      return response.data
    } catch (err) {
      error.value = 'Failed to update student'
      console.error('Error updating student:', err)
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
      
      // Remove from array
      const index = students.value.findIndex(s => s.id === id)
      if (index !== -1) {
        students.value.splice(index, 1)
      }
      
      // Clear current student if it's the same
      if (currentStudent.value?.id === id) {
        currentStudent.value = null
      }
      
      totalStudents.value -= 1
    } catch (err) {
      error.value = 'Failed to delete student'
      console.error('Error deleting student:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const addSkill = async (studentId: number, skill: Omit<Student['skills'][0], 'id'>) => {
    try {
      const response = await api.post(`/students/${studentId}/skills`, skill)
      
      // Update local state
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills.push(response.data)
      }
      
      if (currentStudent.value?.id === studentId) {
        currentStudent.value.skills.push(response.data)
      }
      
      return response.data
    } catch (err) {
      error.value = 'Failed to add skill'
      console.error('Error adding skill:', err)
      throw err
    }
  }

  const removeSkill = async (studentId: number, skillId: number) => {
    try {
      await api.delete(`/students/${studentId}/skills/${skillId}`)
      
      // Update local state
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills = student.skills.filter(s => s.id !== skillId)
      }
      
      if (currentStudent.value?.id === studentId) {
        currentStudent.value.skills = currentStudent.value.skills.filter(s => s.id !== skillId)
      }
    } catch (err) {
      error.value = 'Failed to remove skill'
      console.error('Error removing skill:', err)
      throw err
    }
  }

  const addActivity = async (studentId: number, activity: Omit<Student['activities'][0], 'id'>) => {
    try {
      const response = await api.post(`/students/${studentId}/activities`, activity)
      
      // Update local state
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.activities.push(response.data)
      }
      
      if (currentStudent.value?.id === studentId) {
        currentStudent.value.activities.push(response.data)
      }
      
      return response.data
    } catch (err) {
      error.value = 'Failed to add activity'
      console.error('Error adding activity:', err)
      throw err
    }
  }

  const fetchStatistics = async () => {
    try {
      const response = await api.get<StudentStatistics>('/students/statistics')
      statistics.value = response.data
    } catch (err) {
      console.error('Error fetching statistics:', err)
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

  // Sample data generator for demo purposes
  const generateSampleData = () => {
    const sampleStudents: Student[] = [
      {
        id: 1,
        personalInfo: {
          firstName: 'John',
          lastName: 'Doe',
          middleName: 'Michael',
          studentId: '2021-001',
          email: 'john.doe@ccs.edu',
          phone: '+63 912 345 6789',
          dateOfBirth: '2000-05-15',
          age: 23,
          gender: 'male',
          address: '123 University St',
          city: 'Manila',
          province: 'Metro Manila',
          postalCode: '1000',
          emergencyContact: {
            name: 'Jane Doe',
            relationship: 'Mother',
            phone: '+63 912 345 6788'
          }
        },
        academicHistory: [
          {
            id: 1,
            schoolName: 'CCS University',
            degree: 'Bachelor of Science',
            major: 'Computer Science',
            startDate: '2021-08-01',
            gpa: 3.8,
            status: 'ongoing'
          }
        ],
        academicStanding: {
          currentYear: 3,
          currentSemester: 'second',
          currentGPA: 3.8,
          totalUnits: 90,
          standing: 'good',
          advisor: 'Dr. Smith'
        },
        activities: [
          {
            id: 1,
            name: 'Basketball Varsity',
            type: 'sports',
            role: 'Team Captain',
            startDate: '2021-09-01',
            description: 'Leading the university basketball team',
            achievements: ['MVP 2022', 'Champion 2023'],
            level: 'regional'
          }
        ],
        violations: [],
        skills: [
          {
            id: 1,
            name: 'Basketball',
            category: 'sports',
            proficiency: 'advanced',
            yearsExperience: 8,
            lastUsed: '2024-01-15'
          },
          {
            id: 2,
            name: 'Programming',
            category: 'technical',
            proficiency: 'intermediate',
            certifications: ['Python Basic'],
            yearsExperience: 2
          }
        ],
        affiliations: [
          {
            id: 1,
            name: 'Computer Science Society',
            type: 'student_organization',
            role: 'Member',
            startDate: '2021-09-01',
            position: 'Treasurer'
          }
        ],
        createdAt: '2021-08-01T00:00:00Z',
        updatedAt: '2024-01-15T00:00:00Z',
        isActive: true
      }
    ]
    
    students.value = sampleStudents
    totalStudents.value = sampleStudents.length
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
    addSkill,
    removeSkill,
    addActivity,
    fetchStatistics,
    setFilter,
    clearFilter,
    setDisplayMode,
    setCurrentPage,
    clearCurrentStudent,
    clearError,
    generateSampleData
  }
})
