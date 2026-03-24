<template>
  <div class="dashboard-root">
    <!-- ── Main Content ── -->
    <main class="main-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb-bar">
        <span class="bc-prompt">$</span>
        <router-link to="/dashboard" class="bc-item">HOME</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">ANNOUNCEMENTS</span>
      </div>

      <!-- Page header -->
      <div class="section-header">
        <div>
          <h2 class="section-title">Announcements</h2>
          <p class="page-subtitle">Stay updated with the latest news and updates</p>
        </div>
        <div class="header-actions">
          <button 
            @click="showCreateModal = true"
            class="action-btn"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 4v16m8-8H4"/>
            </svg>
            Create Announcement
          </button>
        </div>
      </div>

      <!-- Filters Panel -->
      <div class="panel mb-section">
        <div class="panel-header">
          <div class="panel-title">Filters & Search</div>
          <div class="filter-stats">
            <span>{{ filteredAnnouncements.length }} announcements</span>
            <span>•</span>
            <span>{{ unreadCount }} unread</span>
          </div>
        </div>
        <div class="panel-body">
          <div class="filters-grid">
            <div class="filter-group">
              <label class="filter-label">Status</label>
              <select 
                v-model="filters.status" 
                @change="loadAnnouncements"
                class="form-select"
              >
                <option value="">All</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
              </select>
            </div>
            
            <div class="filter-group">
              <label class="filter-label">Target Audience</label>
              <select 
                v-model="filters.target_type" 
                @change="loadAnnouncements"
                class="form-select"
              >
                <option value="">All</option>
                <option value="all">All Users</option>
                <option value="students">Students</option>
                <option value="professors">Professors</option>
                <option value="specific">Specific Users</option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label">Search</label>
              <input 
                v-model="searchQuery"
                @input="debouncedSearch"
                type="text" 
                placeholder="Search announcements..."
                class="form-input"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="panel">
        <div class="panel-body">
          <div class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          </div>
        </div>
      </div>

      <!-- Announcements List -->
      <div v-else-if="filteredAnnouncements.length > 0" class="announcements-list">
        <div 
          v-for="announcement in filteredAnnouncements"
          :key="announcement.id"
          class="announcement-item"
          :class="{ 'unread': !announcement.is_viewed }"
        >
          <div class="announcement-header">
            <div class="flex items-start gap-3">
              <div class="announcement-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between">
                  <h4 class="announcement-title">{{ announcement.title }}</h4>
                  <div class="flex items-center gap-2">
                    <span v-if="!announcement.is_viewed" class="unread-badge">New</span>
                    <span class="announcement-time">{{ formatDate(announcement.created_at) }}</span>
                  </div>
                </div>
                <p class="announcement-author">by {{ announcement.user?.name }}</p>
              </div>
            </div>
          </div>
          
          <div class="announcement-content">
            <p>{{ announcement.content }}</p>
          </div>
          
          <div v-if="announcement.image" class="announcement-image">
            <img 
              :src="`http://127.0.0.1:8000/storage/${announcement.image}`" 
              :alt="announcement.title"
              class="w-full h-32 object-cover rounded-md"
              @error="handleImageError"
            />
          </div>
          
          <div class="announcement-footer">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  {{ announcement.view_count || 0 }} views
                </span>
                <span class="flex items-center gap-1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  {{ getTargetTypeLabel(announcement.target_type) }}
                </span>
              </div>
              
              <div class="flex items-center gap-2">
                <button 
                  v-if="!announcement.is_viewed"
                  @click="markAnnouncementAsViewed(announcement.id)"
                  class="mark-read-btn"
                >
                  Mark as read
                </button>
                
                <button 
                  v-if="canEditAnnouncement(announcement)"
                  @click="editAnnouncement(announcement)"
                  class="action-btn small"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </button>
                
                <button 
                  v-if="canDeleteAnnouncement(announcement)"
                  @click="deleteAnnouncement(announcement)"
                  class="action-btn small danger"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2v2"/>
                  </svg>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="panel">
        <div class="panel-body">
          <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <h3 class="empty-title">No announcements found</h3>
            <p class="empty-message">
              {{ searchQuery ? 'Try adjusting your search terms' : 'Get started by creating a new announcement' }}
            </p>
            <div v-if="!searchQuery" class="empty-action">
              <button 
                @click="showCreateModal = true"
                class="action-btn"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 4v16m8-8H4"/>
                </svg>
                Create Announcement
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Create/Edit Modal -->
    <AnnouncementModal
      v-if="showCreateModal || editingAnnouncement"
      :announcement="editingAnnouncement"
      @close="closeModal"
      @saved="handleAnnouncementSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      v-if="deletingAnnouncement"
      title="Delete Announcement"
      :message="`Are you sure you want to delete ${deletingAnnouncement?.title || 'this announcement'}? This action cannot be undone.`"
      confirmText="Delete"
      cancelText="Cancel"
      @confirm="confirmDelete"
      @cancel="deletingAnnouncement = null"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import type { Announcement } from '@/services/announcements'
