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
            <span class="value">{{ student.personalInfo.studentId }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Full Name:</span>
            <span class="value">{{ student.personalInfo.firstName }} {{ student.personalInfo.middleName }} {{ student.personalInfo.lastName }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Email:</span>
            <span class="value">{{ student.personalInfo.email }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Phone:</span>
            <span class="value">{{ student.personalInfo.phone || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Date of Birth:</span>
            <span class="value">{{ formatDate(student.personalInfo.dateOfBirth || '') }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Gender:</span>
            <span class="value">{{ capitalize(student.personalInfo.gender || '') }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Address:</span>
            <span class="value">{{ student.personalInfo.address }}, {{ student.personalInfo.city }}, {{ student.personalInfo.province }} {{ student.personalInfo.postalCode }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h2>Academic Information</h2>
          <div class="detail-row">
            <span class="label">Year Level:</span>
            <span class="value">{{ student.academicStanding.currentYear }}{{ getYearSuffix(student.academicStanding.currentYear) }} Year</span>
          </div>
          <div class="detail-row">
            <span class="label">Semester:</span>
            <span class="value">{{ capitalize(student.academicStanding.currentSemester) }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Academic Standing:</span>
            <span class="value">
              <span :class="['status-badge', student.academicStanding.standing]">
                {{ formatStanding(student.academicStanding.standing) }}
              </span>
            </span>
          </div>
          <div class="detail-row">
            <span class="label">GPA:</span>
            <span class="value">{{ student.academicStanding.currentGPA || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Total Units:</span>
            <span class="value">{{ student.academicStanding.totalUnits || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Academic Advisor:</span>
            <span class="value">{{ student.academicStanding.advisor || 'N/A' }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h2>Emergency Contact</h2>
          <div class="detail-row">
            <span class="label">Contact Name:</span>
            <span class="value">{{ student.personalInfo.emergencyContact.name || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Relationship:</span>
            <span class="value">{{ student.personalInfo.emergencyContact.relationship || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Contact Phone:</span>
            <span class="value">{{ student.personalInfo.emergencyContact.phone || 'N/A' }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h2>Skills ({{ student.skills?.length || 0 }})</h2>
          <div v-if="student.skills && student.skills.length > 0" class="skills-list">
            <div v-for="skill in student.skills" :key="skill.id" class="skill-item">
              <div class="skill-header">
                <strong>{{ skill.name }}</strong>
                <span :class="['proficiency-badge', skill.proficiency]">{{ skill.proficiency }}</span>
              </div>
              <div class="skill-details">
                <span>Category: {{ skill.category }}</span>
                <span v-if="skill.yearsExperience">Experience: {{ skill.yearsExperience }} years</span>
                <span v-if="skill.certifications && skill.certifications.length > 0">
                  Certifications: {{ skill.certifications.join(', ') }}
                </span>
                <span v-if="skill.lastUsed">Last Used: {{ formatDate(skill.lastUsed) }}</span>
              </div>
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
              <span v-if="activity.startDate">From: {{ formatDate(activity.startDate) }}</span>
              <span v-if="activity.endDate">To: {{ formatDate(activity.endDate) }}</span>
            </div>
          </div>
          <p v-else class="no-data">No activities recorded</p>
        </div>

        <div class="detail-section">
          <h2>Academic History ({{ student.academicHistory?.length || 0 }})</h2>
          <div v-if="student.academicHistory && student.academicHistory.length > 0" class="history-list">
            <div v-for="history in student.academicHistory" :key="history.id" class="history-item">
              <strong>{{ history.schoolName }}</strong>
              <span>{{ history.degree }} in {{ history.major }}</span>
              <span>GPA: {{ history.gpa }}</span>
              <span>Status: {{ history.status }}</span>
              <span>{{ formatDate(history.startDate || '') }} - {{ formatDate(history.endDate || '') }}</span>
              <span v-if="history.honors && history.honors.length > 0">Honors: {{ history.honors.join(', ') }}</span>
            </div>
          </div>
          <p v-else class="no-data">No academic history recorded</p>
        </div>

        <div class="detail-section">
          <h2>Affiliations ({{ student.affiliations?.length || 0 }})</h2>
          <div v-if="student.affiliations && student.affiliations.length > 0" class="affiliations-list">
            <div v-for="affiliation in student.affiliations" :key="affiliation.id" class="affiliation-item">
              <div class="affiliation-header">
                <strong>{{ affiliation.name }}</strong>
                <span :class="['type-badge', affiliation.type]">{{ affiliation.type.replace('_', ' ') }}</span>
              </div>
              <div class="affiliation-details">
                <span>Role: {{ affiliation.role }}</span>
                <span v-if="affiliation.position">Position: {{ affiliation.position }}</span>
                <span v-if="affiliation.startDate">Since: {{ formatDate(affiliation.startDate) }}</span>
                <span v-if="affiliation.endDate">Until: {{ formatDate(affiliation.endDate) }}</span>
                <span v-if="affiliation.description">{{ affiliation.description }}</span>
              </div>
            </div>
          </div>
          <p v-else class="no-data">No affiliations recorded</p>
        </div>

        <div class="detail-section">
          <h2>Violations ({{ student.violations?.length || 0 }})</h2>
          <div v-if="student.violations && student.violations.length > 0" class="violations-list">
            <div v-for="violation in student.violations" :key="violation.id" :class="['violation-item', violation.severity]">
              <div class="violation-header">
                <strong>{{ violation.type }}</strong>
                <span :class="['severity-badge', violation.severity]">{{ violation.severity }}</span>
                <span :class="['status-badge', violation.status]">{{ violation.status.replace('_', ' ') }}</span>
              </div>
              <div class="violation-details">
                <span>{{ violation.description }}</span>
                <span>Date: {{ formatDate(violation.date) }}</span>
                <span>Points: {{ violation.points }}</span>
                <span>Reported by: {{ violation.reportedBy }}</span>
                <span v-if="violation.sanctions && violation.sanctions.length > 0">
                  Sanctions: {{ violation.sanctions.join(', ') }}
                </span>
              </div>
            </div>
          </div>
          <p v-else class="no-data">No violations recorded</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useStudentStore } from '@/stores/student'
import type { Student } from '@/types/student'

const route = useRoute()
const studentStore = useStudentStore()

// Use computed property to get student from store
const student = computed(() => studentStore.currentStudent)
const loading = computed(() => studentStore.loading)
const error = computed(() => studentStore.error)

onMounted(async () => {
  try {
    await studentStore.fetchStudentById(Number(route.params.id))
  } catch (err: any) {
    console.error('Failed to fetch student:', err)
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

/* Enhanced Styles for Skills, Affiliations, and Violations */
.skill-header,
.affiliation-header,
.violation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  gap: 0.5rem;
}

.skill-details,
.affiliation-details,
.violation-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

/* Proficiency Badges */
.proficiency-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.proficiency-badge.beginner {
  background: #dbeafe;
  color: #1e40af;
}

.proficiency-badge.intermediate {
  background: #fef3c7;
  color: #92400e;
}

.proficiency-badge.advanced {
  background: #d1fae5;
  color: #065f46;
}

.proficiency-badge.expert {
  background: #e9d5ff;
  color: #6b21a8;
}

/* Type Badges for Affiliations */
.type-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.type-badge.student_organization {
  background: #dbeafe;
  color: #1e40af;
}

.type-badge.professional {
  background: #d1fae5;
  color: #065f46;
}

.type-badge.academic {
  background: #fef3c7;
  color: #92400e;
}

.type-badge.sports {
  background: #fed7aa;
  color: #9a3412;
}

.type-badge.community {
  background: #e9d5ff;
  color: #6b21a8;
}

.type-badge.religious {
  background: #fce7f3;
  color: #9f1239;
}

.type-badge.other {
  background: #f3f4f6;
  color: #374151;
}

/* Severity Badges for Violations */
.severity-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.severity-badge.minor {
  background: #fed7aa;
  color: #9a3412;
}

.severity-badge.major {
  background: #fecaca;
  color: #991b1b;
}

.severity-badge.critical {
  background: #dc2626;
  color: white;
}

/* Status Badges for Violations */
.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.resolved {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.under_review {
  background: #dbeafe;
  color: #1e40af;
}

/* Enhanced Violation Items */
.violation-item.minor {
  background-color: #fff7ed;
  border-left: 4px solid #f59e0b;
}

.violation-item.major {
  background-color: #fef2f2;
  border-left: 4px solid #dc2626;
}

.violation-item.critical {
  background-color: #fee2e2;
  border-left: 4px solid #991b1b;
}
</style>
