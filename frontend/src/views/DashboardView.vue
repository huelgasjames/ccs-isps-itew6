<template>
  <div class="dashboard-root">
    <!-- Background layers -->
    <div class="bg-grid"></div>
    <div class="bg-scanlines"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- ── Navbar ── -->
    <nav class="topbar">
      <div class="topbar-inner">
        <!-- Brand -->
        <router-link to="/dashboard" class="topbar-brand">
          <div class="brand-icon">
            <img src="/image-removebg-preview (1).png" alt="University Logo" class="brand-logo-img" />
          </div>
          <div class="brand-text">
            <span class="brand-sys">SYS://CCS.EDU</span>
            <span class="brand-name">CCS COMPREHENSIVE PROFILING</span>
          </div>
        </router-link>

        <!-- Nav links -->
        <div class="topbar-nav">
          <router-link to="/dashboard" class="nav-pill active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
          </router-link>
          <div v-if="isAdmin" class="nav-pill has-dropdown">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Users
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            <div class="dropdown-panel">
              <router-link to="/students" class="dropdown-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Students
              </router-link>
              <router-link to="/professors" class="dropdown-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Professors
              </router-link>
            </div>
          </div>
          <div v-if="isAdmin" class="nav-pill has-dropdown">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Instruction
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            <div class="dropdown-panel">
              <router-link to="/courses" class="dropdown-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Courses
              </router-link>
              <router-link to="/syllabi" class="dropdown-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Syllabi
              </router-link>
            </div>
          </div>
          <div v-if="isAdmin" class="nav-pill has-dropdown">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Scheduling
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            <div class="dropdown-panel">
              <router-link to="/schedules" class="dropdown-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Class Schedules
              </router-link>
              <router-link to="/rooms" class="dropdown-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Rooms
              </router-link>
            </div>
          </div>
          <router-link to="/events" class="nav-pill">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Events
          </router-link>
          <router-link v-if="isAdmin" to="/violations" class="nav-pill">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Violations
          </router-link>
          <router-link to="/profile" class="nav-pill">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profile
          </router-link>
        </div>

        <!-- User menu -->
        <div class="topbar-user has-dropdown">
          <div class="user-avatar">{{ user?.name?.charAt(0) ?? 'U' }}</div>
          <div class="user-info">
            <span class="user-name">{{ user?.name }}</span>
            <span class="user-role">{{ user?.role?.toUpperCase() ?? 'USER' }}</span>
          </div>
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          <div class="dropdown-panel dropdown-right">
            <router-link to="/profile" class="dropdown-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              My Profile
            </router-link>
            <router-link to="/settings" class="dropdown-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 0 0 2.93 19.07M4.93 4.93a10 10 0 0 0 14.14 14.14"/></svg>
              Settings
            </router-link>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item danger" @click.prevent="handleLogout">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Logout
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- ── Main Content ── -->
    <main class="main-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb-bar">
        <span class="bc-prompt">$</span>
        <router-link to="/dashboard" class="bc-item">HOME</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">DASHBOARD</span>
      </div>

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="corner-deco tl"></div>
        <div class="corner-deco tr"></div>
        <div class="corner-deco bl"></div>
        <div class="corner-deco br"></div>
        <div class="welcome-left">
          <div class="welcome-avatar">{{ user?.name?.charAt(0) ?? 'U' }}</div>
          <div>
            <div class="welcome-eyebrow">ACCESS GRANTED — IDENTITY VERIFIED</div>
            <h1 class="welcome-name">Welcome back, <span class="accent">{{ user?.name }}</span></h1>
            <p class="welcome-sub">
              <span class="tag-chip">{{ user?.role === 'admin' ? 'System Administrator' : user?.role === 'professor' ? 'Professor' : 'Student' }}</span>
              <span class="tag-chip">College of Computing Studies</span>
            </p>
          </div>
        </div>
        <div class="welcome-right">
          <div class="time-display">
            <div class="time-label">SYSTEM TIME</div>
            <div class="time-value">{{ currentTime }}</div>
            <div class="time-date">{{ currentDate }}</div>
          </div>
        </div>
      </div>

      <!-- Page header -->
      <div class="section-header">
        <div>
          <div class="section-eyebrow">REAL-TIME MONITORING & MANAGEMENT</div>
          <h2 class="section-title">Dashboard Overview</h2>
        </div>
        <div class="header-actions">
          <button class="action-btn" @click="fetchStats">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            REFRESH
          </button>
          <button class="action-btn secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            EXPORT
          </button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card" style="--card-accent: #00c8ff; --card-delay: 0s">
          <div class="stat-icon cyan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">TOTAL STUDENTS</div>
            <div class="stat-value">{{ stats.totalStudents }}</div>
            <div class="stat-trend up">▲ 12% from last month</div>
          </div>
          <div class="stat-glow cyan"></div>
        </div>

        <div class="stat-card" style="--card-accent: #00ff88; --card-delay: 0.08s">
          <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">TOTAL PROFESSORS</div>
            <div class="stat-value">{{ stats.totalProfessors }}</div>
            <div class="stat-trend up">▲ 5% from last month</div>
          </div>
          <div class="stat-glow green"></div>
        </div>

        <div class="stat-card" style="--card-accent: #ffb800; --card-delay: 0.16s">
          <div class="stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">PENDING VIOLATIONS</div>
            <div class="stat-value">{{ stats.pendingViolations }}</div>
            <div class="stat-trend neutral">◆ No change</div>
          </div>
          <div class="stat-glow amber"></div>
        </div>

        <div class="stat-card" style="--card-accent: #ff4444; --card-delay: 0.24s">
          <div class="stat-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">AT RISK STUDENTS</div>
            <div class="stat-value">{{ stats.atRiskStudents }}</div>
            <div class="stat-trend down">▲ 8% increase</div>
          </div>
          <div class="stat-glow red"></div>
        </div>
      </div>

      <!-- Activity & System Status -->
      <div class="two-col-grid">
        <!-- Recent Activity -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#00c8ff" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              RECENT ACTIVITY
            </div>
            <button class="icon-btn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
            </button>
          </div>
          <div class="panel-body">
            <div v-for="i in 3" :key="i" class="activity-row">
              <div class="activity-dot" :style="`--dot-color: ${['#00c8ff','#00ff88','#ffb800'][i-1]}`"></div>
              <div class="activity-info">
                <div class="activity-title">New student registered</div>
                <div class="activity-sub">BSIT-1A • John Doe</div>
              </div>
              <div class="activity-time">{{ i }}H AGO</div>
            </div>
          </div>
        </div>

        <!-- System Status -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="#00ff88" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              SYSTEM STATUS
            </div>
            <span class="status-badge">ALL SYSTEMS NOMINAL</span>
          </div>
          <div class="panel-body">
            <div class="sys-row">
              <div class="sys-info">
                <span class="sys-dot online"></span>
                <span class="sys-name">API Server</span>
              </div>
              <span class="sys-status online">OPERATIONAL</span>
            </div>
            <div class="sys-row">
              <div class="sys-info">
                <span class="sys-dot online"></span>
                <span class="sys-name">Database</span>
              </div>
              <span class="sys-status online">CONNECTED</span>
            </div>
            <div class="sys-row">
              <div class="sys-info">
                <span class="sys-dot warn"></span>
                <span class="sys-name">Storage</span>
              </div>
              <span class="sys-status warn">78% FULL</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions (Admin only) -->
      <div v-if="isAdmin" class="panel mb-section">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ffb800" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            QUICK ACTIONS
          </div>
          <span class="admin-badge">ADMIN ONLY</span>
        </div>
        <div class="panel-body">
          <div class="actions-grid">
            <router-link to="/students/create" class="action-card cyan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="12" y1="14" x2="12" y2="20"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
              <span>ADD STUDENT</span>
            </router-link>
            <router-link to="/professors/create" class="action-card green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/><line x1="9" y1="10" x2="15" y2="10"/></svg>
              <span>ADD PROFESSOR</span>
            </router-link>
            <router-link to="/violations/create" class="action-card amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              <span>ADD VIOLATION</span>
            </router-link>
            <router-link to="/students/at-risk" class="action-card red">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span>VIEW AT RISK</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Quick Navigation -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="#00c8ff" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            QUICK NAVIGATION
          </div>
        </div>
        <div class="panel-body">
          <div class="nav-cards-grid">
            <router-link v-if="isAdmin" to="/students" class="nav-card">
              <div class="nav-card-icon cyan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <div class="nav-card-body">
                <div class="nav-card-title">Students</div>
                <div class="nav-card-desc">Manage student profiles and information</div>
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
                <div class="nav-card-desc">Manage professor profiles and information</div>
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
                <div class="nav-card-desc">Track and manage student violations</div>
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
                <div class="nav-card-desc">View and update your profile information</div>
                <div class="nav-card-footer">
                  <span class="nav-badge blue">Personal</span>
                  <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()
