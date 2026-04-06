<template>
  <div class="student-profile-view">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading student profile...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Error Loading Profile</h3>
      <p>{{ error }}</p>
      <button @click="fetchStudent" class="btn primary">Retry</button>
    </div>

    <!-- Student Profile -->
    <div v-else-if="student" class="profile-container">
      <!-- Profile Header -->
      <div class="profile-header">
        <div class="student-avatar large">
          {{ student.personalInfo.firstName.charAt(0) }}{{ student.personalInfo.lastName.charAt(0) }}
        </div>
        <div class="profile-info">
          <h1 class="student-name">
            {{ student.personalInfo.firstName }} {{ student.personalInfo.middleName }} {{ student.personalInfo.lastName }}
          </h1>
          <div class="student-meta">
            <span class="student-id">{{ student.personalInfo.studentId }}</span>
            <span class="student-email">{{ student.personalInfo.email }}</span>
            <span class="student-phone">{{ student.personalInfo.phone }}</span>
          </div>
          <div class="status-badges">
            <span class="status-badge" :class="{ active: student.isActive }">
              {{ student.isActive ? 'Active' : 'Inactive' }}
            </span>
            <span class="standing-badge" :class="student.academicStanding.standing">
              {{ student.academicStanding.standing }}
            </span>
          </div>
        </div>
        <div class="header-actions">
          <button @click="editStudent" class="btn primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Profile
          </button>
        </div>
      </div>

      <!-- Profile Sections -->
      <div class="profile-sections">
        <!-- Personal Information -->
        <section class="profile-section">
          <div class="section-header">
            <h2>Personal Information</h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="8.5" cy="7" r="4"/>
              <line x1="20" y1="8" x2="14" y2="8"/>
            </svg>
          </div>
          <div class="section-content">
            <div class="info-grid">
              <div class="info-item">
                <label>Full Name:</label>
                <span>{{ student.personalInfo.firstName }} {{ student.personalInfo.middleName }} {{ student.personalInfo.lastName }}</span>
              </div>
              <div class="info-item">
                <label>Student ID:</label>
                <span>{{ student.personalInfo.studentId }}</span>
              </div>
              <div class="info-item">
                <label>Email:</label>
                <span>{{ student.personalInfo.email }}</span>
              </div>
              <div class="info-item">
                <label>Phone:</label>
                <span>{{ student.personalInfo.phone }}</span>
              </div>
              <div class="info-item">
                <label>Date of Birth:</label>
                <span>{{ formatDate(student.personalInfo.dateOfBirth) }}</span>
              </div>
              <div class="info-item">
                <label>Age:</label>
                <span>{{ student.personalInfo.age }} years old</span>
              </div>
              <div class="info-item">
                <label>Gender:</label>
                <span class="gender-badge" :class="student.personalInfo.gender">
                  {{ student.personalInfo.gender }}
                </span>
              </div>
              <div class="info-item">
                <label>Address:</label>
                <span>{{ student.personalInfo.address }}, {{ student.personalInfo.city }}, {{ student.personalInfo.province }} {{ student.personalInfo.postalCode }}</span>
              </div>
              <div class="info-item">
                <label>Emergency Contact:</label>
                <span>{{ student.personalInfo.emergencyContact.name }} ({{ student.personalInfo.emergencyContact.relationship }}) - {{ student.personalInfo.emergencyContact.phone }}</span>
              </div>
            </div>
          </div>
        </section>

        <!-- Academic Information -->
        <section class="profile-section">
          <div class="section-header">
            <h2>Academic Information</h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 17H20v-6.5a2.5 2.5 0 0 0-2.5-2.5H6.5A2.5 2.5 0 0 0 4 8.5V17"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0H3.73a2 2 0 0 1-3.46 0L3.73 3A2 2 0 0 1 6.27 3h10.46a2 2 0 0 1 2.54 0l3.27 18a2 2 0 0 1-.54 0z"/>
            </svg>
          </div>
          <div class="section-content">
            <!-- Current Standing -->
            <div class="academic-standing">
              <h3>Current Academic Standing</h3>
              <div class="standing-grid">
                <div class="standing-item">
                  <label>Current Year:</label>
                  <span class="year-badge">{{ student.academicStanding.currentYear }}</span>
                </div>
                <div class="standing-item">
                  <label>Semester:</label>
                  <span class="semester-badge">{{ student.academicStanding.currentSemester }}</span>
                </div>
                <div class="standing-item">
                  <label>Current GPA:</label>
                  <span class="gpa-display" :class="getGPAClass(student.academicStanding.currentGPA)">
                    {{ student.academicStanding.currentGPA.toFixed(2) }}
                  </span>
                </div>
                <div class="standing-item">
                  <label>Total Units:</label>
                  <span>{{ student.academicStanding.totalUnits }}</span>
                </div>
                <div class="standing-item">
                  <label>Standing:</label>
                  <span class="standing-badge large" :class="student.academicStanding.standing">
                    {{ student.academicStanding.standing }}
                  </span>
                </div>
                <div class="standing-item">
                  <label>Advisor:</label>
                  <span>{{ student.academicStanding.advisor }}</span>
                </div>
              </div>
            </div>

            <!-- Academic History -->
            <div class="academic-history">
              <h3>Academic History</h3>
              <div class="history-list">
                <div 
                  v-for="history in student.academicHistory" 
                  :key="history.id" 
                  class="history-item"
                >
                  <div class="history-header">
                    <h4>{{ history.schoolName }}</h4>
                    <span class="status-badge" :class="history.status">
                      {{ history.status }}
                    </span>
                  </div>
                  <div class="history-details">
                    <div class="detail-item">
                      <label>Degree:</label>
                      <span>{{ history.degree }} in {{ history.major }}</span>
                    </div>
                    <div class="detail-item">
                      <label>Period:</label>
                      <span>{{ formatDate(history.startDate) }} - {{ history.endDate ? formatDate(history.endDate) : 'Present' }}</span>
                    </div>
                    <div v-if="history.gpa" class="detail-item">
                      <label>GPA:</label>
                      <span>{{ history.gpa.toFixed(2) }}</span>
                    </div>
                    <div v-if="history.honors && history.honors.length > 0" class="detail-item">
                      <label>Honors:</label>
                      <div class="honors-list">
                        <span 
                          v-for="honor in history.honors" 
                          :key="honor"
                          class="honor-tag"
                        >
                          {{ honor }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Skills Section -->
        <section class="profile-section">
          <div class="section-header">
            <h2>Skills</h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <div class="section-content">
            <div class="skills-grid">
              <div 
                v-for="skill in student.skills" 
                :key="skill.id" 
                class="skill-card"
                :class="skill.category"
              >
                <div class="skill-header">
                  <h4>{{ skill.name }}</h4>
                  <span class="proficiency-badge" :class="skill.proficiency">
                    {{ skill.proficiency }}
                  </span>
                </div>
                <div class="skill-details">
                  <div class="skill-info">
                    <label>Category:</label>
                    <span>{{ skill.category }}</span>
                  </div>
                  <div v-if="skill.yearsExperience" class="skill-info">
                    <label>Experience:</label>
                    <span>{{ skill.yearsExperience }} years</span>
                  </div>
                  <div v-if="skill.lastUsed" class="skill-info">
                    <label>Last Used:</label>
                    <span>{{ formatDate(skill.lastUsed) }}</span>
                  </div>
                </div>
                <div v-if="skill.certifications && skill.certifications.length > 0" class="skill-certifications">
                  <label>Certifications:</label>
                  <div class="certifications-list">
                    <span 
                      v-for="cert in skill.certifications" 
                      :key="cert"
                      class="cert-tag"
                    >
                      {{ cert }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="section-actions">
              <button @click="showAddSkillModal = true" class="btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Skill
              </button>
            </div>
          </div>
        </section>

        <!-- Activities Section -->
        <section class="profile-section">
          <div class="section-header">
            <h2>Non-Academic Activities</h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="section-content">
            <div class="activities-list">
              <div 
                v-for="activity in student.activities" 
                :key="activity.id" 
                class="activity-card"
                :class="activity.type"
              >
                <div class="activity-header">
                  <h4>{{ activity.name }}</h4>
                  <span class="activity-type" :class="activity.type">
                    {{ activity.type }}
                  </span>
                </div>
                <div class="activity-details">
                  <div class="activity-info">
                    <label>Role:</label>
                    <span>{{ activity.role }}</span>
                  </div>
                  <div class="activity-info">
                    <label>Period:</label>
                    <span>{{ formatDate(activity.startDate) }} - {{ activity.endDate ? formatDate(activity.endDate) : 'Present' }}</span>
                  </div>
                  <div class="activity-info">
                    <label>Level:</label>
                    <span class="level-badge" :class="activity.level">
                      {{ activity.level }}
                    </span>
                  </div>
                </div>
                <div class="activity-description">
                  <p>{{ activity.description }}</p>
                </div>
                <div v-if="activity.achievements && activity.achievements.length > 0" class="activity-achievements">
                  <label>Achievements:</label>
                  <div class="achievements-list">
                    <span 
                      v-for="achievement in activity.achievements" 
                      :key="achievement"
                      class="achievement-tag"
                    >
                      {{ achievement }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="section-actions">
              <button @click="showAddActivityModal = true" class="btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Activity
              </button>
            </div>
          </div>
        </section>

        <!-- Violations Section -->
        <section class="profile-section">
          <div class="section-header">
            <h2>Violations</h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>
          <div class="section-content">
            <div v-if="student.violations.length === 0" class="no-violations">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
              <p>No violations on record</p>
            </div>
            <div v-else class="violations-list">
              <div 
                v-for="violation in student.violations" 
                :key="violation.id" 
                class="violation-card"
                :class="violation.severity"
              >
                <div class="violation-header">
                  <h4>{{ violation.type }}</h4>
                  <span class="severity-badge" :class="violation.severity">
                    {{ violation.severity }}
                  </span>
                  <span class="status-badge" :class="violation.status">
                    {{ violation.status }}
                  </span>
                </div>
                <div class="violation-details">
                  <div class="violation-info">
                    <label>Date:</label>
                    <span>{{ formatDate(violation.date) }}</span>
                  </div>
                  <div class="violation-info">
                    <label>Points:</label>
                    <span class="points-display">{{ violation.points }}</span>
                  </div>
                  <div class="violation-info">
                    <label>Reported By:</label>
                    <span>{{ violation.reportedBy }}</span>
                  </div>
                </div>
                <div class="violation-description">
                  <p>{{ violation.description }}</p>
                </div>
                <div v-if="violation.sanctions && violation.sanctions.length > 0" class="violation-sanctions">
                  <label>Sanctions:</label>
                  <div class="sanctions-list">
                    <span 
                      v-for="sanction in violation.sanctions" 
                      :key="sanction"
                      class="sanction-tag"
                    >
                      {{ sanction }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Affiliations Section -->
        <section class="profile-section">
          <div class="section-header">
            <h2>Affiliations</h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="section-content">
            <div class="affiliations-list">
              <div 
                v-for="affiliation in student.affiliations" 
                :key="affiliation.id" 
                class="affiliation-card"
                :class="affiliation.type"
              >
                <div class="affiliation-header">
                  <h4>{{ affiliation.name }}</h4>
                  <span class="affiliation-type" :class="affiliation.type">
                    {{ affiliation.type }}
                  </span>
                </div>
                <div class="affiliation-details">
                  <div class="affiliation-info">
                    <label>Role:</label>
                    <span>{{ affiliation.role }}</span>
                  </div>
                  <div v-if="affiliation.position" class="affiliation-info">
                    <label>Position:</label>
                    <span>{{ affiliation.position }}</span>
                  </div>
                  <div class="affiliation-info">
                    <label>Period:</label>
                    <span>{{ formatDate(affiliation.startDate) }} - {{ affiliation.endDate ? formatDate(affiliation.endDate) : 'Present' }}</span>
                  </div>
                </div>
                <div v-if="affiliation.description" class="affiliation-description">
                  <p>{{ affiliation.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useStudentStore } from '@/stores/student'
import type { Student } from '@/types/student'

const router = useRouter()
const route = useRoute()
const studentStore = useStudentStore()

// Local state
const showAddSkillModal = ref(false)
const showAddActivityModal = ref(false)

// Computed
const { loading, error } = studentStore
const student = computed(() => studentStore.currentStudent)

// Methods
const fetchStudent = async () => {
  const id = Number(route.params.id)
  await studentStore.fetchStudentById(id)
}

const editStudent = () => {
  if (student.value) {
    router.push(`/students/${student.value.id}/edit`)
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

const getGPAClass = (gpa: number) => {
  if (gpa >= 3.5) return 'excellent'
  if (gpa >= 3.0) return 'good'
  if (gpa >= 2.5) return 'average'
  if (gpa >= 2.0) return 'below-average'
  return 'poor'
}

// Lifecycle
onMounted(() => {
  fetchStudent()
})
</script>

<style scoped>
.student-profile-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* Loading and Error States */
.loading-state, .error-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state svg {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin-bottom: 1rem;
}

/* Profile Container */
.profile-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

/* Profile Header */
.profile-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.student-avatar.large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  border: 3px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  flex-shrink: 0;
}

.profile-info {
  flex: 1;
}

.student-name {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.student-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.student-id, .student-email, .student-phone {
  background: rgba(255, 255, 255, 0.1);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
}

.status-badges {
  display: flex;
  gap: 0.75rem;
}

.status-badge {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.status-badge.active {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.standing-badge {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: uppercase;
}

.standing-badge.good { background: #d1fae5; color: #065f46; }
.standing-badge.warning { background: #fef3c7; color: #92400e; }
.standing-badge.probation { background: #fee2e2; color: #991b1b; }

.header-actions {
  flex-shrink: 0;
}

/* Profile Sections */
.profile-sections {
  display: grid;
  gap: 2rem;
}

.profile-section {
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  overflow: hidden;
}

.section-header {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.section-header h2 {
  margin: 0;
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 600;
  flex: 1;
}

.section-header svg {
  width: 24px;
  height: 24px;
  color: #6b7280;
}

.section-content {
  padding: 1.5rem;
}

/* Information Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 0.875rem;
}

.info-item span {
  color: #1f2937;
  font-weight: 400;
}

.gender-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.gender-badge.male { background: #dbeafe; color: #1e40af; }
.gender-badge.female { background: #fce7f3; color: #9f1239; }
.gender-badge.other { background: #f3f4f6; color: #374151; }

/* Academic Information */
.academic-standing {
  margin-bottom: 2rem;
}

.academic-standing h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.standing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.standing-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.standing-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 0.875rem;
}

.year-badge {
  background: #3b82f6;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
}

.semester-badge {
  background: #8b5cf6;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: capitalize;
}

.gpa-display {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 1rem;
  font-weight: 600;
}

.gpa-display.excellent { background: #10b981; color: white; }
.gpa-display.good { background: #3b82f6; color: white; }
.gpa-display.average { background: #f59e0b; color: white; }
.gpa-display.below-average { background: #f97316; color: white; }
.gpa-display.poor { background: #ef4444; color: white; }

/* Academic History */
.academic-history h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.history-list {
  display: grid;
  gap: 1rem;
}

.history-item {
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 1rem;
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.history-header h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.history-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 0.75rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 0.875rem;
}

.honors-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.honor-tag {
  background: #fef3c7;
  color: #92400e;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Skills */
.skills-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1rem;
}

.skill-card {
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 1.5rem;
  transition: transform 0.2s;
}

.skill-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.skill-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.skill-header h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.proficiency-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.proficiency-badge.beginner { background: #dbeafe; color: #1e40af; }
.proficiency-badge.intermediate { background: #fbbf24; color: #92400e; }
.proficiency-badge.advanced { background: #a78bfa; color: #6b21a8; }
.proficiency-badge.expert { background: #8b5cf6; color: white; }

.skill-details {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.skill-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.skill-info label {
  color: #6b7280;
}

.skill-certifications {
  margin-top: 1rem;
}

.certifications-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.cert-tag {
  background: #ecfdf5;
  color: #065f46;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Activities */
.activities-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1rem;
}

.activity-card {
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 1.5rem;
  transition: transform 0.2s;
}

.activity-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.activity-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.activity-header h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.activity-type {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.activity-type.sports { background: #dbeafe; color: #1e40af; }
.activity-type.organization { background: #e0e7ff; color: #6b21a8; }
.activity-type.volunteer { background: #dcfce7; color: #166534; }
.activity-type.leadership { background: #fef3c7; color: #92400e; }

.activity-details {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.activity-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.activity-info label {
  color: #6b7280;
}

.level-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.level-badge.local { background: #dbeafe; color: #1e40af; }
.level-badge.regional { background: #fbbf24; color: #92400e; }
.level-badge.national { background: #a78bfa; color: #6b21a8; }
.level-badge.international { background: #8b5cf6; color: white; }

.activity-description {
  margin-bottom: 1rem;
}

.activity-description p {
  color: #4b5563;
  line-height: 1.6;
}

.achievements-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.achievement-tag {
  background: #fef3c7;
  color: #92400e;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Violations */
.no-violations {
  text-align: center;
  padding: 2rem;
  color: #10b981;
}

.no-violations svg {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
}

.violations-list {
  display: grid;
  gap: 1rem;
}

.violation-card {
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 1.5rem;
}

.violation-card.minor { border-left: 4px solid #fbbf24; }
.violation-card.major { border-left: 4px solid #f59e0b; }
.violation-card.critical { border-left: 4px solid #ef4444; }

.violation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.violation-header h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.severity-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.severity-badge.minor { background: #fbbf24; color: #92400e; }
.severity-badge.major { background: #f59e0b; color: #92400e; }
.severity-badge.critical { background: #ef4444; color: white; }

.violation-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.violation-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.violation-info label {
  font-weight: 500;
  color: #6b7280;
  font-size: 0.875rem;
}

.points-display {
  font-weight: 600;
  color: #ef4444;
  font-size: 1rem;
}

.violation-description {
  margin-bottom: 1rem;
}

.violation-description p {
  color: #4b5563;
  line-height: 1.6;
}

.sanctions-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.sanction-tag {
  background: #fee2e2;
  color: #991b1b;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Affiliations */
.affiliations-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1rem;
}

.affiliation-card {
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 1.5rem;
  transition: transform 0.2s;
}

.affiliation-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.affiliation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.affiliation-header h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
}

.affiliation-type {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.affiliation-type.student_organization { background: #dbeafe; color: #1e40af; }
.affiliation-type.professional { background: #e0e7ff; color: #6b21a8; }
.affiliation-type.religious { background: #dcfce7; color: #166534; }
.affiliation-type.community { background: #f3e8ff; color: #6b21a8; }

.affiliation-details {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.affiliation-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.affiliation-info label {
  color: #6b7280;
}

.affiliation-description {
  margin-bottom: 1rem;
}

.affiliation-description p {
  color: #4b5563;
  line-height: 1.6;
}

/* Section Actions */
.section-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

/* Buttons */
.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn.primary {
  background: #3b82f6;
  color: white;
}

.btn.primary:hover {
  background: #2563eb;
}

.btn.secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn.secondary:hover {
  background: #e5e7eb;
}

/* Responsive Design */
@media (max-width: 768px) {
  .student-profile-view {
    padding: 1rem;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .profile-sections {
    gap: 1rem;
  }
  
  .info-grid,
  .standing-grid,
  .history-details,
  .skill-details,
  .activity-details,
  .violation-details,
  .affiliation-details {
    grid-template-columns: 1fr;
  }
  
  .skills-grid,
  .activities-list,
  .violations-list,
  .affiliations-list {
    grid-template-columns: 1fr;
  }
}
</style>
