<template>
  <div class="page-root">
    <div class="bg-grid"></div>
    <div class="bg-scanlines"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Topbar -->
    <nav class="topbar">
      <div class="topbar-inner">
        <router-link to="/professors" class="topbar-back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          BACK
        </router-link>
        <div class="topbar-title">
          <div class="title-sys">SYS://CCS.EDU</div>
          <div class="title-name">{{ isEdit ? 'EDIT PROFESSOR' : 'CREATE PROFESSOR' }}</div>
        </div>
        <button @click="handleLogout" class="action-btn danger">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          LOGOUT
        </button>
      </div>
    </nav>

    <main class="main-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb-bar">
        <span class="bc-prompt">$</span>
        <router-link to="/dashboard" class="bc-item">HOME</router-link>
        <span class="bc-sep">›</span>
        <router-link to="/professors" class="bc-item">PROFESSORS</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">{{ isEdit ? 'EDIT MODE' : 'CREATE MODE' }}</span>
      </div>

      <!-- Form Panel -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ isEdit ? 'PROFESSOR DATA MODIFICATION' : 'NEW PROFESSOR REGISTRATION' }}
          </div>
          <span class="status-badge">{{ isEdit ? 'EDIT' : 'CREATE' }}</span>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <span>{{ isEdit ? 'UPDATING PROFESSOR RECORDS...' : 'INITIALIZING PROFESSOR PROFILE...' }}</span>
        </div>

        <!-- Form -->
        <form v-else @submit.prevent="handleSubmit" class="form-grid">
          <!-- Personal Information -->
          <div class="form-section">
            <div class="section-header">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              PERSONAL INFORMATION
            </div>
            <div class="fields-grid">
              <div class="field-group">
                <label class="field-label">FIRST NAME</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <input v-model="form.first_name" type="text" class="field-input" placeholder="Enter first name..." required />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">LAST NAME</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <input v-model="form.last_name" type="text" class="field-input" placeholder="Enter last name..." required />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">EMAIL ADDRESS</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  <input v-model="form.email" type="email" class="field-input" placeholder="Enter email address..." required />
                </div>
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="form-section">
            <div class="section-header">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6 4l8 8v8H6z"/></svg>
              ACADEMIC DETAILS
            </div>
            <div class="fields-grid">
              <div class="field-group">
                <label class="field-label">DEPARTMENT</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg>
                  <select v-model="form.department" class="field-input field-select" required>
                    <option value="">SELECT DEPARTMENT</option>
                    <option value="CCS">CCS - College of Computing Studies</option>
                    <option value="COE">COE - College of Engineering</option>
                    <option value="COA">COA - College of Arts</option>
                    <option value="COB">COB - College of Business</option>
                  </select>
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">EMPLOYMENT TYPE</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                  <select v-model="form.employment_type" class="field-input field-select" required>
                    <option value="">SELECT TYPE</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Contract">Contract</option>
                  </select>
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">ROLE</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/></svg>
                  <select v-model="form.role" class="field-input field-select" required>
                    <option value="">SELECT ROLE</option>
                    <option value="professor">Professor</option>
                    <option value="department_head">Department Head</option>
                    <option value="dean">Dean</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="form-section">
            <div class="section-header">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              CONTACT INFORMATION
            </div>
            <div class="fields-grid">
              <div class="field-group">
                <label class="field-label">PHONE NUMBER</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  <input v-model="form.phone" type="tel" class="field-input" placeholder="Enter phone number..." />
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">OFFICE LOCATION</label>
                <div class="field-wrap">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <input v-model="form.office_location" type="text" class="field-input" placeholder="Enter office location..." />
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="form-actions">
            <router-link to="/professors" class="action-btn secondary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
              CANCEL
            </router-link>
            <button type="submit" :disabled="loading" class="action-btn primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/><path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/></svg>
              {{ isEdit ? 'UPDATE RECORD' : 'CREATE RECORD' }}
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

const loading = ref(false)
const isEdit = computed(() => !!route.params.id)

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  department: '',
  employment_type: '',
  role: '',
  phone: '',
  office_location: ''
})

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await api.put(`/professors/${route.params.id}`, form.value)
    } else {
      await api.post('/professors', form.value)
    }
    router.push('/professors')
  } catch (error) {
    console.error('Error saving professor:', error)
  } finally {
    loading.value = false
  }
}

