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
          <div class="title-name">PROFESSORS MANAGEMENT</div>
        </div>
        <div class="topbar-actions">
          <router-link to="/professors/create" class="action-btn primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/><line x1="9" y1="10" x2="15" y2="10"/></svg>
            ADD PROFESSOR
          </router-link>
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
        <span class="bc-current">PROFESSORS</span>
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
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input v-model="searchQuery" type="text" class="field-input" placeholder="Search by name or email..." />
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">DEPARTMENT</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg>
                <select v-model="filterDepartment" class="field-input field-select">
                  <option value="">All Departments</option>
                  <option value="CCS">CCS</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">EMPLOYMENT TYPE</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                <select v-model="filterEmploymentType" class="field-input field-select">
                  <option value="">All Types</option>
                  <option value="Full-time">Full-time</option>
                  <option value="Part-time">Part-time</option>
                  <option value="Contract">Contract</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">&nbsp;</label>
              <button @click="resetFilters" class="reset-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                RESET
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Professors List -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            FACULTY REGISTRY
          </div>
          <span class="count-badge">{{ filteredProfessors.length }} RECORDS</span>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <span>LOADING FACULTY DATA...</span>
        </div>

        <!-- Empty state -->
        <div v-else-if="paginatedProfessors.length === 0" class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          NO RECORDS FOUND
        </div>

        <!-- Professor cards -->
        <div v-else class="prof-list">
          <div v-for="professor in paginatedProfessors" :key="professor.id" class="prof-row">
            <div class="prof-avatar">
              {{ professor.first_name.charAt(0) }}{{ professor.last_name.charAt(0) }}
            </div>
            <div class="prof-info">
              <div class="prof-name">{{ professor.first_name }} {{ professor.last_name }}</div>
              <div class="prof-email mono">{{ professor.email }}</div>
              <div class="prof-tags">
                <span class="tag dept">{{ professor.department }}</span>
                <span :class="['tag', 'emp', getEmpClass(professor.employment_type)]">{{ professor.employment_type }}</span>
                <span class="tag role">{{ professor.role }}</span>
              </div>
            </div>
            <div class="prof-actions">
              <router-link :to="`/professors/${professor.id}`" class="row-btn view" title="View">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                VIEW
              </router-link>
              <router-link :to="`/professors/${professor.id}/edit`" class="row-btn edit" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                EDIT
              </router-link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-bar">
          <div class="page-info">
            SHOWING {{ (currentPage - 1) * itemsPerPage + 1 }}–{{ Math.min(currentPage * itemsPerPage, filteredProfessors.length) }} OF {{ filteredProfessors.length }}
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

interface Professor {
  id: number
  first_name: string
  last_name: string
  email: string
  department: string
  employment_type: string
  role: string
}

const professors = ref<Professor[]>([])
const loading = ref(true)
const searchQuery = ref('')
const filterDepartment = ref('')
const filterEmploymentType = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const filteredProfessors = computed(() => professors.value.filter(p => {
  const q = searchQuery.value.toLowerCase()
  return (!q || p.first_name.toLowerCase().includes(q) || p.last_name.toLowerCase().includes(q) || p.email.toLowerCase().includes(q))
    && (!filterDepartment.value || p.department === filterDepartment.value)
    && (!filterEmploymentType.value || p.employment_type === filterEmploymentType.value)
}))

const totalPages = computed(() => Math.max(1, Math.ceil(filteredProfessors.value.length / itemsPerPage)))

const paginatedProfessors = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredProfessors.value.slice(start, start + itemsPerPage)
})

const visiblePages = computed(() => {
  const total = totalPages.value, cur = currentPage.value, delta = 2
  const range: number[] = []
  for (let i = Math.max(1, cur - delta); i <= Math.min(total, cur + delta); i++) range.push(i)
  return range
})

const getEmpClass = (type: string) => {
  if (type === 'Full-time') return 'full'
  if (type === 'Part-time') return 'part'
  return 'contract'
}

const resetFilters = () => { searchQuery.value = ''; filterDepartment.value = ''; filterEmploymentType.value = ''; currentPage.value = 1 }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const handleLogout = async () => { await authStore.logout(); router.push('/login') }

