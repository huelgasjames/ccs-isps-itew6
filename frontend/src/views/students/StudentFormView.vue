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
              @change="calculateAge"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="age">Age *</label>
            <input 
              id="age"
              v-model="form.age"
              type="number"
              min="16"
              max="100"
              required
              class="form-control"
              readonly
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
            </select>
          </div>
          <div class="form-group">
            <label for="program">Program *</label>
            <select id="program" v-model="form.program" class="form-control" required>
              <option value="">Select Program</option>
              <option value="BSIT">BSIT</option>
              <option value="BSCS">BSCS</option>
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
import { useStudentStore } from '@/stores/student'

const route = useRoute()
const router = useRouter()
const studentStore = useStudentStore()
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
  age: '',
  gender: '',
  city: '',
  student_number: '',
  year_level: '',
  program: '',
  academic_standing: '',
  gpa: '',
  emergency_contact_name: '',
  emergency_contact_phone: '',
  skills: [] as any[],
  activities: [] as any[],
  academic_history: [] as any[],
  affiliations: [] as any[],
  violations: [] as any[]
})

onMounted(async () => {
  if (isEdit.value) {
    try {
      // Try to get student from store first (for generated data)
      let student = studentStore.students.find(s => s.id === Number(route.params.id))
      
      // If not found in store, try to fetch from API
      if (!student) {
        student = await studentStore.fetchStudentById(Number(route.params.id))
      }
      
      if (student) {
        form.value = {
          first_name: student.personalInfo?.firstName || '',
          middle_name: student.personalInfo?.middleName || '',
          last_name: student.personalInfo?.lastName || '',
          email: student.personalInfo?.email || '',
          password: '',
          phone: student.personalInfo?.phone || '',
          date_of_birth: student.personalInfo?.dateOfBirth || '',
          age: student.personalInfo?.age?.toString() || '',
          gender: student.personalInfo?.gender || '',
          city: student.personalInfo?.city || '',
          student_number: student.personalInfo?.studentId || '',
          year_level: student.academicStanding?.currentYear?.toString() || '',
          program: student.academicHistory?.[0]?.major || '',
          academic_standing: student.academicStanding?.standing || '',
          gpa: student.academicStanding?.currentGPA?.toString() || '',
          emergency_contact_name: student.personalInfo?.emergencyContact?.name || '',
          emergency_contact_phone: student.personalInfo?.emergencyContact?.phone || '',
          skills: student.skills || [],
          activities: student.activities || [],
          academic_history: student.academicHistory || [],
          affiliations: student.affiliations || [],
          violations: student.violations || []
        }
      }
    } catch (error) {
      console.error('Error fetching student:', error)
    }
  }
})

function calculateAge() {
  if (form.value.date_of_birth) {
    const today = new Date()
    const birthDate = new Date(form.value.date_of_birth)
    let age = today.getFullYear() - birthDate.getFullYear()
    const monthDiff = today.getMonth() - birthDate.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--
    }
    
    form.value.age = age.toString()
  }
}

async function handleSubmit() {
  loading.value = true
  try {
    if (isEdit.value) {
      // Update existing student in store
      const studentId = Number(route.params.id)
      const updateData = {
        id: studentId,
        personalInfo: {
          firstName: form.value.first_name,
          middleName: form.value.middle_name,
          lastName: form.value.last_name,
          email: form.value.email,
          phone: form.value.phone,
          dateOfBirth: form.value.date_of_birth,
          age: Number(form.value.age) || 0,
          gender: form.value.gender as 'male' | 'female' | 'other',
          address: '', // Add default empty values for required fields
          city: form.value.city,
          province: '', // Add default empty values
          postalCode: '', // Add default empty values
          studentId: form.value.student_number,
          emergencyContact: {
            name: form.value.emergency_contact_name,
            relationship: 'Guardian', // Default relationship
            phone: form.value.emergency_contact_phone
          }
        },
        academicStanding: {
          currentYear: Number(form.value.year_level) || 1,
          currentSemester: 'first' as const,
          currentGPA: Number(form.value.gpa) || 0,
          totalUnits: 0, // Default value
          standing: form.value.academic_standing as 'good' | 'warning' | 'probation',
          advisor: 'TBD' // Default value
        },
        skills: form.value.skills,
        activities: form.value.activities,
        academicHistory: form.value.academic_history,
        affiliations: form.value.affiliations,
        violations: form.value.violations,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      }
      
      await studentStore.updateStudent(studentId, updateData)
    } else {
      // Create new student
      const createData = {
        personalInfo: {
          firstName: form.value.first_name,
          middleName: form.value.middle_name,
          lastName: form.value.last_name,
          email: form.value.email,
          phone: form.value.phone,
          dateOfBirth: form.value.date_of_birth,
          age: Number(form.value.age) || 0,
          gender: form.value.gender as 'male' | 'female' | 'other',
          address: '', // Add default empty values for required fields
          city: form.value.city,
          province: '', // Add default empty values
          postalCode: '', // Add default empty values
          studentId: form.value.student_number,
          emergencyContact: {
            name: form.value.emergency_contact_name,
            relationship: 'Guardian', // Default relationship
            phone: form.value.emergency_contact_phone
          }
        },
        academicStanding: {
          currentYear: Number(form.value.year_level) || 1,
          currentSemester: 'first' as const,
          currentGPA: Number(form.value.gpa) || 0,
          totalUnits: 0, // Default value
          standing: form.value.academic_standing as 'good' | 'warning' | 'probation',
          advisor: 'TBD' // Default value
        }
      }
      
      await studentStore.createStudent(createData)
    }
    router.push('/students')
  } catch (error: any) {
    console.error('Error saving student:', error)
    
    // Handle store errors
    if (studentStore.error) {
      alert(studentStore.error)
    } else {
      alert('Failed to save student. Please try again.')
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
