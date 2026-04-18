<template>
  <div class="student-form-view">
    <div class="page-header">
      <h1>{{ isEdit ? 'Edit Student' : 'Create New Student' }}</h1>
      <router-link to="/students" class="btn btn-secondary">
        Back to List
      </router-link>
    </div>

    <form @submit.prevent="handleSubmit" class="student-form">
      <div class="form-section">
        <h2>Personal Information</h2>
        <div class="form-grid">
          <div class="form-group">
            <label for="first_name">First Name *</label>
            <input 
              id="first_name"
              v-model="form.first_name"
              type="text"
              required
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input 
              id="middle_name"
              v-model="form.middle_name"
              type="text"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="last_name">Last Name *</label>
            <input 
              id="last_name"
              v-model="form.last_name"
              type="text"
              required
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input 
              id="email"
              v-model="form.email"
              type="email"
              required
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="password">Password *</label>
            <input 
              id="password"
              v-model="form.password"
              type="password"
              :required="!isEdit"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input 
              id="phone"
              v-model="form.phone"
              type="text"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input 
              id="date_of_birth"
              v-model="form.date_of_birth"
              type="date"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" v-model="form.gender" class="form-control">
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="city">City</label>
            <input 
              id="city"
              v-model="form.city"
              type="text"
              class="form-control"
            />
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2>Academic Information</h2>
        <div class="form-grid">
          <div class="form-group">
            <label for="student_number">Student Number *</label>
            <input 
              id="student_number"
              v-model="form.student_number"
              type="text"
              required
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="year_level">Year Level *</label>
            <select id="year_level" v-model="form.year_level" class="form-control" required>
              <option value="">Select Year Level</option>
              <option value="1">1st Year</option>
              <option value="2">2nd Year</option>
              <option value="3">3rd Year</option>
              <option value="4">4th Year</option>
              <option value="5">5th Year</option>
            </select>
          </div>
          <div class="form-group">
            <label for="academic_standing">Academic Standing *</label>
            <select id="academic_standing" v-model="form.academic_standing" class="form-control" required>
              <option value="">Select Standing</option>
              <option value="good">Good Standing</option>
              <option value="at_risk">At Risk</option>
              <option value="probation">Probation</option>
            </select>
          </div>
          <div class="form-group">
            <label for="gpa">GPA</label>
            <input 
              id="gpa"
              v-model="form.gpa"
              type="number"
              step="0.01"
              min="0"
              max="5"
              class="form-control"
            />
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2>Emergency Contact</h2>
        <div class="form-grid">
          <div class="form-group">
            <label for="emergency_contact_name">Emergency Contact Name</label>
            <input 
              id="emergency_contact_name"
              v-model="form.emergency_contact_name"
              type="text"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="emergency_contact_phone">Emergency Contact Phone</label>
            <input 
              id="emergency_contact_phone"
              v-model="form.emergency_contact_phone"
              type="text"
              class="form-control"
            />
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Saving...' : (isEdit ? 'Update Student' : 'Create Student') }}
        </button>
        <router-link to="/students" class="btn btn-secondary">
          Cancel
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)
const loading = ref(false)

const form = ref({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: '',
  password: '',
  phone: '',
  date_of_birth: '',
  gender: '',
  city: '',
  student_number: '',
  year_level: '',
  academic_standing: '',
  gpa: '',
  emergency_contact_name: '',
  emergency_contact_phone: '',
  skills: [],
  activities: [],
  academic_history: [],
  affiliations: [],
  violations: []
})

onMounted(async () => {
  if (isEdit.value) {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/students/${route.params.id}`)
      const student = response.data
      form.value = {
        first_name: student.first_name || '',
        middle_name: student.middle_name || '',
        last_name: student.last_name || '',
        email: student.user?.email || '',
        password: '',
        phone: student.phone || '',
        date_of_birth: student.date_of_birth || '',
        gender: student.gender || '',
        city: student.city || '',
        student_number: student.student_number || '',
        year_level: student.year_level || '',
        academic_standing: student.academic_standing || '',
        gpa: student.gpa || '',
        emergency_contact_name: student.emergency_contact_name || '',
        emergency_contact_phone: student.emergency_contact_phone || '',
        skills: student.skills || [],
        activities: student.activities || [],
        academic_history: student.academic_history || [],
        affiliations: student.affiliations || [],
        violations: student.violations || []
      }
    } catch (error) {
      console.error('Error fetching student:', error)
    }
  }
})

async function handleSubmit() {
  loading.value = true
  try {
    if (isEdit.value) {
      await axios.put(`http://127.0.0.1:8000/api/students/${route.params.id}`, form.value)
    } else {
      await axios.post('http://127.0.0.1:8000/api/students', form.value)
    }
    router.push('/students')
  } catch (error: any) {
    console.error('Error saving student:', error)
    console.error('Error response:', error.response?.data)
    
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.keys(errors).map(key => {
        const fieldErrors = Array.isArray(errors[key]) ? errors[key] : [errors[key]]
        return `${key}: ${fieldErrors.join(', ')}`
      })
      alert(`Validation errors:\n${errorMessages.join('\n')}`)
    } else {
      alert(error.response?.data?.message || 'Failed to save student')
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.student-form-view {
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
  transition: all 0.2s;
  display: inline-block;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
  border: none;
  cursor: pointer;
}

.btn-primary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #6b7280;
  color: white;
}

.form-section {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
}

.form-section h2 {
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  color: #1f2937;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-control {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}
</style>
