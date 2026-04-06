<template>
  <nav class="app-navbar">
    <div class="navbar-content">
      <!-- Mobile Menu Toggle -->
      <button class="mobile-menu-toggle" :class="{ active: mobileMenuOpen }" @click="toggleMobileMenu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>

      <!-- Navigation Menu -->
      <div class="nav-menu" :class="{ 'mobile-open': mobileMenuOpen }">
        <!-- Main Navigation -->
        <div class="nav-section">
          <h3 class="nav-section-title">Main</h3>
          <div class="nav-items">
            <router-link to="/dashboard" class="nav-item" :class="{ active: isActive('/dashboard') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
              </svg>
              <span>Dashboard</span>
            </router-link>

            <router-link to="/profile" class="nav-item" :class="{ active: isActive('/profile') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span>Profile</span>
            </router-link>
          </div>
        </div>

        <!-- User Management (Admin Only) -->
        <div v-if="isAdmin" class="nav-section">
          <h3 class="nav-section-title">User Management</h3>
          <div class="nav-items">
            <router-link to="/students" class="nav-item" :class="{ active: isActive('/students') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span>Students</span>
            </router-link>

            <router-link to="/professors" class="nav-item" :class="{ active: isActive('/professors') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
              </svg>
              <span>Professors</span>
            </router-link>
          </div>
        </div>

        <!-- Academic Management (Admin Only) -->
        <div v-if="isAdmin" class="nav-section">
          <h3 class="nav-section-title">Academic</h3>
          <div class="nav-items">
            <router-link to="/courses" class="nav-item" :class="{ active: isActive('/courses') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
              </svg>
              <span>Courses</span>
            </router-link>

            <router-link to="/syllabi" class="nav-item" :class="{ active: isActive('/syllabi') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
              <span>Syllabi</span>
            </router-link>

            <router-link to="/schedules" class="nav-item" :class="{ active: isActive('/schedules') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              <span>Schedules</span>
            </router-link>

            <router-link to="/rooms" class="nav-item" :class="{ active: isActive('/rooms') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
              </svg>
              <span>Rooms</span>
            </router-link>
          </div>
        </div>

        <!-- Activities -->
        <div class="nav-section">
          <h3 class="nav-section-title">Activities</h3>
          <div class="nav-items">
            <router-link to="/events" class="nav-item" :class="{ active: isActive('/events') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              <span>Events</span>
            </router-link>

            <router-link to="/announcements" class="nav-item" :class="{ active: isActive('/announcements') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
              <span>Announcements</span>
            </router-link>

            <router-link v-if="isAdmin" to="/violations" class="nav-item" :class="{ active: isActive('/violations') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
              </svg>
              <span>Violations</span>
            </router-link>
          </div>
        </div>

        <!-- Quick Actions -->
        <div v-if="isAdmin" class="nav-section">
          <h3 class="nav-section-title">Quick Actions</h3>
          <div class="quick-actions">
            <button class="action-btn primary" @click="$router.push('/students/create')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="8.5" cy="7" r="4"/>
                <line x1="20" y1="8" x2="20" y2="14"/>
                <line x1="23" y1="11" x2="17" y2="11"/>
              </svg>
              <span>Add Student</span>
            </button>

            <button class="action-btn success" @click="$router.push('/professors/create')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
                <line x1="9" y1="10" x2="15" y2="10"/>
              </svg>
              <span>Add Professor</span>
            </button>

            <button class="action-btn warning" @click="$router.push('/violations/create')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
              </svg>
              <span>Add Violation</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Overlay -->
      <div v-if="mobileMenuOpen" class="mobile-overlay" :class="{ active: mobileMenuOpen }" @click="toggleMobileMenu"></div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()

const mobileMenuOpen = ref(false)
const isAdmin = computed(() => authStore.isAdmin)

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  
  // Add body scroll lock when menu is open
  if (mobileMenuOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}
</script>

<style scoped>
.app-navbar {
  position: fixed;
  top: 64px; /* Header height */
  left: 0;
  bottom: 0;
  width: 280px;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  overflow-y: auto;
  z-index: 40;
}

