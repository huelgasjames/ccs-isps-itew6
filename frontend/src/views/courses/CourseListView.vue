<template>
  <div class="course-list-view">
    <div class="page-header">
      <h1>📚 Courses Management</h1>
      <router-link 
        v-if="authStore.isAdmin" 
        to="/courses/create" 
        class="btn btn-primary"
      >
        ➕ Create Course
      </router-link>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number">{{ totalCourses }}</div>
        <div class="stat-label">Total Courses</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ totalCredits }}</div>
        <div class="stat-label">Total Credits</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ departmentCount }}</div>
        <div class="stat-label">Departments</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ avgCredits }}</div>
        <div class="stat-label">Avg Credits</div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="search"
          type="text"
          placeholder="🔍 Search courses by code, name, or department..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="selectedDepartment" class="filter-select">
          <option value="">All Departments</option>
          <option v-for="dept in availableDepartments" :key="dept" :value="dept">
            {{ dept }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedLevel" class="filter-select">
          <option value="">All Levels</option>
          <option value="100">100 Level</option>
          <option value="200">200 Level</option>
          <option value="300">300 Level</option>
          <option value="400">400 Level</option>
        </select>
      </div>
      <button @click="resetFilters" class="btn btn-secondary">
        🔄 Reset Filters
      </button>
    </div>

    <!-- Course Table -->
    <div class="course-table-container">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <span>Loading courses...</span>
      </div>
      <div v-else-if="error" class="error-state">
        <div class="error-icon">⚠️</div>
        <h3>Error loading courses</h3>
        <p>{{ error }}</p>
      </div>

      <div v-else class="table-wrapper">
        <table class="course-table">
          <thead>
            <tr>
              <th>📋 Course Code</th>
              <th>📖 Course Name</th>
              <th>💰 Credits</th>
              <th>🏢 Department</th>
              <th>📊 Level</th>
              <th>⚙️ Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="course in filteredCourses" :key="course.id">
              <td>
                <span class="course-code">{{ course.course_code }}</span>
              </td>
              <td>
                <span class="course-name">{{ course.course_name }}</span>
              </td>
              <td>
                <span class="credits-badge">{{ course.credits }}</span>
              </td>
              <td>
                <span :class="['dept-badge', getDeptClass(course.department)]">
                  {{ course.department }}
                </span>
              </td>
              <td>
                <span :class="['level-badge', getLevelClass(course.level)]">
                  {{ course.level }}
                </span>
              </td>
              <td>
                <router-link 
                  :to="`/courses/${course.id}`" 
                  class="btn btn-sm btn-info"
                >
                  👁 View
                </router-link>
                <router-link 
                  v-if="authStore.isAdmin"
                  :to="`/courses/${course.id}/edit`" 
                  class="btn btn-sm btn-warning"
                >
                  ✏️ Edit
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredCourses.length === 0" class="no-results">
          📭 No courses found matching your criteria.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

// Define Course interface
interface Course {
  id: number
  course_code: string
  course_name: string
  credits: number
  department: string
  level: string
}

const authStore = useAuthStore()
const courses = ref<Course[]>([])
const search = ref('')
const selectedDepartment = ref('')
const selectedLevel = ref('')
const loading = ref(true)
const error = ref('')

const fetchCourses = async () => {
  try {
    loading.value = true
    error.value = ''
    const response = await api.get('/courses', {
      params: { 
        search: search.value,
        department: selectedDepartment.value,
        level: selectedLevel.value
      }
    })
    courses.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to fetch courses'
    console.error('Error fetching courses:', err)
  } finally {
    loading.value = false
  }
}

// Computed properties for statistics
const totalCourses = computed(() => courses.value.length)

const totalCredits = computed(() => 
  courses.value.reduce((sum, course) => sum + (course.credits || 0), 0)
)

const departmentCount = computed(() => {
  const depts = [...new Set(courses.value.map(c => c.department))]
  return depts.length
})

const avgCredits = computed(() => {
  if (courses.value.length === 0) return 0
  return Math.round(totalCredits.value / courses.value.length * 10) / 10
})

// Available departments for filter
const availableDepartments = computed(() => {
  const depts = [...new Set(courses.value.map(c => c.department))].sort()
  return depts
})

// Filtered courses
const filteredCourses = computed(() => {
  let filtered = courses.value

  // Search filter
  if (search.value) {
    const query = search.value.toLowerCase()
    filtered = filtered.filter(course =>
      course.course_code.toLowerCase().includes(query) ||
      course.course_name.toLowerCase().includes(query) ||
      course.department.toLowerCase().includes(query)
    )
  }

  // Department filter
  if (selectedDepartment.value) {
    filtered = filtered.filter(course => course.department === selectedDepartment.value)
  }

  // Level filter
  if (selectedLevel.value) {
    filtered = filtered.filter(course => course.level === selectedLevel.value)
  }

  return filtered
})

function getDeptClass(dept: string): string {
  const classes: Record<string, string> = {
    'CCS': 'dept-ccs',
    'Engineering': 'dept-engineering',
    'Business': 'dept-business',
    'Arts': 'dept-arts',
    'Science': 'dept-science',
    'Medicine': 'dept-medicine',
    'Law': 'dept-law'
  }
  return classes[dept] || 'dept-default'
}

function getLevelClass(level: string): string {
  const classes: Record<string, string> = {
    '100': 'level-100',
    '200': 'level-200',
    '300': 'level-300',
    '400': 'level-400'
  }
  return classes[level] || 'level-default'
}

function resetFilters() {
  search.value = ''
  selectedDepartment.value = ''
  selectedLevel.value = ''
}

onMounted(() => {
  fetchCourses()
})

</script>

<style scoped>
.course-list-view {
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
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Statistics Cards */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #3b82f6;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

/* Filters */
.filters-container {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-group {
  min-width: 150px;
}

.filter-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  background: white;
  cursor: pointer;
  transition: border-color 0.2s;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  margin-right: 0.25rem;
}

.btn-info {
  background-color: #0ea5e9;
  color: white;
}

.btn-info:hover {
  background-color: #0284c7;
}

.btn-warning {
  background-color: #f59e0b;
  color: white;
}

.btn-warning:hover {
  background-color: #d97706;
}

/* Course Table */
.course-table-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table-wrapper {
  overflow-x: auto;
}

.course-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

.course-table th {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  white-space: nowrap;
}

.course-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
}

.course-table tbody tr:hover {
  background-color: #f9fafb;
}

/* Course Elements */
.course-code {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #1f2937;
  background: #f3f4f6;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.course-name {
  font-weight: 500;
  color: #374151;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.credits-badge {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 0.875rem;
}

/* Department Badges */
.dept-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.dept-ccs {
  background-color: #dbeafe;
  color: #1e40af;
}

.dept-engineering {
  background-color: #e0e7ff;
  color: #3730a3;
}

.dept-business {
  background-color: #d1fae5;
  color: #065f46;
}

.dept-arts {
  background-color: #fce7f3;
  color: #92400e;
}

.dept-science {
  background-color: #e0f2fe;
  color: #1e40af;
}

.dept-medicine {
  background-color: #fef3c7;
  color: #92400e;
}

.dept-law {
  background-color: #e9d5ff;
  color: #4338ca;
}

.dept-default {
  background-color: #f3f4f6;
  color: #374151;
}

/* Level Badges */
.level-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.level-100 {
  background-color: #d1fae5;
  color: #065f46;
}

.level-200 {
  background-color: #dbeafe;
  color: #1e40af;
}

.level-300 {
  background-color: #fef3c7;
  color: #92400e;
}

.level-400 {
  background-color: #fecaca;
  color: #991b1b;
}

.level-default {
  background-color: #f3f4f6;
  color: #374151;
}

/* States */
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

.no-results {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
  font-style: italic;
  font-size: 1.125rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .course-list-view {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .filters-container {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-box {
    min-width: auto;
  }
  
  .filter-group {
    min-width: auto;
  }
  
  .stats-container {
    grid-template-columns: 1fr;
  }
  
  .course-table {
    font-size: 0.75rem;
  }
  
  .course-table th,
  .course-table td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 480px) {
  .page-header h1 {
    font-size: 1.25rem;
  }
  
  .btn {
    font-size: 0.75rem;
  }
  
  .btn-sm {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
  }
}
</style>
