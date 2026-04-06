<template>
  <header class="app-header">
    <div class="header-content">
      <!-- Logo/Brand -->
      <div class="header-brand">
        <router-link to="/dashboard" class="brand-link">
          <div class="brand-icon">
            <img src="/image-removebg-preview (1).png" alt="University Logo" class="brand-logo" />
          </div>
          <div class="brand-text">
            <span class="brand-sys">SYS://CCS.EDU</span>
            <span class="brand-name">CCS COMPREHENSIVE</span>
          </div>
        </router-link>
      </div>

      <!-- Header Actions -->
      <div class="header-actions">
        <!-- Search Bar -->
        <div class="search-container">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search..." 
            class="search-input"
            @keyup.enter="handleSearch"
          >
          <button class="search-btn" @click="handleSearch">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
          </button>
        </div>

        <!-- Notifications -->
        <button class="header-btn notification-btn" @click="toggleNotifications">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
        </button>

        <!-- Theme Toggle -->
        <button @click="themeStore.toggleTheme()" class="header-btn theme-btn">
          <svg v-if="!themeStore.isDark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
        </button>

        <!-- User Menu -->
        <div class="user-menu" :class="{ active: userMenuOpen }" @click="toggleUserMenu">
          <div class="user-avatar">{{ user?.name?.charAt(0) ?? 'U' }}</div>
          <div class="user-info">
            <span class="user-name">{{ user?.name }}</span>
            <span class="user-role">{{ user?.role?.toUpperCase() ?? 'USER' }}</span>
          </div>
          <svg class="dropdown-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
          
          <div class="user-dropdown">
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
            <button class="dropdown-item danger" @click.prevent="handleLogout">
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
    </div>

    <!-- Notifications Dropdown -->
    <div v-if="showNotifications" class="notifications-dropdown">
      <div class="notifications-header">
        <h3>Notifications</h3>
        <button class="mark-all-read" @click="markAllRead">Mark all as read</button>
      </div>
      <div class="notifications-list">
        <div v-for="notification in notifications" :key="notification.id" 
             class="notification-item" :class="{ unread: !notification.read }">
          <div class="notification-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <div class="notification-content">
            <div class="notification-title">{{ notification.title }}</div>
            <div class="notification-message">{{ notification.message }}</div>
            <div class="notification-time">{{ notification.time }}</div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()

const user = computed(() => authStore.user)
const searchQuery = ref('')
const userMenuOpen = ref(false)
const showNotifications = ref(false)
const unreadCount = ref(3)

const notifications = ref([
  {
    id: 1,
    title: 'New Violation Report',
    message: 'Jose Reyes has 3 pending violations',
    time: '2 hours ago',
    read: false
  },
  {
    id: 2,
    title: 'System Update',
    message: 'Database backup completed successfully',
    time: '5 hours ago',
    read: false
  },
  {
    id: 3,
    title: 'Welcome',
    message: 'Welcome to CCS Comprehensive Profiling System',
    time: '1 day ago',
    read: true
  }
])

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Implement search functionality
    console.log('Searching for:', searchQuery.value)
  }
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
  showNotifications.value = false
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  userMenuOpen.value = false
}

