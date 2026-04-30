import axios from 'axios'
import { offlineService } from './offline'

const API_BASE_URL = 'http://127.0.0.1:8000/api'

// Helper function to check if using demo mode
const isDemoMode = () => {
  const token = localStorage.getItem('auth_token')
  return token ? token.startsWith('demo-') : false
}

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  console.log('API Request:', config.method?.toUpperCase(), config.url, config.data || 'no body')
  const token = localStorage.getItem('auth_token')
  if (token) {
    // Add Authorization header for both real and demo tokens
    config.headers.Authorization = `Bearer ${token}`
    console.log('Adding auth token:', token.substring(0, 20) + '...')
  } else {
    console.warn('No auth token found in localStorage')
  }
  return config
})

api.interceptors.response.use(
  (response) => {
    console.log('API Response:', response.status, response.config.url)
    return response
  },
  (error) => {
    console.error('API Error:', error.response?.status, error.config?.url, error.response?.data)
    
    // Log detailed error information for 422 errors
    if (error.response?.status === 422) {
      console.error('Validation Errors:', error.response.data.errors)
      console.error('Request Data:', error.config.data)
      console.error('Request Headers:', error.config.headers)
    }
    
    // Only auto-logout for real backend tokens, not demo tokens
    if (error.response?.status === 401 && !isDemoMode()) {
      console.warn('401 Unauthorized - logging out')
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Fallback handler for offline mode and demo mode
export const apiWithFallback = {
  async get(url: string, config?: any) {
    try {
      // Always try API first if online and not in demo mode
      if (offlineService.isOnline() && !isDemoMode()) {
        return await api.get(url, config)
      }
    } catch (error) {
      console.warn('API failed, falling back to offline/demo mode:', error)
    }
    
    // Fallback to localStorage
    if (url.includes('/announcements')) {
      return { data: offlineService.getLocalAnnouncements() }
    }
    
    if (url.includes('/students')) {
      const students = offlineService.getLocalStudents()
      // Handle both list and single student requests
      if (url.includes('/students/statistics')) {
        return { 
          data: {
            totalStudents: students.length,
            activeStudents: students.filter(s => s.isActive).length,
            averageGPA: students.reduce((sum, s) => sum + s.academicStanding.currentGPA, 0) / students.length,
            topSkills: [],
            commonActivities: [],
            violationCount: students.reduce((sum, s) => sum + s.violations.length, 0)
          }
        }
      }
      return { 
        data: url.includes('/students/') && !url.endsWith('/students') 
          ? students.find(s => s.id === parseInt(url.split('/').pop() || '0'))
          : { students, total: students.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Professors endpoint
    if (url.includes('/professors')) {
      const professors = offlineService.getLocalProfessors?.() || []
      return { 
        data: url.includes('/professors/') && !url.endsWith('/professors')
          ? professors.find(p => p.id === parseInt(url.split('/').pop() || '0'))
          : { professors, total: professors.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Violations endpoint
    if (url.includes('/violations')) {
      const violations = offlineService.getLocalViolations?.() || []
      return { 
        data: url.includes('/violations/') && !url.endsWith('/violations')
          ? violations.find(v => v.id === parseInt(url.split('/').pop() || '0'))
          : { violations, total: violations.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Courses endpoint
    if (url.includes('/courses')) {
      const courses = offlineService.getLocalCourses?.() || []
      return { 
        data: url.includes('/courses/') && !url.endsWith('/courses')
          ? courses.find(c => c.id === parseInt(url.split('/').pop() || '0'))
          : { courses, total: courses.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Events endpoint
    if (url.includes('/events')) {
      const events = offlineService.getLocalEvents?.() || []
      return { 
        data: url.includes('/events/') && !url.endsWith('/events')
          ? events.find(e => e.id === parseInt(url.split('/').pop() || '0'))
          : { events, total: events.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Rooms endpoint
    if (url.includes('/rooms')) {
      const rooms = offlineService.getLocalRooms?.() || []
      return { 
        data: url.includes('/rooms/') && !url.endsWith('/rooms')
          ? rooms.find(r => r.id === parseInt(url.split('/').pop() || '0'))
          : { rooms, total: rooms.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Schedules endpoint
    if (url.includes('/schedules')) {
      const schedules = offlineService.getLocalSchedules?.() || []
      return { 
        data: url.includes('/schedules/') && !url.endsWith('/schedules')
          ? schedules.find(s => s.id === parseInt(url.split('/').pop() || '0'))
          : { schedules, total: schedules.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    // Syllabi endpoint
    if (url.includes('/syllabi')) {
      const syllabi = offlineService.getLocalSyllabi?.() || []
      return { 
        data: url.includes('/syllabi/') && !url.endsWith('/syllabi')
          ? syllabi.find(s => s.id === parseInt(url.split('/').pop() || '0'))
          : { syllabi, total: syllabi.length, page: 1, limit: 10, totalPages: 1 }
      }
    }
    
    throw new Error('No offline data available for this endpoint')
  },

  async post(url: string, data?: any, config?: any) {
    try {
      if (offlineService.isOnline()) {
        return await api.post(url, data, config)
      }
    } catch (error) {
      console.warn('API failed, saving to offline mode:', error)
    }
    
    // Save to offline operations
    offlineService.addPendingOperation({
      type: 'CREATE',
      endpoint: url,
      data
    })
    
    // Return mock response
    if (url.includes('/announcements')) {
      const announcements = offlineService.getLocalAnnouncements()
      const newId = Math.max(...announcements.map(a => a.id), 0) + 1
      const newItem = { id: newId, ...data, created_at: new Date().toISOString() }
      announcements.push(newItem)
      offlineService.setLocalAnnouncements(announcements)
      return { data: newItem }
    }
    
    if (url.includes('/students')) {
      // Handle nested operations (skills, activities)
      if (url.includes('/skills')) {
        const urlParts = url.split('/')
        const studentId = parseInt(urlParts[2] || '0')
        const students = offlineService.getLocalStudents()
        const student = students.find(s => s.id === studentId)
        if (student) {
          const newSkill = {
            id: Date.now(),
            ...data,
            category: data.category || 'technical',
            proficiency: data.proficiency || 'beginner'
          }
          student.skills.push(newSkill)
          offlineService.setLocalStudents(students)
          return { data: newSkill }
        }
      }
      
      if (url.includes('/activities')) {
        const urlParts = url.split('/')
        const studentId = parseInt(urlParts[2] || '0')
        const students = offlineService.getLocalStudents()
        const student = students.find(s => s.id === studentId)
        if (student) {
          const newActivity = {
            id: Date.now(),
            ...data,
            type: data.type || 'organization',
            level: data.level || 'local'
          }
          student.activities.push(newActivity)
          offlineService.setLocalStudents(students)
          return { data: newActivity }
        }
      }
      
      // Regular student creation
      const students = offlineService.getLocalStudents()
      const newId = Math.max(...students.map(s => s.id), 0) + 1
      const newItem = { 
        id: newId, 
        ...data, 
        academicHistory: [],
        activities: [],
        violations: [],
        skills: [],
        affiliations: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      }
      students.push(newItem)
      offlineService.setLocalStudents(students)
      return { data: newItem }
    }
    
    throw new Error('No offline handler for this endpoint')
  },

  async put(url: string, data?: any, config?: any) {
    try {
      if (offlineService.isOnline()) {
        return await api.put(url, data, config)
      }
    } catch (error) {
      console.warn('API failed, saving to offline mode:', error)
    }
    
    // Save to offline operations
    offlineService.addPendingOperation({
      type: 'UPDATE',
      endpoint: url,
      data
    })
    
    // Update local data
    if (url.includes('/announcements')) {
      const announcements = offlineService.getLocalAnnouncements()
      const id = parseInt(url.split('/').pop() || '0')
      const index = announcements.findIndex(a => a.id === id)
      if (index !== -1) {
        announcements[index] = { ...announcements[index], ...data, updated_at: new Date().toISOString() }
        offlineService.setLocalAnnouncements(announcements)
        return { data: announcements[index] }
      }
    }
    
    if (url.includes('/students')) {
      const students = offlineService.getLocalStudents()
      const id = parseInt(url.split('/').pop() || '0')
      const index = students.findIndex(s => s.id === id)
      if (index !== -1) {
        students[index] = { ...students[index], ...data, updatedAt: new Date().toISOString() }
        offlineService.setLocalStudents(students)
        return { data: students[index] }
      }
    }
    
    throw new Error('No offline handler for this endpoint')
  },

  async delete(url: string, config?: any) {
    try {
      if (offlineService.isOnline()) {
        return await api.delete(url, config)
      }
    } catch (error) {
      console.warn('API failed, saving to offline mode:', error)
    }
    
    // Save to offline operations
    offlineService.addPendingOperation({
      type: 'DELETE',
      endpoint: url,
      data: null
    })
    
    // Remove from local data
    if (url.includes('/announcements')) {
      const announcements = offlineService.getLocalAnnouncements()
      const id = parseInt(url.split('/').pop() || '0')
      const filtered = announcements.filter(a => a.id !== id)
      offlineService.setLocalAnnouncements(filtered)
      return { data: null }
    }
    
    if (url.includes('/students')) {
      // Handle nested operations (skills, activities)
      if (url.includes('/skills')) {
        const urlParts = url.split('/')
        const studentId = parseInt(urlParts[2] || '0')
        const skillId = parseInt(urlParts[4] || '0')
        const students = offlineService.getLocalStudents()
        const student = students.find(s => s.id === studentId)
        if (student) {
          student.skills = student.skills.filter((skill: any) => skill.id !== skillId)
          offlineService.setLocalStudents(students)
          return { data: null }
        }
      }
      
      if (url.includes('/activities')) {
        const urlParts = url.split('/')
        const studentId = parseInt(urlParts[2] || '0')
        const activityId = parseInt(urlParts[4] || '0')
        const students = offlineService.getLocalStudents()
        const student = students.find(s => s.id === studentId)
        if (student) {
          student.activities = student.activities.filter((activity: any) => activity.id !== activityId)
          offlineService.setLocalStudents(students)
          return { data: null }
        }
      }
      
      // Regular student deletion
      const students = offlineService.getLocalStudents()
      const id = parseInt(url.split('/').pop() || '0')
      const filtered = students.filter(s => s.id !== id)
      offlineService.setLocalStudents(filtered)
      return { data: null }
    }
    
    throw new Error('No offline handler for this endpoint')
  },

  async patch(url: string, data?: any, config?: any) {
    try {
      if (offlineService.isOnline()) {
        return await api.patch(url, data, config)
      }
    } catch (error) {
      console.warn('API failed, saving to offline mode:', error)
    }
    
    // Save to offline operations
    offlineService.addPendingOperation({
      type: 'UPDATE',
      endpoint: url,
      data
    })
    
    // Update local data
    if (url.includes('/announcements')) {
      const announcements = offlineService.getLocalAnnouncements()
      const id = parseInt(url.split('/').pop() || '0')
      const index = announcements.findIndex(a => a.id === id)
      if (index !== -1) {
        announcements[index] = { ...announcements[index], ...data, updated_at: new Date().toISOString() }
        offlineService.setLocalAnnouncements(announcements)
        return { data: announcements[index] }
      }
    }
    
    if (url.includes('/students')) {
      const students = offlineService.getLocalStudents()
      const id = parseInt(url.split('/').pop() || '0')
      const index = students.findIndex(s => s.id === id)
      if (index !== -1) {
        students[index] = { ...students[index], ...data, updatedAt: new Date().toISOString() }
        offlineService.setLocalStudents(students)
        return { data: students[index] }
      }
    }
    
    throw new Error('No offline handler for this endpoint')
  }
}

export default api