const fetchProfessor = async () => {
  try {
    const response = await api.get(`/professors/${route.params.id}`)
    form.value = response.data
  } catch (error) {
    console.error('Error fetching professor:', error)
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(async () => {
  if (isEdit.value) {
    await fetchProfessor()
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
.orb-1 { width: 450px; height: 450px; background: radial-gradient(circle, rgba(255,140,0,0.05), transparent 70%); top: -100px; right: -100px; }
.orb-2 { width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,165,0,0.04), transparent 70%); bottom: -80px; left: -80px; }

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
.action-btn {
  display: flex; align-items: center; gap: 7px; padding: 7px 16px; border-radius: 4px;
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s; text-decoration: none; border: 1px solid;
}
.action-btn svg { width: 14px; height: 14px; }
.action-btn.danger { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.action-btn.danger:hover { background: rgba(255,68,68,0.16); border-color: #ff4444; }

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
.status-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px;
  background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3); color: #ff8c00;
}

/* Form */
.form-grid { display: flex; flex-direction: column; gap: 1.5rem; }
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
  width: 100%; background: rgba(255,255,255,0.9);
  border: 1px solid rgba(255,140,0,0.3);
  border-radius: 6px; color: #333333;
  font-family: 'Share Tech Mono', monospace; font-size: 0.82rem;
  padding: 0 12px 0 38px; outline: none; transition: all 0.2s;
}
.field-input::placeholder { color: rgba(255,140,0,0.5); }
.field-input:focus { border-color: rgba(255,140,0,0.6); background: rgba(255,255,255,1); box-shadow: 0 0 0 3px rgba(255,140,0,0.15); }
.field-select { -webkit-appearance: none; appearance: none; cursor: pointer; height: 48px; }
.field-select option { background: #ffffff; color: #333333; }

.form-actions {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: 2rem; padding: 1.5rem; border-top: 1px solid rgba(255,140,0,0.1);
}
.action-btn.secondary { background: rgba(255,255,255,0.04); border-color: rgba(255,140,0,0.15); color: rgba(255,140,0,0.7); }
.action-btn.secondary:hover { border-color: rgba(255,140,0,0.4); color: #ff8c00; background: rgba(255,140,0,0.06); }
.action-btn.primary { background: rgba(255,140,0,0.1); border-color: rgba(255,140,0,0.4); color: #ff8c00; }
.action-btn.primary:hover { background: rgba(255,140,0,0.18); border-color: #ff8c00; }

/* Loading state */
.loading-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 4rem;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.6); letter-spacing: 0.15em;
}
.spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.2);
  border-top-color: rgba(255,140,0,0.7);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Dark Mode Styles */
.dark .page-root {
  background: #0f172a;
}

.dark .bg-grid {
  background-image: 
    linear-gradient(rgba(249, 115, 22, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(249, 115, 22, 0.03) 1px, transparent 1px);
}

.dark .topbar {
  background: #1e293b;
  border-bottom-color: #334155;
}

.dark .topbar-back {
  color: #9ca3af;
  border-color: #334155;
}

.dark .topbar-back:hover {
  background: rgba(249, 115, 22, 0.1);
  border-color: #f97316;
  color: #f97316;
}

.dark .title-name {
  color: #f9fafb;
}

.dark .action-btn {
  border-color: #334155;
  color: #9ca3af;
}

.dark .action-btn.danger {
  background: #ef4444;
  border-color: #ef4444;
  color: white;
}

.dark .panel {
  background: #1e293b;
  border-color: #334155;
}

.dark .panel-header {
  border-bottom-color: #334155;
}

.dark .panel-title {
  color: #f9fafb;
}

.dark .status-badge {
  background: rgba(249, 115, 22, 0.1);
  border-color: rgba(249, 115, 22, 0.3);
  color: #f97316;
}

.dark .form-section {
  background: rgba(30, 41, 59, 0.5);
  border-color: rgba(249, 115, 22, 0.08);
}

.dark .section-header {
  border-bottom-color: rgba(249, 115, 22, 0.2);
}

.dark .field-input {
  background: rgba(55, 65, 81, 0.9);
  border-color: rgba(249, 115, 22, 0.3);
  color: #f9fafb;
}

.dark .field-input:focus {
  border-color: rgba(249, 115, 22, 0.6);
  background: rgba(55, 65, 81, 1);
}

.dark .field-select option {
  background: #1e293b;
  color: #f9fafb;
}

.dark .form-actions {
  border-top-color: rgba(249, 115, 22, 0.1);
}

.dark .action-btn.secondary {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(249, 115, 22, 0.15);
  color: rgba(249, 115, 22, 0.7);
}

.dark .action-btn.secondary:hover {
  border-color: rgba(249, 115, 22, 0.4);
  color: #f97316;
  background: rgba(249, 115, 22, 0.06);
}

.dark .action-btn.primary {
  background: rgba(249, 115, 22, 0.1);
  border-color: rgba(249, 115, 22, 0.4);
  color: #f97316;
}

.dark .action-btn.primary:hover {
  background: rgba(249, 115, 22, 0.18);
  border-color: #f97316;
}

.dark .loading-state {
  color: rgba(249, 115, 22, 0.6);
}

/* Responsive */
@media (max-width: 768px) {
  .fields-grid { grid-template-columns: 1fr; }
  .form-actions { flex-direction: column; gap: 1rem; }
  .action-btn { width: 100%; justify-content: center; }
}
</style>
