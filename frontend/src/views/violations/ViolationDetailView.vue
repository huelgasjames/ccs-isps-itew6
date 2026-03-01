<template>
  <div class="page-root">
    <div class="bg-grid"></div>
    <div class="bg-scanlines"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Topbar -->
    <nav class="topbar">
      <div class="topbar-inner">
        <router-link to="/violations" class="topbar-back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          BACK
        </router-link>
        <div class="topbar-title">
          <div class="title-sys">SYS://CCS.EDU</div>
          <div class="title-name">VIOLATION DETAILS</div>
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
        <router-link to="/violations" class="bc-item">VIOLATIONS</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">INCIDENT #{{ violation?.id }}</span>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <span>LOADING VIOLATION DATA...</span>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="error-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>FAILED TO LOAD VIOLATION RECORD</span>
        <router-link to="/violations" class="back-btn">BACK TO VIOLATIONS</router-link>
      </div>

      <!-- Violation Details -->
      <div v-else-if="violation" class="detail-container">
        <!-- Header Panel -->
        <div class="panel mb-section">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              INCIDENT REPORT
            </div>
            <div class="header-actions">
              <span :class="['status-badge', violation.status.toLowerCase()]">
                <span class="status-dot"></span>
                {{ violation.status.toUpperCase() }}
              </span>
            </div>
          </div>
          <div class="panel-body">
            <div class="incident-header">
              <div class="incident-info">
                <h1 class="incident-title">{{ violation.violation_type }}</h1>
                <div class="incident-meta">
                  <span class="incident-id">INCIDENT #{{ violation.id }}</span>
                  <span class="incident-date">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ violation.date_committed }}
                  </span>
                </div>
              </div>
              <div class="severity-indicator" :class="getSeverityClass(violation.violation_type)">
                {{ violation.violation_type.toUpperCase() }}
              </div>
            </div>
          </div>
        </div>

        <!-- Student Information -->
        <div class="panel mb-section">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              STUDENT INFORMATION
            </div>
          </div>
          <div class="panel-body">
            <div class="student-card">
              <div class="student-avatar">
                {{ violation.student?.first_name?.charAt(0) }}{{ violation.student?.last_name?.charAt(0) }}
              </div>
              <div class="student-details">
                <h3 class="student-name">
                  {{ violation.student?.first_name }} {{ violation.student?.last_name }}
                </h3>
                <div class="student-meta">
                  <span class="student-email">{{ violation.student?.email }}</span>
                  <span class="student-program">{{ violation.student?.program }}</span>
                  <span class="student-year">{{ violation.student?.year_level }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Violation Details -->
        <div class="panel mb-section">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
              VIOLATION DESCRIPTION
            </div>
          </div>
          <div class="panel-body">
            <div class="description-content">
              <p class="violation-description">{{ violation.description }}</p>
            </div>
          </div>
        </div>

        <!-- Sanctions -->
        <div class="panel mb-section">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/></svg>
              SANCTIONS & PUNISHMENT
            </div>
          </div>
          <div class="panel-body">
            <div class="sanction-content">
              <p class="sanction-text">{{ violation.sanction || 'No sanctions specified' }}</p>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M3 12h18m-9-9v18"/></svg>
              ACTIONS
            </div>
          </div>
          <div class="panel-body">
            <div class="action-buttons">
              <router-link :to="`/violations/${violation.id}/edit`" class="action-btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                EDIT VIOLATION
              </router-link>
              <button v-if="violation.status === 'Pending'" @click="resolveViolation" class="action-btn primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                MARK AS RESOLVED
              </button>
              <button @click="printReport" class="action-btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                PRINT REPORT
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

interface Violation {
  id: number
  violation_type: string
  description: string
  sanction: string
  date_committed: string
  status: string
  student: {
    first_name: string
    last_name: string
    email: string
    program: string
    year_level: string
  }
}

const violation = ref<Violation | null>(null)
const loading = ref(true)
const error = ref(false)

const getSeverityClass = (type: string) => {
  if (type === 'Major' || type === 'Disciplinary') return 'high'
  if (type === 'Academic') return 'med'
  return 'low'
}

const fetchViolation = async () => {
  try {
    const response = await api.get(`/violations/${route.params.id}`)
    violation.value = response.data
  } catch (err) {
    console.error('Error fetching violation:', err)
    error.value = true
  } finally {
    loading.value = false
  }
}

const resolveViolation = async () => {
  try {
    await api.post(`/violations/${violation.value?.id}/resolve`)
    if (violation.value) {
      violation.value.status = 'Resolved'
    }
  } catch (err) {
    console.error('Error resolving violation:', err)
  }
}

const printReport = () => {
  window.print()
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  fetchViolation()
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
.topbar-actions { display: flex; gap: 8px; }
.action-btn {
  display: flex; align-items: center; gap: 7px; padding: 7px 16px; border-radius: 4px;
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s; text-decoration: none; border: 1px solid;
}
.action-btn svg { width: 14px; height: 14px; }
.action-btn.danger { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.action-btn.danger:hover { background: rgba(255,68,68,0.16); border-color: #ff4444; }
.action-btn.primary { background: rgba(255,140,0,0.1); border-color: rgba(255,140,0,0.4); color: #ff8c00; }
.action-btn.primary:hover { background: rgba(255,140,0,0.18); border-color: #ff8c00; }
.action-btn.secondary { background: rgba(255,255,255,0.04); border-color: rgba(255,140,0,0.15); color: rgba(255,140,0,0.7); }
.action-btn.secondary:hover { border-color: rgba(255,140,0,0.4); color: #ff8c00; background: rgba(255,140,0,0.06); }

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
.mb-section { margin-bottom: 1.2rem; }
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.3rem 0.8rem;
  border-bottom: 1px solid rgba(255,140,0,0.07);
  background: rgba(255,140,0,0.02);
}
.panel-title { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; color: #333333; letter-spacing: 0.12em; }
.panel-title svg { width: 15px; height: 15px; }
.panel-body { padding: 1.3rem; }

/* Loading/Error States */
.loading-state, .error-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 4rem;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.6); letter-spacing: 0.15em;
}
.loading-state svg, .error-state svg { width: 28px; height: 28px; }
.spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.1); border-top-color: rgba(255,140,0,0.5);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.back-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 8px 16px; border-radius: 4px;
  background: rgba(255,140,0,0.08); border: 1px solid rgba(255,140,0,0.3); color: #ff8c00;
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  text-decoration: none; transition: all 0.2s;
}
.back-btn:hover { background: rgba(255,140,0,0.16); border-color: #ff8c00; }

/* Detail Container */
.detail-container { display: flex; flex-direction: column; gap: 1.2rem; }

/* Header */
.header-actions { display: flex; gap: 8px; }
.status-badge {
  display: inline-flex; align-items: center; gap: 5px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px; border: 1px solid;
}
.status-badge .status-dot { width: 5px; height: 5px; border-radius: 50%; animation: pulse 2s ease-in-out infinite; }
.status-badge.pending { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.status-badge.pending .status-dot { background: #ff4444; box-shadow: 0 0 6px #ff4444; }
.status-badge.resolved { background: rgba(0,255,136,0.08); border-color: rgba(0,255,136,0.25); color: #00ff88; }
.status-badge.resolved .status-dot { background: #00ff88; box-shadow: 0 0 6px #00ff88; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:0.4} }

.incident-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
.incident-info { flex: 1; }
.incident-title { font-size: 1.8rem; font-weight: 700; color: #333333; letter-spacing: 0.06em; margin-bottom: 8px; }
.incident-meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.incident-id { font-family: 'Share Tech Mono', monospace; font-size: 0.7rem; color: rgba(255,140,0,0.6); letter-spacing: 0.1em; }
.incident-date {
  display: flex; align-items: center; gap: 5px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem; color: rgba(255,140,0,0.6);
}
.incident-date svg { width: 12px; height: 12px; }
.severity-indicator {
  padding: 6px 12px; border-radius: 4px; font-family: 'Share Tech Mono', monospace;
  font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
}
.severity-indicator.high { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.3); color: #ff4444; }
.severity-indicator.med { background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3); color: #ff8c00; }
.severity-indicator.low { background: rgba(80,150,255,0.1); border: 1px solid rgba(80,150,255,0.3); color: #5096ff; }

/* Student Card */
.student-card {
  display: flex; align-items: center; gap: 1rem;
  padding: 1rem; background: rgba(255,140,0,0.02); border: 1px solid rgba(255,140,0,0.1);
  border-radius: 6px;
}
.student-avatar {
  width: 48px; height: 48px; border-radius: 50%;
  background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 1.2rem; color: #ff8c00;
}
.student-details { flex: 1; }
.student-name { font-size: 1.1rem; font-weight: 700; color: #333333; margin-bottom: 4px; }
.student-meta {
  display: flex; flex-direction: column; gap: 2px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem;
  color: rgba(51,51,51,0.6);
}

/* Content Sections */
.description-content, .sanction-content {
  padding: 1rem; background: rgba(255,140,0,0.02); border: 1px solid rgba(255,140,0,0.1);
  border-radius: 6px;
}
.violation-description, .sanction-text {
  font-size: 0.95rem; line-height: 1.6; color: rgba(51,51,51,0.8);
}

/* Action Buttons */
.action-buttons {
  display: flex; gap: 12px; flex-wrap: wrap;
}
.action-buttons .action-btn { flex-shrink: 0; }

/* Responsive */
@media (max-width: 768px) {
  .incident-header { flex-direction: column; }
  .incident-meta { flex-direction: column; align-items: flex-start; gap: 8px; }
  .action-buttons { flex-direction: column; }
  .action-buttons .action-btn { width: 100%; justify-content: center; }
}
</style>
