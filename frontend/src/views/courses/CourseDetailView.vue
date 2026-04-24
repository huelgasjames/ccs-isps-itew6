<template>
  <div class="course-detail-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/courses" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M15 19l-7-7 7-7"/>
            </svg>
            Courses
          </router-link>
          <h1>Course Details</h1>
        </div>
        <div class="header-actions">
          <router-link :to="`/courses/${course?.id}/edit`" class="btn btn-primary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Course
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="courseStore.loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading course data...</p>
    </div>

    <!-- Course Details -->
    <div v-else-if="course" class="course-content">
      <!-- Course Overview Card -->
      <div class="course-overview-card">
        <div class="course-header">
          <div class="course-avatar">
            {{ getCourseInitials(course.courseCode) }}
          </div>
          <div class="course-info">
            <h2>{{ course.courseCode }}</h2>
            <p class="course-name">{{ course.courseName }}</p>
            <div class="course-badges">
              <span :class="['status-badge', getStatusClass(course.status)]">
                {{ course.status.charAt(0).toUpperCase() + course.status.slice(1) }}
              </span>
              <span class="department-badge">{{ course.department }}</span>
              <span class="credits-badge">{{ course.credits }} Credits</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Information Grid -->
      <div class="info-grid">
        <!-- Basic Information -->
        <div class="info-card">
          <div class="card-header">
            <h3>Basic Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6 4l8 8v8H6z"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="info-item">
              <span class="label">Course Code</span>
              <span class="value">{{ course.courseCode }}</span>
            </div>
            <div class="info-item">
              <span class="label">Course Name</span>
              <span class="value">{{ course.courseName }}</span>
            </div>
            <div class="info-item">
              <span class="label">Credits</span>
              <span class="value">{{ course.credits }}</span>
            </div>
            <div class="info-item">
              <span class="label">Department</span>
              <span class="value">{{ course.department }}</span>
            </div>
          </div>
        </div>

        <!-- Schedule Information -->
        <div class="info-card">
          <div class="card-header">
            <h3>Schedule Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="info-item">
              <span class="label">Semester</span>
              <span class="value">{{ course.semester }}</span>
            </div>
            <div class="info-item">
              <span class="label">Academic Year</span>
              <span class="value">{{ course.academicYear }}</span>
            </div>
            <div class="info-item">
              <span class="label">Schedule</span>
              <span class="value">{{ course.schedule || 'TBA' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Room</span>
              <span class="value">{{ course.room || 'TBA' }}</span>
            </div>
          </div>
        </div>

        <!-- Instructor Information -->
        <div class="info-card">
          <div class="card-header">
            <h3>Instructor Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="info-item">
              <span class="label">Instructor</span>
              <span class="value">{{ course.instructor || 'TBA' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Max Students</span>
              <span class="value">{{ course.maxStudents }}</span>
            </div>
            <div class="info-item">
              <span class="label">Current Students</span>
              <span class="value">{{ course.currentStudents }}</span>
            </div>
            <div class="info-item">
              <span class="label">Available Slots</span>
              <span class="value">{{ course.maxStudents - course.currentStudents }}</span>
            </div>
            <div class="info-item">
              <span class="label">Enrollment Status</span>
              <span class="value">
                <span :class="['enrollment-status-badge', getCapacityStatusClass(course.currentStudents, course.maxStudents)]">
                  {{ getCapacityStatus(course.currentStudents, course.maxStudents) }}
                </span>
              </span>
            </div>
            <div class="info-item">
              <span class="label">Enrollment Progress</span>
              <div class="enrollment-progress-bar">
                <div 
                  class="enrollment-progress-fill" 
                  :class="getCapacityStatusClass(course.currentStudents, course.maxStudents)"
                  :style="{ width: (course.currentStudents / course.maxStudents * 100) + '%' }"
                ></div>
                <span class="enrollment-percentage">{{ Math.round(course.currentStudents / course.maxStudents * 100) }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Description -->
        <div class="info-card full-width">
          <div class="card-header">
            <h3>Course Description</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14,2 14,8 20,8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10,9 9,9 8,9"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="description-content">
              <p>{{ course.description || 'No description available for this course.' }}</p>
            </div>
          </div>
        </div>

        <!-- Prerequisites -->
        <div class="info-card full-width" v-if="course.prerequisites && course.prerequisites.length > 0">
          <div class="card-header">
            <h3>Prerequisites</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
              <rect x="9" y="5" width="6" height="3"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="prerequisites-content">
              <div class="prerequisite-list">
                <span 
                  v-for="prereq in course.prerequisites" 
                  :key="prereq"
                  class="prerequisite-badge"
                >
                  {{ prereq }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Syllabi -->
        <div class="info-card full-width">
          <div class="card-header">
            <h3>Course Syllabi</h3>
            <router-link :to="`/syllabi/create?course=${course.id}`" class="btn btn-primary btn-small">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
              Create Syllabus
            </router-link>
          </div>
          
          <!-- Loading State -->
          <div v-if="loadingSyllabi" class="loading-small">
            <div class="spinner-small"></div>
            <p>Loading syllabi...</p>
          </div>
          
          <!-- Syllabi List -->
          <div v-else-if="courseSyllabi.length > 0" class="syllabi-list">
            <div
              v-for="syllabus in courseSyllabi"
              :key="syllabus.id"
              class="syllabus-item"
            >
              <div class="syllabus-header">
                <div class="syllabus-info">
                  <h4>{{ syllabus.semester }} {{ syllabus.academicYear }}</h4>
                  <p>{{ syllabus.professor?.firstName }} {{ syllabus.professor?.lastName }}</p>
                </div>
                <div class="syllabus-status">
                  <span :class="['status-badge', syllabus.approved ? 'approved' : 'pending']">
                    {{ syllabus.approved ? 'Approved' : 'Pending' }}
                  </span>
                </div>
              </div>
              <div class="syllabus-description">
                <p>{{ syllabus.courseDescription?.substring(0, 200) }}...</p>
              </div>
              <div class="syllabus-actions">
                <router-link :to="`/syllabi/${syllabus.id}`" class="btn btn-small btn-outline">
                  View Details
                </router-link>
                <router-link :to="`/syllabi/${syllabus.id}/edit`" class="btn btn-small btn-primary">
                  Edit
                </router-link>
                <button
                  v-if="syllabus.filePath"
                  @click="downloadSyllabus(syllabus)"
                  class="btn btn-small btn-secondary"
                >
                  Download
                </button>
              </div>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-else class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
            <h4>No Syllabi Available</h4>
            <p>This course doesn't have any syllabi yet.</p>
            <router-link :to="`/syllabi/create?course=${course.id}`" class="btn btn-primary">
              Create First Syllabus
            </router-link>
          </div>
        </div>

        <!-- Enrolled Students -->
        <div class="info-card full-width">
          <div class="card-header">
            <h3>Enrolled Students ({{ enrolledStudents.length }})</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="card-content">
            <!-- Loading State -->
            <div v-if="loadingEnrollments" class="loading-small">
              <div class="spinner-small"></div>
              <p>Loading enrolled students...</p>
            </div>
            
            <!-- Students List -->
            <div v-else-if="enrolledStudents.length > 0" class="students-list">
              <div class="students-grid">
                <div 
                  v-for="enrollment in enrolledStudents" 
                  :key="enrollment.id"
                  class="student-card"
                >
                  <div class="student-avatar">
                    {{ getStudentInitials(enrollment.student.first_name, enrollment.student.last_name) }}
                  </div>
                  <div class="student-info">
                    <h4>{{ enrollment.student.first_name }} {{ enrollment.student.last_name }}</h4>
                    <p class="student-id">{{ enrollment.student.student_id }}</p>
                    <p class="student-email">{{ enrollment.student.email }}</p>
                    <div class="enrollment-info">
                      <span :class="['status-badge', getEnrollmentStatusClass(enrollment.status)]">
                        {{ enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1) }}
                      </span>
                      <span class="grade-badge" v-if="enrollment.grade">
                        Grade: {{ enrollment.grade }}%
                      </span>
                      <button 
                        v-if="enrollment.status === 'enrolled'" 
                        @click="editEnrollment(enrollment)"
                        class="btn btn-small btn-primary"
                      >
                        Edit
                      </button>
                      <span class="date-badge">
                        Enrolled: {{ formatDate(enrollment.enrollment_date) }}
                      </span>
                    </div>
                  </div>
                  <div class="student-actions">
                    <router-link 
                      :to="`/students/${enrollment.student.id}`" 
                      class="btn btn-small btn-outline"
                    >
                      View Profile
                    </router-link>
                  </div>
                </div>
              </div>
              
              <!-- Enrollment Summary -->
              <div class="enrollment-summary">
                <div class="summary-stats">
                  <div class="stat-item">
                    <span class="stat-number">{{ enrolledStudents.length }}</span>
                    <span class="stat-label">Total Enrolled</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ getEnrolledCount('enrolled') }}</span>
                    <span class="stat-label">Currently Enrolled</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ getEnrolledCount('completed') }}</span>
                    <span class="stat-label">Completed</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ getEnrolledCount('dropped') }}</span>
                    <span class="stat-label">Dropped</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- No Students State -->
            <div v-else class="no-students">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
              <h4>No Students Enrolled</h4>
              <p>No students are currently enrolled in this course.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="courseStore.error" class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Error Loading Course Data</h3>
      <p>{{ courseStore.error }}</p>
      <router-link to="/courses" class="btn btn-primary">Back to Courses</router-link>
    </div>

    <!-- Enrollment Edit Modal -->
    <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Edit Enrollment</h3>
          <button @click="closeEditModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="grade">Grade (%)</label>
            <input
              id="grade"
              v-model.number="editForm.grade"
              type="number"
              min="0"
              max="100"
              class="form-input"
              placeholder="Enter grade"
            />
          </div>
          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" v-model="editForm.status" class="form-input">
              <option value="enrolled">Enrolled</option>
              <option value="completed">Completed</option>
              <option value="failed">Failed</option>
              <option value="dropped">Dropped</option>
            </select>
          </div>
          <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea
              id="remarks"
              v-model="editForm.remarks"
              class="form-input"
              rows="3"
              placeholder="Enter remarks (optional)"
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="closeEditModal" class="btn btn-outline">Cancel</button>
          <button @click="saveEnrollment" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCourseStore } from '@/stores/course'
