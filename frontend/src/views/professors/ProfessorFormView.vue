<template>
  <div class="professor-form-view">
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
          <h1>{{ isEdit ? 'Edit Professor' : 'Create Professor' }}</h1>
        </div>
        <div class="header-actions">
          <span class="status-badge">{{ isEdit ? 'Edit Mode' : 'Create Mode' }}</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>{{ isEdit ? 'Updating professor record...' : 'Creating new professor...' }}</p>
    </div>

    <!-- Form -->
    <div v-else class="form-container">
      <form @submit.prevent="handleSubmit" class="professor-form">
        <!-- Personal Information Section -->
        <div class="form-section">
          <div class="section-header">
            <h3>Personal Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="first_name" class="form-label">First Name</label>
              <input
                id="first_name"
                v-model="form.first_name"
                type="text"
                class="form-input"
                placeholder="Enter first name"
                required
              />
            </div>
            <div class="form-group">
              <label for="last_name" class="form-label">Last Name</label>
              <input
                id="last_name"
                v-model="form.last_name"
                type="text"
                class="form-input"
                placeholder="Enter last name"
                required
              />
            </div>
            <div class="form-group">
              <label for="email" class="form-label">Email Address</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="form-input"
                placeholder="Enter email address"
                required
              />
            </div>
            <div class="form-group">
              <label for="phone" class="form-label">Phone Number</label>
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                class="form-input"
                placeholder="Enter phone number"
              />
            </div>
          </div>
        </div>

        <!-- Academic Information Section -->
        <div class="form-section">
          <div class="section-header">
            <h3>Academic Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="department" class="form-label">Department</label>
              <select id="department" v-model="form.department" class="form-input" required>
                <option value="">Select Department</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Physics">Physics</option>
                <option value="Chemistry">Chemistry</option>
                <option value="Biology">Biology</option>
                <option value="Engineering">Engineering</option>
                <option value="Business">Business</option>
                <option value="Arts">Arts</option>
              </select>
            </div>
            <div class="form-group">
              <label for="employment_type" class="form-label">Employment Type</label>
              <select id="employment_type" v-model="form.employment_type" class="form-input" required>
                <option value="">Select Employment Type</option>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Contract">Contract</option>
              </select>
            </div>
            <div class="form-group">
              <label for="role" class="form-label">Role</label>
              <select id="role" v-model="form.role" class="form-input" required>
                <option value="">Select Role</option>
                <option value="Professor">Professor</option>
                <option value="Associate Professor">Associate Professor</option>
                <option value="Assistant Professor">Assistant Professor</option>
                <option value="Lecturer">Lecturer</option>
                <option value="Department Head">Department Head</option>
                <option value="Dean">Dean</option>
              </select>
            </div>
            <div class="form-group">
              <label for="office_location" class="form-label">Office Location</label>
              <input
                id="office_location"
                v-model="form.office_location"
                type="text"
                class="form-input"
                placeholder="Enter office location"
              />
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <router-link to="/professors" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
            Cancel
          </router-link>
          <button type="submit" :disabled="loading" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <polyline points="20 6 9 17 4 12"/>
              <path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/>
            </svg>
            {{ isEdit ? 'Update Professor' : 'Create Professor' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()

const loading = ref(false)
const isEdit = computed(() => !!route.params.id)

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  department: '',
  employment_type: '',
  role: '',
  phone: '',
  office_location: ''
})

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await axios.put(`http://127.0.0.1:8000/api/professors/${route.params.id}`, form.value)
    } else {
      await axios.post('http://127.0.0.1:8000/api/professors', form.value)
    }
    router.push('/professors')
  } catch (error) {
    console.error('Error saving professor:', error)
  } finally {
    loading.value = false
  }
}

const fetchProfessor = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/professors/${route.params.id}`)
    form.value = response.data
  } catch (error) {
    console.error('Error fetching professor:', error)
  }
}

onMounted(async () => {
  if (isEdit.value) {
    await fetchProfessor()
  }
})
</script>

<style scoped>
.professor-form-view {
  padding: 2rem;
  max-width: 900px;
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

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  background-color: #3b82f6;
  color: white;
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

/* Form Container */
.form-container {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.professor-form {
  padding: 2rem;
}

/* Form Sections */
.form-section {
  margin-bottom: 2rem;
}

.form-section:last-child {
  margin-bottom: 0;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.section-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.section-header svg {
  width: 24px;
  height: 24px;
  color: #6b7280;
}

/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
  background-color: white;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
}

/* Form Actions */
.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
  margin-top: 2rem;
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
  cursor: pointer;
  border: none;
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

.btn-primary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background-color: #4b5563;
}

/* Responsive Design */
@media (max-width: 768px) {
  .professor-form-view {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
    gap: 1rem;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .professor-form {
    padding: 1.5rem;
  }
  
  .header-left h1 {
    font-size: 1.5rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .section-header h3 {
    font-size: 1rem;
  }
}
</style>
