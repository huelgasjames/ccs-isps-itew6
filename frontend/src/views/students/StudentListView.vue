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
        <a href="/dashboard" class="topbar-back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          BACK
        </a>
        <div class="topbar-title">
          <div class="title-sys">SYS://CCS.EDU</div>
          <div class="title-name">STUDENTS MANAGEMENT</div>
        </div>
        <div class="topbar-actions">
          <router-link to="/students/create" class="action-btn primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="12" y1="14" x2="12" y2="20"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
            ADD STUDENT
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
        <a href="/dashboard" class="bc-item">HOME</a>
        <span class="bc-sep">›</span>
        <span class="bc-current">STUDENTS</span>
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
              <label class="field-label">PROGRAM</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg>
                <select v-model="selectedProgram" class="field-input field-select">
                  <option value="">All Programs</option>
                  <option value="BSIT">BSIT</option>
                  <option value="BSCS">BSCS</option>
                  <option value="BSIS">BSIS</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">YEAR LEVEL</label>
              <div class="field-wrap">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <select v-model="selectedYear" class="field-input field-select">
                  <option value="">All Years</option>
                  <option value="1">1st Year</option>
                  <option value="2">2nd Year</option>
                  <option value="3">3rd Year</option>
                  <option value="4">4th Year</option>
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

      <!-- Students Table -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            STUDENT REGISTRY
          </div>
          <span class="count-badge">{{ filteredStudents.length }} RECORDS</span>
        </div>
        <div class="panel-body no-pad">
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>NAME</th>
                  <th>EMAIL</th>
                  <th>PROGRAM</th>
                  <th>YEAR</th>
                  <th>STATUS</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="paginatedStudents.length === 0">
                  <td colspan="7" class="empty-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    NO RECORDS FOUND
                  </td>
                </tr>
                <tr v-for="student in paginatedStudents" :key="student.id" class="data-row">
                  <td class="id-cell">
                    <span class="mono">{{ student.id }}</span>
                  </td>
                  <td>
                    <div class="student-name">
                      <div class="student-avatar">{{ student.name.charAt(0) }}</div>
                      <span>{{ student.name }}</span>
                    </div>
                  </td>
                  <td class="email-cell mono">{{ student.email }}</td>
                  <td>
                    <span class="prog-badge">{{ student.program }}</span>
                  </td>
                  <td class="mono">{{ student.year_level }}{{ getYearSuffix(student.year_level) }} YEAR</td>
                  <td>
                    <span :class="['status-badge', student.is_at_risk ? 'at-risk' : 'normal']">
                      <span class="status-dot"></span>
                      {{ student.is_at_risk ? 'AT RISK' : 'NORMAL' }}
                    </span>
                  </td>
                  <td>
                    <div class="row-actions">
                      <router-link :to="`/students/${student.id}`" class="row-btn view" title="View">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      </router-link>
                      <router-link :to="`/students/${student.id}/edit`" class="row-btn edit" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      </router-link>
                      <button @click="deleteStudent(student.id)" class="row-btn delete" title="Delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="pagination-bar">
            <div class="page-info">
              SHOWING {{ ((currentPage - 1) * itemsPerPage) + 1 }}–{{ Math.min(currentPage * itemsPerPage, filteredStudents.length) }} OF {{ filteredStudents.length }}
            </div>
            <div class="page-controls">
              <button class="page-btn" :disabled="currentPage === 1" @click.prevent="currentPage = 1">«</button>
              <button class="page-btn" :disabled="currentPage === 1" @click.prevent="currentPage--">‹</button>
              <button
                v-for="page in totalPages" :key="page"
                class="page-btn" :class="{ active: currentPage === page }"
                @click.prevent="currentPage = page"
              >{{ page }}</button>
              <button class="page-btn" :disabled="currentPage === totalPages" @click.prevent="currentPage++">›</button>
              <button class="page-btn" :disabled="currentPage === totalPages" @click.prevent="currentPage = totalPages">»</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const selectedProgram = ref('')
const selectedYear = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const students = ref([
  { id: 2001746, name: 'John Doe',      email: 'john.doe@ccs.edu',      program: 'BSIT', year_level: 3, is_at_risk: false },
  { id: 2001747, name: 'Jane Smith',    email: 'jane.smith@ccs.edu',    program: 'BSCS', year_level: 2, is_at_risk: true  },
  { id: 2001748, name: 'Maria Santos',  email: 'maria.santos@ccs.edu',  program: 'BSIS', year_level: 4, is_at_risk: false },
])

const filteredStudents = computed(() => students.value.filter(s => {
  const q = searchQuery.value.toLowerCase()
  return (s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q))
      && (!selectedProgram.value || s.program === selectedProgram.value)
      && (!selectedYear.value || s.year_level.toString() === selectedYear.value)
}))

const totalPages = computed(() => Math.max(1, Math.ceil(filteredStudents.value.length / itemsPerPage)))

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredStudents.value.slice(start, start + itemsPerPage)
})

