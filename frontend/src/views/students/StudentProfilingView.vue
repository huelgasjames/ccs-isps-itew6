<template>
  <div class="student-profiling-system">
    <!-- Header -->
    <div class="system-header">
      <div class="header-content">
        <div class="header-left">
          <h1 class="system-title">Student Profiling System</h1>
          <p class="system-subtitle">CCS Department - Comprehensive Student Management</p>
        </div>
        <div class="header-actions">
          <button @click="showCreateModal = true" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Student
          </button>
          <button @click="generateSampleData" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
            Generate Sample Data
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Students</h3>
          <p class="stat-number">{{ totalStudents }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
            <path d="M2 17l10 5 10-5"/>
            <path d="M2 12l10 5 10-5"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Average GPA</h3>
          <p class="stat-number">{{ averageGPA.toFixed(2) }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 6v6l4 2"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Active Students</h3>
          <p class="stat-number">{{ activeStudents }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Violations</h3>
          <p class="stat-number">{{ totalViolations }}</p>
        </div>
      </div>
    </div>

    <!-- Filter Toggle Button -->
    <div class="filter-toggle">
      <button @click="toggleFilterPanel" class="btn btn-outline">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        {{ filterPanelOpen ? 'Hide Filters' : 'Show Filters' }}
      </button>
    </div>

    <!-- Advanced Filters Panel -->
    <div v-if="filterPanelOpen" class="filters-section">
      <div class="filters-header">
        <h2>Advanced Filters</h2>
        <button @click="clearFilters" class="btn btn-outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
            <path d="M3 3v5h5"/>
          </svg>
          Reset
        </button>
      </div>

      <div class="filters-grid">
        <div class="filter-group">
          <label>Search</label>
          <div class="search-input">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            <input
              v-model="localFilter.search"
              type="text"
              placeholder="Search by name, ID, email..."
              @input="updateFilter"
            />
          </div>
        </div>

        <div class="filter-group">
          <label>Skills</label>
          <select v-model="localFilter.skills" @change="updateFilter" class="filter-select">
            <option value="">All Skills</option>
            <option v-for="skill in availableSkills" :key="skill" :value="skill">
              {{ skill }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label>Activities</label>
          <select v-model="localFilter.activities" @change="updateFilter" class="filter-select">
            <option value="">All Activities</option>
            <option v-for="activity in availableActivities" :key="activity" :value="activity">
              {{ activity }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label>Academic Standing</label>
          <select v-model="localFilter.academicStanding" @change="updateFilter" class="filter-select">
            <option value="">All Standing</option>
            <option value="excellent">Excellent</option>
            <option value="good">Good</option>
            <option value="average">Average</option>
            <option value="warning">Warning</option>
            <option value="probation">Probation</option>
          </select>
        </div>

        <!-- GPA Range -->
        <div class="filter-group">
          <label>GPA Range</label>
          <div class="range-inputs">
            <input 
              v-model="localFilter.gpaRange!.min" 
              type="number" 
              step="0.1" 
              min="0" 
              max="4" 
              placeholder="Min"
              @input="updateFilter"
            />
            <span>-</span>
            <input 
              v-model="localFilter.gpaRange!.max" 
              type="number" 
              step="0.1" 
              min="0" 
              max="4" 
              placeholder="Max"
              @input="updateFilter"
            />
          </div>
        </div>

        <!-- Age Range -->
        <div class="filter-group">
          <label>Age Range</label>
          <div class="range-inputs">
            <input 
              v-model="localFilter.ageRange!.min" 
              type="number" 
              min="16" 
              max="30" 
              placeholder="Min"
              @input="updateFilter"
            />
            <span>-</span>
            <input 
              v-model="localFilter.ageRange!.max" 
              type="number" 
              min="16" 
              max="30" 
              placeholder="Max"
              @input="updateFilter"
            />
          </div>
        </div>
      </div>

      <div class="filter-actions">
        <button @click="applyFilters" class="btn primary">Apply Filters</button>
        <button @click="clearFilters" class="btn secondary">Clear All</button>
      </div>
    </div>

    <!-- Quick Filters -->
    <div class="quick-filters">
      <h3>Quick Filters</h3>
      <div class="quick-filter-buttons">
        <button
          v-for="filter in quickFilters"
          :key="filter.name"
          @click="applyQuickFilter(filter)"
          :class="['quick-filter-btn', { active: isQuickFilterActive(filter) }]"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Display Mode Toggle -->
    <div class="display-controls">
      <div class="display-modes">
        <button
          v-for="mode in displayModes"
          :key="mode.value"
          @click="displayMode = mode.value"
          :class="['display-mode-btn', { active: displayMode === mode.value }]"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <component :is="mode.icon" />
          </svg>
          {{ mode.label }}
        </button>
      </div>

      <div class="results-info">
        <span>{{ filteredStudents.length }} results found</span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading students...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Error Loading Students</h3>
      <p>{{ error }}</p>
      <button @click="fetchStudents" class="btn btn-primary">Retry</button>
    </div>

    <!-- Students Display -->
    <div v-else class="students-display">
      <!-- Table View -->
      <div v-if="displayMode === 'table'" class="table-view">
        <table class="students-table">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Year</th>
              <th>GPA</th>
              <th>Standing</th>
              <th>Skills</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in paginatedStudents" :key="student.id">
              <td>{{ student.personalInfo.studentId }}</td>
              <td>
                <div class="student-name">
                  {{ student.personalInfo.firstName }} {{ student.personalInfo.lastName }}
                </div>
              </td>
              <td>{{ student.personalInfo.email }}</td>
              <td>Year {{ student.academicStanding.currentYear }}</td>
              <td>
                <span :class="['gpa-badge', getGPAClass(student.academicStanding.currentGPA)]">
                  {{ student.academicStanding.currentGPA.toFixed(2) }}
                </span>
              </td>
              <td>
                <span :class="['standing-badge', getStandingClass(student.academicStanding.standing)]">
                  {{ student.academicStanding.standing }}
                </span>
              </td>
              <td>
                <div class="skills-tags">
                  <span
                    v-for="skill in student.skills.slice(0, 2)"
                    :key="skill.id"
                    class="skill-tag"
                  >
                    {{ skill.name }}
                  </span>
                  <span v-if="student.skills.length > 2" class="more-skills">
                    +{{ student.skills.length - 2 }}
                  </span>
                </div>
              </td>
              <td>
                <div class="action-buttons">
                  <button @click="viewStudent(student.id)" class="btn-icon" title="View Profile">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <button @click="editStudent(student.id)" class="btn-icon" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button @click="deleteStudent(student.id)" class="btn-icon danger" title="Delete">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards View -->
      <div v-else-if="displayMode === 'cards'" class="cards-view">
        <div class="cards-grid">
          <div
            v-for="student in paginatedStudents"
            :key="student.id"
            class="student-card"
            @click="viewStudent(student.id)"
          >
            <div class="card-header">
              <div class="student-avatar">
                {{ getInitials(student.personalInfo.firstName, student.personalInfo.lastName) }}
              </div>
              <div class="student-info">
                <h3>{{ student.personalInfo.firstName }} {{ student.personalInfo.lastName }}</h3>
                <p>{{ student.personalInfo.studentId }}</p>
              </div>
              <div class="card-actions">
                <button @click.stop="editStudent(student.id)" class="btn-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="card-body">
              <div class="student-details">
                <div class="detail-item">
                  <span class="label">Email:</span>
                  <span class="value">{{ student.personalInfo.email }}</span>
                </div>
                <div class="detail-item">
                  <span class="label">Year:</span>
                  <span class="value">Year {{ student.academicStanding.currentYear }}</span>
                </div>
                <div class="detail-item">
                  <span class="label">GPA:</span>
                  <span :class="['gpa-badge', getGPAClass(student.academicStanding.currentGPA)]">
                    {{ student.academicStanding.currentGPA.toFixed(2) }}
                  </span>
                </div>
              </div>

              <div class="skills-section">
                <h4>Skills</h4>
                <div class="skills-tags">
                  <span
                    v-for="skill in student.skills.slice(0, 3)"
                    :key="skill.id"
                    class="skill-tag"
                  >
                    {{ skill.name }}
                  </span>
                  <span v-if="student.skills.length > 3" class="more-skills">
                    +{{ student.skills.length - 3 }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Compact View -->
      <div v-else class="compact-view">
        <div class="compact-list">
          <div
            v-for="student in paginatedStudents"
            :key="student.id"
            class="compact-item"
            @click="viewStudent(student.id)"
          >
            <div class="compact-avatar">
              {{ getInitials(student.personalInfo.firstName, student.personalInfo.lastName) }}
            </div>
            <div class="compact-info">
              <h4>{{ student.personalInfo.firstName }} {{ student.personalInfo.lastName }}</h4>
              <p>{{ student.personalInfo.studentId }} • Year {{ student.academicStanding.currentYear }} • GPA {{ student.academicStanding.currentGPA.toFixed(2) }}</p>
              <div class="compact-tags">
                <span 
                  v-for="skill in student.skills.slice(0, 2)" 
                  :key="skill.id"
                  class="compact-tag"
                >
                  {{ skill.name }}
                </span>
              </div>
            </div>
            <div class="compact-status">
              <div class="status-badge" :class="{ active: student.isActive }">
                {{ student.isActive ? 'Active' : 'Inactive' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination">
      <button 
        @click="goToPage(currentPage - 1)" 
        :disabled="currentPage === 1"
        class="pagination-btn"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
        Previous
      </button>

      <div class="page-numbers">
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="goToPage(page)"
          :class="['page-number', { active: page === currentPage }]"
        >
          {{ page }}
        </button>
      </div>

      <button 
        @click="goToPage(currentPage + 1)" 
        :disabled="currentPage === totalPages"
        class="pagination-btn"
      >
        Next
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </button>
    </div>

    <!-- Create Student Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Create New Student</h2>
          <button @click="closeCreateModal" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="handleCreateStudent" class="create-student-form">
            <!-- Personal Information Section -->
            <div class="form-section">
              <h3>Personal Information</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>First Name *</label>
                  <input v-model="newStudent.firstName" type="text" required placeholder="Enter first name" />
                </div>
                <div class="form-group">
                  <label>Last Name *</label>
                  <input v-model="newStudent.lastName" type="text" required placeholder="Enter last name" />
                </div>
                <div class="form-group">
                  <label>Middle Name</label>
                  <input v-model="newStudent.middleName" type="text" placeholder="Enter middle name (optional)" />
                </div>
                <div class="form-group">
                  <label>Student ID *</label>
                  <input v-model="newStudent.studentId" type="text" required placeholder="e.g., 2024-001" />
                </div>
                <div class="form-group">
                  <label>Email *</label>
                  <input v-model="newStudent.email" type="email" required placeholder="student@ccs.edu" />
                </div>
                <div class="form-group">
                  <label>Phone *</label>
                  <input v-model="newStudent.phone" type="tel" required placeholder="+63 XXX XXX XXXX" />
                </div>
                <div class="form-group">
                  <label>Date of Birth *</label>
                  <input v-model="newStudent.dateOfBirth" type="date" required />
                </div>
                <div class="form-group">
                  <label>Age *</label>
                  <input v-model.number="newStudent.age" type="number" required min="16" max="30" />
                </div>
                <div class="form-group">
                  <label>Gender *</label>
                  <select v-model="newStudent.gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="form-group full-width">
                  <label>Address *</label>
                  <input v-model="newStudent.address" type="text" required placeholder="Enter address" />
                </div>
                <div class="form-group">
                  <label>City *</label>
                  <input v-model="newStudent.city" type="text" required placeholder="Enter city" />
                </div>
                <div class="form-group">
                  <label>Province *</label>
                  <input v-model="newStudent.province" type="text" required placeholder="Enter province" />
                </div>
                <div class="form-group">
                  <label>Postal Code *</label>
                  <input v-model="newStudent.postalCode" type="text" required placeholder="Enter postal code" />
                </div>
              </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="form-section">
              <h3>Emergency Contact</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>Contact Name *</label>
                  <input v-model="newStudent.emergencyContact.name" type="text" required placeholder="Enter contact name" />
                </div>
                <div class="form-group">
                  <label>Relationship *</label>
                  <input v-model="newStudent.emergencyContact.relationship" type="text" required placeholder="e.g., Mother, Father" />
                </div>
                <div class="form-group">
                  <label>Contact Phone *</label>
                  <input v-model="newStudent.emergencyContact.phone" type="tel" required placeholder="+63 XXX XXX XXXX" />
                </div>
              </div>
            </div>

            <!-- Academic Information Section -->
            <div class="form-section">
              <h3>Academic Information</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>Current Year *</label>
                  <select v-model.number="newStudent.academicStanding.currentYear" required>
                    <option value="">Select Year</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Semester *</label>
                  <select v-model="newStudent.academicStanding.currentSemester" required>
                    <option value="">Select Semester</option>
                    <option value="first">First Semester</option>
                    <option value="second">Second Semester</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Current GPA *</label>
                  <input v-model.number="newStudent.academicStanding.currentGPA" type="number" step="0.01" min="0" max="4" required placeholder="0.00" />
                </div>
                <div class="form-group">
                  <label>Total Units</label>
                  <input v-model.number="newStudent.academicStanding.totalUnits" type="number" min="0" placeholder="0" />
                </div>
                <div class="form-group">
                  <label>Academic Standing *</label>
                  <select v-model="newStudent.academicStanding.standing" required>
                    <option value="">Select Standing</option>
                    <option value="good">Good</option>
                    <option value="warning">Warning</option>
                    <option value="probation">Probation</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Advisor</label>
                  <input v-model="newStudent.academicStanding.advisor" type="text" placeholder="Enter advisor name" />
                </div>
              </div>
            </div>

            <!-- Academic History Section -->
            <div class="form-section">
              <h3>Academic History</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>School Name *</label>
                  <input v-model="newStudent.schoolName" type="text" required placeholder="Enter school name" />
                </div>
                <div class="form-group">
                  <label>Degree *</label>
                  <input v-model="newStudent.degree" type="text" required placeholder="e.g., Bachelor of Science" />
                </div>
                <div class="form-group">
                  <label>Major *</label>
                  <input v-model="newStudent.major" type="text" required placeholder="e.g., Computer Science" />
                </div>
                <div class="form-group">
                  <label>Start Date *</label>
                  <input v-model="newStudent.startDate" type="date" required />
                </div>
                <div class="form-group">
                  <label>GPA</label>
                  <input v-model.number="newStudent.gpa" type="number" step="0.01" min="0" max="4" placeholder="0.00" />
                </div>
                <div class="form-group">
                  <label>Status *</label>
                  <select v-model="newStudent.status" required>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="transferred">Transferred</option>
                  </select>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button @click="closeCreateModal" class="btn btn-secondary">Cancel</button>
          <button @click="handleCreateStudent" class="btn btn-primary" :disabled="isCreating">
            <span v-if="isCreating">Creating...</span>
            <span v-else>Create Student</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useStudentStore } from '@/stores/student'
import type { StudentFilter, DisplayMode, Student } from '@/types/student'

const router = useRouter()
const studentStore = useStudentStore()

// Local state
const filterPanelOpen = ref(false)
const showCreateModal = ref(false)
const isCreating = ref(false)
const localFilter = ref<StudentFilter>({
  search: '',
  skills: [],
  activities: [],
  affiliations: [],
  violationStatus: [],
  academicStanding: [],
  yearLevel: [],
  gpaRange: { min: 0, max: 4 },
  ageRange: { min: 16, max: 30 }
})

// New student form data
const newStudent = ref({
  // Personal Information
  firstName: '',
  lastName: '',
  middleName: '',
  studentId: '',
  email: '',
  phone: '',
  dateOfBirth: '',
  age: 0,
  gender: '',
  address: '',
  city: '',
  province: '',
  postalCode: '',
  emergencyContact: {
    name: '',
    relationship: '',
    phone: ''
  },
  // Academic Standing
  academicStanding: {
    currentYear: 1,
    currentSemester: 'first',
    currentGPA: 0,
    totalUnits: 0,
    standing: 'good',
    advisor: ''
  },
  // Academic History
  schoolName: '',
  degree: '',
  major: '',
  startDate: '',
  gpa: 0,
  status: 'ongoing'
})

// Available options for filters
const availableSkills = ref(['basketball', 'programming', 'python', 'javascript', 'leadership', 'communication'])
const availableActivities = ref(['basketball', 'volunteer', 'organization', 'sports', 'leadership'])

// Computed
const {
  filteredStudents,
  paginatedStudents,
  loading,
  error,
  displayMode,
  currentPage,
  pageSize,
  totalStudents,
  totalPages
} = studentStore

// Access store directly for reactive values
const activeStudents = computed(() => 
  studentStore.students.filter((s: Student) => s.isActive).length
)

const averageGPA = computed(() => {
  if (studentStore.students.length === 0) return 0
  const total = studentStore.students.reduce((sum: number, student: Student) => sum + student.academicStanding.currentGPA, 0)
  return total / studentStore.students.length
})

const totalViolations = computed(() => 
  studentStore.students.reduce((sum: number, student: Student) => sum + student.violations.length, 0)
)

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, (currentPage as any).value - 2)
  const end = Math.min((totalPages as any).value, start + 4)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Display modes
const displayModes = [
  { value: 'table', label: 'Table', icon: 'Table' },
  { value: 'cards', label: 'Cards', icon: 'Grid' },
  { value: 'compact', label: 'Compact', icon: 'List' }
]

// Quick filters
const quickFilters = [
  { name: 'highGPA', label: 'High GPA (3.5+)', filter: { gpaRange: { min: 3.5, max: 4 } } },
  { name: 'atRisk', label: 'At Risk', filter: { academicStanding: ['probation'] } },
  { name: 'programmers', label: 'Programmers', filter: { skills: ['programming'] } },
  { name: 'athletes', label: 'Athletes', filter: { activities: ['basketball'] } },
  { name: 'firstYear', label: 'First Year', filter: { yearLevel: [1] } },
  { name: 'senior', label: 'Senior Students', filter: { yearLevel: [4] } }
]

// Methods
const toggleFilterPanel = () => {
  filterPanelOpen.value = !filterPanelOpen.value
}

const updateFilter = () => {
  studentStore.setFilter(localFilter.value)
}

const toggleSkill = (skill: string) => {
  const skills = localFilter.value?.skills || []
  const index = skills.indexOf(skill)
  if (index > -1) {
    skills.splice(index, 1)
  } else {
    skills.push(skill)
  }
  localFilter.value = { ...localFilter.value, skills: [...skills] }
}

const toggleActivity = (activity: string) => {
  const activities = localFilter.value?.activities || []
  const index = activities.indexOf(activity)
  if (index > -1) {
    activities.splice(index, 1)
  } else {
    activities.push(activity)
  }
  localFilter.value = { ...localFilter.value, activities: [...activities] }
}

const applyFilters = () => {
  studentStore.setFilter(localFilter.value)
  filterPanelOpen.value = false
}

const clearFilters = () => {
  localFilter.value = {
    search: '',
    skills: [],
    activities: [],
    affiliations: [],
    violationStatus: [],
    academicStanding: [],
    yearLevel: [],
    gpaRange: { min: 0, max: 4 },
    ageRange: { min: 16, max: 30 }
  }
  studentStore.clearFilter()
}

const applyQuickFilter = (filter: any) => {
  Object.assign(localFilter.value, filter.filter)
  applyFilters()
}

const isQuickFilterActive = (filter: any) => {
  return Object.entries(filter.filter).every(([key, value]) => {
    return (localFilter.value as any)[key] === value
  })
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
}

const getGPAClass = (gpa: number) => {
  if (gpa >= 3.5) return 'excellent'
  if (gpa >= 3.0) return 'good'
  if (gpa >= 2.5) return 'average'
  return 'low'
}

const getStandingClass = (standing: string) => {
  switch (standing) {
    case 'excellent': return 'excellent'
    case 'good': return 'good'
    case 'average': return 'average'
    case 'warning': return 'warning'
    case 'probation': return 'probation'
    default: return 'average'
  }
}

const viewStudent = (id: number) => {
  router.push(`/students/${id}`)
}

const editStudent = (id: number) => {
  router.push(`/students/${id}/edit`)
}

const deleteStudent = async (id: number) => {
  if (confirm('Are you sure you want to delete this student?')) {
    try {
      await studentStore.deleteStudent(id)
    } catch (error) {
      console.error('Failed to delete student:', error)
    }
  }
}

const goToPage = (page: number) => {
  studentStore.goToPage(page)
}

const closeCreateModal = () => {
  showCreateModal.value = false
  resetForm()
}

const resetForm = () => {
  newStudent.value = {
    // Personal Information
    firstName: '',
    lastName: '',
    middleName: '',
    studentId: '',
    email: '',
    phone: '',
    dateOfBirth: '',
    age: 0,
    gender: '',
    address: '',
    city: '',
    province: '',
    postalCode: '',
    emergencyContact: {
      name: '',
      relationship: '',
      phone: ''
    },
    // Academic Standing
    academicStanding: {
      currentYear: 1,
      currentSemester: 'first',
      currentGPA: 0,
      totalUnits: 0,
      standing: 'good',
      advisor: ''
    },
    // Academic History
    schoolName: '',
    degree: '',
    major: '',
    startDate: '',
    gpa: 0,
    status: 'ongoing'
  }
}

const handleCreateStudent = async () => {
  try {
    isCreating.value = true
    
    // Create student object matching the Student interface
    const studentData: any = {
      personalInfo: {
        firstName: newStudent.value.firstName,
        lastName: newStudent.value.lastName,
        middleName: newStudent.value.middleName || undefined,
        studentId: newStudent.value.studentId,
        email: newStudent.value.email,
        phone: newStudent.value.phone,
        dateOfBirth: newStudent.value.dateOfBirth,
        age: newStudent.value.age,
        gender: newStudent.value.gender,
        address: newStudent.value.address,
        city: newStudent.value.city,
        province: newStudent.value.province,
        postalCode: newStudent.value.postalCode,
        emergencyContact: newStudent.value.emergencyContact
      },
      academicStanding: newStudent.value.academicStanding,
      academicHistory: [{
        id: Date.now(),
        schoolName: newStudent.value.schoolName,
        degree: newStudent.value.degree,
        major: newStudent.value.major,
        startDate: newStudent.value.startDate,
        gpa: newStudent.value.gpa || undefined,
        status: newStudent.value.status
      }],
      activities: [],
      violations: [],
      skills: [],
      affiliations: [],
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
      isActive: true
    }
    
    // Add the student to the store
    await studentStore.createStudent(studentData)
    
    // Close modal and reset form
    closeCreateModal()
    
    // Show success message (you could add a toast notification here)
    console.log('Student created successfully!')
    
  } catch (error) {
    console.error('Failed to create student:', error)
    // You could show an error message here
  } finally {
    isCreating.value = false
  }
}

const generateSampleData = () => {
  studentStore.generateSampleData()
}

const fetchStudents = () => {
  studentStore.fetchStudents()
}

// Lifecycle
onMounted(() => {
  fetchStudents()
})
</script>

<style scoped>
.student-profiling-system {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Header */
.system-header {
  background: linear-gradient(135deg, #f97316 0%, #fd7e14 100%);
  color: white;
  padding: 2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.system-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0;
}

.system-subtitle {
  margin: 0.5rem 0 0 0;
  opacity: 0.9;
  font-size: 1rem;
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
  text-decoration: none;
}

.btn-primary {
  background: #f97316;
  color: white;
}

.btn-primary:hover {
  background: #ea580c;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-outline {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-outline:hover {
  background: rgba(255, 255, 255, 0.1);
}

.btn-icon {
  padding: 0.5rem;
  background: transparent;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  color: #6b7280;
}

.btn-icon:hover {
  background: #f3f4f6;
  color: #374151;
}

.btn-icon.danger:hover {
  background: #fef2f2;
  color: #ef4444;
}

/* Statistics */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #f97316 0%, #fd7e14 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-content h3 {
  margin: 0;
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

/* Filter Toggle */
.filter-toggle {
  margin-bottom: 2rem;
}

/* Filters */
.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.filters-header h2 {
  margin: 0;
  color: #1f2937;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.search-input {
  position: relative;
}

.search-input svg {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #6b7280;
}

.search-input input {
  padding-left: 2.5rem;
}

.filter-select, .search-input input {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: border-color 0.2s;
  background: white;
}

.filter-select:focus, .search-input input:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.range-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.range-inputs input {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  width: 80px;
}

.range-inputs span {
  color: #6b7280;
}

.filter-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

/* Quick Filters */
.quick-filters {
  margin-bottom: 2rem;
}

.quick-filters h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
}

.quick-filter-buttons {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.quick-filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 20px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.quick-filter-btn:hover {
  border-color: #f97316;
  color: #f97316;
}

.quick-filter-btn.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

/* Display Controls */
.display-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.display-modes {
  display: flex;
  gap: 0.5rem;
}

.display-mode-btn {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.display-mode-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.display-mode-btn.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.display-mode-btn svg {
  width: 20px;
  height: 20px;
}

.results-info {
  color: #6b7280;
  font-size: 0.875rem;
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

/* Table View */
.table-view {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.students-table {
  width: 100%;
  border-collapse: collapse;
}

.students-table th {
  background: #f8fafc;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.students-table td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.student-name {
  font-weight: 500;
  color: #1f2937;
}

.gpa-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.gpa-badge.excellent { background: #10b981; color: white; }
.gpa-badge.good { background: #3b82f6; color: white; }
.gpa-badge.average { background: #f59e0b; color: white; }
.gpa-badge.low { background: #ef4444; color: white; }

.standing-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.standing-badge.excellent { background: #10b981; color: white; }
.standing-badge.good { background: #3b82f6; color: white; }
.standing-badge.average { background: #f59e0b; color: white; }
.standing-badge.warning { background: #f59e0b; color: white; }
.standing-badge.probation { background: #ef4444; color: white; }

.skills-tags {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.skill-tag {
  padding: 0.25rem 0.5rem;
  background: #e5e7eb;
  color: #374151;
  border-radius: 12px;
  font-size: 0.75rem;
}

.more-skills {
  padding: 0.25rem 0.5rem;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 12px;
  font-size: 0.75rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

/* Cards View */
.cards-view {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.student-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.student-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.student-avatar {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.25rem;
}

.student-info h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
}

.student-info p {
  margin: 0.25rem 0 0 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.card-actions {
  margin-left: auto;
}

.card-body {
  padding: 1.5rem;
}

.student-details {
  margin-bottom: 1.5rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.detail-item .label {
  color: #6b7280;
  font-size: 0.875rem;
}

.detail-item .value {
  color: #1f2937;
  font-weight: 500;
}

.skills-section h4 {
  margin: 0 0 0.75rem 0;
  color: #1f2937;
  font-size: 0.875rem;
}

/* Compact View */
.compact-view {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.compact-list {
  display: flex;
  flex-direction: column;
}

.compact-item {
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e5e7eb;
  cursor: pointer;
  transition: background-color 0.2s;
}

.compact-item:hover {
  background: #f8fafc;
}

.compact-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1rem;
}

.compact-info {
  flex: 1;
}

.compact-info h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1rem;
}

.compact-info p {
  margin: 0.25rem 0 0 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.compact-tags {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.compact-tag {
  padding: 0.25rem 0.5rem;
  background: #e5e7eb;
  color: #374151;
  border-radius: 12px;
  font-size: 0.75rem;
}

.compact-status {
  display: flex;
  align-items: center;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  background: #f3f4f6;
  color: #6b7280;
}

.status-badge.active {
  background: #10b981;
  color: white;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.pagination-btn {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.pagination-btn:hover:not(:disabled) {
  border-color: #f97316;
  color: #f97316;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.page-number {
  padding: 0.75rem;
  min-width: 40px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.page-number:hover {
  border-color: #f97316;
  color: #f97316;
}

.page-number.active {
  background: #f97316;
  color: white;
  border-color: #f97316;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

/* Create Student Form Styles */
.create-student-form {
  max-height: 70vh;
  overflow-y: auto;
  padding-right: 1rem;
}

.form-section {
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.form-section:last-child {
  border-bottom: none;
}

.form-section h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.form-section h3::before {
  content: '';
  width: 4px;
  height: 20px;
  background: #f97316;
  border-radius: 2px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.form-group input,
.form-group select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: white;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-group input:invalid {
  border-color: #ef4444;
}

.form-group input::placeholder {
  color: #9ca3af;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  color: #1f2937;
}

.modal-close {
  padding: 0.5rem;
  background: transparent;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  color: #6b7280;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.modal-body p {
  margin: 0 0 1rem 0;
  color: #4b5563;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .student-profiling-system {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .system-title {
    font-size: 2rem;
  }
  
  .stats-container {
    grid-template-columns: 1fr;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .display-controls {
    flex-direction: column;
    gap: 1rem;
  }
  
  .cards-view {
    grid-template-columns: 1fr;
  }
  
  .compact-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .compact-status {
    width: 100%;
    justify-content: space-between;
  }
  
  .pagination {
    flex-direction: column;
    gap: 1rem;
  }
  
  .quick-filter-buttons {
    flex-direction: column;
  }
  
  .quick-filter-btn {
    width: 100%;
    text-align: center;
  }
}
</style>