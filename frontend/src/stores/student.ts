import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { 
  Student, 
  StudentFilter, 
  StudentListResponse, 
  CreateStudentRequest, 
  UpdateStudentRequest,
  DisplayMode,
  StudentStatistics,
  Violation
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
  const pageSize = ref(25)
  const totalStudents = ref(0)
  const totalPages = ref(0)
  const statistics = ref<StudentStatistics | null>(null)
  const allSkills = ref<string[]>([]) // Store all unique skills
  const allAffiliations = ref<string[]>([]) // Store all unique affiliations

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
      
      // Fetch skills and affiliations for all students
      await fetchAllStudentSkillsAndAffiliations()
      
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
    // Transform skills if they exist in backend
    const skills = backendStudent.skills ? backendStudent.skills.map((skill: any) => ({
      id: skill.id || Date.now() + Math.random(),
      name: skill.name || skill.skill_name || '',
      category: skill.category || 'technical',
      proficiency: skill.proficiency || 'intermediate',
      certifications: skill.certifications || [],
      yearsExperience: skill.years_experience,
      lastUsed: skill.last_used
    })) : []
    
    // Transform affiliations if they exist in backend
    const affiliations = backendStudent.affiliations ? backendStudent.affiliations.map((affiliation: any) => ({
      id: affiliation.id || Date.now() + Math.random(),
      name: affiliation.name || affiliation.organization_name || '',
      type: affiliation.type || 'student_organization',
      role: affiliation.role || 'Member',
      startDate: affiliation.start_date || affiliation.join_date || '',
      endDate: affiliation.end_date,
      position: affiliation.position,
      description: affiliation.description
    })) : []
    
    // Transform violations if they exist in backend
    const violations = backendStudent.violations ? backendStudent.violations.map((violation: any) => ({
      id: violation.id || Date.now() + Math.random(),
      type: violation.type || violation.violation_type || '',
      severity: violation.severity || 'minor',
      description: violation.description || violation.violation_description || '',
      date: violation.date || violation.violation_date || '',
      status: violation.status || 'pending',
      sanctions: violation.sanctions || [],
      points: violation.points || 0,
      reportedBy: violation.reported_by || violation.reporter || 'System'
    })) : []
    
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
      violations,
      skills,
      affiliations,
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

  // Fetch student skills from API
  const fetchStudentSkills = async (studentId: number) => {
    try {
      const response = await api.get(`/students/${studentId}/skills`)
      return response.data
    } catch (err) {
      console.error(`Failed to fetch skills for student ${studentId}:`, err)
      return []
    }
  }

  // Fetch student affiliations from API
  const fetchStudentAffiliations = async (studentId: number) => {
    try {
      const response = await api.get(`/students/${studentId}/affiliations`)
      return response.data
    } catch (err) {
      console.error(`Failed to fetch affiliations for student ${studentId}:`, err)
      return []
    }
  }

  // Fetch all skills and affiliations for all students
  const fetchAllStudentSkillsAndAffiliations = async () => {
    if (students.value.length === 0) return

    const uniqueSkills = new Set<string>()
    const uniqueAffiliations = new Set<string>()

    for (const student of students.value) {
      try {
        // Fetch skills for this student
        const skills = await fetchStudentSkills(student.id)
        student.skills = skills.map((skill: any) => ({
          id: skill.id,
          name: skill.name,
          category: skill.category,
          proficiency: skill.proficiency,
          certifications: skill.certifications,
          yearsExperience: skill.years_experience,
          lastUsed: skill.last_used
        }))

        // Collect unique skill names
        skills.forEach((skill: any) => {
          uniqueSkills.add(skill.name)
        })

        // Fetch affiliations for this student
        const affiliations = await fetchStudentAffiliations(student.id)
        student.affiliations = affiliations.map((affiliation: any) => ({
          id: affiliation.id,
          name: affiliation.name,
          type: affiliation.type,
          role: affiliation.role,
          startDate: affiliation.start_date,
          endDate: affiliation.end_date,
          position: affiliation.position,
          description: affiliation.description
        }))

        // Collect unique affiliation names
        affiliations.forEach((affiliation: any) => {
          uniqueAffiliations.add(affiliation.name)
        })

      } catch (err) {
        console.error(`Failed to fetch data for student ${student.id}:`, err)
      }
    }

    // Update the reactive arrays
    allSkills.value = Array.from(uniqueSkills).sort()
    allAffiliations.value = Array.from(uniqueAffiliations).sort()
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

  // Helper function to generate random affiliations
  const generateRandomAffiliations = (studentId: number) => {
    const allAffiliations = [
      // Student Organizations
      { name: 'Computer Science Society', type: 'student_organization', role: 'Member', position: 'Events Coordinator' },
      { name: 'Programming Club', type: 'student_organization', role: 'Member', position: 'Treasurer' },
      { name: 'Women in Tech', type: 'student_organization', role: 'President', position: 'Founder' },
      { name: 'Debate Club', type: 'student_organization', role: 'Member', position: 'Secretary' },
      { name: 'Student Council', type: 'student_organization', role: 'Member', position: 'Class Representative' },
      { name: 'Tech Innovation Club', type: 'student_organization', role: 'Vice President', position: 'Technical Lead' },
      { name: 'Entrepreneurship Society', type: 'student_organization', role: 'Member', position: 'Marketing Lead' },
      { name: 'Data Science Club', type: 'student_organization', role: 'President', position: 'Founder' },
      
      // Academic Groups
      { name: 'Honor Society', type: 'academic', role: 'Member', position: 'Academic Chair' },
      { name: 'Research Lab', type: 'academic', role: 'Research Assistant', position: 'Lab Assistant' },
      { name: 'Academic Team', type: 'academic', role: 'Member', position: 'Study Group Leader' },
      { name: 'Scholarship Committee', type: 'academic', role: 'Member', position: 'Scholarship Coordinator' },
      
      // Sports Teams
      { name: 'Basketball Varsity Team', type: 'sports', role: 'Member', position: 'Point Guard' },
      { name: 'Football Team', type: 'sports', role: 'Member', position: 'Quarterback' },
      { name: 'Volleyball Team', type: 'sports', role: 'Member', position: 'Setter' },
      { name: 'Swimming Team', type: 'sports', role: 'Member', position: 'Team Captain' },
      { name: 'Tennis Club', type: 'sports', role: 'Member', position: 'Team Captain' },
      { name: 'Badminton Team', type: 'sports', role: 'Member', position: 'Doubles Partner' },
      { name: 'Chess Club', type: 'sports', role: 'Member', position: 'Team Captain' },
      
      // Professional Organizations
      { name: 'Tech Innovation Lab', type: 'professional', role: 'Lead Developer', position: 'CTO' },
      { name: 'Software Development Association', type: 'professional', role: 'Member', position: 'Junior Developer' },
      { name: 'IT Professionals Network', type: 'professional', role: 'Member', position: 'Network Administrator' },
      { name: 'Web Developers Guild', type: 'professional', role: 'Member', position: 'Frontend Developer' },
      { name: 'Data Science Association', type: 'professional', role: 'Member', position: 'Data Analyst' },
      
      // Community Organizations
      { name: 'Community Outreach', type: 'community', role: 'Volunteer', position: 'Volunteer Coordinator' },
      { name: 'Environmental Club', type: 'community', role: 'Member', position: 'Event Organizer' },
      { name: 'Youth Mentoring Program', type: 'community', role: 'Mentor', position: 'Senior Mentor' },
      { name: 'Food Bank Volunteers', type: 'community', role: 'Volunteer', position: 'Distribution Lead' },
      { name: 'Senior Care Program', type: 'community', role: 'Volunteer', position: 'Care Assistant' },
      
      // Religious Groups
      { name: 'Christian Fellowship', type: 'religious', role: 'Member', position: 'Youth Leader' },
      { name: 'Muslim Student Association', type: 'religious', role: 'Member', position: 'Prayer Coordinator' },
      { name: 'Bible Study Group', type: 'religious', role: 'Member', position: 'Group Leader' },
      
      // Other Organizations
      { name: 'Alumni Association', type: 'other', role: 'Member', position: 'Student Representative' },
      { name: 'International Students Club', type: 'other', role: 'Member', position: 'Cultural Ambassador' },
      { name: 'Photography Club', type: 'other', role: 'Member', position: 'Photography Lead' },
      { name: 'Music Club', type: 'other', role: 'Member', position: 'Band Leader' }
    ]

    const affiliationCount = Math.floor(Math.random() * 3) + 1 // 1-3 affiliations per student
    const selectedAffiliations: any[] = []
    
    // Randomly select affiliations
    const shuffled = [...allAffiliations].sort(() => Math.random() - 0.5)
    const chosen = shuffled.slice(0, affiliationCount)
    
    chosen.forEach((affiliation, index) => {
      const startDate = new Date(Date.now() - Math.floor(Math.random() * 365 * 2) * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
      const endDate = Math.random() > 0.7 ? new Date(Date.now() - Math.floor(Math.random() * 365) * 24 * 60 * 60 * 1000).toISOString().split('T')[0] : undefined
      
      selectedAffiliations.push({
        id: studentId * 200 + index,
        name: affiliation.name,
        type: affiliation.type,
        role: affiliation.role,
        startDate,
        endDate,
        position: affiliation.position,
        description: `Active member of ${affiliation.name} since ${startDate}`
      })
    })
    
    return selectedAffiliations
  }

// Helper function to generate random skills
  const generateRandomSkills = (studentId: number) => {
    const allSkills = [
      // Technical Skills
      { name: 'JavaScript', category: 'technical', certifications: ['JavaScript Certification', 'React Developer'] },
      { name: 'Python', category: 'technical', certifications: ['Python Developer Certificate'] },
      { name: 'Java', category: 'technical', certifications: ['Oracle Certified Professional'] },
      { name: 'TypeScript', category: 'technical', certifications: [] },
      { name: 'React', category: 'technical', certifications: ['React Certification'] },
      { name: 'Vue.js', category: 'technical', certifications: [] },
      { name: 'Node.js', category: 'technical', certifications: [] },
      { name: 'HTML/CSS', category: 'technical', certifications: ['Web Design Certificate'] },
      { name: 'SQL', category: 'technical', certifications: [] },
      { name: 'MongoDB', category: 'technical', certifications: [] },
      { name: 'Docker', category: 'technical', certifications: ['Docker Certification'] },
      { name: 'Git', category: 'technical', certifications: [] },
      { name: 'Machine Learning', category: 'technical', certifications: ['ML Certificate'] },
      { name: 'Data Analysis', category: 'technical', certifications: [] },
      { name: 'Cloud Computing', category: 'technical', certifications: ['AWS Certification'] },
      
      // Soft Skills
      { name: 'Leadership', category: 'soft', certifications: [] },
      { name: 'Communication', category: 'soft', certifications: ['Public Speaking Certificate'] },
      { name: 'Teamwork', category: 'soft', certifications: [] },
      { name: 'Problem Solving', category: 'soft', certifications: [] },
      { name: 'Time Management', category: 'soft', certifications: [] },
      { name: 'Critical Thinking', category: 'soft', certifications: [] },
      { name: 'Creativity', category: 'soft', certifications: [] },
      { name: 'Adaptability', category: 'soft', certifications: [] },
      { name: 'Project Management', category: 'soft', certifications: ['PMP Certification'] },
      { name: 'Mentoring', category: 'soft', certifications: [] },
      
      // Sports Skills
      { name: 'Basketball', category: 'sports', certifications: [] },
      { name: 'Football', category: 'sports', certifications: [] },
      { name: 'Volleyball', category: 'sports', certifications: [] },
      { name: 'Swimming', category: 'sports', certifications: [] },
      { name: 'Tennis', category: 'sports', certifications: [] },
      { name: 'Badminton', category: 'sports', certifications: [] },
      
      // Creative Skills
      { name: 'Graphic Design', category: 'creative', certifications: ['Adobe Certified'] },
      { name: 'Video Editing', category: 'creative', certifications: [] },
      { name: 'Photography', category: 'creative', certifications: [] },
      { name: 'Writing', category: 'creative', certifications: [] },
      { name: 'Music Production', category: 'creative', certifications: [] },
      
      // Language Skills
      { name: 'English', category: 'language', certifications: ['IELTS', 'TOEFL'] },
      { name: 'Spanish', category: 'language', certifications: [] },
      { name: 'French', category: 'language', certifications: [] },
      { name: 'Mandarin', category: 'language', certifications: [] },
      { name: 'Japanese', category: 'language', certifications: [] }
    ]

    const proficiencies: Array<'beginner' | 'intermediate' | 'advanced' | 'expert'> = ['beginner', 'intermediate', 'advanced', 'expert']
    const skillCount = Math.floor(Math.random() * 4) + 3 // 3-6 skills per student
    const selectedSkills: any[] = []
    
    // Randomly select skills
    const shuffled = [...allSkills].sort(() => Math.random() - 0.5)
    const chosen = shuffled.slice(0, skillCount)
    
    chosen.forEach((skill, index) => {
      const proficiency = proficiencies[Math.floor(Math.random() * proficiencies.length)]
      const yearsExperience = Math.floor(Math.random() * 5) + 1 // 1-5 years
      const lastUsed = new Date(Date.now() - Math.floor(Math.random() * 365) * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
      
      selectedSkills.push({
        id: studentId * 100 + index,
        name: skill.name,
        category: skill.category,
        proficiency,
        certifications: Math.random() > 0.6 ? skill.certifications : [],
        yearsExperience,
        lastUsed
      })
    })
    
    return selectedSkills
  }

  const generateSampleData = () => {
    console.log('Generating 1000 random students...')
    const startTime = performance.now()
    
    const firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Carlos', 'Sofia', 'Miguel', 'Isabella', 'Antonio', 'Carmen', 
                       'Francisco', 'Rosa', 'Luis', 'Patricia', 'Diego', 'Laura', 'Manuel', 'Elena', 'Pedro', 'Teresa',
                       'Javier', 'Monica', 'Ricardo', 'Claudia', 'Alberto', 'Gabriela', 'Roberto', 'Veronica', 'Fernando', 'Luz',
                       'Rafael', 'Adriana', 'Santiago', 'Mariana', 'Eduardo', 'Daniela', 'Alejandro', 'Valeria', 'Jorge', 'Paola']
    
    const lastNames = ['Garcia', 'Rodriguez', 'Hernandez', 'Lopez', 'Martinez', 'Gonzalez', 'Perez', 'Sanchez', 'Ramirez', 'Cruz',
                      'Flores', 'Morales', 'Reyes', 'Jimenez', 'Gomez', 'Mendoza', 'Diaz', 'Torres', 'Vargas', 'Castillo',
                      'Silva', 'Molina', 'Ortiz', 'Delgado', 'Roman', 'Serrano', 'Alvarez', 'Cortes', 'Guzman', 'Vega']
    
    const cities = ['Quezon City', 'Manila', 'Caloocan', 'Pasig', 'Makati', 'Taguig', 'Pasay', 'Mandaluyong', 'Marikina', 'Las Piñas',
                   'Muntinlupa', 'Parañaque', 'San Juan', 'Valenzuela', 'Malabon', 'Navotas', 'Cainta', 'Taytay', 'Antipolo', 'San Pedro']
    
    const provinces = ['Metro Manila', 'Laguna', 'Cavite', 'Rizal', 'Bulacan', 'Pampanga', 'Batangas', 'Quezon', 'Nueva Ecija']
    
    const violationTypes = ['Late Submission', 'Absence', 'Cheating', 'Plagiarism', 'Disruptive Behavior', 'Dress Code Violation', 
                           'Mobile Phone Use', 'Incomplete Requirements', 'Poor Attendance', 'Academic Dishonesty']
    
    const violationSeverities: ('minor' | 'major' | 'critical')[] = ['minor', 'major', 'critical']
    const violationStatuses: ('pending' | 'resolved' | 'under_review')[] = ['pending', 'resolved', 'under_review']
    const academicStandings: ('good' | 'warning' | 'probation')[] = ['good', 'warning', 'probation']
    const semesters = ['first', 'second']
    const advisors = ['Dr. Smith', 'Dr. Johnson', 'Dr. Brown', 'Dr. Davis', 'Dr. Martinez', 'Dr. Wilson', 'Dr. Taylor', 'Dr. Anderson']
    
    const sampleStudents: Student[] = []
    
    for (let i = 1; i <= 1000; i++) {
      const firstName: string = firstNames[Math.floor(Math.random() * firstNames.length)] || 'Student'
      const lastName: string = lastNames[Math.floor(Math.random() * lastNames.length)] || 'Name'
      const city: string = cities[Math.floor(Math.random() * cities.length)] || 'Quezon City'
      const province: string = provinces[Math.floor(Math.random() * provinces.length)] || 'Metro Manila'
      const gender: 'male' | 'female' = Math.random() > 0.5 ? 'male' : 'female'
      const year = Math.floor(Math.random() * 4) + 1
      const semester: 'first' | 'second' = semesters[Math.floor(Math.random() * semesters.length)] as 'first' | 'second'
      const gpa = (Math.random() * 2 + 2).toFixed(2)
      const standing: 'good' | 'warning' | 'probation' = academicStandings[Math.floor(Math.random() * academicStandings.length)] || 'good'
      const advisor: string = advisors[Math.floor(Math.random() * advisors.length)] || 'Dr. Smith'
      
      const birthYear = 2000 + Math.floor(Math.random() * 5)
      const birthMonth = String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')
      const birthDay = String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')
      const dateOfBirth = `${birthYear}-${birthMonth}-${birthDay}`
      const age = new Date().getFullYear() - birthYear
      
      const studentId = `20${String(birthYear).slice(2)}${String(i).padStart(4, '0')}`
      const email = `${firstName.toLowerCase()}.${lastName.toLowerCase()}${i}@ccs.edu`
      const phone = `09${Math.floor(Math.random() * 900000000) + 100000000}`
      
      const streetNumber = Math.floor(Math.random() * 9999) + 1
      const streetNames = ['Main St', 'Oak Ave', 'Pine Rd', 'Elm St', 'Maple Ave', 'Cedar Rd', 'Walnut St', 'Birch Ave', 'Ash St', 'Spruce Rd']
      const street = streetNames[Math.floor(Math.random() * streetNames.length)] || 'Main St'
      const address = `${streetNumber} ${street}`
      const postalCode = String(Math.floor(Math.random() * 9000) + 1000)
      
      const emergencyNames = gender === 'male' ? ['Maria', 'Ana', 'Sofia', 'Carmen', 'Laura'] : ['Juan', 'Jose', 'Carlos', 'Miguel', 'Antonio']
      const emergencyName = emergencyNames[Math.floor(Math.random() * emergencyNames.length)]
      const emergencyRelationship = Math.random() > 0.5 ? 'Mother' : 'Father'
      const emergencyPhone = `09${Math.floor(Math.random() * 900000000) + 100000000}`
      
      const violations: Violation[] = []
      const numViolations = Math.random() > 0.7 ? Math.floor(Math.random() * 3) + 1 : 0
      
      for (let j = 0; j < numViolations; j++) {
        const violationDate: string = new Date(2023, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1)
          .toISOString().split('T')[0] || '2023-01-01'
        
        violations.push({
          id: i * 10 + j,
          type: violationTypes[Math.floor(Math.random() * violationTypes.length)] || 'Other',
          severity: violationSeverities[Math.floor(Math.random() * violationSeverities.length)] || 'minor',
          description: `Violation recorded for ${firstName} ${lastName}`,
          date: violationDate,
          status: violationStatuses[Math.floor(Math.random() * violationStatuses.length)] || 'pending',
          points: Math.floor(Math.random() * 10) + 1,
          reportedBy: advisors[Math.floor(Math.random() * advisors.length)] || 'Dr. Smith'
        })
      }
      
      sampleStudents.push({
        id: i,
        personalInfo: {
          firstName,
          lastName,
          studentId,
          email,
          phone,
          dateOfBirth,
          age,
          gender,
          address,
          city,
          province,
          postalCode,
          emergencyContact: {
            name: `${emergencyName} ${lastName}`,
            relationship: emergencyRelationship,
            phone: emergencyPhone
          }
        },
        academicHistory: [],
        academicStanding: {
          currentYear: year,
          currentSemester: semester,
          currentGPA: parseFloat(gpa),
          totalUnits: year * 40,
          standing,
          advisor
        },
        activities: [],
        violations,
        skills: generateRandomSkills(i),
        affiliations: generateRandomAffiliations(i),
        createdAt: '2023-01-01',
        updatedAt: '2023-01-01',
        isActive: true
      })
    }
    
    // Collect all unique skills and affiliations from generated students
    const uniqueSkills = new Set<string>()
    const uniqueAffiliations = new Set<string>()
    sampleStudents.forEach(student => {
      student.skills.forEach(skill => {
        uniqueSkills.add(skill.name)
      })
      student.affiliations.forEach(affiliation => {
        uniqueAffiliations.add(affiliation.name)
      })
    })
    allSkills.value = Array.from(uniqueSkills).sort()
    allAffiliations.value = Array.from(uniqueAffiliations).sort()
    
    const endTime = performance.now()
    console.log(`Generated ${sampleStudents.length} random students in ${(endTime - startTime).toFixed(2)}ms`)
    console.log(`Unique skills: ${allSkills.value.length}, Unique affiliations: ${allAffiliations.value.length}`)
    
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
    allSkills,
    allAffiliations,
    
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
    fetchStudentSkills,
    fetchStudentAffiliations,
    fetchAllStudentSkillsAndAffiliations,
    setFilter,
    clearFilter,
    setDisplayMode,
    setCurrentPage,
    clearCurrentStudent,
    clearError,
    generateSampleData
  }
})