const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const isStudent = computed(() => authStore.isStudent)
const isProfessor = computed(() => authStore.isProfessor)

const now = ref(new Date())
let timer: any
const currentTime = computed(() => now.value.toLocaleTimeString('en-US', { hour12: false }))
const currentDate = computed(() => now.value.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }))

const stats = ref({ totalStudents: 0, totalProfessors: 0, pendingViolations: 0, atRiskStudents: 0 })

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

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
    stats.value = { totalStudents: 2, totalProfessors: 1, pendingViolations: 3, atRiskStudents: 1 }
  }
}

onMounted(() => {
  fetchStats()
  timer = setInterval(() => { now.value = new Date() }, 1000)
})
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Root & Background ── */
.dashboard-root {
  min-height: 100vh;
  background: #f8f9fa;
  font-family: 'Rajdhani', sans-serif;
  color: #333333;
  position: relative;
}

.bg-grid {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background-image:
    linear-gradient(rgba(253, 126, 20, 0.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(253, 126, 20, 0.035) 1px, transparent 1px);
  background-size: 40px 40px;
}
.bg-scanlines {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.03) 2px, rgba(0,0,0,0.03) 4px);
}
.orb { position: fixed; border-radius: 50%; filter: blur(90px); z-index: 0; pointer-events: none; }
.orb-1 {
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(253, 126, 20, 0.08), transparent 70%);
  top: -150px; left: -150px;
  animation: orbDrift 10s ease-in-out infinite alternate;
}
.orb-2 {
  width: 350px; height: 350px;
  background: radial-gradient(circle, rgba(255, 146, 43, 0.07), transparent 70%);
  bottom: 0; right: 10%;
  animation: orbDrift 14s ease-in-out infinite alternate-reverse;
}
.orb-3 {
  width: 250px; height: 250px;
  background: radial-gradient(circle, rgba(253, 126, 20, 0.05), transparent 70%);
  top: 50%; left: 50%;
  animation: orbDrift 18s ease-in-out infinite alternate;
}
@keyframes orbDrift { from { transform: translate(0,0); } to { transform: translate(50px, 30px); } }

