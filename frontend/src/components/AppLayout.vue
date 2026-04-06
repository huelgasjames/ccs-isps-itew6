<template>
  <div class="app-layout" :class="{ 'dark': isDark, 'sidebar-collapsed': isCollapsed }">
    <!-- Sidebar -->
    <AppSidebar ref="sidebarRef" />
    
    <!-- Main Content Area -->
    <div class="main-wrapper">
      <!-- Top Bar (optional - can be removed if not needed) -->
      <header v-if="showTopBar" class="top-bar">
        <div class="top-bar-content">
          <!-- Mobile menu toggle -->
          <button @click="toggleMobileSidebar" class="mobile-menu-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>
          
          <!-- Breadcrumb -->
          <div class="breadcrumb">
            <span class="bc-prompt">$</span>
            <router-link to="/dashboard" class="bc-item">HOME</router-link>
            <span class="bc-sep">›</span>
            <span class="bc-current">{{ currentPageName }}</span>
          </div>
          
          <!-- Quick actions (optional) -->
          <div class="quick-actions">
            <!-- Add any quick actions here -->
          </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <main class="page-content" :class="{ 'with-top-bar': showTopBar }">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import AppSidebar from './AppSidebar.vue'

const route = useRoute()
const themeStore = useThemeStore()

const sidebarRef = ref<InstanceType<typeof AppSidebar> | null>(null)
const showTopBar = ref(true) // Set to false if you don't want a top bar
const isMobile = ref(false)

const isDark = computed(() => themeStore.isDark)
const isCollapsed = computed(() => sidebarRef.value?.isCollapsed ?? false)

// Get current page name for breadcrumb
const currentPageName = computed(() => {
  const routeName = route.name as string
  if (!routeName) return 'UNKNOWN'
  
  const names: Record<string, string> = {
    dashboard: 'DASHBOARD',
    students: 'STUDENTS',
    'student-create': 'CREATE STUDENT',
    'student-detail': 'STUDENT DETAILS',
    'student-edit': 'EDIT STUDENT',
    professors: 'PROFESSORS',
    'professor-create': 'CREATE PROFESSOR',
    'professor-detail': 'PROFESSOR DETAILS',
    'professor-edit': 'EDIT PROFESSOR',
    violations: 'VIOLATIONS',
    'violation-create': 'CREATE VIOLATION',
    'violation-detail': 'VIOLATION DETAILS',
    'violation-edit': 'EDIT VIOLATION',
    announcements: 'ANNOUNCEMENTS',
    profile: 'PROFILE',
    settings: 'SETTINGS',
    courses: 'COURSES',
    syllabi: 'SYLLABI',
    schedules: 'SCHEDULES',
    rooms: 'ROOMS',
    events: 'EVENTS'
  }
  
  return names[routeName] || routeName.toUpperCase()
})

const toggleMobileSidebar = () => {
  if (sidebarRef.value) {
    sidebarRef.value.isCollapsed = !sidebarRef.value.isCollapsed
  }
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
  // Auto-collapse sidebar on mobile
  if (isMobile.value && sidebarRef.value) {
    sidebarRef.value.isCollapsed = true
  }
}

onMounted(() => {
  themeStore.initTheme()
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: #ffffff;
  transition: background-color 0.3s;
}

.app-layout.dark {
  background: #0f172a;
}

.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-left: 260px;
  transition: margin-left 0.3s ease;
}

.sidebar-collapsed .main-wrapper {
  margin-left: 70px;
}

/* Top Bar */
.top-bar {
  position: sticky;
  top: 0;
  z-index: 50;
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  height: 60px;
}

.dark .top-bar {
  background: #1e293b;
  border-bottom-color: #334155;
}

.top-bar-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0 1.5rem;
  height: 100%;
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  padding: 8px;
  border-radius: 4px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.dark .mobile-menu-btn {
  color: #9ca3af;
}

.mobile-menu-btn:hover {
  background: rgba(253, 126, 20, 0.1);
  color: #fd7e14;
}

.mobile-menu-btn svg {
  width: 20px;
  height: 20px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.9rem;
}

.bc-prompt {
  color: #fd7e14;
  font-weight: 600;
}

.bc-item {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.dark .bc-item {
  color: #9ca3af;
}

.bc-item:hover {
  color: #fd7e14;
}

.bc-sep {
  color: #9ca3af;
}

.bc-current {
  color: #333333;
  font-weight: 600;
}

.dark .bc-current {
  color: #f1f5f9;
}

.quick-actions {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Page Content */
.page-content {
  flex: 1;
  padding: 0;
  min-height: calc(100vh - 60px);
}

.page-content.with-top-bar {
  padding: 0;
}

.page-content:not(.with-top-bar) {
  min-height: 100vh;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .main-wrapper {
    margin-left: 0;
  }
  
  .sidebar-collapsed .main-wrapper {
    margin-left: 0;
  }
  
  .mobile-menu-btn {
    display: block;
  }
  
  .breadcrumb {
    display: none; /* Hide breadcrumb on mobile to save space */
  }
}

@media (max-width: 480px) {
  .top-bar-content {
    padding: 0 1rem;
  }
}
</style>
