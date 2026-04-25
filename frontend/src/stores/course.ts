import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Course, CourseFormData, CourseFilter, CourseAnalytics, DepartmentCourseCount, SemesterCourseCount } from '@/types/course'
import api from '@/services/api'

// Helper function to convert snake_case to camelCase
const toCamelCase = (obj: any): any => {
  if (obj === null || obj === undefined) return obj
  
  if (Array.isArray(obj)) {
    return obj.map(item => toCamelCase(item))
  }
  
  if (typeof obj === 'object') {
    const result: any = {}
    for (const key in obj) {
      if (obj.hasOwnProperty(key)) {
        const camelKey = key.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase())
        result[camelKey] = toCamelCase(obj[key])
      }
    }
    return result
  }
  
  return obj
}

export const useCourseStore = defineStore('course', () => {
  // State
  const courses = ref<Course[]>([])
  const loading = ref(false)
  const error = ref('')
  const filter = ref<CourseFilter>({
    search: '',
    department: '',
    semester: '',
    status: '',
    academicYear: ''
  })

  // Getters
  const filteredCourses = computed(() => {
    let filtered = courses.value

    // Search filter
    if (filter.value.search) {
      const query = filter.value.search.toLowerCase()
      filtered = filtered.filter(course =>
        course.courseCode.toLowerCase().includes(query) ||
        course.courseName.toLowerCase().includes(query) ||
        course.description.toLowerCase().includes(query) ||
        course.instructor?.toLowerCase().includes(query)
      )
    }

    // Department filter
    if (filter.value.department) {
      filtered = filtered.filter(course => course.department === filter.value.department)
    }

    // Semester filter
    if (filter.value.semester) {
      filtered = filtered.filter(course => course.semester === filter.value.semester)
    }

    // Status filter
    if (filter.value.status) {
      filtered = filtered.filter(course => course.status === filter.value.status)
    }

    // Academic year filter
    if (filter.value.academicYear) {
      filtered = filtered.filter(course => course.academicYear === filter.value.academicYear)
    }

    return filtered
  })

  const analytics = computed((): CourseAnalytics => {
    const total = courses.value.length
    const active = courses.value.filter(c => c.status === 'active').length
    const inactive = courses.value.filter(c => c.status === 'inactive').length
    const archived = courses.value.filter(c => c.status === 'archived').length
    
    const departments = [...new Set(courses.value.map(c => c.department))]
    const totalStudents = courses.value.reduce((sum, course) => sum + course.currentStudents, 0)
    const averageCredits = total > 0 ? courses.value.reduce((sum, course) => sum + course.credits, 0) / total : 0

    // Courses by department
    const deptGroups = courses.value.reduce((groups: Record<string, number>, course) => {
      groups[course.department] = (groups[course.department] || 0) + 1
      return groups
    }, {})

    const coursesByDepartment: DepartmentCourseCount[] = Object.entries(deptGroups).map(([department, count]) => ({
      department,
      count,
      percentage: total > 0 ? (count / total) * 100 : 0
    })).sort((a, b) => b.count - a.count)

    // Courses by semester
    const semesterGroups = courses.value.reduce((groups: Record<string, number>, course) => {
      groups[course.semester] = (groups[course.semester] || 0) + 1
      return groups
    }, {})

    const coursesBySemester: SemesterCourseCount[] = Object.entries(semesterGroups).map(([semester, count]) => ({
      semester,
      count,
      percentage: total > 0 ? (count / total) * 100 : 0
    })).sort((a, b) => b.count - a.count)

    return {
      totalCourses: total,
      activeCourses: active,
      inactiveCourses: inactive,
      archivedCourses: archived,
      totalDepartments: departments.length,
      averageCredits,
      totalStudents,
      coursesByDepartment,
      coursesBySemester
    }
  })

  const availableDepartments = computed(() => {
    return [...new Set(courses.value.map(c => c.department))].sort()
  })

  const availableSemesters = computed(() => {
    return [...new Set(courses.value.map(c => c.semester))].sort()
  })

  const availableAcademicYears = computed(() => {
    return [...new Set(courses.value.map(c => c.academicYear))].sort()
  })

  const availableStatuses = computed(() => {
    return ['active', 'inactive', 'archived']
  })

  // Actions
  const fetchCourses = async () => {
    loading.value = true
    error.value = ''
    try {
      console.log('Fetching courses from API...')
      const response = await api.get('/courses')
      // Convert snake_case response to camelCase
      courses.value = toCamelCase(response.data)
      console.log('Courses fetched successfully:', courses.value.length, 'courses')
    } catch (err: any) {
      console.error('Error fetching courses:', err)
      
      // If 401 error, user is not authenticated
      if (err.response?.status === 401) {
        error.value = 'Authentication required. Please log in to access courses.'
        console.warn('Authentication required for courses API')
      } else {
        error.value = err.response?.data?.message || 'Failed to fetch courses'
      }
      
      // Fallback to sample data if available
      if (courses.value.length === 0) {
        console.log('No courses in store, generating sample data as fallback')
        generateSampleData()
      }
    } finally {
      loading.value = false
    }
  }

  const fetchCourse = async (id: number): Promise<Course | null> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.get(`/courses/${id}`)
      // Convert snake_case response to camelCase
      return toCamelCase(response.data)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch course'
      console.error('Error fetching course:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const createCourse = async (courseData: CourseFormData): Promise<Course | null> => {
    loading.value = true
    error.value = ''
    try {
      const apiData = {
        courseCode: courseData.courseCode,
        courseName: courseData.courseName,
        description: courseData.description,
        credits: courseData.credits,
        department: courseData.department,
        semester: courseData.semester,
        academicYear: courseData.academicYear,
        instructor: courseData.instructor,
        schedule: courseData.schedule,
        room: courseData.room,
        maxStudents: courseData.maxStudents,
        status: courseData.status,
        prerequisites: courseData.prerequisites
      }
      
      const response = await api.post('/courses', apiData)
      const newCourse = toCamelCase(response.data)
      courses.value.push(newCourse)
      return newCourse
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create course'
      console.error('Error creating course:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const updateCourse = async (id: number, courseData: CourseFormData): Promise<Course | null> => {
    loading.value = true
    error.value = ''
    try {
      const apiData = {
        courseCode: courseData.courseCode,
        courseName: courseData.courseName,
        description: courseData.description,
        credits: courseData.credits,
        department: courseData.department,
        semester: courseData.semester,
        academicYear: courseData.academicYear,
        instructor: courseData.instructor,
        schedule: courseData.schedule,
        room: courseData.room,
        maxStudents: courseData.maxStudents,
        status: courseData.status,
        prerequisites: courseData.prerequisites
      }
      
      const response = await api.put(`/courses/${id}`, apiData)
      const updatedCourse = toCamelCase(response.data)
      const index = courses.value.findIndex(c => c.id === id)
      if (index !== -1) {
        courses.value[index] = updatedCourse
      }
      return updatedCourse
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update course'
      console.error('Error updating course:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const deleteCourse = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      await api.delete(`/courses/${id}`)
      courses.value = courses.value.filter(c => c.id !== id)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete course'
      console.error('Error deleting course:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  const archiveCourse = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.patch(`/courses/${id}/archive`)
      const archivedCourse = toCamelCase(response.data)
      const index = courses.value.findIndex(c => c.id === id)
      if (index !== -1) {
        courses.value[index] = archivedCourse
      }
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to archive course'
      console.error('Error archiving course:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  const generateSampleData = () => {
    const sampleCourses: Course[] = [
      {
        id: 1,
        courseCode: 'CCS 101',
        courseName: 'Introduction to Computer Science',
        description: 'Fundamental concepts of computer science and programming',
        credits: 3,
        department: 'Computer Science',
        semester: 'First Semester',
        academicYear: '2024-2025',
        instructor: 'Dr. John Smith',
        schedule: 'MWF 9:00-10:30 AM',
        room: 'Room 101',
        maxStudents: 40,
        currentStudents: 35,
        status: 'active',
        prerequisites: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      },
      {
        id: 2,
        courseCode: 'CCS 102',
        courseName: 'Programming Fundamentals',
        description: 'Introduction to programming concepts and problem solving',
        credits: 3,
        department: 'Computer Science',
        semester: 'First Semester',
        academicYear: '2024-2025',
        instructor: 'Prof. Jane Doe',
        schedule: 'TTH 1:00-2:30 PM',
        room: 'Room 102',
        maxStudents: 35,
        currentStudents: 32,
        status: 'active',
        prerequisites: ['CCS 101'],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      },
      {
        id: 3,
        courseCode: 'CCS 103',
        courseName: 'Data Structures and Algorithms',
        description: 'Advanced data structures and algorithm analysis',
        credits: 4,
        department: 'Computer Science',
        semester: 'Second Semester',
        academicYear: '2024-2025',
        instructor: 'Dr. Robert Johnson',
        schedule: 'MWF 2:00-3:30 PM',
        room: 'Room 103',
        maxStudents: 30,
        currentStudents: 28,
        status: 'active',
        prerequisites: ['CCS 102'],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      }
    ]

    // Generate CCS 101 to CCS 112
    for (let i = 4; i <= 12; i++) {
      const courseNames = [
        'Web Development Fundamentals',
        'Database Management Systems',
        'Software Engineering',
        'Computer Networks',
        'Operating Systems',
        'Artificial Intelligence',
        'Machine Learning',
        'Cybersecurity Fundamentals',
        'Mobile Application Development'
      ]

      sampleCourses.push({
        id: i,
        courseCode: `CCS ${100 + i}`,
        courseName: courseNames[i - 4] || `Advanced Computer Science ${i}`,
        description: `Advanced topics in computer science focusing on ${courseNames[i - 4]?.toLowerCase() || 'specialized areas'}`,
        credits: 3 + (i % 2),
        department: 'Computer Science',
        semester: i % 2 === 0 ? 'First Semester' : 'Second Semester',
        academicYear: '2024-2025',
        instructor: `Prof. Instructor ${i}`,
        schedule: i % 2 === 0 ? 'TTH 9:00-10:30 AM' : 'MWF 1:00-2:30 PM',
        room: `Room ${100 + i}`,
        maxStudents: 30 + (i % 3) * 5,
        currentStudents: 25 + (i % 4) * 3,
        status: i <= 8 ? 'active' : 'inactive',
        prerequisites: i > 1 ? [`CCS ${99 + i}`] : [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }

    courses.value = sampleCourses
  }

  const setFilter = (newFilter: Partial<CourseFilter>) => {
    filter.value = { ...filter.value, ...newFilter }
  }

  const resetFilter = () => {
    filter.value = {
      search: '',
      department: '',
      semester: '',
      status: '',
      academicYear: ''
    }
  }

  return {
    // State
    courses,
    loading,
    error,
    filter,

    // Getters
    filteredCourses,
    analytics,
    availableDepartments,
    availableSemesters,
    availableAcademicYears,
    availableStatuses,

    // Actions
    fetchCourses,
    fetchCourse,
    createCourse,
    updateCourse,
    deleteCourse,
    archiveCourse,
    generateSampleData,
    setFilter,
    resetFilter
  }
})