import { announcementService } from '@/services/announcements'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import AnnouncementCard from '@/components/AnnouncementCard.vue'
import AnnouncementModal from '@/components/AnnouncementModal.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()
const user = computed(() => authStore.user)

// Simple debounce implementation
const debounce = (func: Function, wait: number) => {
  let timeout: number
  return function executedFunction(...args: any[]) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

const announcements = ref<Announcement[]>([])
const loading = ref(true)
const showCreateModal = ref(false)
const editingAnnouncement = ref<Announcement | null>(null)
const deletingAnnouncement = ref<Announcement | null>(null)
const searchQuery = ref('')

const filters = ref({
  status: '',
  target_type: ''
})

const canCreateAnnouncements = computed(() => {
  return authStore.user?.role === 'admin' || authStore.user?.role === 'professor'
})

const filteredAnnouncements = computed(() => {
  let filtered = announcements.value

  // Apply status filter
  if (filters.value.status) {
    filtered = filtered.filter(a => a.status === filters.value.status)
  }

  // Apply target type filter
  if (filters.value.target_type) {
    filtered = filtered.filter(a => a.target_type === filters.value.target_type)
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(a => 
      a.title.toLowerCase().includes(query) ||
      a.content.toLowerCase().includes(query) ||
      a.user?.name.toLowerCase().includes(query)
    )
  }

  // Filter announcements user can view
  filtered = filtered.filter(a => canViewAnnouncement(a))

  // Sort by creation date (newest first)
  filtered.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())

  return filtered
})

const unreadCount = computed(() => {
  return filteredAnnouncements.value.filter(a => !a.is_viewed).length
})

const canViewAnnouncement = (announcement: Announcement) => {
  if (!authStore.user) return false
  
  // Admin can see all announcements
  if (authStore.user.role === 'admin') return true
  
  // Check if announcement is published
  if (announcement.status !== 'published') return false
  
  // Check target type
  switch (announcement.target_type) {
    case 'all':
      return true
    case 'students':
      return authStore.user.role === 'student'
    case 'professors':
      return authStore.user.role === 'professor'
    case 'specific':
      return announcement.target_users?.includes(authStore.user.id) || false
    default:
      return false
  }
}

const canEditAnnouncement = (announcement: Announcement) => {
  if (!authStore.user) return false
  return authStore.user.role === 'admin' || announcement.user_id === authStore.user.id
}

const canDeleteAnnouncement = (announcement: Announcement) => {
  if (!authStore.user) return false
  return authStore.user.role === 'admin' || announcement.user_id === authStore.user.id
}

const loadAnnouncements = async () => {
  try {
    loading.value = true
    announcements.value = await announcementService.getAnnouncements()
  } catch (error) {
    console.error('Error fetching announcements:', error)
    announcements.value = []
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  // Search is reactive, no additional action needed
}, 300)

const editAnnouncement = (announcement: Announcement) => {
  editingAnnouncement.value = announcement
  showCreateModal.value = true
}

const deleteAnnouncement = (announcement: Announcement) => {
  deletingAnnouncement.value = announcement
}