const markAllRead = () => {
  notifications.value.forEach(n => n.read = true)
  unreadCount.value = 0
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Close dropdowns when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('.user-menu') && !target.closest('.notification-btn')) {
    userMenuOpen.value = false
    showNotifications.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.app-header {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 50;
}

.dark .app-header {
  background: #1e293b;
  border-bottom-color: #334155;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  height: 64px;
  max-width: 100%;
}

/* Brand */
.header-brand .brand-link {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.brand-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: rgba(253, 126, 20, 0.1);
  border: 1px solid rgba(253, 126, 20, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fd7e14;
}

.brand-logo {
  width: 26px;
  height: 26px;
  object-fit: contain;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-sys {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.6rem;
  color: rgba(253, 126, 20, 0.45);
  letter-spacing: 0.1em;
}

.brand-name {
  font-size: 0.9rem;
  font-weight: 700;
  color: #333333;
  letter-spacing: 0.08em;
  line-height: 1.2;
}

.dark .brand-name {
  color: #f9fafb;
}

/* Header Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Search */
.search-container {
  display: flex;
  align-items: center;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  min-width: 300px;
}

.dark .search-container {
  background: #374151;
  border-color: #4b5563;
}

.search-input {
  flex: 1;
  padding: 8px 12px;
  border: none;
  background: none;
  outline: none;
  font-size: 0.875rem;
  color: #111827;
}

.dark .search-input {
  color: #f9fafb;
}

.search-input::placeholder {
  color: #9ca3af;
}

.search-btn {
  padding: 8px;
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  transition: color 0.2s;
}

.search-btn:hover {
  color: #f97316;
}

.search-btn svg {
  width: 16px;
  height: 16px;
}

/* Header Buttons */
.header-btn {
  position: relative;
  padding: 8px;
  background: none;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
}

.dark .header-btn {
  border-color: #4b5563;
  color: #9ca3af;
}

.header-btn:hover {
  border-color: #f97316;
  color: #f97316;
  background: rgba(249, 115, 22, 0.05);
}

.header-btn svg {
  width: 18px;
  height: 18px;
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #ef4444;
  color: white;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 2px 5px;
  border-radius: 10px;
  min-width: 16px;
  text-align: center;
}

/* User Menu */
.user-menu {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 10px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
}

.dark .user-menu {
  border-color: #4b5563;
}

.user-menu:hover {
  border-color: #f97316;
  background: rgba(249, 115, 22, 0.05);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.3), rgba(255, 146, 43, 0.2));
  border: 1px solid rgba(253, 126, 20, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Rajdhani', sans-serif;
  font-weight: 700;
  font-size: 0.9rem;
  color: #fd7e14;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 0.8rem;
  font-weight: 600;
  color: #111827;
  line-height: 1.2;
}

.dark .user-name {
  color: #f9fafb;
}

.user-role {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.58rem;
  color: #6b7280;
  letter-spacing: 0.1em;
}

.dark .user-role {
  color: #9ca3af;
}

.dropdown-arrow {
  width: 14px;
  height: 14px;
  color: #6b7280;
  transition: transform 0.2s;
}

.user-menu.active .dropdown-arrow {
  transform: rotate(180deg);
}

/* User Dropdown */
.user-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  min-width: 200px;
  padding: 4px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s;
}

.dark .user-dropdown {
  background: #1e293b;
  border-color: #334155;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
}

.user-menu.active .user-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s;
  border: none;
  background: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
}

.dark .dropdown-item {
  color: #f9fafb;
}

.dropdown-item:hover {
  background: rgba(253, 126, 20, 0.1);
  color: #f97316;
}

.dropdown-item.danger:hover {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.dropdown-item svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.dropdown-divider {
  border-top: 1px solid #e5e7eb;
  margin: 4px 0;
}

.dark .dropdown-divider {
  border-color: #334155;
}

/* Notifications Dropdown */
.notifications-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 100px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  width: 350px;
  max-height: 400px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

.dark .notifications-dropdown {
  background: #1e293b;
  border-color: #334155;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
}

.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.dark .notifications-header {
  border-color: #334155;
}

.notifications-header h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.dark .notifications-header h3 {
  color: #f9fafb;
}

.mark-all-read {
  font-size: 0.75rem;
  color: #f97316;
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 500;
}

.mark-all-read:hover {
  text-decoration: underline;
}

.notifications-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.2s;
}

.dark .notification-item {
  border-color: #374151;
}

.notification-item:hover {
  background: #f9fafb;
}

.dark .notification-item:hover {
  background: #374151;
}

.notification-item.unread {
  background: rgba(253, 126, 20, 0.02);
  border-left: 3px solid #f97316;
}

.notification-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(253, 126, 20, 0.1);
  color: #f97316;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.notification-icon svg {
  width: 16px;
  height: 16px;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 2px;
}

.dark .notification-title {
  color: #f9fafb;
}

.notification-message {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 4px;
  line-height: 1.4;
}

.notification-time {
  font-size: 0.7rem;
  color: #9ca3af;
}

/* Responsive */
@media (max-width: 1024px) {
  .header-content {
    padding: 0 1rem;
  }
  
  .search-container {
    min-width: 250px;
  }
  
  .brand-text {
    display: none;
  }
}

@media (max-width: 768px) {
  .header-content {
    padding: 0 1rem;
  }
  
  .search-container {
    min-width: 200px;
  }
  
  .user-info {
    display: none;
  }
  
  .notifications-dropdown {
    right: 10px;
    width: 300px;
  }
  
  .header-actions {
    gap: 0.75rem;
  }
}

@media (max-width: 640px) {
  .header-content {
    padding: 0 0.75rem;
    height: 60px;
  }
  
  .search-container {
    display: none;
  }
  
  .header-actions {
    gap: 0.5rem;
  }
  
  .header-btn {
    padding: 6px;
  }
  
  .user-menu {
    padding: 4px 8px;
  }
  
  .user-avatar {
    width: 28px;
    height: 28px;
    font-size: 0.8rem;
  }
  
  .notifications-dropdown {
    width: calc(100vw - 2rem);
    right: 0.75rem;
    left: 0.75rem;
  }
}

@media (max-width: 480px) {
  .brand-icon {
    width: 32px;
    height: 32px;
  }
  
  .brand-logo {
    width: 20px;
    height: 20px;
  }
  
  .header-btn svg {
    width: 16px;
    height: 16px;
  }
}
</style>
