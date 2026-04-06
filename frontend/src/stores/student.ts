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
    const firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Mary', 'William', 'Patricia', 'Richard', 'Jennifer', 'Charles', 'Linda']
    const lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Wilson', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson']
    const majors = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science', 'Cybersecurity', 'Web Development', 'Network Engineering', 'Database Administration']
    const skillOptions = ['Programming', 'Web Development', 'Database Management', 'Network Security', 'Data Analysis', 'Machine Learning', 'Cloud Computing', 'Mobile Development', 'UI/UX Design', 'Project Management']
    const activityOptions = ['Basketball Varsity', 'Computer Science Society', 'Debate Club', 'Student Government', 'Drama Club', 'Music Club', 'Photography Club', 'Volunteer Corps', 'Robotics Club', 'Hackathon Team']
    
    const sampleStudents: Student[] = []
    
    for (let i = 1; i <= 20; i++) {
      const firstName = firstNames[Math.floor(Math.random() * firstNames.length)]!
      const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]!
      const major = majors[Math.floor(Math.random() * majors.length)]!
      const year = Math.floor(Math.random() * 4) + 1
      const gpa = (Math.random() * 2 + 2).toFixed(2) // GPA between 2.0 and 4.0
      const age = Math.floor(Math.random() * 8) + 18 // Age between 18 and 25
      
      const studentSkills: any[] = []
      const numSkills = Math.floor(Math.random() * 3) + 1
      for (let j = 0; j < numSkills; j++) {
        const skill = skillOptions[Math.floor(Math.random() * skillOptions.length)]!
        if (!studentSkills.find((s: any) => s.name === skill)) {
          studentSkills.push({
            id: j + 1,
            name: skill,
            category: skill.includes('Programming') || skill.includes('Development') ? 'technical' : 'soft',
            proficiency: ['beginner', 'intermediate', 'advanced'][Math.floor(Math.random() * 3)] as any,
            yearsExperience: Math.floor(Math.random() * 4) + 1,
            lastUsed: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
          })
        }
      }
      
      const studentActivities: any[] = []
      const numActivities = Math.floor(Math.random() * 2)
      for (let j = 0; j < numActivities; j++) {
        const activity = activityOptions[Math.floor(Math.random() * activityOptions.length)]!
        if (!studentActivities.find((a: any) => a.name === activity)) {
          studentActivities.push({
            id: j + 1,
            name: activity,
            type: activity.includes('Club') ? 'organization' : activity.includes('Varsity') ? 'sports' : 'other',
            role: ['Member', 'President', 'Vice President', 'Secretary', 'Treasurer'][Math.floor(Math.random() * 5)],
            startDate: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            description: `Active participation in ${activity}`,
            achievements: [],
            level: ['local', 'regional', 'national'][Math.floor(Math.random() * 3)] as any
          })
        }
      }
      
      const violations: any[] = []
      if (Math.random() > 0.7) { // 30% chance of having violations
        const numViolations = Math.floor(Math.random() * 2) + 1
        for (let j = 0; j < numViolations; j++) {
          violations.push({
            id: j + 1,
            type: ['Attendance', 'Academic', 'Conduct'][Math.floor(Math.random() * 3)],
            severity: 'minor' as const,
            description: 'Minor violation',
            date: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            status: 'resolved' as const,
            points: Math.floor(Math.random() * 3) + 1,
            reportedBy: 'System'
          })
        }
      }
      
      sampleStudents.push({
        id: i,
        personalInfo: {
          firstName,
          lastName,
          middleName: Math.random() > 0.5 ? ['A.', 'B.', 'C.', 'D.'][Math.floor(Math.random() * 4)] : undefined,
          studentId: `202${Math.floor(Math.random() * 4) + 1}-${String(i).padStart(3, '0')}`,
          email: `${firstName.toLowerCase()}.${lastName.toLowerCase()}@ccs.edu`,
          phone: `+63 9${Math.floor(Math.random() * 900000000) + 100000000}`,
          dateOfBirth: new Date(Date.now() - age * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] || '',
          age,
          gender: ['male', 'female'][Math.floor(Math.random() * 2)] as any,
          address: `${Math.floor(Math.random() * 999) + 1} University St`,
          city: ['Manila', 'Quezon City', 'Makati', 'Pasig', 'Mandaluyong'][Math.floor(Math.random() * 5)] || '',
          province: 'Metro Manila',
          postalCode: String(Math.floor(Math.random() * 9000) + 1000),
          emergencyContact: {
            name: `${['Mother', 'Father', 'Guardian'][Math.floor(Math.random() * 3)]} ${lastName}`,
            relationship: ['Mother', 'Father', 'Guardian'][Math.floor(Math.random() * 3)] || '',
            phone: `+63 9${Math.floor(Math.random() * 900000000) + 100000000}`
          }
        },
        academicHistory: [
          {
            id: 1,
            schoolName: 'CCS University',
            degree: 'Bachelor of Science',
            major,
            startDate: '2021-08-01',
            gpa: parseFloat(gpa),
            status: 'ongoing' as const
          }
        ],
        academicStanding: {
          currentYear: year,
          currentSemester: ['first', 'second'][Math.floor(Math.random() * 2)] as any,
          currentGPA: parseFloat(gpa),
          totalUnits: year * 24,
          standing: parseFloat(gpa) >= 3.5 ? 'good' : parseFloat(gpa) >= 2.5 ? 'warning' : 'probation' as any,
          advisor: ['Dr. Smith', 'Prof. Johnson', 'Dr. Williams', 'Prof. Brown'][Math.floor(Math.random() * 4)] || ''
        },
        activities: studentActivities,
        violations,
        skills: studentSkills,
        affiliations: studentActivities.map((a: any) => ({
          id: a.id,
          name: a.name,
          type: 'student_organization' as const,
          role: a.role,
          startDate: a.startDate,
          position: a.role
        })),
        createdAt: '2021-08-01T00:00:00Z',
        updatedAt: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString(),
        isActive: Math.random() > 0.1 // 90% active
      })
    }
    
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
