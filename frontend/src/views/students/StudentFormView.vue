<template>
  <div class="page-root">
    <!-- Background layers -->
    <div class="bg-grid"></div>
    <div class="bg-scanlines"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Topbar -->
    <nav class="topbar">
      <div class="topbar-inner">
        <router-link to="/dashboard" class="topbar-back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          BACK
        </router-link>
        <div class="topbar-title">
          <div class="title-sys">SYS://CCS.EDU</div>
          <div class="title-name">{{ isEdit ? 'EDIT STUDENT' : 'STUDENT REGISTRATION' }}</div>
        </div>
        <div class="topbar-actions">
          <button @click="handleLogout" class="action-btn danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            LOGOUT
          </button>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb-bar">
        <span class="bc-prompt">$</span>
        <router-link to="/dashboard" class="bc-item">HOME</router-link>
        <span class="bc-sep">›</span>
        <router-link to="/students" class="bc-item">STUDENTS</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">{{ isEdit ? 'EDIT MODE' : 'REGISTRATION' }}</span>
      </div>

      <!-- Form Panel -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ isEdit ? 'STUDENT DATA MODIFICATION' : 'NEW STUDENT PROTOCOL' }}
          </div>
          <span class="status-badge">{{ isEdit ? 'EDIT' : 'CREATE' }}</span>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <span>{{ isEdit ? 'UPDATING STUDENT RECORDS...' : 'INITIALIZING STUDENT PROFILE...' }}</span>
        </div>

        <!-- Form -->
        <form v-else @submit.prevent="handleSubmit" class="form-grid">
          <!-- Personal Information -->
          <div class="form-section">
            <div class="section-header">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              PERSONAL IDENTITY MATRIX
            </div>
            <div class="fields-grid">
              <div class="field-group">
                <label class="field-label">FIRST NAME</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                  <input v-model="form.first_name" type="text" class="field-input" placeholder="Enter first name" required />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">LAST NAME</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                  <input v-model="form.last_name" type="text" class="field-input" placeholder="Enter last name" required />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">EMAIL ADDRESS</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  <input v-model="form.email" type="email" class="field-input" placeholder="student@university.edu" required />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">AGE</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0m-5 0v4"/></svg>
                  <input v-model="form.age" type="number" class="field-input" placeholder="16-100" min="16" max="100" required />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">ACCESS PASSWORD <span v-if="!isEdit" class="required">*</span></label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0"/></svg>
                  <input v-model="form.password" type="password" class="field-input" :placeholder="isEdit ? 'Leave blank to keep current' : 'Create new password'" :required="!isEdit" minlength="6" />
                </div>
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="form-section">
            <div class="section-header">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              ACADEMIC PROTOCOLS
            </div>
            <div class="fields-grid">
              <div class="field-group">
                <label class="field-label">PROGRAM</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6 4l8 8v8H6z"/></svg>
                  <select v-model="form.program" class="field-input field-select" required>
                    <option value="">SELECT PROGRAM</option>
                    <option value="BSIT">BSIT - Information Technology</option>
                    <option value="BSCS">BSCS - Computer Science</option>
                  </select>
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">YEAR LEVEL</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10 5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                  <select v-model="form.year_level" class="field-input field-select">
                    <option value="">SELECT YEAR</option>
                    <option value="1st">1ST YEAR</option>
                    <option value="2nd">2ND YEAR</option>
                    <option value="3rd">3RD YEAR</option>
                    <option value="4th">4TH YEAR</option>
                  </select>
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">SECTION</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h2l7.17 7.17"/></svg>
                  <input v-model="form.section" type="text" class="field-input" placeholder="e.g., 1A, 2B, 3C" />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">ACADEMIC STATUS</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 13.89 7 13.89 5.79 13.89 5.79 13.89 3.7 13.89 3.7 13.89 1.61 13.89 1.61 13.89"/></svg>
                  <select v-model="form.academic_status" class="field-input field-select">
                    <option value="Regular">REGULAR</option>
                    <option value="Probation">PROBATION</option>
                    <option value="Suspended">SUSPENDED</option>
                    <option value="Graduated">GRADUATED</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="form-actions">
            <router-link to="/students" class="action-btn secondary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
              CANCEL
            </router-link>
            <button type="submit" :disabled="loading" class="action-btn primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/><path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/></svg>
              {{ isEdit ? 'UPDATE RECORD' : 'CREATE PROFILE' }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const user = computed(() => authStore.user)
const isEdit = ref(false)
const loading = ref(false)

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  age: '',
  password: '',
  program: '',
  year_level: '',
  section: '',
  academic_status: 'Regular'
})

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await api.put(`/students/${route.params.id}`, form.value)
    } else {
      await api.post('/students', form.value)
    }
    router.push('/students')
  } catch (error) {
    console.error('Error saving student:', error)
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

const fetchStudent = async () => {
  try {
    const response = await api.get(`/students/${route.params.id}`)
    form.value = { ...response.data, password: '' }
  } catch (error) {
    console.error('Error fetching student:', error)
  }
}

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true
    fetchStudent()
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&display=swap');
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.page-root {
  min-height: 100vh;
  background: #ffffff;
  font-family: 'Rajdhani', sans-serif;
  color: #333333;
  position: relative;
}

.bg-grid {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background-image: linear-gradient(rgba(255,140,0,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,140,0,0.03) 1px, transparent 1px);
  background-size: 40px 40px;
}
.bg-scanlines {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,140,0,0.02) 2px, rgba(255,140,0,0.02) 4px);
}
.orb { position: fixed; border-radius: 50%; filter: blur(90px); z-index: 0; pointer-events: none; }
.orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,140,0,0.05), transparent 70%); top: -150px; left: -150px; }
.orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,165,0,0.04), transparent 70%); bottom: 0; right: 10%; }