import { useSyllabusStore } from '@/stores/syllabus'
import api from '@/services/api'
import type { Course } from '@/types/course'
import type { Syllabus } from '@/types/syllabus'

interface CourseEnrollment {
  id: number
  student: {
    id: number
    first_name: string
    last_name: string
    student_id: string
    email: string
  }
  semester: string
  academic_year: string
  grade?: number
  status: string
  enrollment_date: string
  remarks?: string
}

const route = useRoute()
const courseStore = useCourseStore()
const syllabusStore = useSyllabusStore()
const course = ref<Course | null>(null)
const enrolledStudents = ref<CourseEnrollment[]>([])
const loadingEnrollments = ref(false)
const courseSyllabi = ref<Syllabus[]>([])
const loadingSyllabi = ref(false)

const fetchCourse = async () => {
  const courseData = await courseStore.fetchCourse(Number(route.params.id))
  course.value = courseData
  if (courseData) {
    await fetchEnrolledStudents(courseData.id)
    await fetchCourseSyllabi(courseData.id)
  }
}

const fetchEnrolledStudents = async (courseId: number) => {
  loadingEnrollments.value = true
  try {
    const response = await api.get(`/courses/${courseId}/enrollments`)
    // Convert snake_case response to camelCase
    enrolledStudents.value = toCamelCase(response.data)
  } catch (error) {
    console.error('Error fetching enrolled students:', error)
  } finally {
    loadingEnrollments.value = false
  }
}

