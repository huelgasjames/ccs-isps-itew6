<template>
  <div class="course-form-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/courses" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M15 19l-7-7 7-7"/>
            </svg>
            Courses
          </router-link>
          <h1>{{ isEdit ? 'Edit Course' : 'Create Course' }}</h1>
        </div>
        <div class="header-actions">
          <span class="status-badge">{{ isEdit ? 'Edit Mode' : 'Create Mode' }}</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="courseStore.loading" class="loading-state">
      <div class="spinner"></div>
      <p>{{ isEdit ? 'Updating course...' : 'Creating course...' }}</p>
    </div>

    <!-- Form -->
    <div v-else class="form-container">
      <form @submit.prevent="handleSubmit" class="course-form">
        <!-- Basic Information -->
        <div class="form-section">
          <div class="section-header">
            <h3>Basic Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6 4l8 8v8H6z"/>
            </svg>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="courseCode" class="form-label">Course Code</label>
              <input
                id="courseCode"
                v-model="form.courseCode"
                type="text"
                class="form-input"
                placeholder="e.g., CCS 101"
                required
              />
            </div>
            <div class="form-group">
              <label for="courseName" class="form-label">Course Name</label>
              <input
                id="courseName"
                v-model="form.courseName"
                type="text"
                class="form-input"
                placeholder="Enter course name"
                required
              />
            </div>
            <div class="form-group">
              <label for="credits" class="form-label">Credits</label>
              <input
                id="credits"
                v-model.number="form.credits"
                type="number"
                min="1"
                max="12"
                class="form-input"
                required
              />
            </div>
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
          </div>
        </div>

        <!-- Schedule Information -->
        <div class="form-section">
          <div class="section-header">
            <h3>Schedule Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="semester" class="form-label">Semester</label>
              <select id="semester" v-model="form.semester" class="form-input" required>
                <option value="">Select Semester</option>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
                <option value="Summer">Summer</option>
              </select>
            </div>
            <div class="form-group">
              <label for="academicYear" class="form-label">Academic Year</label>
              <input
                id="academicYear"
                v-model="form.academicYear"
                type="text"
                class="form-input"
                placeholder="e.g., 2024-2025"
                required
              />
            </div>
            <div class="form-group">
              <label for="schedule" class="form-label">Schedule</label>
              <input
                id="schedule"
                v-model="form.schedule"
                type="text"
                class="form-input"
                placeholder="e.g., MWF 9:00-10:30 AM"
              />
            </div>
            <div class="form-group">
              <label for="room" class="form-label">Room</label>
              <input
                id="room"
                v-model="form.room"
                type="text"
                class="form-input"
                placeholder="e.g., Room 101"
              />
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
          <div class="section-header">
            <h3>Additional Information</h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14,2 14,8 20,8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10,9 9,9 8,9"/>
            </svg>
          </div>
          <div class="form-grid">
            <div class="form-group full-width">
              <label for="description" class="form-label">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                class="form-input"
                rows="4"
                placeholder="Enter course description"
              ></textarea>
            </div>
            <div class="form-group">
              <label for="instructor" class="form-label">Instructor</label>
              <input
                id="instructor"
                v-model="form.instructor"
                type="text"
                class="form-input"
                placeholder="Enter instructor name"
              />
            </div>
            <div class="form-group">
              <label for="maxStudents" class="form-label">Max Students (Max: 40)</label>
              <input
                id="maxStudents"
                v-model.number="form.maxStudents"
                type="number"
                min="1"
                max="40"
                class="form-input"
                required
              />
            </div>
            <div class="form-group">
              <label for="status" class="form-label">Status</label>
              <select id="status" v-model="form.status" class="form-input" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <router-link to="/courses" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
            Cancel
          </router-link>
          <button type="submit" :disabled="courseStore.loading" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <polyline points="20 6 9 17 4 12"/>
              <path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/>
            </svg>
            {{ isEdit ? 'Update Course' : 'Create Course' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCourseStore } from '@/stores/course'
import type { CourseFormData } from '@/types/course'

const route = useRoute()
const router = useRouter()
const courseStore = useCourseStore()

const isEdit = computed(() => !!route.params.id)

const form = ref<CourseFormData>({
  courseCode: '',
  courseName: '',
  description: '',
  credits: 3,
  department: '',
  semester: '',
  academicYear: '',
  instructor: '',
  schedule: '',
  room: '',
  maxStudents: 300
  ,
  status: 'active',
  prerequisites: []
})

const handleSubmit = async () => {
  try {
    const success = isEdit.value 
      ? await courseStore.updateCourse(Number(route.params.id), form.value)
      : await courseStore.createCourse(form.value)
    
    if (success) {
      router.push('/courses')
    } else {
      alert('Failed to save course. Please try again.')
    }
  } catch (error) {
    console.error('Form submission error:', error)
    alert(`Failed to save course: ${error instanceof Error ? error.message : 'Unknown error'}`)
  }
}

const fetchCourse = async () => {
  const courseData = await courseStore.fetchCourse(Number(route.params.id))
  if (courseData) {
    form.value = {
      courseCode: courseData.courseCode,
      courseName: courseData.courseName,
      description: courseData.description,
      credits: courseData.credits,
      department: courseData.department,
      semester: courseData.semester,
      academicYear: courseData.academicYear,
      instructor: courseData.instructor || '',
      schedule: courseData.schedule || '',
      room: courseData.room || '',
      maxStudents: courseData.maxStudents,
      status: courseData.status,
      prerequisites: courseData.prerequisites || []
    }
  }
}

onMounted(() => {
  if (isEdit.value) {
    fetchCourse()
  }
})
</script>

<style scoped>
.course-form-view {
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

.course-form {
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

.form-group.full-width {
  grid-column: 1 / -1;
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
  .course-form-view {
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
  .course-form {
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
