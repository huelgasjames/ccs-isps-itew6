<template>
  <div class="professor-detail-view">
    <!-- Header -->
    <div class="page-header">
      <router-link to="/professors" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M15 19l-7-7 7-7" />
        </svg>
        Back to Professors
      </router-link>
      <h1>Professor Details</h1>
      <router-link
        v-if="professor"
        :to="`/professors/${professor.id}/edit`"
        class="btn btn-primary"
      >
        Edit Professor
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <span>Loading professor data...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h3>Error loading professor data</h3>
      <p>{{ error }}</p>
      <router-link to="/professors" class="btn btn-secondary">
        Back to Professors
      </router-link>
    </div>

    <!-- Professor Details -->
    <div v-else-if="professor" class="professor-content">
      <!-- Professor Info Card -->
      <div class="info-card">
        <div class="card-header">
          <h2>👤 Professor Information</h2>
        </div>
        <div class="card-content">
          <div class="info-grid">
            <div class="info-item">
              <label>Full Name</label>
              <value>{{ professor.first_name }} {{ professor.middle_name }} {{ professor.last_name }}</value>
            </div>
            <div class="info-item">
              <label>Professor ID</label>
              <value>{{ professor.professor_unique_id || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Email</label>
              <value>{{ professor.email }}</value>
            </div>
            <div class="info-item">
              <label>Contact Number</label>
              <value>{{ professor.contact_number || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Age</label>
              <value>{{ professor.age || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Birthday</label>
              <value>{{ professor.birthday || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Blood Type</label>
              <value>{{ professor.blood_type || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Department</label>
              <value>{{ professor.department }}</value>
            </div>
            <div class="info-item">
              <label>Employment Type</label>
              <value>
                <span :class="['status-badge', getEmploymentClass(professor.employment_type)]">
                  {{ professor.employment_type }}
                </span>
              </value>
            </div>
            <div class="info-item">
              <label>Role</label>
              <value>{{ professor.role || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Application Date</label>
              <value>{{ professor.application_date || 'N/A' }}</value>
            </div>
          </div>
        </div>
      </div>

      <!-- Professional Information Card -->
      <div class="info-card">
        <div class="card-header">
          <h2>💼 Professional Information</h2>
        </div>
        <div class="card-content">
          <div class="info-grid">
            <div class="info-item">
              <label>Educational Attainment</label>
              <value>{{ professor.educational_attainment || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Experience</label>
              <value>{{ professor.experience || 'N/A' }}</value>
            </div>
            <div class="info-item full-width">
              <label>Courses Handled</label>
              <value>{{ professor.courses_handled || 'N/A' }}</value>
            </div>
            <div class="info-item">
              <label>Organization</label>
              <value>{{ professor.organization || 'N/A' }}</value>
            </div>
            <div class="info-item full-width">
              <label>Address</label>
              <value>{{ professor.address || 'N/A' }}</value>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const professor = ref<any>(null)
const loading = ref(true)
const error = ref('')

const fetchProfessor = async () => {
  try {
    const response = await api.get(`/professors/${route.params.id}`)
    professor.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load professor data'
  } finally {
    loading.value = false
  }
}

function getEmploymentClass(type: string) {
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
  min-height: 100vh;
  background: #f9fafb;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  background: white;
  padding: 1rem 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.back-link {
  display: flex;
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

.back-link svg {
  width: 20px;
  height: 20px;
}

/* Buttons */
.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-secondary {
  background-color: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background-color: #4b5563;
}

/* Loading and Error States */
.loading {
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
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  text-align: center;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.error-state h3 {
  color: #dc2626;
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.error-state p {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

/* Content Layout */
.professor-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 1024px) {
  .professor-content {
    grid-template-columns: 1fr 1fr;
  }
}

/* Info Cards */
.info-card {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.card-header h2 {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.card-content {
  padding: 1.5rem;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.info-item label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.25rem;
}

.info-item value {
  font-size: 0.875rem;
  color: #1f2937;
  padding: 0.5rem 0.75rem;
  background: #f9fafb;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
  min-height: 2.5rem;
  display: flex;
  align-items: center;
}

/* Status Badges */
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

/* Responsive Design */
@media (max-width: 768px) {
  .professor-detail-view {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .card-header {
    padding: 0.75rem 1rem;
  }
  
  .card-content {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .page-header h1 {
    font-size: 1.25rem;
  }
  
  .card-header h2 {
    font-size: 1rem;
  }
}
</style>
