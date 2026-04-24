<template>
  <div class="syllabus-list-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Syllabi Management</h1>
          <p>Manage course syllabi and approval workflow</p>
        </div>
        <div class="header-actions">
          <button @click="generateSampleData" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 20V10"/>
              <path d="M18 20V4"/>
              <path d="M6 20v-4"/>
            </svg>
            Generate Sample Data
          </button>
          <router-link to="/syllabi/create" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Create Syllabus
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="syllabusStore.loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading syllabi...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="syllabusStore.error" class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Error Loading Syllabi</h3>
      <p>{{ syllabusStore.error }}</p>
      <button @click="syllabusStore.fetchSyllabi" class="btn btn-primary">Retry</button>
    </div>

    <!-- Content -->
    <div v-else class="content-section">
      <!-- Filters -->
      <div class="filters-section">
        <div class="filters-header">
          <h3>Filters</h3>
          <button @click="syllabusStore.resetFilter" class="btn btn-outline btn-small">
            Clear Filters
          </button>
        </div>
        <div class="filters-grid">
          <div class="form-group">
            <label for="search" class="form-label">Search</label>
            <input
              id="search"
              v-model="syllabusStore.filter.search"
              type="text"
              class="form-input"
              placeholder="Search by course, professor, or description..."
            />
          </div>
          <div class="form-group">
            <label for="courseFilter" class="form-label">Course</label>
            <select id="courseFilter" v-model="syllabusStore.filter.courseId" class="form-input">
              <option value="">All Courses</option>
              <option v-for="course in availableCourses" :key="course.id" :value="course.id">
                {{ course.courseCode }} - {{ course.courseName }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="academicYearFilter" class="form-label">Academic Year</label>
            <select id="academicYearFilter" v-model="syllabusStore.filter.academicYear" class="form-input">
              <option value="">All Years</option>
              <option v-for="year in syllabusStore.availableAcademicYears" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="semesterFilter" class="form-label">Semester</label>
            <select id="semesterFilter" v-model="syllabusStore.filter.semester" class="form-input">
              <option value="">All Semesters</option>
              <option v-for="semester in syllabusStore.availableSemesters" :key="semester" :value="semester">
                {{ semester }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="statusFilter" class="form-label">Status</label>
            <select id="statusFilter" v-model="syllabusStore.filter.approved" class="form-input">
              <option :value="undefined">All Status</option>
              <option :value="true">Approved</option>
              <option :value="false">Pending</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="stats-section">
        <div class="stat-card">
          <div class="stat-icon approved">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <div class="stat-content">
            <h4>{{ syllabusStore.approvedSyllabi.length }}</h4>
            <p>Approved</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon pending">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div class="stat-content">
            <h4>{{ syllabusStore.pendingSyllabi.length }}</h4>
            <p>Pending</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon total">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
          </div>
          <div class="stat-content">
            <h4>{{ syllabusStore.filteredSyllabi.length }}</h4>
            <p>Total</p>
          </div>
        </div>
      </div>

      <!-- Syllabi List -->
      <div class="syllabi-section">
        <div class="section-header">
          <h3>Syllabi ({{ syllabusStore.filteredSyllabi.length }})</h3>
          <div class="section-actions">
            <button @click="syllabusStore.fetchSyllabi" class="btn btn-outline btn-small">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 4v6h-6"/>
                <path d="M1 20v-6h6"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              Refresh
            </button>
          </div>
        </div>

        <div v-if="syllabusStore.filteredSyllabi.length === 0" class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
          <h3>No Syllabi Found</h3>
          <p>No syllabi match your current filters.</p>
          <button @click="syllabusStore.resetFilter" class="btn btn-primary">Clear Filters</button>
        </div>

        <div v-else class="syllabi-grid">
          <div
            v-for="syllabus in syllabusStore.filteredSyllabi"
            :key="syllabus.id"
            class="syllabus-card"
          >
            <div class="syllabus-header">
              <div class="syllabus-info">
                <h4>{{ syllabus.course?.courseName }}</h4>
                <p class="course-code">{{ syllabus.course?.courseCode }}</p>
              </div>
              <div class="syllabus-status">
                <span :class="['status-badge', syllabus.approved ? 'approved' : 'pending']">
                  {{ syllabus.approved ? 'Approved' : 'Pending' }}
                </span>
              </div>
            </div>

            <div class="syllabus-details">
              <div class="detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>{{ syllabus.professor?.firstName }} {{ syllabus.professor?.lastName }}</span>
              </div>
              <div class="detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>{{ syllabus.semester }} {{ syllabus.academicYear }}</span>
              </div>
            </div>

            <div class="syllabus-description">
              <p>{{ syllabus.courseDescription.substring(0, 150) }}...</p>
            </div>

            <div class="syllabus-actions">
              <router-link :to="`/syllabi/${syllabus.id}`" class="btn btn-small btn-outline">
                View Details
              </router-link>
              <router-link :to="`/syllabi/${syllabus.id}/edit`" class="btn btn-small btn-primary">
                Edit
              </router-link>
              <button
                v-if="!syllabus.approved"
                @click="approveSyllabus(syllabus)"
                class="btn btn-small btn-success"
              >
                Approve
              </button>
              <button
                @click="deleteSyllabus(syllabus)"
                class="btn btn-small btn-danger"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useSyllabusStore } from '@/stores/syllabus'
import { useCourseStore } from '@/stores/course'
import type { Syllabus } from '@/types/syllabus'

const syllabusStore = useSyllabusStore()
const courseStore = useCourseStore()

const availableCourses = computed(() => courseStore.courses)

const approveSyllabus = async (syllabus: Syllabus) => {
  if (confirm(`Are you sure you want to approve the syllabus for ${syllabus.course?.courseName}?`)) {
    await syllabusStore.approveSyllabus(syllabus.id)
  }
}

const deleteSyllabus = async (syllabus: Syllabus) => {
  if (confirm(`Are you sure you want to delete the syllabus for ${syllabus.course?.courseName}?`)) {
    await syllabusStore.deleteSyllabus(syllabus.id)
  }
}

const generateSampleData = async () => {
  if (confirm('This will generate sample syllabus data. Continue?')) {
    await syllabusStore.generateSampleData()
    await courseStore.fetchCourses()
  }
}

onMounted(() => {
  syllabusStore.fetchSyllabi()
  courseStore.fetchCourses()
})
</script>

<style scoped>
.syllabus-list-view {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.header-left h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.header-left p {
  color: #6b7280;
  font-size: 1rem;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.filters-section {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.filters-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-icon.approved {
  background: #10b981;
}

.stat-icon.pending {
  background: #f59e0b;
}

.stat-icon.total {
  background: #3b82f6;
}

.stat-content h4 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.stat-content p {
  color: #6b7280;
  font-size: 0.875rem;
}

.syllabi-section {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.syllabi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.syllabus-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
}

.syllabus-card:hover {
  border-color: #d1d5db;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.syllabus-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.syllabus-info h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.course-code {
  color: #6b7280;
  font-size: 0.875rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge.approved {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.pending {
  background: #fed7aa;
  color: #92400e;
}

.syllabus-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.detail-item svg {
  width: 1rem;
  height: 1rem;
}

.syllabus-description {
  margin-bottom: 1rem;
}

.syllabus-description p {
  color: #4b5563;
  font-size: 0.875rem;
  line-height: 1.5;
}

.syllabus-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

/* Common button styles */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.btn-outline {
  background: transparent;
  color: #6b7280;
  border: 1px solid #d1d5db;
}

.btn-outline:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-small {
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
}

/* Form styles */
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.form-input {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Loading and error states */
.loading-state, .error-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state svg, .empty-state svg {
  width: 4rem;
  height: 4rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.error-state h3, .empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.error-state p, .empty-state p {
  color: #6b7280;
  margin-bottom: 1.5rem;
}
</style>
