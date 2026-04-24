<template>
  <div class="syllabus-detail-view">
    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading syllabus...</p>
    </div>

    <!-- Content -->
    <div v-else-if="syllabus">
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
            <h1>{{ syllabus.course?.courseName || 'Syllabus Details' }}</h1>
            <p class="subtitle">{{ syllabus.course?.courseCode }} - {{ syllabus.semester }} {{ syllabus.academicYear }}</p>
          </div>
          <div class="header-actions">
            <span :class="['status-badge', syllabus.approved ? 'approved' : 'pending']">
              {{ syllabus.approved ? 'Approved' : 'Pending Approval' }}
            </span>
            <router-link :to="`/syllabi/${syllabus.id}/edit`" class="btn btn-primary">
              Edit Syllabus
            </router-link>
          </div>
        </div>
      </div>

      <!-- Syllabus Content -->
      <div class="syllabus-content">
        <!-- Course Info Card -->
        <div class="info-card">
          <div class="card-header">
            <h3>Course Information</h3>
          </div>
          <div class="card-body">
            <div class="info-grid">
              <div class="info-item">
                <label>Course</label>
                <p>{{ syllabus.course?.courseCode }} - {{ syllabus.course?.courseName }}</p>
              </div>
              <div class="info-item">
                <label>Instructor</label>
                <p>{{ syllabus.professor?.firstName }} {{ syllabus.professor?.lastName }}</p>
              </div>
              <div class="info-item">
                <label>Academic Year</label>
                <p>{{ syllabus.academicYear }}</p>
              </div>
              <div class="info-item">
                <label>Semester</label>
                <p>{{ syllabus.semester }}</p>
              </div>
              <div class="info-item" v-if="syllabus.approved">
                <label>Approved By</label>
                <p>{{ syllabus.approvedByUser?.name || 'System' }}</p>
              </div>
              <div class="info-item" v-if="syllabus.approvedAt">
                <label>Approved At</label>
                <p>{{ formatDate(syllabus.approvedAt) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Description -->
        <div class="info-card">
          <div class="card-header">
            <h3>Course Description</h3>
          </div>
          <div class="card-body">
            <div class="text-content" v-html="formatText(syllabus.courseDescription)"></div>
          </div>
        </div>

        <!-- Learning Objectives -->
        <div class="info-card">
          <div class="card-header">
            <h3>Learning Objectives</h3>
          </div>
          <div class="card-body">
            <div class="text-content" v-html="formatText(syllabus.learningObjectives)"></div>
          </div>
        </div>

        <!-- Topics Outline -->
        <div class="info-card">
          <div class="card-header">
            <h3>Topics Outline</h3>
          </div>
          <div class="card-body">
            <div class="text-content" v-html="formatText(syllabus.topicsOutline)"></div>
          </div>
        </div>

        <!-- Assessment & Grading -->
        <div class="info-card">
          <div class="card-header">
            <h3>Assessment & Grading</h3>
          </div>
          <div class="card-body">
            <div class="two-column">
              <div>
                <h4 class="section-title">Assessment Methods</h4>
                <div class="text-content" v-html="formatText(syllabus.assessmentMethods)"></div>
              </div>
              <div>
                <h4 class="section-title">Grading Policy</h4>
                <div class="text-content" v-html="formatText(syllabus.gradingPolicy)"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Materials & Policies -->
        <div class="info-card">
          <div class="card-header">
            <h3>Materials & Policies</h3>
          </div>
          <div class="card-body">
            <div class="two-column">
              <div>
                <h4 class="section-title">Required Materials</h4>
                <div class="text-content" v-html="formatText(syllabus.requiredMaterials)"></div>
              </div>
              <div>
                <h4 class="section-title">Class Policies</h4>
                <div class="text-content" v-html="formatText(syllabus.classPolicies)"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- File Download -->
        <div class="info-card" v-if="syllabus.filePath">
          <div class="card-header">
            <h3>Syllabus File</h3>
          </div>
          <div class="card-body">
            <button @click="downloadFile" class="btn btn-primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Download Syllabus File
            </button>
          </div>
        </div>

        <!-- Approval Actions -->
        <div class="info-card" v-if="!syllabus.approved">
          <div class="card-header">
            <h3>Approval Actions</h3>
          </div>
          <div class="card-body">
            <div class="approval-actions">
              <button @click="approveSyllabus" class="btn btn-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 6L9 17l-5-5"/>
                </svg>
                Approve Syllabus
              </button>
              <button @click="rejectSyllabus" class="btn btn-danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="15" y1="9" x2="9" y2="15"/>
                  <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                Reject Syllabus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Syllabus Not Found</h3>
      <p>The requested syllabus could not be loaded.</p>
      <router-link to="/syllabi" class="btn btn-primary">Back to Syllabi</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useSyllabusStore } from '@/stores/syllabus'
import api from '@/services/api'
import type { Syllabus } from '@/types/syllabus'

const route = useRoute()
const syllabusStore = useSyllabusStore()

const syllabus = ref<Syllabus | null>(null)
const loading = ref(true)

const fetchSyllabus = async () => {
  loading.value = true
  const data = await syllabusStore.fetchSyllabus(Number(route.params.id))
  syllabus.value = data
  loading.value = false
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatText = (text: string | undefined) => {
  if (!text) return ''
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\n/g, '<br>')
    .replace(/•/g, '&bull;')
}

const downloadFile = async () => {
  if (!syllabus.value) return
  try {
    const response = await api.get(`/syllabi/${syllabus.value.id}/download-file`)
    if (response.data.download_url) {
      window.open(response.data.download_url, '_blank')
    }
  } catch (error) {
    console.error('Error downloading file:', error)
    alert('Failed to download file')
  }
}

const approveSyllabus = async () => {
  if (!syllabus.value) return
  if (confirm('Are you sure you want to approve this syllabus?')) {
    const success = await syllabusStore.approveSyllabus(syllabus.value.id)
    if (success) {
      syllabus.value = { ...syllabus.value, approved: true, approvedAt: new Date().toISOString() }
    }
  }
}

const rejectSyllabus = async () => {
  if (!syllabus.value) return
  if (confirm('Are you sure you want to reject this syllabus?')) {
    const success = await syllabusStore.rejectSyllabus(syllabus.value.id)
    if (success) {
      syllabus.value = { ...syllabus.value, approved: false }
    }
  }
}

onMounted(() => {
  fetchSyllabus()
})
</script>

<style scoped>
.syllabus-detail-view {
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

.subtitle {
  color: #6b7280;
  font-size: 1rem;
  margin-top: 0.25rem;
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

.back-link:hover { color: #3b82f6; }

.icon-small { width: 1rem; height: 1rem; }

.header-actions {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.status-badge {
  padding: 0.375rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.status-badge.approved { background: #d1fae5; color: #065f46; }
.status-badge.pending { background: #fed7aa; color: #92400e; }

.syllabus-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-card {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.card-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.card-body {
  padding: 1.5rem;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.25rem;
}

.info-item label {
  display: block;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.25rem;
}

.info-item p {
  font-size: 0.9375rem;
  color: #1f2937;
  font-weight: 500;
}

.text-content {
  font-size: 0.9375rem;
  line-height: 1.75;
  color: #374151;
}

.two-column {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.section-title {
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e5e7eb;
}

.approval-actions {
  display: flex;
  gap: 1rem;
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

.btn svg { width: 1rem; height: 1rem; }

.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-success { background: #10b981; color: white; }
.btn-success:hover { background: #059669; }
.btn-danger { background: #ef4444; color: white; }
.btn-danger:hover { background: #dc2626; }

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.spinner {
  width: 3rem; height: 3rem;
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

.error-state svg { width: 4rem; height: 4rem; color: #6b7280; margin-bottom: 1rem; }
.error-state h3 { font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; }
.error-state p { color: #6b7280; margin-bottom: 1.5rem; }

@media (max-width: 768px) {
  .two-column { grid-template-columns: 1fr; }
  .header-content { flex-direction: column; }
}
</style>
