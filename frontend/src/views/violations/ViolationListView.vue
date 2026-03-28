<template>
  <div class="page-root">
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
          <div class="title-name">VIOLATIONS MANAGEMENT</div>
        </div>
        <div class="topbar-actions">
          <router-link to="/violations/create" class="action-btn amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            ADD VIOLATION
          </router-link>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb-bar">
        <span class="bc-prompt">$</span>
        <router-link to="/dashboard" class="bc-item">HOME</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">VIOLATIONS</span>
      </div>

      <!-- Filter Panel -->
      <div class="panel mb-section">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            SEARCH & FILTER
          </div>
        </div>
        <div class="panel-body">
          <div class="filter-grid">
            <div class="field-group">
              <label class="field-label">SEARCH</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input v-model="searchQuery" type="text" class="field-input" placeholder="Student name or violation type..." />
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">STATUS</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <select v-model="filterStatus" class="field-input field-select">
                  <option value="">All Status</option>
                  <option value="Pending">Pending</option>
                  <option value="Resolved">Resolved</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">VIOLATION TYPE</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                <select v-model="filterType" class="field-input field-select">
                  <option value="">All Types</option>
                  <option value="Minor">Minor</option>
                  <option value="Major">Major</option>
                  <option value="Academic">Academic</option>
                  <option value="Disciplinary">Disciplinary</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">DATE RANGE</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <select v-model="filterDateRange" class="field-input field-select">
                  <option value="">All Time</option>
                  <option value="today">Today</option>
                  <option value="week">This Week</option>
                  <option value="month">This Month</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">&nbsp;</label>
              <button @click="resetFilters" class="reset-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                RESET
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Violations List -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            INCIDENT REGISTRY
          </div>
          <span class="count-badge">{{ filteredViolations.length }} RECORDS</span>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="empty-state">
          <div class="spinner"></div>
          <span>LOADING VIOLATION DATA...</span>
        </div>

        <!-- Empty -->
        <div v-else-if="paginatedViolations.length === 0" class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          NO RECORDS FOUND
        </div>

        <!-- Violation rows -->
        <div v-else class="violation-list">
          <div v-for="violation in paginatedViolations" :key="violation.id" class="violation-row">
            <!-- Status indicator -->
            <div :class="['vio-indicator', violation.status === 'Pending' ? 'pending' : 'resolved']"></div>

            <!-- Icon -->
            <div :class="['vio-icon', violation.status === 'Pending' ? 'pending' : 'resolved']">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>

            <!-- Info -->
            <div class="vio-info">
              <div class="vio-top">
                <span class="vio-type">{{ violation.violation_type }}</span>
                <span :class="['vio-status', violation.status === 'Pending' ? 'pending' : 'resolved']">
                  <span class="status-dot"></span>
                  {{ violation.status.toUpperCase() }}
                </span>
                <span :class="['vio-severity', getSeverityClass(violation.violation_type)]">
                  {{ violation.violation_type.toUpperCase() }}
                </span>
              </div>
              <div class="vio-student">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ violation.student?.first_name }} {{ violation.student?.last_name }}
              </div>
              <div class="vio-meta">
                <span class="vio-date mono">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  {{ violation.date_committed }}
                </span>
                <span class="vio-desc">{{ violation.description }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="vio-actions">
              <router-link :to="`/violations/${violation.id}`" class="row-btn view" title="View">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                VIEW
              </router-link>
              <router-link :to="`/violations/${violation.id}/edit`" class="row-btn edit" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                EDIT
              </router-link>
              <button v-if="violation.status === 'Pending'" @click="resolveViolation(violation.id)" class="row-btn resolve" title="Resolve">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                RESOLVE
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-bar">
          <div class="page-info">
            SHOWING {{ (currentPage - 1) * itemsPerPage + 1 }}–{{ Math.min(currentPage * itemsPerPage, filteredViolations.length) }} OF {{ filteredViolations.length }}
          </div>
          <div class="page-controls">
            <button class="page-btn" :disabled="currentPage === 1" @click="prevPage">‹ PREV</button>
            <button
              v-for="page in visiblePages" :key="page"
              class="page-btn" :class="{ active: page === currentPage }"
              @click="currentPage = Number(page)"
            >{{ page }}</button>
            <button class="page-btn" :disabled="currentPage === totalPages" @click="nextPage">NEXT ›</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

interface Violation {
  id: number
  violation_type: string
  description: string
  sanction: string
  date_committed: string
  status: string
  student: { first_name: string; last_name: string }
}

const violations = ref<Violation[]>([])
const loading = ref(true)
const searchQuery = ref('')
const filterStatus = ref('')
const filterType = ref('')
const filterDateRange = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const filteredViolations = computed(() => violations.value.filter(v => {
  const q = searchQuery.value.toLowerCase()
  return (!q || v.violation_type.toLowerCase().includes(q) || v.description.toLowerCase().includes(q) ||
      `${v.student?.first_name} ${v.student?.last_name}`.toLowerCase().includes(q))
    && (!filterStatus.value || v.status === filterStatus.value)
    && (!filterType.value || v.violation_type === filterType.value)
}))

const totalPages = computed(() => Math.max(1, Math.ceil(filteredViolations.value.length / itemsPerPage)))

const paginatedViolations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredViolations.value.slice(start, start + itemsPerPage)
})

