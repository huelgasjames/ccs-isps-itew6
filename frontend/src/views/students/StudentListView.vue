<template>
  <div class="student-list-view">
    <div class="page-header">
      <h1>Students List</h1>
      <router-link to="/students/create" class="btn btn-primary">
        Create New Student
      </router-link>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number">{{ totalStudents }}</div>
        <div class="stat-label">Total Students</div>
      </div>
      <div class="stat-card probation">
        <div class="stat-number">{{ probationCount }}</div>
        <div class="stat-label">On Probation</div>
      </div>
      <div class="stat-card good">
        <div class="stat-number">{{ goodStandingCount }}</div>
        <div class="stat-label">Good Standing</div>
      </div>
      <div class="stat-card at-risk">
        <div class="stat-number">{{ atRiskCount }}</div>
        <div class="stat-label">At Risk</div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name or email..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="selectedYear" class="filter-select">
          <option value="">All Years</option>
          <option v-for="year in availableYears" :key="year" :value="year">
            Year {{ year }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedStanding" class="filter-select">
          <option value="">All Standing</option>
          <option value="excellent">Excellent</option>
          <option value="good">Good</option>
          <option value="average">Average</option>
          <option value="probation">Probation</option>
        </select>
      </div>
      <button @click="resetFilters" class="btn btn-secondary">
        Reset Filters
      </button>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="student-table-container">
      <table class="student-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Year Level</th>
            <th>Academic Standing</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in filteredStudents" :key="student.id">
            <td>{{ student.id }}</td>
            <td>{{ student.first_name }} {{ student.last_name }}</td>
            <td>{{ student.user?.email }}</td>
            <td>{{ student.year_level }}</td>
            <td>
              <span :class="['status-badge', student.academic_standing]">
                {{ formatStanding(student.academic_standing) }}
              </span>
            </td>
            <td>
              <router-link :to="`/students/${student.id}`" class="btn btn-sm btn-info">
                View
              </router-link>
              <router-link :to="`/students/${student.id}/edit`" class="btn btn-sm btn-warning">
                Edit
              </router-link>
              <button @click="archiveStudent(student.id)" class="btn btn-sm btn-danger">
                Archive
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="filteredStudents.length === 0" class="no-results">
        No students found matching your criteria.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

interface Student {
  id: number
  first_name: string
  last_name: string
  user?: { email: string }
  year_level: number
  academic_standing: string
}

const students = ref<Student[]>([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')
const selectedYear = ref('')
const selectedStanding = ref('')

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/students')
    students.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to fetch students'
  } finally {
    loading.value = false
  }
})

// Computed properties for statistics
const totalStudents = computed(() => students.value.length)

const probationCount = computed(() => 
  students.value.filter(student => student.academic_standing === 'probation').length
)

const goodStandingCount = computed(() => 
  students.value.filter(student => ['excellent', 'good'].includes(student.academic_standing)).length
)

const atRiskCount = computed(() => 
  students.value.filter(student => ['average', 'probation'].includes(student.academic_standing)).length
)

// Available years for filter
const availableYears = computed(() => {
  const years = [...new Set(students.value.map(s => s.year_level))].sort((a, b) => a - b)
  return years
})

// Filtered students
const filteredStudents = computed(() => {
  let filtered = students.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(student =>
      student.first_name.toLowerCase().includes(query) ||
      student.last_name.toLowerCase().includes(query) ||
      student.user?.email?.toLowerCase().includes(query)
    )
  }

  // Year filter
  if (selectedYear.value) {
    filtered = filtered.filter(student => student.year_level === Number(selectedYear.value))
  }

  // Standing filter
  if (selectedStanding.value) {
    filtered = filtered.filter(student => student.academic_standing === selectedStanding.value)
  }

  return filtered
})

function formatStanding(standing: string) {
  return standing.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

function resetFilters() {
  searchQuery.value = ''
  selectedYear.value = ''
  selectedStanding.value = ''
}

async function archiveStudent(studentId: number) {
  const student = students.value.find(s => s.id === studentId)
  const studentName = student ? `${student.first_name} ${student.last_name}` : 'this student'
  
  if (confirm(`Are you sure you want to archive ${studentName}? This will remove them from the active list but keep their data for records.`)) {
    try {
      const response = await axios.delete(`http://127.0.0.1:8000/api/students/${studentId}`)
      
      // Show success message
      const message = response.data?.message || 'Student archived successfully'
      alert(`${message}\n\nStudent ID: ${response.data?.student_id}\nArchived at: ${new Date().toLocaleString()}`)
      
      // Remove from local list
      students.value = students.value.filter(student => student.id !== studentId)
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Failed to archive student'
      alert(`Error: ${errorMessage}`)
    }
  }
}
</script>

<style scoped>
.student-list-view {
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
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

.stat-card.probation {
  border-left-color: #ef4444;
}

.stat-card.good {
  border-left-color: #10b981;
}

.stat-card.at-risk {
  border-left-color: #f59e0b;
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
  min-width: 250px;
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
  margin-right: 0.25rem;
  font-size: 0.75rem;
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

.btn-danger {
  background-color: #ef4444;
  color: white;
}

.btn-danger:hover {
  background-color: #dc2626;
}

/* Table */
.student-table-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.student-table {
  width: 100%;
  border-collapse: collapse;
}

.student-table th,
.student-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.student-table th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
}

.student-table tbody tr:hover {
  background-color: #f9fafb;
}

/* Status Badges */
.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.excellent {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.good {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.average {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.probation {
  background-color: #fecaca;
  color: #991b1b;
}

/* States */
.loading,
.error {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #dc2626;
}

.no-results {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
  font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
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
}
</style>
