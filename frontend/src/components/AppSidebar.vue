<template>
  <aside class="sidebar" :class="{ 'collapsed': isCollapsed, 'dark': isDark }">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <router-link to="/dashboard" class="sidebar-brand">
        <div class="brand-icon">
          <img src="/image-removebg-preview (1).png" alt="University Logo" class="brand-logo-img" />
        </div>
        <div v-if="!isCollapsed" class="brand-text">
          <span class="brand-sys">SYS://CCS.EDU</span>
          <span class="brand-name">CCS COMPREHENSIVE</span>
        </div>
      </router-link>
      <button @click="toggleCollapse" class="collapse-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="11 19 17 12 11 5"></polyline>
        </svg>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <!-- Dashboard -->
      <router-link to="/dashboard" class="nav-item" :class="{ active: $route.name === 'dashboard' }">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7"/>
          <rect x="14" y="3" width="7" height="7"/>
          <rect x="3" y="14" width="7" height="7"/>
          <rect x="14" y="14" width="7" height="7"/>
        </svg>
        <span v-if="!isCollapsed" class="nav-text">Dashboard</span>
      </router-link>

      <!-- Users Dropdown -->
      <div v-if="isAdmin" class="nav-group" :class="{ active: isUsersActive }">
        <button @click="toggleUsersDropdown" class="nav-item nav-toggle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <span v-if="!isCollapsed" class="nav-text">Users</span>
          <svg v-if="!isCollapsed" class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div v-if="!isCollapsed && usersDropdownOpen" class="nav-submenu">
          <router-link to="/students" class="nav-subitem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>Students</span>
          </router-link>
          <router-link to="/professors" class="nav-subitem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="3" width="20" height="14" rx="2"/>
              <line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
            <span>Professors</span>
          </router-link>
        </div>
      </div>

      <!-- Instruction Dropdown -->
      <div v-if="isAdmin" class="nav-group" :class="{ active: isInstructionActive }">
        <button @click="toggleInstructionDropdown" class="nav-item nav-toggle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
          <span v-if="!isCollapsed" class="nav-text">Instruction</span>
          <svg v-if="!isCollapsed" class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div v-if="!isCollapsed && instructionDropdownOpen" class="nav-submenu">
          <router-link to="/courses" class="nav-subitem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            <span>Courses</span>
          </router-link>
          <router-link to="/syllabi" class="nav-subitem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
            <span>Syllabi</span>
          </router-link>
        </div>
      </div>

      <!-- Scheduling Dropdown -->
      <div v-if="isAdmin" class="nav-group" :class="{ active: isSchedulingActive }">
        <button @click="toggleSchedulingDropdown" class="nav-item nav-toggle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          <span v-if="!isCollapsed" class="nav-text">Scheduling</span>
          <svg v-if="!isCollapsed" class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div v-if="!isCollapsed && schedulingDropdownOpen" class="nav-submenu">
          <router-link to="/schedules" class="nav-subitem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Class Schedules</span>
          </router-link>
          <router-link to="/rooms" class="nav-subitem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span>Rooms</span>
          </router-link>
        </div>
      </div>

      <!-- Events -->
      <router-link to="/events" class="nav-item" :class="{ active: $route.name === 'events' }">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span v-if="!isCollapsed" class="nav-text">Events</span>
      </router-link>

      <!-- Violations -->
      <router-link v-if="isAdmin" to="/violations" class="nav-item" :class="{ active: $route.name === 'violations' }">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/>
          <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span v-if="!isCollapsed" class="nav-text">Violations</span>
      </router-link>

      <!-- Announcements -->
      <router-link to="/announcements" class="nav-item" :class="{ active: $route.name === 'announcements' }">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
        </svg>
        <span v-if="!isCollapsed" class="nav-text">Announcements</span>
      </router-link>

      <!-- Profile -->
      <router-link to="/profile" class="nav-item" :class="{ active: $route.name === 'profile' }">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <span v-if="!isCollapsed" class="nav-text">Profile</span>
      </router-link>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <!-- Theme Toggle -->
      <button @click="themeStore.toggleTheme()" class="footer-btn">
        <svg v-if="!isDark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="1" x2="12" y2="3"/>
          <line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
          <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/>
          <line x1="21" y1="12" x2="23" y2="12"/>
          <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
          <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <span v-if="!isCollapsed">Theme</span>
      </button>

      <!-- User Menu -->
      <div class="user-menu" :class="{ 'has-dropdown': !isCollapsed }">
        <button @click="toggleUserMenu" class="user-btn">
          <div class="user-avatar">{{ user?.name?.charAt(0) ?? 'U' }}</div>
          <div v-if="!isCollapsed" class="user-info">
            <span class="user-name">{{ user?.name }}</span>
            <span class="user-role">{{ user?.role?.toUpperCase() ?? 'USER' }}</span>
          </div>
          <svg v-if="!isCollapsed" class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div v-if="!isCollapsed && userMenuOpen" class="user-dropdown">
          <router-link to="/profile" class="dropdown-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            My Profile
          </router-link>
          <router-link to="/settings" class="dropdown-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.07 4.93A10 10 0 0 0 2.93 19.07M4.93 4.93a10 10 0 0 0 14.14 14.14"/>
            </svg>
            Settings
          </router-link>
          <div class="dropdown-divider"></div>
          <button @click="handleLogout" class="dropdown-item danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
          </button>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const themeStore = useThemeStore()

