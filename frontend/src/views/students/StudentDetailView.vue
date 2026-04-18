<template>
  <div class="student-detail-view">
    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else-if="student">
      <div class="page-header">
        <h1>Student Details</h1>
        <div class="actions">
          <router-link :to="`/students/${student.id}/edit`" class="btn btn-primary">
            Edit Student
          </router-link>
          <router-link to="/students" class="btn btn-secondary">
            Back to List
          </router-link>
        </div>
      </div>

      <div class="detail-card">
        <div class="detail-section">
          <h2>Basic Information</h2>
          <div class="detail-row">
            <span class="label">Student Number:</span>
            <span class="value">{{ student.student_number }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Full Name:</span>
            <span class="value">{{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Email:</span>
            <span class="value">{{ student.user?.email }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Phone:</span>
            <span class="value">{{ student.phone || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Date of Birth:</span>
            <span class="value">{{ formatDate(student.date_of_birth || '') }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Gender:</span>
            <span class="value">{{ capitalize(student.gender || '') }}</span>
          </div>
          <div class="detail-row">
            <span class="label">City:</span>
            <span class="value">{{ student.city || 'N/A' }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h2>Academic Information</h2>
          <div class="detail-row">
            <span class="label">Year Level:</span>
            <span class="value">{{ student.year_level }}{{ getYearSuffix(student.year_level) }} Year</span>
          </div>
          <div class="detail-row">
            <span class="label">Academic Standing:</span>
            <span class="value">
              <span :class="['status-badge', student.academic_standing]">
                {{ formatStanding(student.academic_standing) }}
              </span>
            </span>
          </div>
          <div class="detail-row">
            <span class="label">GPA:</span>
            <span class="value">{{ student.gpa || 'N/A' }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h2>Emergency Contact</h2>
          <div class="detail-row">
            <span class="label">Contact Name:</span>
            <span class="value">{{ student.emergency_contact_name || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Contact Phone:</span>
            <span class="value">{{ student.emergency_contact_phone || 'N/A' }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h2>Skills ({{ student.skills?.length || 0 }})</h2>
          <div v-if="student.skills && student.skills.length > 0" class="skills-list">
            <div v-for="skill in student.skills" :key="skill.id" class="skill-item">
              <strong>{{ skill.name }}</strong>
              <span>Category: {{ skill.category }}</span>
              <span>Proficiency: {{ skill.proficiency }}</span>
              <span v-if="skill.certifications">Certifications: {{ skill.certifications }}</span>
            </div>
          </div>
          <p v-else class="no-data">No skills recorded</p>
        </div>

        <div class="detail-section">
          <h2>Activities ({{ student.activities?.length || 0 }})</h2>
          <div v-if="student.activities && student.activities.length > 0" class="activities-list">
            <div v-for="activity in student.activities" :key="activity.id" class="activity-item">
              <strong>{{ activity.name }}</strong>
              <span>Type: {{ activity.type }}</span>
              <span>Role: {{ activity.role }}</span>
              <span v-if="activity.start_date">From: {{ formatDate(activity.start_date) }}</span>
              <span v-if="activity.end_date">To: {{ formatDate(activity.end_date) }}</span>
            </div>
          </div>
          <p v-else class="no-data">No activities recorded</p>
        </div>

        <div class="detail-section">
          <h2>Academic History ({{ student.academic_history?.length || 0 }})</h2>
          <div v-if="student.academic_history && student.academic_history.length > 0" class="history-list">
            <div v-for="history in student.academic_history" :key="history.id" class="history-item">
              <strong>{{ history.school_name }}</strong>
              <span>{{ history.degree }} in {{ history.major }}</span>
              <span>GPA: {{ history.gpa }}</span>
              <span>Status: {{ history.status }}</span>
              <span>{{ formatDate(history.start_date) }} - {{ formatDate(history.end_date) }}</span>
              <span v-if="history.honors && history.honors.length > 0">Honors: {{ history.honors.join(', ') }}</span>
            </div>
          </div>
          <p v-else class="no-data">No academic history recorded</p>
        </div>

        <div class="detail-section">
          <h2>Affiliations ({{ student.affiliations?.length || 0 }})</h2>
          <div v-if="student.affiliations && student.affiliations.length > 0" class="affiliations-list">
            <div v-for="affiliation in student.affiliations" :key="affiliation.id" class="affiliation-item">
              <strong>{{ affiliation.name }}</strong>
              <span>Type: {{ affiliation.type }}</span>
              <span>Role: {{ affiliation.role }}</span>
              <span v-if="affiliation.position">Position: {{ affiliation.position }}</span>
              <span v-if="affiliation.start_date">Since: {{ formatDate(affiliation.start_date) }}</span>
            </div>
          </div>
          <p v-else class="no-data">No affiliations recorded</p>
        </div>

        <div class="detail-section">
          <h2>Violations ({{ student.violations?.length || 0 }})</h2>
          <div v-if="student.violations && student.violations.length > 0" class="violations-list">
            <div v-for="violation in student.violations" :key="violation.id" class="violation-item">
              <strong>{{ violation.type }}</strong>
              <span>{{ violation.description }}</span>
              <span>Date: {{ formatDate(violation.date) }}</span>
              <span :class="['severity', violation.severity]">Severity: {{ violation.severity }}</span>
              <span>Status: {{ violation.status }}</span>
              <span v-if="violation.consequence">Consequence: {{ violation.consequence }}</span>
            </div>
          </div>
          <p v-else class="no-data">No violations recorded</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

interface Student {
  id: number
  first_name: string
  middle_name?: string
  last_name: string
  student_number: string
  phone?: string
  date_of_birth?: string
  gender?: string
  city?: string
  year_level: number
  academic_standing: string
  gpa?: number
  emergency_contact_name?: string
  emergency_contact_phone?: string
  user?: { email: string }
  skills?: Array<{ id: number; name: string; category: string; proficiency: string; certifications?: string }>
  activities?: Array<{ id: number; name: string; type: string; role: string; start_date?: string; end_date?: string }>
  academic_history?: Array<{ id: number; school_name: string; degree: string; major: string; gpa: number; status: string; start_date: string; end_date: string; honors?: string[] }>
  affiliations?: Array<{ id: number; name: string; type: string; role: string; position?: string; start_date?: string }>
  violations?: Array<{ id: number; type: string; description: string; date: string; severity: string; status: string; consequence?: string }>
}

const route = useRoute()
const student = ref<Student | null>(null)
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/students/${route.params.id}`)
    student.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to fetch student'
  } finally {
    loading.value = false
  }
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
</script>

<style scoped>
.student-detail-view {
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

.actions {
  display: flex;
  gap: 1rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
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

.detail-card {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.detail-section {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.detail-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.detail-section h2 {
  margin-bottom: 1rem;
  color: #1f2937;
  font-size: 1.25rem;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row .label {
  font-weight: 500;
  color: #6b7280;
}

.detail-row .value {
  color: #1f2937;
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

.status-badge.probation {
  background-color: #fee2e2;
  color: #991b1b;
}

.skills-list,
.activities-list,
.history-list,
.affiliations-list,
.violations-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.skill-item,
.activity-item,
.history-item,
.affiliation-item,
.violation-item {
  padding: 1rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.skill-item strong,
.activity-item strong,
.history-item strong,
.affiliation-item strong,
.violation-item strong {
  color: #1f2937;
}

.skill-item span,
.activity-item span,
.history-item span,
.affiliation-item span,
.violation-item span {
  color: #6b7280;
  font-size: 0.875rem;
}

.violation-item {
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

.no-data {
  color: #9ca3af;
  font-style: italic;
}
</style>