const visiblePages = computed(() => {
  const total = totalPages.value, cur = currentPage.value, delta = 2
  const range: number[] = []
  for (let i = Math.max(1, cur - delta); i <= Math.min(total, cur + delta); i++) range.push(i)
  return range
})

const getSeverityClass = (type: string) => {
  if (type === 'Major' || type === 'Disciplinary') return 'high'
  if (type === 'Academic') return 'med'
  return 'low'
}

const resetFilters = () => { searchQuery.value = ''; filterStatus.value = ''; filterType.value = ''; filterDateRange.value = ''; currentPage.value = 1 }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const fetchViolations = async () => {
  try {
    const response = await api.get('/violations')
    violations.value = response.data
  } catch (error) {
    console.error('Error fetching violations:', error)
  } finally {
    loading.value = false
  }
}

const resolveViolation = async (id: number) => {
  try {
    await api.post(`/violations/${id}/resolve`)
    const v = violations.value.find(v => v.id === id)
    if (v) v.status = 'Resolved'
  } catch (error) {
    console.error('Error resolving violation:', error)
  }
}

const handleLogout = async () => { await authStore.logout(); router.push('/login') }

onMounted(() => fetchViolations())
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.page-root {
  min-height: 100vh;
  background: #f9fafb;
  font-family: 'Inter', sans-serif;
  color: #111827;
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
.orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,165,0,0.04), transparent 70%); bottom: 0; left: -80px; }

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
  font-family: 'Inter', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.7); text-decoration: none; letter-spacing: 0.12em;
  padding: 6px 12px; border-radius: 4px; border: 1px solid rgba(255,140,0,0.15);
  transition: all 0.2s; flex-shrink: 0;
}
.topbar-back svg { width: 14px; height: 14px; }
.topbar-back:hover { color: #ff8c00; border-color: rgba(255,140,0,0.4); background: rgba(255,140,0,0.06); }
.topbar-title { flex: 1; }
.title-sys { font-family: 'Inter', monospace; font-size: 0.58rem; color: rgba(255,140,0,0.5); letter-spacing: 0.12em; }
.title-name { font-size: 1rem; font-weight: 700; color: #333333; letter-spacing: 0.14em; }
.topbar-actions { display: flex; gap: 8px; }
.action-btn {
  display: flex; align-items: center; gap: 7px;
  padding: 7px 16px; border-radius: 4px;
  font-family: 'Inter', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s; text-decoration: none; border: 1px solid;
}
.action-btn svg { width: 14px; height: 14px; }
.action-btn.amber { background: rgba(255,140,0,0.09); border-color: rgba(255,140,0,0.3); color: #ff8c00; }
.action-btn.amber:hover { background: rgba(255,140,0,0.18); border-color: #ff8c00; }

/* Main */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 1200px; margin: 0 auto; }

.breadcrumb-bar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.bc-prompt {
  color: #f97316;
  font-family: 'Inter', monospace;
}

.bc-item {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.bc-item:hover {
  color: #f97316;
}

.bc-sep {
  color: #d1d5db;
}

.bc-current {
  color: #374151;
  font-weight: 500;
}

/* Panel */
.panel { background: rgba(255,255,255,0.9); border: 1px solid rgba(255,140,0,0.15); border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.mb-section { margin-bottom: 1.2rem; }
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.3rem 0.8rem;
  border-bottom: 1px solid rgba(255,140,0,0.07);
}
.panel-title { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; color: #333333; letter-spacing: 0.12em; }
.panel-title svg { width: 15px; height: 15px; }
.panel-body { padding: 1rem 1.3rem; }
.count-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px;
  background: rgba(255,140,0,0.07); border: 1px solid rgba(255,140,0,0.2); color: #ff8c00;
}

/* Filters */
.filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end; }
.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: rgba(255,140,0,0.6); letter-spacing: 0.18em; }
.field-wrap { position: relative; }
.field-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: rgba(255,140,0,0.5); pointer-events: none; }
.field-input {
  width: 100%; height: 44px;
  background: rgba(255,255,255,0.9); border: 1px solid rgba(255,140,0,0.2);
  border-radius: 4px; color: #333333;
  font-family: 'Share Tech Mono', monospace; font-size: 0.82rem;
  padding: 0 12px 0 38px; outline: none; transition: all 0.2s;
}
.field-input::placeholder { color: rgba(255,140,0,0.5); }
.field-input:focus { border-color: rgba(255,140,0,0.55); background: rgba(255,255,255,1); box-shadow: 0 0 0 3px rgba(255,140,0,0.07); }
.field-select { -webkit-appearance: none; appearance: none; cursor: pointer; }
.field-select option { background: #ffffff; color: #333333; }
.reset-btn {
  display: flex; align-items: center; justify-content: center; gap: 7px;
  height: 44px; width: 100%; padding: 0 16px; border-radius: 4px;
  background: rgba(255,255,255,0.05); border: 1px solid rgba(255,140,0,0.15);
  color: rgba(255,140,0,0.7);
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s;
}
.reset-btn svg { width: 13px; height: 13px; }
.reset-btn:hover { border-color: rgba(255,140,0,0.35); color: #ff8c00; background: rgba(255,140,0,0.06); }

/* Violation list */
.violation-list { padding: 0.4rem 0; }
.violation-row {
  display: flex; align-items: center; gap: 1rem;
  padding: 1rem 1.3rem;
  border-bottom: 1px solid rgba(255,140,0,0.06);
  transition: background 0.15s; position: relative;
  overflow: hidden;
}
.violation-row:last-child { border-bottom: none; }
.violation-row:hover { background: rgba(255,140,0,0.03); }

/* Left color strip */
.vio-indicator {
  position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
}
.vio-indicator.pending { background: linear-gradient(180deg, #ff4444, rgba(255,68,68,0.2)); }
.vio-indicator.resolved { background: linear-gradient(180deg, #00ff88, rgba(0,255,136,0.2)); }

.vio-icon {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; margin-left: 4px;
}
.vio-icon svg { width: 16px; height: 16px; }
.vio-icon.pending { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.3); color: #ff4444; }
.vio-icon.resolved { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.3); color: #00ff88; }

.vio-info { flex: 1; min-width: 0; }
.vio-top { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 5px; }
.vio-type { font-size: 0.95rem; font-weight: 700; color: #333333; }

.vio-status {
  display: inline-flex; align-items: center; gap: 5px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; letter-spacing: 0.1em;
  padding: 3px 8px; border-radius: 3px; border: 1px solid;
}
.vio-status .status-dot { width: 5px; height: 5px; border-radius: 50%; animation: pulse 2s ease-in-out infinite; }
.vio-status.pending { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.vio-status.pending .status-dot { background: #ff4444; box-shadow: 0 0 6px #ff4444; }
.vio-status.resolved { background: rgba(0,255,136,0.08); border-color: rgba(0,255,136,0.25); color: #00ff88; }
.vio-status.resolved .status-dot { background: #00ff88; box-shadow: 0 0 6px #00ff88; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:0.4} }

.vio-severity {
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; letter-spacing: 0.08em;
  padding: 3px 8px; border-radius: 3px; border: 1px solid;
}
.vio-severity.high { background: rgba(255,68,68,0.07); border-color: rgba(255,68,68,0.2); color: rgba(255,68,68,0.7); }
.vio-severity.med { background: rgba(255,184,0,0.07); border-color: rgba(255,184,0,0.2); color: rgba(255,184,0,0.7); }
.vio-severity.low { background: rgba(80,150,255,0.07); border-color: rgba(80,150,255,0.2); color: rgba(80,150,255,0.7); }

.vio-student {
  display: flex; align-items: center; gap: 6px;
  font-size: 0.85rem; color: rgba(51,51,51,0.6); margin-bottom: 4px;
}
.vio-student svg { width: 12px; height: 12px; }

.vio-meta { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.vio-date {
  display: flex; align-items: center; gap: 5px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.65rem; color: rgba(255,140,0,0.5);
}
.vio-date svg { width: 11px; height: 11px; }
.vio-desc { font-size: 0.78rem; color: rgba(51,51,51,0.5); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; }
.mono { font-family: 'Share Tech Mono', monospace; }

.vio-actions { display: flex; gap: 5px; flex-shrink: 0; }
.row-btn {
  display: flex; align-items: center; gap: 5px;
  padding: 5px 10px; border-radius: 4px;
  font-family: 'Rajdhani', sans-serif; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
  cursor: pointer; transition: all 0.18s; text-decoration: none; border: 1px solid; background: none;
}
.row-btn svg { width: 11px; height: 11px; }
.row-btn.view { border-color: rgba(255,140,0,0.2); color: rgba(255,140,0,0.5); }
.row-btn.view:hover { border-color: #ff8c00; color: #ff8c00; background: rgba(255,140,0,0.08); }
.row-btn.edit { border-color: rgba(255,140,0,0.2); color: rgba(255,140,0,0.5); }
.row-btn.edit:hover { border-color: #ff8c00; color: #ff8c00; background: rgba(255,140,0,0.08); }
.row-btn.resolve { border-color: rgba(0,255,136,0.2); color: rgba(0,255,136,0.5); }
.row-btn.resolve:hover { border-color: #00ff88; color: #00ff88; background: rgba(0,255,136,0.08); }

/* Empty / Loading */
.empty-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 4rem;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.4); letter-spacing: 0.15em;
}
.empty-state svg { width: 28px; height: 28px; }
.spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.1); border-top-color: rgba(255,140,0,0.5);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Pagination */
.pagination-bar {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 1.3rem; border-top: 1px solid rgba(255,140,0,0.07);
}
.page-info { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: rgba(255,140,0,0.5); letter-spacing: 0.1em; }
.page-controls { display: flex; gap: 4px; }
.page-btn {
  padding: 0 12px; height: 32px; border-radius: 4px;
  background: rgba(255,255,255,0.6); border: 1px solid rgba(255,140,0,0.15);
  color: rgba(255,140,0,0.5);
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem;
  cursor: pointer; transition: all 0.18s; white-space: nowrap;
}
.page-btn:hover:not(:disabled) { border-color: rgba(255,140,0,0.45); color: #ff8c00; background: rgba(255,140,0,0.08); }
.page-btn.active { background: rgba(255,140,0,0.14); border-color: rgba(255,140,0,0.5); color: #ff8c00; }
.page-btn:disabled { opacity: 0.3; cursor: not-allowed; }

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

.dark .action-btn.primary {
  background: #f97316;
  border-color: #f97316;
  color: white;
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

.dark .field-input {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .field-input:focus {
  border-color: rgba(249, 115, 22, 0.6);
  background: rgba(55, 65, 81, 0.95);
}

.dark .field-select option {
  background: #1e293b;
}

.dark .reset-btn {
  background: rgba(249, 115, 22, 0.05);
  border-color: rgba(249, 115, 22, 0.2);
  color: rgba(249, 115, 22, 0.7);
}

.dark .reset-btn:hover {
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
  background: rgba(249, 115, 22, 0.1);
}

.dark .data-table thead tr {
  background: rgba(249, 115, 22, 0.08);
  border-bottom-color: rgba(249, 115, 22, 0.15);
}

.dark .data-row {
  border-bottom-color: rgba(249, 115, 22, 0.08);
}

.dark .data-row:hover {
  background: rgba(249, 115, 22, 0.04);
}

.dark .data-row td {
  color: #f9fafb;
}

.dark .page-btn {
  background: rgba(30, 41, 59, 0.6);
  border-color: rgba(249, 115, 22, 0.15);
  color: rgba(249, 115, 22, 0.5);
}

.dark .page-btn:hover:not(:disabled) {
  border-color: rgba(249, 115, 22, 0.45);
  color: #f97316;
  background: rgba(249, 115, 22, 0.08);
}

.dark .page-btn.active {
  background: rgba(249, 115, 22, 0.14);
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
}

.dark .pagination-bar {
  border-top-color: rgba(249, 115, 22, 0.07);
}

.dark .loading-state,
.dark .empty-state {
  color: rgba(249, 115, 22, 0.4);
}

/* Responsive */
@media (max-width: 1000px) {
  .filter-grid { grid-template-columns: 1fr 1fr 1fr; }
}
@media (max-width: 700px) {
  .filter-grid { grid-template-columns: 1fr 1fr; }
  .vio-actions { flex-direction: column; }
  .topbar-title { display: none; }
  .vio-desc { display: none; }
}
@media (max-width: 480px) {
  .filter-grid { grid-template-columns: 1fr; }
}
</style>