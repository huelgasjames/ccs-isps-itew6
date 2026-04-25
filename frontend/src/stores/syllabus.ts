import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import type { Syllabus, SyllabusFormData, SyllabusFilter, SyllabusStats } from '@/types/syllabus'

const toCamelCase = (obj: any): any => {
  if (obj === null || obj === undefined) return obj
  if (Array.isArray(obj)) return obj.map((item) => toCamelCase(item))
  if (typeof obj !== 'object') return obj

  const result: any = {}
  for (const key in obj) {
    if (Object.prototype.hasOwnProperty.call(obj, key)) {
      const camelKey = key.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase())
      result[camelKey] = toCamelCase(obj[key])
    }
  }
  return result
}

export const useSyllabusStore = defineStore('syllabus', () => {
  // State
  const syllabi = ref<Syllabus[]>([])
  const loading = ref(false)
  const error = ref('')
  const filter = ref<SyllabusFilter>({})
  const stats = ref<SyllabusStats | null>(null)

  // Getters
  const filteredSyllabi = computed(() => {
    let filtered = syllabi.value

    if (filter.value.courseId) {
      filtered = filtered.filter(s => s.courseId === filter.value.courseId)
    }

    if (filter.value.professorId) {
      filtered = filtered.filter(s => s.professorId === filter.value.professorId)
    }

    if (filter.value.academicYear) {
      filtered = filtered.filter(s => s.academicYear === filter.value.academicYear)
    }

    if (filter.value.semester) {
      filtered = filtered.filter(s => s.semester === filter.value.semester)
    }

    if (filter.value.approved !== undefined) {
      filtered = filtered.filter(s => s.approved === filter.value.approved)
    }

    if (filter.value.search) {
      const search = filter.value.search.toLowerCase()
      filtered = filtered.filter(s => 
        s.course?.courseName?.toLowerCase().includes(search) ||
        s.course?.courseCode?.toLowerCase().includes(search) ||
        s.professor?.firstName?.toLowerCase().includes(search) ||
        s.professor?.lastName?.toLowerCase().includes(search) ||
        s.courseDescription.toLowerCase().includes(search)
      )
    }

    return filtered
  })

  const approvedSyllabi = computed(() => 
    syllabi.value.filter(s => s.approved)
  )

  const pendingSyllabi = computed(() => 
    syllabi.value.filter(s => !s.approved && s.approvedAt === null)
  )

  const rejectedSyllabi = computed(() => 
    syllabi.value.filter(s => !s.approved && s.approvedAt !== null)
  )

  const availableAcademicYears = computed(() => {
    const years = [...new Set(syllabi.value.map(s => s.academicYear))]
    return years.sort((a, b) => b.localeCompare(a))
  })

  const availableSemesters = computed(() => {
    const semesters = [...new Set(syllabi.value.map(s => s.semester))]
    return semesters.sort()
  })

  // Actions
  const fetchSyllabi = async () => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.get('/syllabi', { params: filter.value })
      syllabi.value = toCamelCase(response.data.data || response.data)
      console.log('Syllabi fetched successfully:', syllabi.value.length, 'syllabi')
    } catch (err: any) {
      console.error('Error fetching syllabi:', err)
      
      if (err.response?.status === 401) {
        error.value = 'Authentication required. Please log in to access syllabi.'
      } else {
        error.value = err.response?.data?.message || 'Failed to fetch syllabi'
      }
      
      // Fallback to sample data if available
      if (syllabi.value.length === 0) {
        console.log('No syllabi in store, generating sample data as fallback')
        generateSampleData()
      }
    } finally {
      loading.value = false
    }
  }

  const fetchSyllabus = async (id: number): Promise<Syllabus | null> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.get(`/syllabi/${id}`)
      const syllabus = toCamelCase(response.data)
      
      // Update in store if exists
      const index = syllabi.value.findIndex(s => s.id === id)
      if (index !== -1) {
        syllabi.value[index] = syllabus
      }
      
      return syllabus
    } catch (err: any) {
      console.error('Error fetching syllabus:', err)
      error.value = err.response?.data?.message || 'Failed to fetch syllabus'
      return null
    } finally {
      loading.value = false
    }
  }

  const createSyllabus = async (syllabusData: SyllabusFormData): Promise<Syllabus | null> => {
    loading.value = true
    error.value = ''
    try {
      // Convert camelCase to snake_case for the API
      const apiData = {
        course_id: syllabusData.courseId,
        professor_id: syllabusData.professorId,
        academic_year: syllabusData.academicYear,
        semester: syllabusData.semester,
        course_description: syllabusData.courseDescription,
        learning_objectives: syllabusData.learningObjectives,
        topics_outline: syllabusData.topicsOutline,
        assessment_methods: syllabusData.assessmentMethods,
        grading_policy: syllabusData.gradingPolicy,
        required_materials: syllabusData.requiredMaterials,
        class_policies: syllabusData.classPolicies,
        approved: syllabusData.approved || false
      }
      
      const response = await api.post('/syllabi', apiData)
      const newSyllabus = toCamelCase(response.data.syllabus || response.data)
      syllabi.value.push(newSyllabus)
      return newSyllabus
    } catch (err: any) {
      console.error('Error creating syllabus:', err)
      error.value = err.response?.data?.message || 'Failed to create syllabus'
      return null
    } finally {
      loading.value = false
    }
  }

  const updateSyllabus = async (id: number, syllabusData: Partial<SyllabusFormData>): Promise<Syllabus | null> => {
    loading.value = true
    error.value = ''
    try {
      // Convert camelCase to snake_case for the API
      const apiData: any = {}
      if (syllabusData.courseId !== undefined) apiData.course_id = syllabusData.courseId
      if (syllabusData.professorId !== undefined) apiData.professor_id = syllabusData.professorId
      if (syllabusData.academicYear !== undefined) apiData.academic_year = syllabusData.academicYear
      if (syllabusData.semester !== undefined) apiData.semester = syllabusData.semester
      if (syllabusData.courseDescription !== undefined) apiData.course_description = syllabusData.courseDescription
      if (syllabusData.learningObjectives !== undefined) apiData.learning_objectives = syllabusData.learningObjectives
      if (syllabusData.topicsOutline !== undefined) apiData.topics_outline = syllabusData.topicsOutline
      if (syllabusData.assessmentMethods !== undefined) apiData.assessment_methods = syllabusData.assessmentMethods
      if (syllabusData.gradingPolicy !== undefined) apiData.grading_policy = syllabusData.gradingPolicy
      if (syllabusData.requiredMaterials !== undefined) apiData.required_materials = syllabusData.requiredMaterials
      if (syllabusData.classPolicies !== undefined) apiData.class_policies = syllabusData.classPolicies
      if (syllabusData.approved !== undefined) apiData.approved = syllabusData.approved
      
      const response = await api.put(`/syllabi/${id}`, apiData)
      const updatedSyllabus = toCamelCase(response.data.syllabus || response.data)
      
      // Update in store
      const index = syllabi.value.findIndex(s => s.id === id)
      if (index !== -1) {
        syllabi.value[index] = updatedSyllabus
      }
      
      return updatedSyllabus
    } catch (err: any) {
      console.error('Error updating syllabus:', err)
      error.value = err.response?.data?.message || 'Failed to update syllabus'
      return null
    } finally {
      loading.value = false
    }
  }

  const deleteSyllabus = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      await api.delete(`/syllabi/${id}`)
      
      // Remove from store
      const index = syllabi.value.findIndex(s => s.id === id)
      if (index !== -1) {
        syllabi.value.splice(index, 1)
      }
      
      return true
    } catch (err: any) {
      console.error('Error deleting syllabus:', err)
      error.value = err.response?.data?.message || 'Failed to delete syllabus'
      return false
    } finally {
      loading.value = false
    }
  }

  const approveSyllabus = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.patch(`/syllabi/${id}/approve`)
      const updatedSyllabus = toCamelCase(response.data.syllabus || response.data)
      
      // Update in store
      const index = syllabi.value.findIndex(s => s.id === id)
      if (index !== -1) {
        syllabi.value[index] = updatedSyllabus
      }
      
      return true
    } catch (err: any) {
      console.error('Error approving syllabus:', err)
      error.value = err.response?.data?.message || 'Failed to approve syllabus'
      return false
    } finally {
      loading.value = false
    }
  }

  const rejectSyllabus = async (id: number): Promise<boolean> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.patch(`/syllabi/${id}/reject`)
      const updatedSyllabus = toCamelCase(response.data.syllabus || response.data)
      
      // Update in store
      const index = syllabi.value.findIndex(s => s.id === id)
      if (index !== -1) {
        syllabi.value[index] = updatedSyllabus
      }
      
      return true
    } catch (err: any) {
      console.error('Error rejecting syllabus:', err)
      error.value = err.response?.data?.message || 'Failed to reject syllabus'
      return false
    } finally {
      loading.value = false
    }
  }

  const uploadFile = async (id: number, file: File): Promise<string | null> => {
    loading.value = true
    error.value = ''
    try {
      const formData = new FormData()
      formData.append('file', file)
      
      const response = await api.post(`/syllabi/${id}/upload-file`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      const filePath = response.data.file_path
      const downloadUrl = response.data.download_url
      
      // Update in store
      const index = syllabi.value.findIndex(s => s.id === id)
      if (index !== -1 && syllabi.value[index]) {
        syllabi.value[index].filePath = filePath
      }
      
      return downloadUrl
    } catch (err: any) {
      console.error('Error uploading file:', err)
      error.value = err.response?.data?.message || 'Failed to upload file'
      return null
    } finally {
      loading.value = false
    }
  }

  const getSyllabiByCourse = async (courseId: number): Promise<Syllabus[]> => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.get(`/courses/${courseId}/syllabi`)
      return toCamelCase(response.data)
    } catch (err: any) {
      console.error('Error fetching course syllabi:', err)
      error.value = err.response?.data?.message || 'Failed to fetch course syllabi'
      return []
    } finally {
      loading.value = false
    }
  }

  const generateSampleData = async () => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.post('/syllabi/generate-sample-data')
      const sampleSyllabi = toCamelCase(response.data.syllabi || [])
      
      // Add to store
      syllabi.value.push(...sampleSyllabi)
      
      console.log('Sample syllabi generated:', sampleSyllabi.length, 'syllabi')
    } catch (err: any) {
      console.error('Error generating sample syllabi:', err)
      error.value = err.response?.data?.message || 'Failed to generate sample syllabi'
    } finally {
      loading.value = false
    }
  }

  const setFilter = (newFilter: Partial<SyllabusFilter>) => {
    filter.value = { ...filter.value, ...newFilter }
  }

  const resetFilter = () => {
    filter.value = {}
  }

  const clearError = () => {
    error.value = ''
  }

  return {
    // State
    syllabi,
    loading,
    error,
    filter,
    stats,
    
    // Getters
    filteredSyllabi,
    approvedSyllabi,
    pendingSyllabi,
    rejectedSyllabi,
    availableAcademicYears,
    availableSemesters,
    
    // Actions
    fetchSyllabi,
    fetchSyllabus,
    createSyllabus,
    updateSyllabus,
    deleteSyllabus,
    approveSyllabus,
    rejectSyllabus,
    uploadFile,
    getSyllabiByCourse,
    generateSampleData,
    setFilter,
    resetFilter,
    clearError
  }
})