const getYearSuffix = (y: number) => (['st','nd','rd','th'])[y - 1] ?? 'th'
const resetFilters = () => { searchQuery.value = ''; selectedProgram.value = ''; selectedYear.value = ''; currentPage.value = 1 }
const deleteStudent = (id: number) => { if (confirm('Delete this student?')) students.value = students.value.filter(s => s.id !== id) }
const handleLogout = async () => { await authStore.logout(); router.push('/login') }
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
  background-image: linear-gradient(rgba(255,140,0,0.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,140,0,0.035) 1px, transparent 1px);
  background-size: 40px 40px;
}
.bg-scanlines {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,140,0,0.08) 2px, rgba(255,140,0,0.08) 4px);
}
.orb { position: fixed; border-radius: 50%; filter: blur(90px); z-index: 0; pointer-events: none; }
.orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,140,0,0.08), transparent 70%); top: -150px; left: -150px; }
.orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,165,0,0.07), transparent 70%); bottom: 0; right: 10%; }

/* Topbar */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(255,255,255,0.95);
  border-bottom: 1px solid rgba(255,140,0,0.2);
  backdrop-filter: blur(12px);
}
.topbar-inner {
  display: flex; align-items: center; gap: 1rem;
  padding: 0 1.5rem; height: 60px;
}
.topbar-back {
  display: flex; align-items: center; gap: 6px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.7); text-decoration: none; letter-spacing: 0.12em;
  padding: 6px 12px; border-radius: 4px;
  border: 1px solid rgba(255,140,0,0.2);
  transition: all 0.2s; flex-shrink: 0;
}
.topbar-back svg { width: 14px; height: 14px; }
.topbar-back:hover { color: #ff8c00; border-color: rgba(255,140,0,0.5); background: rgba(255,140,0,0.08); }
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
.action-btn.primary { background: rgba(255,140,0,0.1); border-color: rgba(255,140,0,0.35); color: #ff8c00; }
.action-btn.primary:hover { background: rgba(255,140,0,0.18); border-color: #ff8c00; }
.action-btn.danger { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.action-btn.danger:hover { background: rgba(255,68,68,0.16); border-color: #ff4444; }

/* Main */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

.breadcrumb-bar {
  display: flex; align-items: center; gap: 8px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem; color: rgba(255,140,0,0.5);
  letter-spacing: 0.12em; margin-bottom: 1.5rem;
}
.bc-prompt { color: #ff8c00; }
.bc-item { color: rgba(255,140,0,0.6); text-decoration: none; }
.bc-item:hover { color: #ff8c00; }
.bc-sep { color: rgba(255,140,0,0.3); }
.bc-current { color: rgba(255,140,0,0.8); }

/* Panel */
.panel {
  background: rgba(255,255,255,0.9);
  border: 1px solid rgba(255,140,0,0.15);
  border-radius: 6px; overflow: hidden;
}
.mb-section { margin-bottom: 1.2rem; }
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.3rem 0.8rem;
  border-bottom: 1px solid rgba(255,140,0,0.1);
}
.panel-title {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.85rem; font-weight: 700; color: #333333; letter-spacing: 0.12em;
}
.panel-title svg { width: 15px; height: 15px; }
.panel-body { padding: 1rem 1.3rem; }
.panel-body.no-pad { padding: 0; }
.count-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px;
  background: rgba(255,140,0,0.08); border: 1px solid rgba(255,140,0,0.25); color: #ff8c00;
}

/* Filter grid */
.filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end; }
.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem;
  color: rgba(255,140,0,0.6); letter-spacing: 0.18em;
}
.field-wrap { position: relative; }
.field-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  width: 14px; height: 14px; color: rgba(255,140,0,0.5); pointer-events: none;
}
.field-input {
  width: 100%; height: 44px;
  background: rgba(255,255,255,0.8);
  border: 1px solid rgba(255,140,0,0.2);
  border-radius: 4px; color: #333333;
  font-family: 'Share Tech Mono', monospace; font-size: 0.82rem;
  padding: 0 12px 0 38px; outline: none; transition: all 0.2s;
}
.field-input::placeholder { color: rgba(255,140,0,0.4); }
.field-input:focus { border-color: rgba(255,140,0,0.6); background: rgba(255,255,255,0.95); box-shadow: 0 0 0 3px rgba(255,140,0,0.1); }
.field-select { -webkit-appearance: none; appearance: none; cursor: pointer; }
.field-select option { background: #ffffff; }
.reset-btn {
  display: flex; align-items: center; justify-content: center; gap: 7px;
  height: 44px; width: 100%; padding: 0 16px; border-radius: 4px;
  background: rgba(255,140,0,0.05); border: 1px solid rgba(255,140,0,0.2);
  color: rgba(255,140,0,0.7);
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s;
}
.reset-btn svg { width: 13px; height: 13px; }
.reset-btn:hover { border-color: rgba(255,140,0,0.5); color: #ff8c00; background: rgba(255,140,0,0.1); }

/* Table */
.table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table thead tr {
  background: rgba(255,140,0,0.08);
  border-bottom: 1px solid rgba(255,140,0,0.15);
}
.data-table th {
  padding: 12px 16px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem;
  color: rgba(255,140,0,0.7); letter-spacing: 0.18em; font-weight: 400;
  white-space: nowrap; text-align: left;
}
.data-row {
  border-bottom: 1px solid rgba(255,140,0,0.08);
  transition: background 0.15s;
}
.data-row:hover { background: rgba(255,140,0,0.04); }
.data-row td { padding: 12px 16px; font-size: 0.88rem; color: #333333; vertical-align: middle; }
.mono { font-family: 'Share Tech Mono', monospace; font-size: 0.78rem; }
.id-cell { color: rgba(255,140,0,0.7); }
.email-cell { color: rgba(100,100,100,0.7); font-size: 0.72rem !important; }

.student-name { display: flex; align-items: center; gap: 10px; }
.student-avatar {
  width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, rgba(255,140,0,0.2), rgba(255,165,0,0.1));
  border: 1px solid rgba(255,140,0,0.3);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 0.85rem; color: #ff8c00;
}

.prog-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.65rem; letter-spacing: 0.08em;
  padding: 3px 10px; border-radius: 3px;
  background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3); color: #ff8c00;
}

.status-badge {
  display: inline-flex; align-items: center; gap: 6px;
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; letter-spacing: 0.1em;
  padding: 4px 10px; border-radius: 3px; border: 1px solid;
}
.status-badge .status-dot { width: 6px; height: 6px; border-radius: 50%; animation: pulse 2s ease-in-out infinite; }
.status-badge.normal { background: rgba(0,255,136,0.08); border-color: rgba(0,255,136,0.25); color: #00ff88; }
.status-badge.normal .status-dot { background: #00ff88; box-shadow: 0 0 6px #00ff88; }
.status-badge.at-risk { background: rgba(255,68,68,0.08); border-color: rgba(255,68,68,0.3); color: #ff4444; }
.status-badge.at-risk .status-dot { background: #ff4444; box-shadow: 0 0 6px #ff4444; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:0.4} }

.row-actions { display: flex; gap: 5px; }
.row-btn {
  width: 30px; height: 30px; border-radius: 4px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.18s; text-decoration: none; border: 1px solid;
  background: none;
}
.row-btn svg { width: 13px; height: 13px; }
.row-btn.view { border-color: rgba(255,140,0,0.2); color: rgba(255,140,0,0.6); }
.row-btn.view:hover { border-color: #ff8c00; color: #ff8c00; background: rgba(255,140,0,0.1); }
.row-btn.edit { border-color: rgba(255,184,0,0.2); color: rgba(255,184,0,0.5); }
.row-btn.edit:hover { border-color: #ffb800; color: #ffb800; background: rgba(255,184,0,0.1); }
.row-btn.delete { border-color: rgba(255,68,68,0.2); color: rgba(255,68,68,0.5); }
.row-btn.delete:hover { border-color: #ff4444; color: #ff4444; background: rgba(255,68,68,0.1); }

.empty-row {
  text-align: center; padding: 3rem !important;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.4); letter-spacing: 0.15em;
  display: flex; flex-direction: column; align-items: center; gap: 12px;
}
.empty-row svg { width: 28px; height: 28px; }

/* Pagination */
.pagination-bar {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 1.3rem;
  border-top: 1px solid rgba(255,140,0,0.1);
}
.page-info {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem;
  color: rgba(255,140,0,0.5); letter-spacing: 0.1em;
}
.page-controls { display: flex; gap: 4px; }
.page-btn {
  min-width: 32px; height: 32px; padding: 0 8px;
  border-radius: 4px;
  background: rgba(255,255,255,0.8); border: 1px solid rgba(255,140,0,0.2);
  color: rgba(255,140,0,0.6);
  font-family: 'Share Tech Mono', monospace; font-size: 0.75rem;
  cursor: pointer; transition: all 0.18s;
}
.page-btn:hover:not(:disabled) { border-color: rgba(255,140,0,0.5); color: #ff8c00; background: rgba(255,140,0,0.08); }
.page-btn.active { background: rgba(255,140,0,0.15); border-color: rgba(255,140,0,0.5); color: #ff8c00; }
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

.dark .email-cell {
  color: rgba(156, 163, 175, 0.7);
}

.dark .page-btn {
  background: rgba(30, 41, 59, 0.8);
  border-color: rgba(249, 115, 22, 0.2);
  color: rgba(249, 115, 22, 0.6);
}

.dark .page-btn:hover:not(:disabled) {
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
  background: rgba(249, 115, 22, 0.08);
}

.dark .page-btn.active {
  background: rgba(249, 115, 22, 0.15);
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
}

.dark .pagination-bar {
  border-top-color: rgba(249, 115, 22, 0.1);
}

/* Responsive */
@media (max-width: 900px) {
  .filter-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .filter-grid { grid-template-columns: 1fr; }
  .topbar-title { display: none; }
  .email-cell { display: none; }
}
</style>