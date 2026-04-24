<template>
  <div class="student-profile-view">
    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else-if="student">
      <div class="page-header">
        <h1>Student Profile</h1>
        <router-link to="/students" class="btn btn-secondary">
          Back to List
        </router-link>
      </div>

      <div class="profile-container">
        <div class="profile-header">
          <div class="avatar">
            {{ (student.first_name || '')?.charAt(0) }}{{ (student.last_name || '')?.charAt(0) }}
          </div>
          <div class="profile-info">
            <h2>{{ student.first_name }} {{ student.last_name }}</h2>
            <p>{{ student.student_number }}</p>
            <span :class="['status-badge', student.academic_standing]">
              {{ formatStanding(student.academic_standing || '') }}
            </span>
          </div>
        </div>

        <div class="profile-sections">
          <div class="section">
            <h3>Personal Information</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Email:</label>
                <span>{{ student.user?.email }}</span>
              </div>
              <div class="info-item">
                <label>Phone:</label>
                <span>{{ student.phone || 'N/A' }}</span>
              </div>
              <div class="info-item">
                <label>Date of Birth:</label>
                <span>{{ formatDate(student.date_of_birth || '') }}</span>
              </div>
              <div class="info-item">
                <label>Gender:</label>
                <span>{{ capitalize(student.gender || '') }}</span>
              </div>
              <div class="info-item">
                <label>City:</label>
                <span>{{ student.city || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <div class="section">
            <h3>Academic Information</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Year Level:</label>
                <span>{{ student.year_level }}{{ getYearSuffix(student.year_level) }} Year</span>
              </div>
              <div class="info-item">
                <label>GPA:</label>
                <span>{{ student.gpa || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <div class="section">
            <h3>Skills</h3>
            <div v-if="student.skills && student.skills.length > 0" class="tags">
              <span v-for="skill in student.skills" :key="skill.id" class="tag">
                {{ skill.name }} ({{ skill.proficiency }})
              </span>
            </div>
            <p v-else>No skills recorded</p>
          </div>

          <div class="section">
            <h3>Activities</h3>
            <div v-if="student.activities && student.activities.length > 0" class="list">
              <div v-for="activity in student.activities" :key="activity.id" class="list-item">
                <strong>{{ activity.name }}</strong>
                <span>{{ activity.role }}</span>
              </div>
            </div>
            <p v-else>No activities recorded</p>
          </div>

          <div class="section">
            <h3>Academic History</h3>
            <div v-if="student.academic_history && student.academic_history.length > 0" class="list">
              <div v-for="history in student.academic_history" :key="history.id" class="list-item">
                <strong>{{ history.school_name }}</strong>
                <span>{{ history.degree }} - {{ history.major }}</span>
                <span>{{ formatDate(history.start_date) }} to {{ formatDate(history.end_date) }}</span>
              </div>
            </div>
            <p v-else>No academic history recorded</p>
          </div>

          <div class="section">
            <h3>Enrolled Courses</h3>
            <div v-if="loadingEnrollments" class="loading-small">
              <div class="spinner-small"></div>
              <p>Loading enrolled courses...</p>
            </div>
            <div v-else-if="enrolledCourses.length > 0" class="courses-grid">
              <div v-for="enrollment in enrolledCourses" :key="enrollment.id" class="course-card">
                <div class="course-header">
                  <h4>{{ enrollment.course.course_code }}</h4>
                  <span :class="['status-badge', getEnrollmentStatusClass(enrollment.status)]">
                    {{ enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1) }}
                  </span>
                </div>
                <div class="course-info">
                  <p class="course-name">{{ enrollment.course.course_name }}</p>
                  <p class="course-instructor">{{ enrollment.course.instructor || 'TBA' }}</p>
                  <p class="course-credits">{{ enrollment.course.credits }} Credits</p>
                  <p class="course-semester">{{ enrollment.semester }} {{ enrollment.academic_year }}</p>
                  <p class="grade-info" v-if="enrollment.grade">
                    Grade: <strong>{{ enrollment.grade }}%</strong> 
                    <span :class="['grade-status', enrollment.grade >= 75 ? 'pass' : 'fail']">
                      ({{ enrollment.grade >= 75 ? 'Passed' : 'Failed' }})
                    </span>
                  </p>
                </div>
              </div>
            </div>
            <p v-else>No courses enrolled</p>
          </div>

          <div class="section">
            <h3>Affiliations</h3>
            <div v-if="student.affiliations && student.affiliations.length > 0" class="list">
              <div v-for="affiliation in student.affiliations" :key="affiliation.id" class="list-item">
                <strong>{{ affiliation.name }}</strong>
                <span>{{ affiliation.role }}</span>
              </div>
            </div>
            <p v-else>No affiliations recorded</p>
          </div>

          <div class="section">
            <h3>Violations</h3>
            <div v-if="student.violations && student.violations.length > 0" class="list violations">
              <div v-for="violation in student.violations" :key="violation.id" class="list-item">
                <strong>{{ violation.type }}</strong>
                <span>{{ violation.description }}</span>
                <span :class="['severity', violation.severity]">{{ violation.severity }}</span>
              </div>
            </div>
            <p v-else>No violations recorded</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import api from '@/services/api'

interface Student {
  id: number
  first_name: string
  last_name: string
  student_number: string
  phone?: string
  date_of_birth?: string
  gender?: string
  city?: string
  year_level: number
  academic_standing: string
  gpa?: number
  user?: { email: string }
  skills?: Array<{ id: number; name: string; proficiency: string }>
  activities?: Array<{ id: number; name: string; role: string }>
  academic_history?: Array<{ id: number; school_name: string; degree: string; major: string; start_date: string; end_date: string }>
  affiliations?: Array<{ id: number; name: string; role: string }>
  violations?: Array<{ id: number; type: string; description: string; severity: string }>
}

interface CourseEnrollment {
  id: number
  student_id: number
  course_id: number
  semester: string
  academic_year: string
  grade?: number
  status: string
  enrollment_date: string
  remarks?: string
  course: {
    id: number
    course_code: string
    course_name: string
    credits: number
    instructor?: string
  }
}

const route = useRoute()
const student = ref<Student | null>(null)
const loading = ref(true)
const error = ref('')
const enrolledCourses = ref<CourseEnrollment[]>([])
const loadingEnrollments = ref(false)

const fetchStudent = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/students/${route.params.id}`)
    student.value = response.data
    if (response.data) {
      await fetchEnrolledCourses(response.data.id)
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to fetch student'
  } finally {
    loading.value = false
  }
}

const fetchEnrolledCourses = async (studentId: number) => {
  loadingEnrollments.value = true
  try {
    const response = await api.get(`/students/${studentId}/enrollments`)
    enrolledCourses.value = response.data
  } catch (error) {
    console.error('Error fetching enrolled courses:', error)
  } finally {
    loadingEnrollments.value = false
  }
}

onMounted(() => {
  fetchStudent()
})

function formatStanding(standing: string) {
  return standing.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

function formatDate(date: string) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString()
}

function capitalize(str: string) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

function getYearSuffix(year: number) {
  if (year === 1) return 'st'
  if (year === 2) return 'nd'
  if (year === 3) return 'rd'
  return 'th'
}

function getEnrollmentStatusClass(status: string) {
  if (status === 'enrolled') return 'enrolled'
  if (status === 'completed') return 'completed'
  if (status === 'dropped') return 'dropped'
  if (status === 'failed') return 'failed'
  return 'enrolled'
}
</script>

<style scoped>
.student-profile-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
}

.btn-secondary {
  background-color: #6b7280;
  color: white;
}

.loading,
.error {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #dc2626;
}

.profile-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.profile-header {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  padding: 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.avatar {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
}

.profile-info h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
}

.profile-info p {
  margin: 0 0 0.5rem 0;
  opacity: 0.9;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge.good {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.at_risk {
  background-color: #fef3c7;
  color: #92400e;
}

.profile-sections {
  padding: 2rem;
}

.section {
  margin-bottom: 2rem;
}

.section h3 {
  margin-bottom: 1rem;
  color: #1f2937;
  font-size: 1.25rem;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
}

.info-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.info-item span {
  color: #1f2937;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tag {
  background-color: #e0f2fe;
  color: #0369a1;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
}

.list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.list-item {
  padding: 1rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.list-item strong {
  color: #1f2937;
}

.list-item span {
  color: #6b7280;
  font-size: 0.875rem;
}

.violations .list-item {
  background-color: #fef2f2;
  border-left: 4px solid #dc2626;
}

.severity {
  font-weight: 500;
  text-transform: capitalize;
}

.severity.minor {
  color: #f59e0b;
}

.severity.major {
  color: #dc2626;
}

.severity.critical {
  color: #991b1b;
}

/* Enrolled Courses Styles */
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

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1rem;
}

.course-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
}

.course-card:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.course-header h4 {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.course-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.course-name {
  font-weight: 500;
  color: #374151;
  margin: 0;
}

.course-instructor,
.course-credits,
.course-semester {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.grade-info {
  font-size: 0.875rem;
  color: #374151;
  margin: 0;
}

.grade-status.pass {
  color: #059669;
  font-weight: 500;
}

.grade-status.fail {
  color: #dc2626;
  font-weight: 500;
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
</style>