const fetchCourseSyllabi = async (courseId: number) => {
  loadingSyllabi.value = true
  try {
    courseSyllabi.value = await syllabusStore.getSyllabiByCourse(courseId)
  } catch (error) {
    console.error('Error fetching course syllabi:', error)
  } finally {
    loadingSyllabi.value = false
  }
}

const downloadSyllabus = async (syllabus: Syllabus) => {
  try {
    const response = await api.get(`/syllabi/${syllabus.id}/download-file`)
    const downloadUrl = response.data.download_url
    window.open(downloadUrl, '_blank')
  } catch (error) {
    console.error('Error downloading syllabus:', error)
    alert('Failed to download syllabus')
  }
}

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

const getCourseInitials = (courseCode: string) => {
  const parts = courseCode.split(' ')
  if (parts.length >= 2) {
    return parts[0] + ' ' + parts[1]
  }
  return courseCode.substring(0, 3).toUpperCase()
}

const getStudentInitials = (firstName: string, lastName: string) => {
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase()
}

const getStatusClass = (status: string) => {
  if (status === 'active') return 'active'
  if (status === 'inactive') return 'inactive'
  return 'archived'
}

const getEnrollmentStatusClass = (status: string) => {
  if (status === 'enrolled') return 'enrolled'
  if (status === 'completed') return 'completed'
  if (status === 'dropped') return 'dropped'
  if (status === 'failed') return 'failed'
  return 'enrolled'
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

const getEnrolledCount = (status: string) => {
  return enrolledStudents.value.filter(enrollment => enrollment.status === status).length
}

const getCapacityStatusClass = (current: number, max: number) => {
  const percentage = (current / max) * 100
  if (percentage >= 100) return 'full'
  if (percentage >= 80) return 'almost-full'
  if (percentage >= 60) return 'moderate'
  return 'available'
}

const getCapacityStatus = (current: number, max: number) => {
  const percentage = (current / max) * 100
  if (percentage >= 100) return 'Full'
  if (percentage >= 80) return 'Almost Full'
  if (percentage >= 60) return 'Moderate'
  return 'Available'
}

// Enrollment editing
const showEditModal = ref(false)
const editingEnrollment = ref<CourseEnrollment | null>(null)
const editForm = ref({
  grade: '',
  status: '',
  remarks: ''
})

const editEnrollment = (enrollment: CourseEnrollment) => {
  editingEnrollment.value = enrollment
  editForm.value = {
    grade: enrollment.grade?.toString() || '',
    status: enrollment.status,
    remarks: enrollment.remarks || ''
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  editingEnrollment.value = null
  editForm.value = {
    grade: '',
    status: '',
    remarks: ''
  }
}

const saveEnrollment = async () => {
  if (!editingEnrollment.value) return
  
  try {
    const response = await api.patch(`/enrollments/${editingEnrollment.value.id}`, editForm.value)
    
    // Update the enrollment in the list
    const index = enrolledStudents.value.findIndex(e => e.id === editingEnrollment.value!.id)
    if (index !== -1) {
      enrolledStudents.value[index] = response.data
    }
    
    closeEditModal()
  } catch (error) {
    console.error('Error updating enrollment:', error)
    alert('Failed to update enrollment. Please try again.')
  }
}

onMounted(() => {
  fetchCourse()
})
</script>

<style scoped>
.course-detail-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.back-link:hover {
  color: #3b82f6;
}

.header-left h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.icon-small {
  width: 16px;
  height: 16px;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Course Overview */
.course-overview-card {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
  border-left: 4px solid #3b82f6;
}

.course-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.course-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
  flex-shrink: 0;
}

.course-info h2 {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.course-name {
  color: #6b7280;
  font-size: 1.125rem;
  margin: 0 0 1rem 0;
}

.course-badges {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.active {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.inactive {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.archived {
  background-color: #f3f4f6;
  color: #374151;
}

.department-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  background-color: #dbeafe;
  color: #1e40af;
}

.credits-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

/* Information Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
}

.info-card {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.info-card.full-width {
  grid-column: 1 / -1;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.card-header h3 {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.card-header svg {
  width: 24px;
  height: 24px;
  color: #6b7280;
}

.card-content {
  padding: 1.5rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
  border-bottom: none;
}

.info-item .label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
  min-width: 140px;
  flex-shrink: 0;
}

.info-item .value {
  font-size: 0.875rem;
  color: #1f2937;
  text-align: right;
  word-break: break-word;
}

.description-content p {
  color: #1f2937;
  line-height: 1.6;
  margin: 0;
}

.enrollment-status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.enrollment-status-badge.available {
  background-color: #d1fae5;
  color: #065f46;
}

.enrollment-status-badge.moderate {
  background-color: #fef3c7;
  color: #92400e;
}

.enrollment-status-badge.almost-full {
  background-color: #fed7aa;
  color: #9a3412;
}

.enrollment-status-badge.full {
  background-color: #fee2e2;
  color: #991b1b;
}

.enrollment-progress-bar {
  position: relative;
  width: 100%;
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.enrollment-progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.enrollment-progress-fill.available {
  background: linear-gradient(90deg, #10b981, #059669);
}

.enrollment-progress-fill.moderate {
  background: linear-gradient(90deg, #f59e0b, #d97706);
}

.enrollment-progress-fill.almost-full {
  background: linear-gradient(90deg, #f97316, #ea580c);
}

.enrollment-progress-fill.full {
  background: linear-gradient(90deg, #ef4444, #dc2626);
}

.enrollment-percentage {
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
}

.prerequisites-content {
  margin-top: 0.5rem;
}

.prerequisite-list {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 1rem;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.modal-footer .btn {
  min-width: 80px;
}

.prerequisite-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #e5e7eb;
}

/* Syllabi Styles */
.syllabi-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.syllabus-item {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  background: #fafafa;
}

.syllabus-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.syllabus-info h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.syllabus-info p {
  color: #6b7280;
  font-size: 0.875rem;
}

.syllabus-status .status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.syllabus-status .status-badge.approved {
  background: #d1fae5;
  color: #065f46;
}

.syllabus-status .status-badge.pending {
  background: #fed7aa;
  color: #92400e;
}

.syllabus-description {
  margin-bottom: 0.75rem;
}

.syllabus-description p {
  color: #4b5563;
  font-size: 0.875rem;
  line-height: 1.5;
}

.syllabus-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

/* Error State */
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  text-align: center;
  color: #6b7280;
}

.error-state svg {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin-bottom: 1rem;
}

.error-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.error-state p {
  color: #6b7280;
  margin: 0 0 1.5rem 0;
  max-width: 400px;
}

/* Enrolled Students Styles */
.loading-small {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem;
  color: #6b7280;
}

.spinner-small {
  width: 24px;
  height: 24px;
  border: 2px solid #f3f4f6;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 0.5rem;
}

.students-list {
  margin-bottom: 2rem;
}

.students-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.student-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  transition: all 0.2s;
}

.student-card:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.student-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1rem;
  flex-shrink: 0;
}

.student-info {
  flex: 1;
  min-width: 0;
}

.student-info h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.student-id {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0 0 0.25rem 0;
  font-family: 'Courier New', monospace;
}

.student-email {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0 0 0.75rem 0;
  word-break: break-word;
}

.enrollment-info {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.status-badge.enrolled {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.completed {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.dropped {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.failed {
  background-color: #fee2e2;
  color: #991b1b;
}

.grade-badge {
  background-color: #f3f4f6;
  color: #374151;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.date-badge {
  background-color: #f9fafb;
  color: #6b7280;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.student-actions {
  flex-shrink: 0;
}

.btn-outline {
  background-color: transparent;
  color: #3b82f6;
  border: 1px solid #3b82f6;
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
}

.btn-outline:hover {
  background-color: #3b82f6;
  color: white;
}

.enrollment-summary {
  background: #f9fafb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
}

.summary-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.stat-item {
  text-align: center;
  padding: 1rem;
  background: white;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.stat-label {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.no-students {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  text-align: center;
  color: #6b7280;
}

.no-students svg {
  width: 48px;
  height: 48px;
  color: #9ca3af;
  margin-bottom: 1rem;
}

.no-students h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.no-students p {
  color: #6b7280;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .course-detail-view {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .course-header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .course-badges {
    justify-content: center;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .info-item {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .info-item .label,
  .info-item .value {
    text-align: left;
  }
}

@media (max-width: 480px) {
  .course-overview-card {
    padding: 1.5rem;
  }
  
  .course-avatar {
    width: 60px;
    height: 60px;
    font-size: 1.25rem;
  }
  
  .course-info h2 {
    font-size: 1.5rem;
  }
  
  .card-header {
    padding: 1rem;
  }
  
  .card-content {
    padding: 1rem;
  }
}
</style>