.dark .app-navbar {
  background: #1e293b;
  border-right-color: #334155;
}

.navbar-content {
  height: 100%;
  padding: 1.5rem 1rem;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
  display: none;
  position: fixed;
  top: 80px;
  left: 1rem;
  z-index: 60;
  padding: 10px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dark .mobile-menu-toggle {
  background: #1e293b;
  border-color: #4b5563;
  color: #9ca3af;
}

.mobile-menu-toggle:hover {
  border-color: #f97316;
  color: #f97316;
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(249, 115, 22, 0.2);
}

.mobile-menu-toggle:active {
  transform: scale(0.95);
}

.mobile-menu-toggle.active {
  border-color: #f97316;
  color: #f97316;
  background: rgba(249, 115, 22, 0.1);
}

.mobile-menu-toggle svg {
  width: 20px;
  height: 20px;
  transition: transform 0.3s ease;
}

.mobile-menu-toggle.active svg {
  transform: rotate(90deg);
}

/* Navigation Menu */
.nav-menu {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Navigation Sections */
.nav-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.nav-section-title {
  font-size: 0.75rem;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  padding: 0 0.5rem;
  margin: 0;
}

.dark .nav-section-title {
  color: #6b7280;
}

/* Navigation Items */
.nav-items {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 8px;
  text-decoration: none;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
  position: relative;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

.dark .nav-item {
  color: #9ca3af;
}

.nav-item:hover {
  background: rgba(253, 126, 20, 0.08);
  color: #f97316;
}

.nav-item.active {
  background: rgba(253, 126, 20, 0.12);
  color: #f97316;
  border-left: 3px solid #f97316;
  border-radius: 0 8px 8px 0;
}

.nav-item svg {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

/* Quick Actions */
.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border: none;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
  width: 100%;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

.action-btn svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.action-btn.primary:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.action-btn.primary:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(59, 130, 246, 0.2);
}

.action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.action-btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.action-btn.success {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.action-btn.success:hover {
  background: rgba(16, 185, 129, 0.2);
  border-color: #10b981;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.action-btn.success:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(16, 185, 129, 0.2);
}

.action-btn.warning {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
  border: 1px solid rgba(245, 158, 11, 0.2);
}

.action-btn.warning:hover {
  background: rgba(245, 158, 11, 0.2);
  border-color: #f59e0b;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

.action-btn.warning:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(245, 158, 11, 0.2);
}

/* Mobile Overlay */
.mobile-overlay {
  display: none;
  position: fixed;
  top: 64px;
  left: 280px;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 35;
  backdrop-filter: blur(2px);
  transition: opacity 0.3s ease;
}

.mobile-overlay.active {
  opacity: 1;
}

/* Responsive */
@media (max-width: 1024px) {
  .app-navbar {
    width: 260px;
  }
  
  .mobile-overlay {
    left: 260px;
  }
}

@media (max-width: 768px) {
  .app-navbar {
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  }

  .app-navbar.mobile-open {
    transform: translateX(0);
  }

  .mobile-menu-toggle {
    display: flex;
  }

  .mobile-overlay {
    display: block;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }

  .mobile-overlay.active {
    opacity: 1;
    visibility: visible;
  }

  .navbar-content {
    padding: 1rem;
  }

  .nav-section-title {
    padding: 0 0.25rem;
  }

  .nav-item {
    padding: 12px 14px;
    font-size: 0.9rem;
  }

  .action-btn {
    padding: 14px 16px;
    font-size: 0.85rem;
  }
}

@media (max-width: 640px) {
  .app-navbar {
    width: 100%;
    top: 60px;
  }

  .mobile-menu-toggle {
    top: 76px;
    left: 0.75rem;
    padding: 8px;
  }

  .mobile-overlay {
    left: 100%;
    top: 60px;
  }

  .navbar-content {
    padding: 0.75rem;
  }

  .nav-section {
    gap: 0.5rem;
  }

  .nav-item {
    padding: 14px 12px;
    font-size: 0.95rem;
  }

  .action-btn {
    padding: 16px 14px;
    font-size: 0.9rem;
  }
}
</style>