/* Topbar */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(255,255,255,0.95);
  border-bottom: 1px solid rgba(255,140,0,0.15);
  backdrop-filter: blur(12px);
}
.topbar-inner { display: flex; align-items: center; gap: 1rem; padding: 0 1.5rem; height: 60px; }
.topbar-back {
  display: flex; align-items: center; gap: 6px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.7); text-decoration: none; letter-spacing: 0.12em;
  padding: 6px 12px; border-radius: 4px; border: 1px solid rgba(255,140,0,0.2);
  transition: all 0.2s; flex-shrink: 0;
}
.topbar-back svg { width: 14px; height: 14px; }
.topbar-back:hover { color: #ff8c00; border-color: rgba(255,140,0,0.4); background: rgba(255,140,0,0.08); }
.topbar-title { flex: 1; }
.title-sys { font-family: 'Share Tech Mono', monospace; font-size: 0.58rem; color: rgba(255,140,0,0.5); letter-spacing: 0.12em; }
.title-name { font-size: 1rem; font-weight: 700; color: #333333; letter-spacing: 0.14em; }
.topbar-user { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.user-avatar {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, rgba(255,140,0,0.25), rgba(255,100,0,0.15));
  border: 1px solid rgba(255,140,0,0.3);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 0.85rem; color: #ff8c00;
}
.user-info { display: flex; flex-direction: column; }
.user-name { font-size: 0.82rem; font-weight: 700; color: #333333; letter-spacing: 0.04em; }
.user-role { font-family: 'Share Tech Mono', monospace; font-size: 0.56rem; color: rgba(255,140,0,0.5); letter-spacing: 0.1em; }

/* Main */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 900px; margin: 0 auto; }

.breadcrumb-bar {
  display: flex; align-items: center; gap: 8px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem; color: rgba(255,140,0,0.6);
  letter-spacing: 0.12em; margin-bottom: 1.5rem;
}
.bc-prompt { color: #ff8c00; }
.bc-item { color: rgba(255,140,0,0.7); text-decoration: none; }
.bc-item:hover { color: #ff8c00; }
.bc-sep { color: rgba(255,140,0,0.4); }
.bc-current { color: rgba(255,140,0,0.8); }

/* Page header */
.page-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 1.8rem; gap: 1rem;
}
.page-eyebrow { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: rgba(255,140,0,0.6); letter-spacing: 0.18em; margin-bottom: 4px; }
.page-title { font-size: 1.6rem; font-weight: 700; color: #333333; letter-spacing: 0.06em; margin-bottom: 4px; }
.page-sub { font-size: 0.85rem; color: rgba(255,255,255,0.6); }

/* Action buttons */
.action-btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 18px; border-radius: 4px;
  font-family: 'Rajdhani', sans-serif; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s; text-decoration: none; border: 1px solid;
}
.action-btn svg { width: 14px; height: 14px; }
.action-btn.secondary { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.7); }
.action-btn.secondary:hover { border-color: rgba(255,140,0,0.5); color: #ff8c00; background: rgba(255,140,0,0.06); }
.action-btn.submit {
  background: rgba(255,140,0,0.1); border-color: rgba(255,140,0,0.4); color: #ff8c00;
}
.action-btn.submit:hover:not(:disabled) { background: rgba(255,140,0,0.18); border-color: #ff8c00; }
.action-btn.submit:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-spinner {
  width: 14px; height: 14px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.2); border-top-color: #ff8c00;
  animation: spin 0.7s linear infinite; flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Panel */
.panel { background: rgba(255,255,255,0.9); border: 1px solid rgba(255,140,0,0.15); border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.3rem 0.8rem;
  border-bottom: 1px solid rgba(255,140,0,0.1);
  background: rgba(255,140,0,0.02);
}
.panel-title { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; color: #333333; letter-spacing: 0.12em; }
.panel-title svg { width: 15px; height: 15px; }
.panel-body { padding: 1rem 1.3rem; }
.status-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px;
  background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3); color: #ff8c00;
}
.panel-body { padding: 1.3rem; }

/* Form grid */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
.form-section { background: rgba(255,255,255,0.5); border-radius: 4px; padding: 1.5rem; border: 1px solid rgba(255,140,0,0.08); }
.section-header {
  display: flex; align-items: center; gap: 8px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.75rem; color: #ff8c00; letter-spacing: 0.15em;
  margin-bottom: 1.5rem; padding-bottom: 0.8rem; border-bottom: 1px solid rgba(255,140,0,0.2);
}
.section-header svg { width: 16px; height: 16px; }
.fields-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; }
.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem;
  color: rgba(255,140,0,0.8); letter-spacing: 0.18em;
}
.field-wrap { position: relative; }
.field-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  width: 14px; height: 14px; color: rgba(255,140,0,0.6); pointer-events: none;
}
.field-input {
  width: 100%; height: 48px;
  background: rgba(255,255,255,0.9);
  border: 1px solid rgba(255,140,0,0.3);
  border-radius: 6px; color: #333333;
  font-family: 'Share Tech Mono', monospace; font-size: 0.82rem;
  padding: 0 12px 0 38px; outline: none; transition: all 0.2s;
}
.field-input::placeholder { color: rgba(255,140,0,0.5); }
.field-input:focus { border-color: rgba(255,140,0,0.6); background: rgba(255,255,255,1); box-shadow: 0 0 0 3px rgba(255,140,0,0.15); }
.field-select { -webkit-appearance: none; appearance: none; cursor: pointer; }
.field-select option { background: #ffffff; color: #333333; }
.required { color: #ff8c00; }

.form-actions {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: 2rem; padding: 1.5rem; border-top: 1px solid rgba(255,140,0,0.1);
}

/* Loading state */
.loading-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 4rem;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.6); letter-spacing: 0.15em;
}
.loading-state svg { width: 30px; height: 30px; }
.spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.2);
  border-top-color: rgba(255,140,0,0.7);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 768px) {
  .fields-grid { grid-template-columns: 1fr; }
  .form-actions { flex-direction: column; gap: 1rem; }
  .action-btn { width: 100%; justify-content: center; }
}
</style>