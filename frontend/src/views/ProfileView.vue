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
          <div class="title-name">USER PROFILE</div>
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
        <span class="bc-current">PROFILE</span>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <span>LOADING PROFILE DATA...</span>
      </div>

      <!-- Profile Content -->
      <div v-else class="profile-container">
        <!-- Profile Header -->
        <div class="panel mb-section">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              USER IDENTITY MATRIX
            </div>
            <div class="header-actions">
              <button class="action-btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                EDIT PROFILE
              </button>
            </div>
          </div>
          <div class="panel-body">
            <div class="profile-header">
              <div class="profile-avatar">
                {{ user?.name?.charAt(0)?.toUpperCase() }}
              </div>
              <div class="profile-info">
                <h1 class="profile-name">{{ user?.name }}</h1>
                <div class="profile-email">{{ user?.email }}</div>
                <div class="profile-role">
                  <span class="role-badge">{{ user?.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'User' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Information -->
        <div class="panel mb-section">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              ACCOUNT PROTOCOLS
            </div>
          </div>
          <div class="panel-body">
            <div class="info-grid">
              <div class="info-item">
                <label class="info-label">FULL NAME</label>
                <div class="info-value">{{ user?.name || 'NOT PROVIDED' }}</div>
              </div>
              <div class="info-item">
                <label class="info-label">EMAIL ADDRESS</label>
                <div class="info-value">{{ user?.email || 'NOT PROVIDED' }}</div>
              </div>
              <div class="info-item">
                <label class="info-label">ACCESS LEVEL</label>
                <div class="info-value">
                  <span class="role-badge">{{ user?.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'NOT SPECIFIED' }}</span>
                </div>
              </div>
              <div class="info-item">
                <label class="info-label">ACCOUNT STATUS</label>
                <div class="info-value">
                  <span class="status-badge active">ACTIVE</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Role-Specific Information -->
        <div v-if="profileData" class="panel">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6 4l8 8v8H6z"/></svg>
              {{ user?.role === 'student' ? 'STUDENT' : 'PROFESSOR' }} DATA
            </div>
          </div>
          <div class="panel-body">
            <div class="info-grid">
              <div v-if="user?.role === 'student'" class="info-item">
                <label class="info-label">STUDENT ID</label>
                <div class="info-value">{{ profileData.student_unique_id || 'NOT PROVIDED' }}</div>
              </div>
              <div v-if="user?.role === 'professor'" class="info-item">
                <label class="info-label">PROFESSOR ID</label>
                <div class="info-value">{{ profileData.professor_unique_id || 'NOT PROVIDED' }}</div>
              </div>
              <div class="info-item">
                <label class="info-label">{{ user?.role === 'student' ? 'PROGRAM' : 'DEPARTMENT' }}</label>
                <div class="info-value">
                  {{ user?.role === 'student' ? (profileData.program || 'NOT SPECIFIED') : (profileData.department || 'NOT SPECIFIED') }}
                </div>
              </div>
              <div class="info-item">
                <label class="info-label">MEMBER SINCE</label>
                <div class="info-value">{{ memberSince }}</div>
              </div>
            </div>
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

const user = computed(() => authStore.user)
const profileData = ref<any>(null)
const loading = ref(true)

const memberSince = computed(() => {
  return new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
})

const fetchProfile = async () => {
  try {
    const response = await api.get('/me')
    profileData.value = response.data.profile
  } catch (error) {
    console.error('Error fetching profile:', error)
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  fetchProfile()
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
.header-actions { display: flex; gap: 8px; }

/* Profile Header */
.profile-header {
  display: flex; align-items: center; gap: 1.5rem;
}
.profile-avatar {
  width: 80px; height: 80px; border-radius: 50%;
  background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 2rem; color: #ff8c00;
}
.profile-info { flex: 1; }
.profile-name { font-size: 1.8rem; font-weight: 700; color: #333333; letter-spacing: 0.06em; margin-bottom: 4px; }
.profile-email { font-size: 0.95rem; color: rgba(51,51,51,0.6); margin-bottom: 8px; }
.profile-role { display: flex; align-items: center; gap: 8px; }

/* Badges */
.role-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.65rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px;
  background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3); color: #ff8c00;
}
.status-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.65rem; letter-spacing: 0.1em;
  padding: 4px 12px; border-radius: 3px;
}
.status-badge.active { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.3); color: #00ff88; }

/* Info Grid */
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.2rem; }
.info-item { display: flex; flex-direction: column; gap: 6px; }
.info-label {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem;
  color: rgba(255,140,0,0.6); letter-spacing: 0.18em;
}
.info-value {
  font-size: 0.95rem; font-weight: 600; color: #333333;
}

/* Loading State */
.loading-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 4rem;
  font-family: 'Share Tech Mono', monospace; font-size: 0.72rem;
  color: rgba(255,140,0,0.6); letter-spacing: 0.15em;
}
.spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,140,0,0.1); border-top-color: rgba(255,140,0,0.5);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 768px) {
  .profile-header { flex-direction: column; text-align: center; }
  .header-actions { justify-content: center; }
  .info-grid { grid-template-columns: 1fr; }
}
</style>