const confirmDelete = async () => {
  if (!deletingAnnouncement.value) return
  
  try {
    await announcementService.deleteAnnouncement(deletingAnnouncement.value.id)
    await loadAnnouncements()
    deletingAnnouncement.value = null
  } catch (error) {
    console.error('Error deleting announcement:', error)
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingAnnouncement.value = null
}

const handleAnnouncementSaved = async () => {
  await loadAnnouncements()
  closeModal()
}

const handleViewed = (announcementId: number) => {
  const announcement = announcements.value.find(a => a.id === announcementId)
  if (announcement) {
    announcement.is_viewed = true
    announcement.view_count = (announcement.view_count || 0) + 1
  }
}

const markAnnouncementAsViewed = async (announcementId: number) => {
  try {
    await announcementService.markAsViewed(announcementId)
    const announcement = announcements.value.find(a => a.id === announcementId)
    if (announcement) {
      announcement.is_viewed = true
      announcement.view_count = (announcement.view_count || 0) + 1
    }
  } catch (error) {
    console.error('Error marking announcement as viewed:', error)
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getTargetTypeLabel = (targetType: string) => {
  const labels = {
    all: 'All Users',
    students: 'Students',
    professors: 'Professors',
    specific: 'Specific Users'
  }
  return labels[targetType as keyof typeof labels] || targetType
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.style.display = 'none'
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Initialize
onMounted(() => {
  themeStore.initTheme()
  loadAnnouncements()
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* Root */
.dashboard-root {
  min-height: 100vh;
  background: #f9fafb;
  transition: background-color 0.3s;
}

.dark .dashboard-root { background: #0f172a; }

/* Typography */
body {
  font-family: 'Rajdhani', sans-serif;
  font-weight: 500;
  color: #374151;
  line-height: 1.6;
  letter-spacing: 0.025em;
}

.dark body { color: #e2e8f0; }

code, pre, .mono { font-family: 'Share Tech Mono', monospace; }

/* ── Topbar ── */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: #0f172a;
  border-bottom: 1px solid #1e293b;
}
.topbar-inner {
  max-width: 1400px; margin: 0 auto;
  display: flex; align-items: center; gap: 2rem;
  padding: 0.75rem 1.5rem; height: 60px;
}
.topbar-brand {
  display: flex; align-items: center; gap: 0.75rem;
  text-decoration: none; flex-shrink: 0;
}
.brand-icon {
  width: 36px; height: 36px; border-radius: 8px;
  background: linear-gradient(135deg, #f97316, #fb923c);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.brand-logo-img { width: 24px; height: 24px; object-fit: contain; }
.brand-text { display: flex; flex-direction: column; }
.brand-sys {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.65rem; font-weight: 600; color: #64748b;
  letter-spacing: 0.15em; line-height: 1;
}
.brand-name {
  font-size: 0.85rem; font-weight: 700; color: #f1f5f9;
  letter-spacing: 0.05em; line-height: 1.2;
}

/* Nav */
.topbar-nav {
  display: flex; align-items: center; gap: 0.5rem;
  flex: 1; justify-content: center;
}
.nav-pill {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.5rem 1rem; border-radius: 6px;
  font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em;
  color: #cbd5e1; text-decoration: none;
  transition: all 0.2s; cursor: pointer;
  border: 1px solid transparent;
}
.nav-pill:hover { background: rgba(251, 146, 60, 0.1); color: #fb923c; border-color: rgba(251, 146, 60, 0.2); }
.nav-pill.active {
  background: rgba(251, 146, 60, 0.15); color: #fb923c;
  border-color: rgba(251, 146, 60, 0.3);
}
.nav-pill svg { width: 16px; height: 16px; flex-shrink: 0; }

/* Dropdowns */
.has-dropdown { position: relative; }
.dropdown-panel {
  display: none; position: absolute; top: calc(100% + 4px); left: 0;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px; min-width: 140px;
  padding: 4px; z-index: 200;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.dark .dropdown-panel {
  background: #1e293b;
  border-color: #334155;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
}
.dropdown-right { left: auto; right: 0; }
.has-dropdown:hover .dropdown-panel { display: block; }
.dropdown-item {
  display: flex; align-items: center; gap: 8px;
  padding: 6px 10px; border-radius: 4px;
  font-size: 0.78rem; font-weight: 600; letter-spacing: 0.06em;
  color: #6b7280; text-decoration: none;
  transition: all 0.15s;
}

.dark .dropdown-item {
  color: #9ca3af;
}

.dropdown-item:hover { background: rgba(253, 126, 20, 0.1); color: #fd7e14; }
.dropdown-item.danger:hover { background: rgba(255, 50, 50, 0.1); color: #ff6666; }
.dropdown-divider { border-top: 1px solid rgba(253, 126, 20, 0.1); margin: 2px 0; }

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
.user-name { font-size: 0.85rem; font-weight: 700; color: #111827; letter-spacing: 0.05em; }
.dark .user-name { color: #f9fafb; }
.user-role { font-family: 'Share Tech Mono', monospace; font-size: 0.58rem; color: #6b7280; letter-spacing: 0.1em; }
.dark .user-role { color: #9ca3af; }

/* Theme Toggle */
.theme-toggle-btn {
  background: none;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
}

.dark .theme-toggle-btn {
  border-color: #334155;
  color: #9ca3af;
}

.theme-toggle-btn:hover {
  border-color: #f97316;
  color: #f97316;
  background: rgba(249, 115, 22, 0.05);
}

.theme-toggle-btn svg {
  width: 16px;
  height: 16px;
}

/* ── Main Content ── */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

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
.action-btn.small { padding: 6px 12px; font-size: 0.75rem; }
.action-btn.danger:hover { background: #fef2f2; border-color: #ef4444; color: #ef4444; }

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

/* Filter Stats */
.filter-stats {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.75rem; color: #6b7280;
}

/* Filters Grid */
.filters-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;
}
.filter-group { display: flex; flex-direction: column; gap: 0.5rem; }
.filter-label {
  font-size: 0.75rem; font-weight: 600; color: #374151;
  text-transform: uppercase; letter-spacing: 0.05em;
}
.dark .filter-label { color: #f9fafb; }
.form-select, .form-input {
  padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px;
  background: white; color: #374151;
  font-size: 0.85rem; font-weight: 500;
  transition: all 0.2s;
}
.dark .form-select, .dark .form-input {
  background: #1e293b; border-color: #334155; color: #f9fafb;
}
.form-select:focus, .form-input:focus {
  outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

/* Announcements List */
.announcements-list {
  display: flex; flex-direction: column; gap: 1rem;
}

.announcement-item {
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 0.75rem;
  background: #ffffff;
  transition: all 0.2s;
}

.dark .announcement-item {
  background: #1e293b;
  border-color: #334155;
}

.announcement-item.unread {
  border-left: 3px solid #f97316;
  background: #fffbeb;
}

.dark .announcement-item.unread {
  background: rgba(249, 115, 22, 0.05);
  border-left-color: #f97316;
}

.announcement-item:hover {
  border-color: #f97316;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.announcement-header {
  margin-bottom: 0.5rem;
}

.announcement-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(249, 115, 22, 0.1);
  color: #f97316;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.announcement-icon svg {
  width: 12px;
  height: 12px;
}

.announcement-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.125rem;
}

.dark .announcement-title {
  color: #f9fafb;
}

.announcement-author {
  font-size: 0.75rem;
  color: #6b7280;
}

.dark .announcement-author {
  color: #9ca3af;
}

.announcement-time {
  font-size: 0.625rem;
  color: #9ca3af;
}

.unread-badge {
  font-size: 0.625rem;
  font-weight: 500;
  padding: 1px 6px;
  border-radius: 8px;
  background: #f97316;
  color: white;
}

.announcement-content {
  margin-bottom: 0.5rem;
}

.announcement-content p {
  font-size: 0.75rem;
  color: #374151;
  line-height: 1.4;
}

.dark .announcement-content p {
  color: #e2e8f0;
}

.announcement-image {
  margin-bottom: 0.5rem;
}

.announcement-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 0.5rem;
  border-top: 1px solid #f3f4f6;
}

.dark .announcement-footer {
  border-top-color: #374151;
}

.mark-read-btn {
  font-size: 0.75rem;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.2s;
}

.mark-read-btn:hover {
  color: #f97316;
}

/* Empty State */
.empty-title {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.dark .empty-title {
  color: #f9fafb;
}

.empty-message {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.dark .empty-message {
  color: #9ca3af;
}

.empty-action {
  display: flex;
  justify-content: center;
}

/* Dark mode overrides */
.dark .action-btn {
  background: #1e293b;
  border-color: #334155;
  color: #e2e8f0;
}

.dark .action-btn:hover {
  background: #334155;
  border-color: #f97316;
  color: #f97316;
}
</style>
