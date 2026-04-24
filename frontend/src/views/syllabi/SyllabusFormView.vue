<template>
  <div class="syllabus-form-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/syllabi" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M15 19l-7-7 7-7"/>
            </svg>
            Syllabi
          </router-link>
          <h1>{{ isEdit ? 'Edit Syllabus' : 'Create Syllabus' }}</h1>
        </div>
        <div class="header-actions">
          <span class="status-badge">{{ isEdit ? 'Edit Mode' : 'Create Mode' }}</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="syllabusStore.loading" class="loading-state">
      <div class="spinner"></div>
      <p>{{ isEdit ? 'Loading syllabus...' : 'Creating syllabus...' }}</p>
    </div>

    <!-- Form -->
    <div v-else class="form-container">
      <form @submit.prevent="handleSubmit" class="syllabus-form">
        <!-- Course & Professor -->
        <div class="form-section">
          <div class="section-header">
            <h3>Course & Instructor</h3>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="courseId" class="form-label">Course *</label>
              <select id="courseId" v-model="form.courseId" class="form-input" required>
                <option value="">Select Course</option>
                <option v-for="course in courses" :key="course.id" :value="course.id">
                  {{ course.courseCode }} - {{ course.courseName }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="professorId" class="form-label">Instructor *</label>
              <select id="professorId" v-model="form.professorId" class="form-input" required>
                <option value="">Select Instructor</option>
                <option v-for="prof in professors" :key="prof.id" :value="prof.id">
                  {{ prof.firstName || prof.first_name }} {{ prof.lastName || prof.last_name }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="academicYear" class="form-label">Academic Year *</label>
              <select id="academicYear" v-model="form.academicYear" class="form-input" required>
                <option value="">Select Academic Year</option>
                <option value="2024-2025">2024-2025</option>
                <option value="2023-2024">2023-2024</option>
                <option value="2025-2026">2025-2026</option>
              </select>
            </div>
            <div class="form-group">
              <label for="semester" class="form-label">Semester *</label>
              <select id="semester" v-model="form.semester" class="form-input" required>
                <option value="">Select Semester</option>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
                <option value="Summer">Summer</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Course Description -->
        <div class="form-section">
          <div class="section-header">
            <h3>Course Description</h3>
          </div>
          <div class="form-group full-width">
            <label for="courseDescription" class="form-label">Course Description *</label>
            <textarea
              id="courseDescription"
              v-model="form.courseDescription"
              class="form-input"
              rows="4"
              placeholder="Enter a detailed course description..."
              required
            ></textarea>
          </div>
        </div>

        <!-- Learning Objectives -->
        <div class="form-section">
          <div class="section-header">
            <h3>Learning Objectives</h3>
          </div>
          <div class="form-group full-width">
            <label for="learningObjectives" class="form-label">Learning Objectives *</label>
            <textarea
              id="learningObjectives"
              v-model="form.learningObjectives"
              class="form-input"
              rows="5"
              placeholder="Enter learning objectives (one per line)&#10;• Objective 1&#10;• Objective 2&#10;• Objective 3"
              required
            ></textarea>
          </div>
        </div>

        <!-- Topics Outline -->
        <div class="form-section">
          <div class="section-header">
            <h3>Topics Outline</h3>
          </div>
          <div class="form-group full-width">
            <label for="topicsOutline" class="form-label">Topics / Weekly Outline *</label>
            <textarea
              id="topicsOutline"
              v-model="form.topicsOutline"
              class="form-input"
              rows="8"
              placeholder="Enter weekly topics outline&#10;Week 1: Introduction&#10;Week 2: Basic Concepts&#10;Week 3: Advanced Topics&#10;..."
              required
            ></textarea>
          </div>
        </div>

        <!-- Assessment & Grading -->
        <div class="form-section">
          <div class="section-header">
            <h3>Assessment & Grading</h3>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="assessmentMethods" class="form-label">Assessment Methods *</label>
              <textarea
                id="assessmentMethods"
                v-model="form.assessmentMethods"
                class="form-input"
                rows="5"
                placeholder="Enter assessment methods&#10;• Quizzes: 20%&#10;• Midterm: 30%&#10;• Final: 40%&#10;• Participation: 10%"
                required
              ></textarea>
            </div>
            <div class="form-group">
              <label for="gradingPolicy" class="form-label">Grading Policy *</label>
              <textarea
                id="gradingPolicy"
                v-model="form.gradingPolicy"
                class="form-input"
                rows="5"
                placeholder="Enter grading policy&#10;A: 90-100%&#10;B: 80-89%&#10;C: 70-79%&#10;D: 60-69%&#10;F: Below 60%"
                required
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Materials & Policies -->
        <div class="form-section">
          <div class="section-header">
            <h3>Materials & Policies</h3>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="requiredMaterials" class="form-label">Required Materials *</label>
              <textarea
                id="requiredMaterials"
                v-model="form.requiredMaterials"
                class="form-input"
                rows="4"
                placeholder="Enter required materials&#10;• Textbook: Main Course Text&#10;• Laptop with required software&#10;• Notebook and writing materials"
                required
              ></textarea>
            </div>
            <div class="form-group">
              <label for="classPolicies" class="form-label">Class Policies *</label>
              <textarea
                id="classPolicies"
                v-model="form.classPolicies"
                class="form-input"
                rows="4"
                placeholder="Enter class policies&#10;• Attendance is mandatory&#10;• Late submissions penalized&#10;• Academic integrity enforced"
                required
              ></textarea>
            </div>
          </div>
        </div>

        <!-- File Upload -->
        <div class="form-section" v-if="isEdit">
          <div class="section-header">
            <h3>Syllabus File</h3>
          </div>
          <div class="form-group full-width">
            <label for="syllabusFile" class="form-label">Upload Syllabus File (PDF, DOC, DOCX - Max 10MB)</label>
            <input
              id="syllabusFile"
              type="file"
              accept=".pdf,.doc,.docx"
              class="form-input"
              @change="handleFileUpload"
            />
            <p v-if="currentFilePath" class="file-info">Current file: {{ currentFilePath }}</p>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <router-link to="/syllabi" class="btn btn-outline">Cancel</router-link>
          <button type="submit" class="btn btn-primary" :disabled="syllabusStore.loading">
            {{ isEdit ? 'Update Syllabus' : 'Create Syllabus' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSyllabusStore } from '@/stores/syllabus'
import { useCourseStore } from '@/stores/course'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const syllabusStore = useSyllabusStore()
const courseStore = useCourseStore()

const isEdit = computed(() => !!route.params.id)

const courses = computed(() => courseStore.courses)
const professors = ref<any[]>([])
const currentFilePath = ref('')

const form = ref({
  courseId: Number(route.query.course) || 0,
  professorId: 0,
  academicYear: '',
  semester: '',
  courseDescription: '',
  learningObjectives: '',
  topicsOutline: '',
  assessmentMethods: '',
  gradingPolicy: '',
  requiredMaterials: '',
  classPolicies: '',
})

const uploadFile = ref<File | null>(null)

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    uploadFile.value = target.files[0]
  }
}

const fetchProfessors = async () => {
  try {
    const response = await api.get('/professors')
    professors.value = response.data
  } catch (error) {
    console.error('Error fetching professors:', error)
  }
}

const fetchSyllabus = async () => {
  if (!isEdit.value) return
  
  const syllabus = await syllabusStore.fetchSyllabus(Number(route.params.id))
  if (syllabus) {
    form.value = {
      courseId: syllabus.courseId,
      professorId: syllabus.professorId,
      academicYear: syllabus.academicYear,
      semester: syllabus.semester,
      courseDescription: syllabus.courseDescription,
      learningObjectives: syllabus.learningObjectives,
      topicsOutline: syllabus.topicsOutline,
      assessmentMethods: syllabus.assessmentMethods,
      gradingPolicy: syllabus.gradingPolicy,
      requiredMaterials: syllabus.requiredMaterials,
      classPolicies: syllabus.classPolicies,
    }
    currentFilePath.value = syllabus.filePath || ''
  }
}

const handleSubmit = async () => {
  try {
    const success = isEdit.value
      ? await syllabusStore.updateSyllabus(Number(route.params.id), form.value)
      : await syllabusStore.createSyllabus(form.value)
    
    if (success) {
      // Upload file if one was selected
      if (isEdit.value && uploadFile.value && success.id) {
        await syllabusStore.uploadFile(success.id, uploadFile.value)
      }
      router.push('/syllabi')
    } else {
      alert('Failed to save syllabus. Please try again.')
    }
  } catch (error) {
    console.error('Form submission error:', error)
    alert(`Failed to save syllabus: ${error instanceof Error ? error.message : 'Unknown error'}`)
  }
}

onMounted(() => {
  courseStore.fetchCourses()
  fetchProfessors()
  if (isEdit.value) {
    fetchSyllabus()
  }
})
</script>

<style scoped>
.syllabus-form-view {
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
  align-items: flex-start;
  gap: 1rem;
}

.header-left h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-top: 0.5rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.2s;
}

.back-link:hover {
  color: #3b82f6;
}

.icon-small {
  width: 1rem;
  height: 1rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  background: #dbeafe;
  color: #1e40af;
}

.form-container {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.syllabus-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1.5rem;
}

.form-section:last-of-type {
  border-bottom: none;
  padding-bottom: 0;
}

.section-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
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
  font-family: inherit;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
}

.file-info {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding-top: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.25rem;
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

.btn-primary:disabled {
  background: #93c5fd;
  cursor: not-allowed;
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

.loading-state {
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
</style>