const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const isDark = computed(() => themeStore.isDark)

const isCollapsed = ref(false)
const usersDropdownOpen = ref(false)
const instructionDropdownOpen = ref(false)
const schedulingDropdownOpen = ref(false)
const userMenuOpen = ref(false)

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
  // Close all dropdowns when collapsing
  if (isCollapsed.value) {
    usersDropdownOpen.value = false
    instructionDropdownOpen.value = false
    schedulingDropdownOpen.value = false
    userMenuOpen.value = false
  }
}

const toggleUsersDropdown = () => {
  usersDropdownOpen.value = !usersDropdownOpen.value
  instructionDropdownOpen.value = false
  schedulingDropdownOpen.value = false
}

const toggleInstructionDropdown = () => {
  instructionDropdownOpen.value = !instructionDropdownOpen.value
  usersDropdownOpen.value = false
  schedulingDropdownOpen.value = false
}

const toggleSchedulingDropdown = () => {
  schedulingDropdownOpen.value = !schedulingDropdownOpen.value
  usersDropdownOpen.value = false
  instructionDropdownOpen.value = false
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Active route detection
const isUsersActive = computed(() => 
  ['students', 'student-create', 'student-detail', 'student-edit', 
   'professors', 'professor-create', 'professor-detail', 'professor-edit'].includes(route.name as string)
)

const isInstructionActive = computed(() => 
  ['courses', 'syllabi'].includes(route.name as string)
)

const isSchedulingActive = computed(() => 
  ['schedules', 'rooms'].includes(route.name as string)
)

// Auto-open dropdown based on current route
watch(() => route.name, (newRoute) => {
  if (isUsersActive.value) usersDropdownOpen.value = true
  if (isInstructionActive.value) instructionDropdownOpen.value = true
  if (isSchedulingActive.value) schedulingDropdownOpen.value = true
}, { immediate: true })
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&display=swap');

.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 260px;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  z-index: 1000;
  transition: width 0.3s ease, transform 0.3s ease;
}

.sidebar.dark {
  background: #1e293b;
  border-right-color: #334155;
}

.sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  min-height: 70px;
}

.sidebar.dark .sidebar-header {
  border-bottom-color: #334155;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  flex: 1;
}