const fetchProfessors = async () => {
  try {
    const response = await api.get('/professors')
    professors.value = response.data
  } catch (error) {
    console.error('Error fetching professors:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchProfessors())
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
.orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,140,0,0.05), transparent 70%); top: -150px; right: 10%; }
.orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,165,0,0.04), transparent 70%); bottom: 0; left: -100px; }

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
  padding: 6px 12px; border-radius: 4px; border: 1px solid rgba(255,140,0,0.15);
  transition: all 0.2s; flex-shrink: 0;
}
.topbar-back svg { width: 14px; height: 14px; }
.topbar-back:hover { color: #ff8c00; border-color: rgba(255,140,0,0.4); background: rgba(255,140,0,0.06); }
.topbar-title { flex: 1; }
.title-sys { font-family: 'Share Tech Mono', monospace; font-size: 0.58rem; color: rgba(255,140,0,0.5); letter-spacing: 0.12em; }
.title-name { font-size: 1rem; font-weight: 700; color: #333333; letter-spacing: 0.14em; }
.topbar-actions { display: flex; gap: 8px; }
.action-btn {
  display: flex; align-items: center; gap: 7px;
  padding: 7px 16px; border-radius: 4px;
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s; text-decoration: none; border: 1px solid;
}
.action-btn svg { width: 14px; height: 14px; }
.action-btn.primary { background: rgba(255,140,0,0.09); border-color: rgba(255,140,0,0.3); color: #ff8c00; }
.action-btn.primary:hover { background: rgba(255,140,0,0.18); border-color: #ff8c00; }
.action-btn.danger { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.action-btn.danger:hover { background: rgba(255,68,68,0.16); border-color: #ff4444; }

/* Main */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 1200px; margin: 0 auto; }

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
.filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end; }
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

/* Professor list */
.prof-list { padding: 0.5rem 0; }
.prof-row {
  display: flex; align-items: center; gap: 1rem;
  padding: 1rem 1.3rem;
  border-bottom: 1px solid rgba(255,140,0,0.06);
  transition: background 0.15s;
}
.prof-row:last-child { border-bottom: none; }
.prof-row:hover { background: rgba(255,140,0,0.03); }

.prof-avatar {
  width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, rgba(255,140,0,0.2), rgba(255,100,0,0.15));
  border: 1px solid rgba(255,140,0,0.3);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 0.9rem; color: #ff8c00;
  letter-spacing: 0.05em;
}

.prof-info { flex: 1; min-width: 0; }
.prof-name { font-size: 0.95rem; font-weight: 700; color: #333333; margin-bottom: 3px; }
.prof-email { font-size: 0.72rem; color: rgba(51,51,51,0.6); margin-bottom: 7px; }
.mono { font-family: 'Share Tech Mono', monospace; }
.prof-tags { display: flex; gap: 6px; flex-wrap: wrap; }
.tag {
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; letter-spacing: 0.08em;
  padding: 2px 8px; border-radius: 3px; border: 1px solid;
}
.tag.dept { background: rgba(255,140,0,0.08); border-color: rgba(255,140,0,0.25); color: #ff8c00; }
.tag.role { background: rgba(255,140,0,0.08); border-color: rgba(255,140,0,0.2); color: #ff8c00; }
.tag.emp.full { background: rgba(255,140,0,0.07); border-color: rgba(255,140,0,0.22); color: #ff8c00; }
.tag.emp.part { background: rgba(255,140,0,0.07); border-color: rgba(255,140,0,0.22); color: #ff8c00; }
.tag.emp.contract { background: rgba(255,140,0,0.07); border-color: rgba(255,140,0,0.22); color: #ff8c00; }

.prof-actions { display: flex; gap: 6px; flex-shrink: 0; }
.row-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 6px 12px; border-radius: 4px;
  font-family: 'Rajdhani', sans-serif; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;
  cursor: pointer; transition: all 0.18s; text-decoration: none; border: 1px solid;
}
.row-btn svg { width: 12px; height: 12px; }
.row-btn.view { background: rgba(255,140,0,0.06); border-color: rgba(255,140,0,0.2); color: rgba(255,140,0,0.6); }
.row-btn.view:hover { border-color: #ff8c00; color: #ff8c00; background: rgba(255,140,0,0.12); }
.row-btn.edit { background: rgba(255,140,0,0.06); border-color: rgba(255,140,0,0.2); color: rgba(255,140,0,0.6); }
.row-btn.edit:hover { border-color: #ff8c00; color: #ff8c00; background: rgba(255,140,0,0.12); }

/* Loading / Empty */
.loading-state, .empty-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 4rem;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.4); letter-spacing: 0.15em;
}
.loading-state svg, .empty-state svg { width: 30px; height: 30px; }
.spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.1);
  border-top-color: rgba(255,140,0,0.5);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Pagination */
.pagination-bar {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 1.3rem;
  border-top: 1px solid rgba(255,140,0,0.07);
}
.page-info { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: rgba(255,140,0,0.5); letter-spacing: 0.1em; }
.page-controls { display: flex; gap: 4px; }
.page-btn {
  padding: 0 12px; height: 32px;
  border-radius: 4px; background: rgba(255,255,255,0.6); border: 1px solid rgba(255,140,0,0.15);
  color: rgba(255,140,0,0.5);
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem;
  cursor: pointer; transition: all 0.18s; white-space: nowrap;
}
.page-btn:hover:not(:disabled) { border-color: rgba(255,140,0,0.45); color: #ff8c00; background: rgba(255,140,0,0.08); }
.page-btn.active { background: rgba(255,140,0,0.14); border-color: rgba(255,140,0,0.5); color: #ff8c00; }
.page-btn:disabled { opacity: 0.3; cursor: not-allowed; }

/* Responsive */
@media (max-width: 900px) {
  .filter-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .filter-grid { grid-template-columns: 1fr; }
  .topbar-title { display: none; }
  .prof-actions { flex-direction: column; }
  .row-btn span { display: none; }
}
</style>