/* ── Topbar ── */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(248, 249, 250, 0.95);
  border-bottom: 1px solid rgba(253, 126, 20, 0.12);
  backdrop-filter: blur(12px);
}
.topbar-inner {
  display: flex; align-items: center; gap: 1.5rem;
  padding: 0 1.5rem; height: 60px;
  max-width: 100%;
}

/* Brand */
.topbar-brand {
  display: flex; align-items: center; gap: 10px;
  text-decoration: none; flex-shrink: 0;
}
.brand-icon {
  width: 36px; height: 36px; border-radius: 6px;
  background: rgba(253, 126, 20, 0.1);
  border: 1px solid rgba(253, 126, 20, 0.3);
  display: flex; align-items: center; justify-content: center;
  color: #fd7e14;
}
.brand-logo-img {
  width: 24px;
  height: 24px;
  object-fit: contain;
}
.brand-text { display: flex; flex-direction: column; }
.brand-sys { font-family: 'Share Tech Mono', monospace; font-size: 0.58rem; color: rgba(253, 126, 20, 0.45); letter-spacing: 0.1em; }
.brand-name { font-size: 0.95rem; font-weight: 700; color: #333333; letter-spacing: 0.12em; line-height: 1; }

/* Nav pills */
.topbar-nav { display: flex; align-items: center; gap: 4px; flex: 1; }
.nav-pill {
  display: flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 4px;
  font-size: 0.82rem; font-weight: 600; letter-spacing: 0.1em;
  color: rgba(51, 51, 51, 0.55); text-decoration: none;
  transition: all 0.2s; cursor: pointer;
  position: relative;
}
.nav-pill:hover, .nav-pill.active {
  background: rgba(253, 126, 20, 0.08);
  color: #fd7e14;
}
.nav-pill.active { border-bottom: 2px solid #fd7e14; border-radius: 4px 4px 0 0; }
.nav-pill svg { width: 14px; height: 14px; }
.chevron { width: 12px; height: 12px; }

/* Dropdowns */
.has-dropdown { position: relative; }
.dropdown-panel {
  display: none; position: absolute; top: calc(100% + 4px); left: 0;
  background: rgba(255, 255, 255, 0.98);
  border: 1px solid rgba(253, 126, 20, 0.2);
  border-radius: 6px; min-width: 180px;
  padding: 6px; z-index: 200;
  backdrop-filter: blur(10px);
}
.dropdown-right { left: auto; right: 0; }
.has-dropdown:hover .dropdown-panel { display: block; }
.dropdown-item {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 12px; border-radius: 4px;
  font-size: 0.82rem; font-weight: 600; letter-spacing: 0.06em;
  color: rgba(51, 51, 51, 0.7); text-decoration: none;
  transition: all 0.15s;
}
.dropdown-item svg { width: 14px; height: 14px; }
.dropdown-item:hover { background: rgba(253, 126, 20, 0.1); color: #fd7e14; }
.dropdown-item.danger:hover { background: rgba(255, 50, 50, 0.1); color: #ff6666; }
.dropdown-divider { border-top: 1px solid rgba(253, 126, 20, 0.1); margin: 4px 0; }

/* User */
.topbar-user {
  display: flex; align-items: center; gap: 10px;
  padding: 6px 10px; border-radius: 6px;
  cursor: pointer; transition: all 0.2s;
  border: 1px solid transparent;
  flex-shrink: 0;
}
.topbar-user:hover { background: rgba(253, 126, 20, 0.06); border-color: rgba(253, 126, 20, 0.15); }
.user-avatar {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.3), rgba(255, 146, 43, 0.2));
  border: 1px solid rgba(253, 126, 20, 0.4);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Rajdhani', sans-serif; font-weight: 700; font-size: 0.9rem; color: #fd7e14;
}
.user-info { display: flex; flex-direction: column; }
.user-name { font-size: 0.85rem; font-weight: 700; color: #333333; letter-spacing: 0.05em; }
.user-role { font-family: 'Share Tech Mono', monospace; font-size: 0.58rem; color: rgba(253, 126, 20, 0.4); letter-spacing: 0.1em; }

/* ── Main Content ── */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

/* Breadcrumb */
.breadcrumb-bar {
  display: flex; align-items: center; gap: 8px;
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.7rem; color: rgba(253, 126, 20, 0.4);
  letter-spacing: 0.12em; margin-bottom: 1.5rem;
}
.bc-prompt { color: #fd7e14; }
.bc-item { color: rgba(253, 126, 20, 0.5); text-decoration: none; }
.bc-item:hover { color: #fd7e14; }
.bc-sep { color: rgba(253, 126, 20, 0.25); }
.bc-current { color: rgba(253, 126, 20, 0.7); }

/* Welcome Banner */
.welcome-banner {
  position: relative; padding: 1.8rem 2rem;
  background: linear-gradient(135deg, rgba(248, 249, 250, 0.85) 0%, rgba(233, 236, 239, 0.9) 100%);
  border: 1px solid rgba(253, 126, 20, 0.12);
  border-radius: 6px; margin-bottom: 1.5rem;
  display: flex; align-items: center; justify-content: space-between; gap: 2rem;
  overflow: hidden;
}
.welcome-banner::before {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(90deg, rgba(253, 126, 20, 0.04) 0%, transparent 60%);
  pointer-events: none;
}
.corner-deco { position: absolute; width: 16px; height: 16px; border-color: rgba(253, 126, 20, 0.4); border-style: solid; }
.corner-deco.tl { top: 10px; left: 10px; border-width: 1.5px 0 0 1.5px; }
.corner-deco.tr { top: 10px; right: 10px; border-width: 1.5px 1.5px 0 0; }
.corner-deco.bl { bottom: 10px; left: 10px; border-width: 0 0 1.5px 1.5px; }
.corner-deco.br { bottom: 10px; right: 10px; border-width: 0 1.5px 1.5px 0; }
.welcome-left { display: flex; align-items: center; gap: 1.2rem; }
.welcome-avatar {
  width: 56px; height: 56px; border-radius: 50%;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.25), rgba(255, 146, 43, 0.1));
  border: 1px solid rgba(253, 126, 20, 0.4);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; font-weight: 700; color: #fd7e14;
  box-shadow: 0 0 20px rgba(253, 126, 20, 0.1);
  flex-shrink: 0;
}
.welcome-eyebrow {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.62rem; color: rgba(253, 126, 20, 0.45); letter-spacing: 0.18em; margin-bottom: 4px;
}
.welcome-name { font-size: 1.7rem; font-weight: 700; color: #333333; margin-bottom: 8px; }
.welcome-name .accent {
  color: transparent;
  background: linear-gradient(90deg, #fd7e14, #ff922b);
  -webkit-background-clip: text; background-clip: text;
}
.welcome-sub { display: flex; gap: 8px; flex-wrap: wrap; }
.tag-chip {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.65rem; padding: 3px 10px; border-radius: 3px;
  background: rgba(253, 126, 20, 0.07); border: 1px solid rgba(253, 126, 20, 0.2);
  color: rgba(253, 126, 20, 0.6); letter-spacing: 0.08em;
}
.welcome-right { flex-shrink: 0; }
.time-display {
  text-align: right;
  background: rgba(253, 126, 20, 0.04);
  border: 1px solid rgba(253, 126, 20, 0.1);
  border-radius: 4px; padding: 12px 16px;
}
.time-label { font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; color: rgba(253, 126, 20, 0.4); letter-spacing: 0.15em; margin-bottom: 4px; }
.time-value { font-family: 'Share Tech Mono', monospace; font-size: 1.4rem; color: #fd7e14; letter-spacing: 0.08em; }
.time-date { font-size: 0.72rem; color: rgba(51, 51, 51, 0.4); margin-top: 4px; }

/* Section header */
.section-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 1.2rem;
}
.section-eyebrow {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.62rem; color: rgba(253, 126, 20, 0.4); letter-spacing: 0.18em; margin-bottom: 4px;
}
.section-title { font-size: 1.4rem; font-weight: 700; color: #333333; letter-spacing: 0.06em; }
.header-actions { display: flex; gap: 8px; }
.action-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 16px; border-radius: 4px;
  background: rgba(253, 126, 20, 0.08);
  border: 1px solid rgba(253, 126, 20, 0.25);
  color: #fd7e14;
  font-family: 'Rajdhani', sans-serif; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
  cursor: pointer; transition: all 0.2s;
}
.action-btn svg { width: 13px; height: 13px; }
.action-btn:hover { background: rgba(253, 126, 20, 0.14); border-color: rgba(253, 126, 20, 0.5); }
.action-btn.secondary { background: rgba(51, 51, 51, 0.04); border-color: rgba(51, 51, 51, 0.15); color: rgba(51, 51, 51, 0.5); }
.action-btn.secondary:hover { color: rgba(51, 51, 51, 0.8); border-color: rgba(51, 51, 51, 0.35); }

/* Stat cards */
.stats-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;
  margin-bottom: 1.5rem;
}
.stat-card {
  position: relative; overflow: hidden;
  background: rgba(248, 249, 250, 0.7);
  border: 1px solid rgba(253, 126, 20, 0.1);
  border-radius: 6px; padding: 1.4rem 1.2rem;
  display: flex; align-items: center; gap: 1rem;
  animation: fadeUp 0.4s ease both;
  animation-delay: var(--card-delay);
  transition: all 0.25s;
}
.stat-card:hover {
  border-color: color-mix(in srgb, var(--card-accent) 40%, transparent);
  transform: translateY(-3px);
}
@keyframes fadeUp { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }
.stat-icon {
  width: 46px; height: 46px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.stat-icon svg { width: 20px; height: 20px; }
.stat-icon.cyan { background: rgba(253, 126, 20, 0.1); border: 1px solid rgba(253, 126, 20, 0.25); color: #fd7e14; }
.stat-icon.green { background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.25); color: #00ff88; }
.stat-icon.amber { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.25); color: #ffb800; }
.stat-icon.red { background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.25); color: #ff4444; }
.stat-label { font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; color: rgba(253, 126, 20, 0.4); letter-spacing: 0.15em; margin-bottom: 4px; }
.stat-value { font-size: 2rem; font-weight: 700; color: #333333; line-height: 1; margin-bottom: 6px; }
.stat-trend { font-family: 'Share Tech Mono', monospace; font-size: 0.65rem; letter-spacing: 0.06em; }
.stat-trend.up { color: #00ff88; }
.stat-trend.down { color: #ff4444; }
.stat-trend.neutral { color: rgba(51, 51, 51, 0.35); }
.stat-glow { position: absolute; bottom: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; filter: blur(30px); pointer-events: none; }
.stat-glow.cyan { background: rgba(253, 126, 20, 0.12); }
.stat-glow.green { background: rgba(0, 255, 136, 0.12); }
.stat-glow.amber { background: rgba(255, 184, 0, 0.12); }
.stat-glow.red { background: rgba(255, 68, 68, 0.12); }

/* Two-col grid */
.two-col-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }

/* Panel */
.panel {
  background: rgba(248, 249, 250, 0.7);
  border: 1px solid rgba(253, 126, 20, 0.1);
  border-radius: 6px; overflow: hidden;
}
.mb-section { margin-bottom: 1.5rem; }
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.3rem 0.8rem;
  border-bottom: 1px solid rgba(253, 126, 20, 0.07);
}
.panel-title {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.85rem; font-weight: 700; color: #333333; letter-spacing: 0.12em;
}
.panel-title svg { width: 15px; height: 15px; }
.panel-body { padding: 1rem 1.3rem; }
.icon-btn {
  background: none; border: 1px solid rgba(253, 126, 20, 0.12); border-radius: 4px;
  width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
  color: rgba(253, 126, 20, 0.4); cursor: pointer; transition: all 0.2s;
}
.icon-btn svg { width: 14px; height: 14px; }
.icon-btn:hover { border-color: rgba(253, 126, 20, 0.35); color: #fd7e14; }

/* Status badge */
.status-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem;
  padding: 4px 10px; border-radius: 20px; letter-spacing: 0.1em;
  background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.3); color: #00ff88;
}
.admin-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem;
  padding: 4px 10px; border-radius: 20px; letter-spacing: 0.1em;
  background: rgba(253, 126, 20, 0.1); border: 1px solid rgba(253, 126, 20, 0.3); color: #fd7e14;
}

/* Activity */
.activity-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid rgba(253, 126, 20, 0.06);
}
.activity-row:last-child { border-bottom: none; }
.activity-dot {
  width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
  background: var(--dot-color);
  box-shadow: 0 0 8px var(--dot-color);
}
.activity-info { flex: 1; }
.activity-title { font-size: 0.85rem; font-weight: 600; color: #333333; margin-bottom: 2px; }
.activity-sub { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: rgba(253, 126, 20, 0.35); }
.activity-time { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: rgba(51, 51, 51, 0.3); letter-spacing: 0.08em; }

/* System status */
.sys-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid rgba(253, 126, 20, 0.06);
}
.sys-row:last-child { border-bottom: none; }
.sys-info { display: flex; align-items: center; gap: 10px; }
.sys-dot {
  width: 8px; height: 8px; border-radius: 50%;
  animation: pulse 2s ease-in-out infinite;
}
.sys-dot.online { background: #00ff88; box-shadow: 0 0 8px #00ff88; }
.sys-dot.warn { background: #ffb800; box-shadow: 0 0 8px #ffb800; }
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.4; } }
.sys-name { font-size: 0.85rem; font-weight: 600; color: #333333; }
.sys-status {
  font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; letter-spacing: 0.1em;
  padding: 3px 10px; border-radius: 3px;
}
.sys-status.online { background: rgba(0, 255, 136, 0.08); border: 1px solid rgba(0, 255, 136, 0.25); color: #00ff88; }
.sys-status.warn { background: rgba(255, 184, 0, 0.08); border: 1px solid rgba(255, 184, 0, 0.25); color: #ffb800; }

/* Actions grid */
.actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.action-card {
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
  padding: 1.4rem 1rem; border-radius: 5px; text-decoration: none;
  font-size: 0.78rem; font-weight: 700; letter-spacing: 0.12em;
  border: 1px solid; transition: all 0.25s;
}
.action-card svg { width: 22px; height: 22px; }
.action-card.cyan { background: rgba(253, 126, 20, 0.07); border-color: rgba(253, 126, 20, 0.2); color: #fd7e14; }
.action-card.cyan:hover { background: rgba(253, 126, 20, 0.14); border-color: rgba(253, 126, 20, 0.5); transform: translateY(-3px); }
.action-card.green { background: rgba(0, 255, 136, 0.07); border-color: rgba(0, 255, 136, 0.2); color: #00ff88; }
.action-card.green:hover { background: rgba(0, 255, 136, 0.14); border-color: rgba(0, 255, 136, 0.5); transform: translateY(-3px); }
.action-card.amber { background: rgba(255, 184, 0, 0.07); border-color: rgba(255, 184, 0, 0.2); color: #ffb800; }
.action-card.amber:hover { background: rgba(255, 184, 0, 0.14); border-color: rgba(255, 184, 0, 0.5); transform: translateY(-3px); }
.action-card.red { background: rgba(255, 68, 68, 0.07); border-color: rgba(255, 68, 68, 0.2); color: #ff4444; }
.action-card.red:hover { background: rgba(255, 68, 68, 0.14); border-color: rgba(255, 68, 68, 0.5); transform: translateY(-3px); }

/* Nav cards */
.nav-cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.nav-card {
  display: flex; flex-direction: column; gap: 12px;
  padding: 1.2rem; border-radius: 5px; text-decoration: none;
  background: rgba(248, 249, 250, 0.5);
  border: 1px solid rgba(253, 126, 20, 0.1);
  transition: all 0.25s;
}
.nav-card:hover { border-color: rgba(253, 126, 20, 0.3); background: rgba(233, 236, 239, 0.5); transform: translateY(-3px); }
.nav-card-icon {
  width: 38px; height: 38px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
.nav-card-icon svg { width: 16px; height: 16px; }
.nav-card-icon.cyan { background: rgba(253, 126, 20, 0.1); border: 1px solid rgba(253, 126, 20, 0.25); color: #fd7e14; }
.nav-card-icon.green { background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.25); color: #00ff88; }
.nav-card-icon.amber { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.25); color: #ffb800; }
.nav-card-icon.blue { background: rgba(80, 150, 255, 0.1); border: 1px solid rgba(80, 150, 255, 0.25); color: #5096ff; }
.nav-card-title { font-size: 0.95rem; font-weight: 700; color: #333333; letter-spacing: 0.06em; margin-bottom: 4px; }
.nav-card-desc { font-size: 0.78rem; color: rgba(51, 51, 51, 0.45); line-height: 1.4; }
.nav-card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
.nav-badge {
  font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; letter-spacing: 0.08em;
  padding: 2px 8px; border-radius: 3px; border: 1px solid;
}
.nav-badge.cyan { background: rgba(253, 126, 20, 0.08); border-color: rgba(253, 126, 20, 0.2); color: #fd7e14; }
.nav-badge.green { background: rgba(0, 255, 136, 0.08); border-color: rgba(0, 255, 136, 0.2); color: #00ff88; }
.nav-badge.amber { background: rgba(255, 184, 0, 0.08); border-color: rgba(255, 184, 0, 0.2); color: #ffb800; }
.nav-badge.blue { background: rgba(80, 150, 255, 0.08); border-color: rgba(80, 150, 255, 0.2); color: #5096ff; }
.nav-arrow { width: 14px; height: 14px; color: rgba(253, 126, 20, 0.4); transition: transform 0.2s; }
.nav-card:hover .nav-arrow { transform: translateX(3px); color: #fd7e14; }

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
  .topbar-nav { display: none; }
  .stats-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 480px) {
  .stats-grid, .actions-grid, .nav-cards-grid { grid-template-columns: 1fr; }
}
</style>