.brand-icon {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  background: rgba(253, 126, 20, 0.1);
  border: 1px solid rgba(253, 126, 20, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fd7e14;
  flex-shrink: 0;
}

.brand-logo-img {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-sys {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.58rem;
  color: rgba(253, 126, 20, 0.45);
  letter-spacing: 0.1em;
}

.brand-name {
  font-size: 0.95rem;
  font-weight: 700;
  color: #333333;
  letter-spacing: 0.12em;
  line-height: 1;
}

.sidebar.dark .brand-name {
  color: #f1f5f9;
}

.collapse-btn {
  background: none;
  border: none;
  padding: 6px;
  border-radius: 4px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.sidebar.dark .collapse-btn {
  color: #9ca3af;
}

.collapse-btn:hover {
  background: rgba(253, 126, 20, 0.1);
  color: #fd7e14;
}

.collapse-btn svg {
  width: 16px;
  height: 16px;
  transition: transform 0.3s;
}

.sidebar.collapsed .collapse-btn svg {
  transform: rotate(180deg);
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0.75rem 1rem;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
}

.sidebar.dark .nav-item {
  color: #9ca3af;
}

.nav-item:hover {
  background: rgba(253, 126, 20, 0.08);
  color: #fd7e14;
}

.sidebar.dark .nav-item:hover {
  background: rgba(253, 126, 20, 0.15);
}

.nav-item.active {
  background: rgba(253, 126, 20, 0.12);
  color: #fd7e14;
  border-left: 3px solid #fd7e14;
}

.nav-item svg {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.nav-text {
  flex: 1;
}

.nav-toggle {
  justify-content: space-between;
}

.chevron {
  width: 14px;
  height: 14px;
  transition: transform 0.2s;
}

.nav-group.active .chevron {
  transform: rotate(180deg);
}

.nav-submenu {
  background: rgba(0, 0, 0, 0.02);
  padding-left: 1rem;
}

.sidebar.dark .nav-submenu {
  background: rgba(0, 0, 0, 0.2);
}

.nav-subitem {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0.5rem 1rem;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s;
  font-size: 0.85rem;
  margin: 2px 0;
}

.sidebar.dark .nav-subitem {
  color: #9ca3af;
}

.nav-subitem:hover {
  background: rgba(253, 126, 20, 0.08);
  color: #fd7e14;
}

.nav-subitem.active {
  background: rgba(253, 126, 20, 0.12);
  color: #fd7e14;
}

.nav-subitem svg {
  width: 16px;
  height: 16px;
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
}

.sidebar.dark .sidebar-footer {
  border-top-color: #334155;
}

.footer-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0.75rem;
  width: 100%;
  background: none;
  border: none;
  border-radius: 6px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
  font-weight: 500;
}

.sidebar.dark .footer-btn {
  color: #9ca3af;
}

.footer-btn:hover {
  background: rgba(253, 126, 20, 0.08);
  color: #fd7e14;
}

.footer-btn svg {
  width: 18px;
  height: 18px;
}

.user-menu {
  margin-top: 0.5rem;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0.75rem;
  width: 100%;
  background: none;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.user-btn:hover {
  background: rgba(253, 126, 20, 0.08);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.9rem;
  flex-shrink: 0;
}

.user-info {
  flex: 1;
  text-align: left;
}

.user-name {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #333333;
}

.sidebar.dark .user-name {
  color: #f1f5f9;
}

.user-role {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
}

.sidebar.dark .user-role {
  color: #9ca3af;
}

.user-dropdown {
  margin-top: 0.5rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.sidebar.dark .user-dropdown {
  background: #1e293b;
  border-color: #334155;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.5rem;
  color: #6b7280;
  text-decoration: none;
  border-radius: 4px;
  transition: all 0.15s;
  font-size: 0.85rem;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.sidebar.dark .dropdown-item {
  color: #9ca3af;
}

.dropdown-item:hover {
  background: rgba(253, 126, 20, 0.1);
  color: #fd7e14;
}

.dropdown-item.danger:hover {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.dropdown-item svg {
  width: 16px;
  height: 16px;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 0.5rem 0;
}

.sidebar.dark .dropdown-divider {
  background: #334155;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar:not(.collapsed) {
    transform: translateX(0);
  }
}
</style>
