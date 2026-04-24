<template>
  <div class="professor-detail-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/professors" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M15 19l-7-7 7-7"/>
            </svg>
            Professors
          </router-link>
          <h1>Professor Details</h1>
        </div>
        <div class="header-actions">
          <router-link :to="`/professors/${professor?.id}/edit`" class="btn btn-primary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Professor
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading professor data...</p>
    </div>

    <!-- Professor Details -->
    <div v-else-if="professor" class="professor-content">
      <!-- Profile Overview Card -->
      <div class="profile-overview-card">
        <div class="profile-header">
          <div class="profile-avatar">
            {{ getInitials(professor.first_name, professor.last_name) }}
          </div>
          <div class="profile-info">
            <h2>{{ professor.first_name }} {{ professor.middle_name }} {{ professor.last_name }}</h2>
            <p class="professor-id">{{ professor.professor_unique_id }}</p>
            <div class="profile-badges">
              <span :class="['status-badge', getEmploymentClass(professor.employment_type)]">
                {{ professor.employment_type }}
              </span>
              <span class="department-badge">{{ professor.department }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Information Grid -->
      <div class="info-grid">
        <!-- Personal Information -->
        <div class="info-card">
          <div class="card-header">
            <h3>Personal Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="info-item">
              <span class="label">Email</span>
              <span class="value">{{ professor.email }}</span>
            </div>
            <div class="info-item">
              <span class="label">Contact Number</span>
              <span class="value">{{ professor.contact_number || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Age</span>
              <span class="value">{{ professor.age }}</span>
            </div>
            <div class="info-item">
              <span class="label">Birthday</span>
              <span class="value">{{ professor.birthday }}</span>
            </div>
            <div class="info-item">
              <span class="label">Blood Type</span>
              <span class="value">{{ professor.blood_type || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Address</span>
              <span class="value">{{ professor.address || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Professional Information -->
        <div class="info-card">
          <div class="card-header">
            <h3>Professional Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="info-item">
              <span class="label">Role</span>
              <span class="value">{{ professor.role || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Educational Attainment</span>
              <span class="value">{{ professor.educational_attainment || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Experience</span>
              <span class="value">{{ professor.experience || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Organization</span>
              <span class="value">{{ professor.organization || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Application Date</span>
              <span class="value">{{ professor.application_date || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Courses Information -->
        <div class="info-card full-width">
          <div class="card-header">
            <h3>Courses Handled</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
          </div>
          <div class="card-content">
            <div class="courses-content">
              <p>{{ professor.courses_handled || 'No courses assigned yet' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Error Loading Professor Data</h3>
      <p>{{ error }}</p>
      <router-link to="/professors" class="btn btn-primary">Back to Professors</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const professor = ref<any>(null)
const loading = ref(true)
const error = ref('')

const fetchProfessor = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/professors/${route.params.id}`)
    professor.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load professor data'
  } finally {
    loading.value = false
  }
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
}

const getEmploymentClass = (type: string) => {
  if (type === 'Full-time') return 'full-time'
  if (type === 'Part-time') return 'part-time'
  return 'contract'
}

onMounted(() => {
  fetchProfessor()
})
</script>

<style scoped>
.professor-detail-view {
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

/* Profile Overview */
.profile-overview-card {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
  border-left: 4px solid #3b82f6;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  flex-shrink: 0;
}

.profile-info h2 {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.professor-id {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0 0 1rem 0;
  font-family: 'Courier New', monospace;
}

.profile-badges {
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

.status-badge.full-time {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.part-time {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.contract {
  background-color: #fef3c7;
  color: #92400e;
}

.department-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  background-color: #f3f4f6;
  color: #374151;
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

.courses-content p {
  color: #1f2937;
  line-height: 1.6;
  margin: 0;
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

/* Responsive Design */
@media (max-width: 768px) {
  .professor-detail-view {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .profile-badges {
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
  .profile-overview-card {
    padding: 1.5rem;
  }
  
  .profile-avatar {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
  
  .profile-info h2 {
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
