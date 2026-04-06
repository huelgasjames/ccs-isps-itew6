<template>
  <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <span class="bc-prompt">$</span>
      <router-link to="/dashboard" class="bc-item">HOME</router-link>
      <span class="bc-sep">›</span>
      <span class="bc-current">DASHBOARD</span>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-left">
          <div class="welcome-avatar">{{ user?.name?.charAt(0) ?? 'U' }}</div>
          <div>
            <h1 class="welcome-name">Welcome, <span class="accent">{{ user?.name }}</span></h1>
            <p class="welcome-sub">
              <span class="tag-chip">{{ user?.role === 'admin' ? 'Administrator' : user?.role === 'professor' ? 'Professor' : 'Student' }}</span>
              <span class="tag-chip">CCS Department</span>
            </p>
          </div>
        </div>
        <div class="welcome-right">
          <div class="time-display">
            <div class="time-value">{{ currentTime }}</div>
            <div class="time-date">{{ currentDate }}</div>
          </div>
        </div>
      </div>

      <!-- Page header -->
      <div class="section-header">
        <div>
          <h2 class="section-title">Dashboard Overview</h2>
        </div>
        <div class="header-actions">
          <button class="action-btn" @click="fetchStats">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Refresh
          </button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon cyan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ stats.totalStudents }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Total Professors</div>
            <div class="stat-value">{{ stats.totalProfessors }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Pending Violations</div>
            <div class="stat-value">{{ stats.pendingViolations }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">At Risk Students</div>
            <div class="stat-value">{{ stats.atRiskStudents }}</div>
          </div>
        </div>
      </div>

      <!-- Activity & System Status -->
      <div class="two-col-grid">
        <!-- Recent Activity -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">Recent Activity</div>
          </div>
          <div class="panel-body">
            <div v-for="i in 3" :key="i" class="activity-row">
              <div class="activity-dot" :style="`--dot-color: ${['#00c8ff','#00ff88','#ffb800'][i-1]}`"></div>
              <div class="activity-info">
                <div class="activity-title">New student registered</div>
                <div class="activity-sub">BSIT-1A • John Doe</div>
              </div>
              <div class="activity-time">{{ i }}h ago</div>
            </div>
          </div>
        </div>

        <!-- System Status -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">System Status</div>
            <span class="status-badge">All Systems Nominal</span>
          </div>
          <div class="panel-body">
            <div class="sys-row">
              <div class="sys-info">
                <span class="sys-dot online"></span>
                <span class="sys-name">API Server</span>
              </div>
              <span class="sys-status online">Operational</span>
            </div>
            <div class="sys-row">
              <div class="sys-info">
                <span class="sys-dot online"></span>
                <span class="sys-name">Frontend</span>
              </div>
              <span class="sys-status online">Active</span>
            </div>
            <div class="sys-row">
              <div class="sys-info">
                <span class="sys-dot warn"></span>
                <span class="sys-name">Storage</span>
              </div>
              <span class="sys-status warn">78% Full</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions (Admin only) -->
      <div v-if="isAdmin" class="panel mb-section">
        <div class="panel-header">
          <div class="panel-title">Quick Actions</div>
          <span class="admin-badge">Admin Only</span>
        </div>
        <div class="panel-body">
          <div class="actions-grid">
            <router-link to="/students/create" class="action-card cyan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="12" y1="14" x2="12" y2="20"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
              <span>Add Student</span>
            </router-link>
            <router-link to="/professors/create" class="action-card green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/><line x1="9" y1="10" x2="15" y2="10"/></svg>
              <span>Add Professor</span>
            </router-link>
            <router-link to="/violations/create" class="action-card amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              <span>Add Violation</span>
            </router-link>
            <router-link to="/students/at-risk" class="action-card red">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span>View At Risk</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Recent Announcements -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">Recent Announcements</div>
          <router-link to="/announcements" class="view-all-btn">View All</router-link>
        </div>
        <div class="panel-body">
          <div v-if="announcements.length === 0" class="empty-announcements">
            <p>No recent announcements</p>
          </div>
          <div v-else class="announcements-list">
            <div v-for="announcement in announcements" :key="announcement.id" class="announcement-item">
              <div class="announcement-header">
                <span class="announcement-title">{{ announcement.title }}</span>
                <span class="announcement-priority" :class="announcement.priority">{{ announcement.priority }}</span>
              </div>
              <div class="announcement-excerpt">{{ announcement.excerpt }}</div>
              <div class="announcement-meta">
                <span class="announcement-date">{{ formatDate(announcement.publish_date) }}</span>
                <span class="announcement-type">{{ announcement.type }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Navigation -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">Quick Navigation</div>
        </div>
        <div class="panel-body">
          <div class="nav-cards-grid">
            <router-link v-if="isAdmin" to="/students" class="nav-card">
              <div class="nav-card-icon cyan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <div class="nav-card-body">
                <div class="nav-card-title">Students</div>
                <div class="nav-card-desc">Manage student profiles</div>
                <div class="nav-card-footer">
                  <span class="nav-badge cyan">{{ stats.totalStudents }} Total</span>
                  <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
              </div>
            </router-link>

            <router-link v-if="isAdmin" to="/professors" class="nav-card">
              <div class="nav-card-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              </div>
              <div class="nav-card-body">
                <div class="nav-card-title">Professors</div>
                <div class="nav-card-desc">Manage professor profiles</div>
                <div class="nav-card-footer">
                  <span class="nav-badge green">{{ stats.totalProfessors }} Total</span>
                  <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
              </div>
            </router-link>

            <router-link v-if="isAdmin" to="/violations" class="nav-card">
              <div class="nav-card-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              </div>
              <div class="nav-card-body">
                <div class="nav-card-title">Violations</div>
                <div class="nav-card-desc">Track student violations</div>
                <div class="nav-card-footer">
                  <span class="nav-badge amber">{{ stats.pendingViolations }} Pending</span>
                  <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
              </div>
            </router-link>

            <router-link to="/profile" class="nav-card">
              <div class="nav-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <div class="nav-card-body">
                <div class="nav-card-title">My Profile</div>
                <div class="nav-card-desc">View and edit your profile</div>
                <div class="nav-card-footer">
                  <span class="nav-badge blue">Personal</span>
                  <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/services/api'
import type { Announcement } from '@/types/announcement'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()
const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const isStudent = computed(() => authStore.isStudent)
const isProfessor = computed(() => authStore.isProfessor)

const now = ref(new Date())
let timer: any
const currentTime = computed(() => now.value.toLocaleTimeString('en-US', { hour12: false }))
const currentDate = computed(() => now.value.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }))

const stats = ref({ totalStudents: 0, totalProfessors: 0, pendingViolations: 0, atRiskStudents: 0 })
const announcements = ref<Announcement[]>([])

const fetchStats = async () => {
  try {
    if (isAdmin.value) {
      const studentsRes = await api.get('/students')
      const professorsRes = await api.get('/professors')
      stats.value = {
        totalStudents: studentsRes.data.length,
        totalProfessors: professorsRes.data.length,
        pendingViolations: 0,
        atRiskStudents: 0
      }
      try {
        const v = await api.get('/violations/pending')
        stats.value.pendingViolations = v.data.length
      } catch {}
      try {
        const r = await api.get('/students/at-risk')
        stats.value.atRiskStudents = r.data.length
      } catch {
        stats.value.atRiskStudents = studentsRes.data.filter((s: any) => s.is_at_risk).length
      }
    }
  } catch {
    stats.value = { totalStudents: 0, totalProfessors: 0, pendingViolations: 0, atRiskStudents: 0 }
  }
}

const fetchAnnouncements = async () => {
  try {
    const response = await api.get('/announcements/recent?limit=3')
    announcements.value = response.data
  } catch (error) {
    console.error('Error fetching announcements:', error)
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchStats()
  fetchAnnouncements()
  timer = setInterval(() => { now.value = new Date() }, 1000)
})
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* Breadcrumb */
.breadcrumb-bar {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.8rem; color: #6b7280;
  margin-bottom: 1.5rem;
}
.bc-prompt { color: #f97316; }
.bc-item { color: #6b7280; text-decoration: none; }
.bc-item:hover { color: #f97316; }
.bc-sep { color: #d1d5db; }
.bc-current { color: #374151; font-weight: 500; }

/* Welcome Banner */
.welcome-banner {
  position: relative; padding: 1.5rem 1.5rem;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px; margin-bottom: 1.5rem;
  display: flex; align-items: center; justify-content: space-between; gap: 2rem;
}

.dark .welcome-banner {
  background: #1e293b;
  border-color: #334155;
}
.welcome-left { display: flex; align-items: center; gap: 1rem; }
.welcome-avatar {
  width: 48px; height: 48px; border-radius: 50%;
  background: linear-gradient(135deg, #f97316, #fb923c);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; font-weight: 600; color: white;
  flex-shrink: 0;
}
.welcome-name { font-size: 1.5rem; font-weight: 600; color: #111827; margin-bottom: 4px; }
.welcome-name .accent { color: #f97316; }
.welcome-sub { display: flex; gap: 8px; flex-wrap: wrap; }
.tag-chip {
  font-size: 0.75rem; padding: 4px 10px; border-radius: 4px;
  background: #f3f4f6; border: 1px solid #e5e7eb;
  color: #6b7280;
}
.welcome-right { flex-shrink: 0; }
.time-display {
  text-align: right;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px; padding: 10px 14px;
}
.time-value { font-size: 1.25rem; font-weight: 600; color: #111827; }
.time-date { font-size: 0.75rem; color: #6b7280; margin-top: 2px; }

/* Section header */
.section-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 1.2rem;
}
.section-title { font-size: 1.25rem; font-weight: 600; color: #111827; }
.header-actions { display: flex; gap: 8px; }
.action-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 6px;
  background: white;
  border: 1px solid #e5e7eb;
  color: #374151;
  font-size: 0.85rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
}
.action-btn svg { width: 14px; height: 14px; }
.action-btn:hover { background: #f9fafb; border-color: #f97316; color: #f97316; }

/* Stat cards */
.stats-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;
  margin-bottom: 1.5rem;
}
.stat-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px; padding: 1.25rem;
  display: flex; align-items: center; gap: 1rem;
  transition: all 0.2s;
}

.dark .stat-card {
  background: #1e293b;
  border-color: #334155;
}
.stat-card:hover {
  border-color: #f97316;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.stat-icon {
  width: 44px; height: 44px; border-radius: 8px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.stat-icon svg { width: 20px; height: 20px; }
.stat-icon.cyan { background: #fef3c7; color: #f59e0b; }
.stat-icon.green { background: #d1fae5; color: #10b981; }
.stat-icon.amber { background: #ffedd5; color: #f97316; }
.stat-icon.red { background: #fee2e2; color: #ef4444; }
.stat-label { font-size: 0.75rem; color: #6b7280; margin-bottom: 4px; }
.stat-value { font-size: 1.75rem; font-weight: 600; color: #111827; }
.dark .stat-value { color: #f9fafb; }

/* Two-col grid */
.two-col-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }

/* Panel */
.panel {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px; overflow: hidden;
}

.dark .panel {
  background: #1e293b;
  border-color: #334155;
}
.mb-section { margin-bottom: 1.5rem; }
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
}
.dark .panel-header { border-color: #334155; }
.panel-title {
  font-size: 0.95rem; font-weight: 600; color: #111827;
}

.dark .panel-title {
  color: #f9fafb;
}
.panel-body { padding: 1rem 1.25rem; }

/* Status badge */
.status-badge {
  font-size: 0.75rem; font-weight: 500;
  padding: 4px 10px; border-radius: 12px;
  background: #d1fae5; color: #059669;
}
.admin-badge {
  font-size: 0.75rem; font-weight: 500;
  padding: 4px 10px; border-radius: 12px;
  background: #fef3c7; color: #d97706;
}

/* Activity */
.activity-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f3f4f6;
}
.dark .activity-row { border-color: #374151; }
.activity-row:last-child { border-bottom: none; }
.activity-dot {
  width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
  background: var(--dot-color);
}
.activity-info { flex: 1; }
.activity-title { font-size: 0.9rem; font-weight: 500; color: #111827; margin-bottom: 2px; }
.dark .activity-title { color: #f9fafb; }
.activity-sub { font-size: 0.75rem; color: #9ca3af; }
.activity-time { font-size: 0.75rem; color: #9ca3af; }

/* System status */
.sys-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f3f4f6;
}
.dark .sys-row { border-color: #374151; }
.sys-row:last-child { border-bottom: none; }
.sys-info { display: flex; align-items: center; gap: 10px; }
.sys-dot {
  width: 8px; height: 8px; border-radius: 50%;
}
.sys-dot.online { background: #10b981; }
.sys-dot.warn { background: #f59e0b; }
.sys-name { font-size: 0.9rem; font-weight: 500; color: #111827; }
.dark .sys-name { color: #f9fafb; }
.sys-status {
  font-size: 0.75rem; font-weight: 500;
  padding: 3px 8px; border-radius: 4px;
}
.sys-status.online { background: #d1fae5; color: #059669; }
.sys-status.warn { background: #fef3c7; color: #d97706; }

/* Actions grid */
.actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.action-card {
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
  padding: 1.25rem 1rem; border-radius: 8px; text-decoration: none;
  font-size: 0.8rem; font-weight: 500;
  border: 1px solid; transition: all 0.2s;
}
.action-card svg { width: 20px; height: 20px; }
.action-card.cyan { background: #fffbeb; border-color: #fcd34d; color: #d97706; }
.action-card.cyan:hover { background: #fef3c7; transform: translateY(-2px); }
.action-card.green { background: #ecfdf5; border-color: #6ee7b7; color: #059669; }
.action-card.green:hover { background: #d1fae5; transform: translateY(-2px); }
.action-card.amber { background: #fff7ed; border-color: #fdba74; color: #ea580c; }
.action-card.amber:hover { background: #ffedd5; transform: translateY(-2px); }
.action-card.red { background: #fef2f2; border-color: #fca5a5; color: #dc2626; }
.action-card.red:hover { background: #fee2e2; transform: translateY(-2px); }

/* Nav cards */
.nav-cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.nav-card {
  display: flex; flex-direction: column; gap: 12px;
  padding: 1.2rem; border-radius: 8px; text-decoration: none;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  transition: all 0.2s;
}

.dark .nav-card {
  background: #1e293b;
  border-color: #334155;
}

.nav-card:hover { border-color: #f97316; background: #f9fafb; transform: translateY(-2px); }
.dark .nav-card:hover { border-color: #f97316; background: #334155; }
.nav-card-icon {
  width: 40px; height: 40px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
}
.nav-card-icon svg { width: 18px; height: 18px; }
.nav-card-icon.cyan { background: #fffbeb; color: #f59e0b; }
.nav-card-icon.green { background: #ecfdf5; color: #10b981; }
.nav-card-icon.amber { background: #fff7ed; color: #f97316; }
.nav-card-icon.blue { background: #eff6ff; color: #3b82f6; }
.nav-card-title { font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 4px; }
.dark .nav-card-title { color: #f9fafb; }
.nav-card-desc { font-size: 0.8rem; color: #6b7280; line-height: 1.4; }
.dark .nav-card-desc { color: #9ca3af; }
.nav-card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
.nav-badge {
  font-size: 0.75rem; font-weight: 500;
  padding: 3px 8px; border-radius: 4px;
}
.nav-badge.cyan { background: #fffbeb; color: #d97706; }
.nav-badge.green { background: #ecfdf5; color: #059669; }
.nav-badge.amber { background: #fff7ed; color: #c2410c; }
.nav-badge.blue { background: #eff6ff; color: #2563eb; }
.nav-arrow { width: 16px; height: 16px; color: #d1d5db; transition: transform 0.2s; }
.nav-card:hover .nav-arrow { transform: translateX(3px); color: #f97316; }

/* Responsive */
@media (max-width: 1100px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .actions-grid { grid-template-columns: repeat(2, 1fr); }
  .nav-cards-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .two-col-grid { grid-template-columns: 1fr; }
  .welcome-banner { flex-direction: column; }
  .welcome-right { width: 100%; }
  .time-display { text-align: left; }
  .sidebar { 
    transform: translateX(-100%); 
    transition: transform 0.3s ease;
  }
  .sidebar.open { transform: translateX(0); }
  .main-content { 
    margin-left: 0; 
    max-width: 100vw; 
  }
  .stats-grid { grid-template-columns: 1fr 1fr; }
}

/* Announcements Section */
.view-all-btn {
  font-size: 0.875rem; color: #f97316; text-decoration: none;
  font-weight: 500; transition: color 0.2s;
}
.view-all-btn:hover { color: #ea580c; }

.empty-announcements {
  text-align: center; color: #6b7280; padding: 2rem;
}

.announcements-list {
  display: flex; flex-direction: column; gap: 1rem;
}

.announcement-item {
  padding: 1rem; background: #f9fafb; border-radius: 8px;
  border-left: 3px solid #e5e7eb; transition: all 0.2s;
}

.announcement-item:hover {
  background: #f3f4f6; border-left-color: #f97316;
}

.announcement-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 0.5rem;
}

.announcement-title {
  font-weight: 600; color: #111827; font-size: 0.875rem;
  line-height: 1.3; flex: 1;
}

.announcement-priority {
  font-size: 0.7rem; font-weight: 500; padding: 2px 6px;
  border-radius: 4px; text-transform: uppercase;
}

.announcement-priority.urgent {
  background: #fee2e2; color: #dc2626;
}

.announcement-priority.high {
  background: #fef3c7; color: #d97706;
}

.announcement-priority.medium {
  background: #dbeafe; color: #2563eb;
}

.announcement-priority.low {
  background: #d1fae5; color: #059669;
}

.announcement-excerpt {
  color: #6b7280; font-size: 0.8rem; line-height: 1.4;
  margin-bottom: 0.5rem;
}

.announcement-meta {
  display: flex; gap: 1rem; font-size: 0.75rem; color: #9ca3af;
}

.announcement-date, .announcement-type {
  display: flex; align-items: center; gap: 4px;
}

@media (max-width: 480px) {
  .stats-grid, .actions-grid, .nav-cards-grid { grid-template-columns: 1fr; }
}
</style>