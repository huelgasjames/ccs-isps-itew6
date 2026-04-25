<template>
  <div class="announcements-view">
    <!-- Header -->
    <div class="header">
      <div class="breadcrumb">
        <router-link to="/dashboard" class="breadcrumb-link">Dashboard</router-link>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Announcements</span>
      </div>
      
      <div class="header-content">
        <div>
          <h1 class="page-title">Announcements</h1>
          <p class="page-subtitle">Stay updated with the latest news and updates</p>
        </div>
        <button 
          v-if="canCreateAnnouncements"
          @click="openCreateModal"
          class="create-btn"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          Create Announcement
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
      <div class="filters-header">
        <h3>Filters & Search</h3>
        <div class="stats">
          <span>{{ filteredAnnouncements.length }} announcements</span>
          <span>•</span>
          <span>{{ unreadCount }} unread</span>
        </div>
      </div>
      
      <div class="filters-grid">
        <div class="filter-group">
          <label>Status</label>
          <select v-model="filters.status" @change="loadAnnouncements">
            <option value="">All</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Target Audience</label>
          <select v-model="filters.target_type" @change="loadAnnouncements">
            <option value="">All</option>
            <option value="all">All Users</option>
            <option value="students">Students</option>
            <option value="professors">Professors</option>
            <option value="specific">Specific Users</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Search</label>
          <input 
            v-model="searchQuery"
            @input="debouncedSearch"
            type="text" 
            placeholder="Search announcements..."
          />
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="content-section">
      <!-- Loading State -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Loading announcements...</p>
      </div>

      <!-- Announcements List -->
      <div v-else-if="filteredAnnouncements.length > 0" class="announcements-list">
        <div 
          v-for="announcement in filteredAnnouncements"
          :key="announcement.id"
          class="announcement-card"
          :class="{ 'unread': !announcement.is_viewed }"
        >
          <div class="announcement-header">
            <div class="announcement-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
              </svg>
            </div>
            <div class="announcement-info">
              <div class="announcement-title-row">
                <h3 class="announcement-title">{{ announcement.title }}</h3>
                <div class="announcement-meta">
                  <span v-if="!announcement.is_viewed" class="unread-badge">New</span>
                  <span class="announcement-time">{{ formatDate(announcement.created_at) }}</span>
                </div>
              </div>
              <p class="announcement-author">by {{ announcement.user?.name }}</p>
            </div>
          </div>
          
          <div class="announcement-content">
            <p>{{ announcement.content }}</p>
          </div>
          
          <div v-if="announcement.image" class="announcement-image">
            <img 
              :src="announcement.image_url || `http://127.0.0.1:8000/${announcement.image}`" 
              :alt="announcement.title"
              @error="handleImageError"
            />
          </div>
          
          <div class="announcement-footer">
            <div class="announcement-stats">
              <span class="stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ announcement.view_count || 0 }} views
              </span>
              <span class="stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ getTargetTypeLabel(announcement.target_type) }}
              </span>
            </div>
            
            <div class="announcement-actions">
              <button 
                v-if="!announcement.is_viewed"
                @click="markAsViewed(announcement.id)"
                class="action-btn secondary"
              >
                Mark as read
              </button>
              
              <button 
                v-if="canEditAnnouncement(announcement)"
                @click="editAnnouncement(announcement)"
                class="action-btn"
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
                class="action-btn danger"
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

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
          </svg>
        </div>
        <h3>No announcements found</h3>
        <p>{{ searchQuery ? 'Try adjusting your search terms' : 'Get started by creating a new announcement' }}</p>
        <button 
          v-if="!searchQuery && canCreateAnnouncements"
          @click="openCreateModal"
          class="create-btn"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          Create Announcement
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <AnnouncementModal
      v-if="showModal"
      :announcement="editingAnnouncement"
      @close="closeModal"
      @saved="handleSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      v-if="deletingAnnouncement"
      title="Delete Announcement"
      :message="`Are you sure you want to delete ${deletingAnnouncement?.title || 'this announcement'}?`"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Announcement } from '@/services/announcements'
import { announcementService } from '@/services/announcements'
import { useAuthStore } from '@/stores/auth'
import AnnouncementModal from '@/views/announcements/AnnouncementModal.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const authStore = useAuthStore()

// State
const announcements = ref<Announcement[]>([])
const loading = ref(true)
const showModal = ref(false)
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

// Simple debounce implementation
const debounce = (func: Function, wait: number) => {
  let timeout: number
  return (...args: any[]) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => func(...args), wait)
  }
}

const debouncedSearch = debounce(() => {
  // Search is reactive, no additional action needed
}, 300)

// Methods
const openCreateModal = () => {
  editingAnnouncement.value = null
  showModal.value = true
}

const editAnnouncement = (announcement: Announcement) => {
  editingAnnouncement.value = announcement
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingAnnouncement.value = null
}

const handleSaved = async () => {
  await loadAnnouncements()
  closeModal()
}

const deleteAnnouncement = (announcement: Announcement) => {
  deletingAnnouncement.value = announcement
}

const confirmDelete = async () => {
  if (!deletingAnnouncement.value) return
  
  try {
    await announcementService.deleteAnnouncement(deletingAnnouncement.value.id)
    await loadAnnouncements()
  } catch (error) {
    console.error('Error deleting announcement:', error)
  } finally {
    deletingAnnouncement.value = null
  }
}

const cancelDelete = () => {
  deletingAnnouncement.value = null
}

const markAsViewed = async (announcementId: number) => {
  try {
    await announcementService.markAsViewed(announcementId)
    const announcement = announcements.value.find(a => a.id === announcementId)
    if (announcement) {
      announcement.is_viewed = true
      announcement.view_count = (announcement.view_count || 0) + 1
    }
  } catch (error) {
    console.error('Error marking as viewed:', error)
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

// Initialize
onMounted(() => {
  loadAnnouncements()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.announcements-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  font-family: 'Inter', sans-serif;
}

/* Header */
.header {
  margin-bottom: 2rem;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.breadcrumb-link {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #f97316;
}

.breadcrumb-separator {
  color: #d1d5db;
}

.breadcrumb-current {
  color: #111827;
  font-weight: 500;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.page-subtitle {
  color: #6b7280;
  margin: 0;
  font-size: 1rem;
}

.create-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #f97316;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.create-btn:hover {
  background: #ea580c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.create-btn svg {
  width: 16px;
  height: 16px;
}

/* Filters */
.filters-section {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 2rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.filters-header h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.stats {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.filter-group select,
.filter-group input {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.filter-group select:focus,
.filter-group input:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

/* Content */
.content-section {
  min-height: 400px;
}

/* Loading State */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #f97316;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Announcements List */
.announcements-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.announcement-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.announcement-card:hover {
  border-color: #f97316;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.announcement-card.unread {
  border-left: 4px solid #f97316;
  background: #fffbeb;
}

.announcement-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1rem;
}

.announcement-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(249, 115, 22, 0.1);
  color: #f97316;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.announcement-icon svg {
  width: 20px;
  height: 20px;
}

.announcement-info {
  flex: 1;
}

.announcement-title-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 0.25rem;
}

.announcement-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  line-height: 1.4;
}

.announcement-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
}

.unread-badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.125rem 0.5rem;
  background: #f97316;
  color: white;
  border-radius: 9999px;
}

.announcement-time {
  font-size: 0.75rem;
  color: #9ca3af;
}

.announcement-author {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.announcement-content {
  margin-bottom: 1rem;
}

.announcement-content p {
  color: #374151;
  line-height: 1.6;
  margin: 0;
}

.announcement-image {
  margin-bottom: 1rem;
}

.announcement-image img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 0.5rem;
}

.announcement-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.announcement-stats {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.stat svg {
  width: 16px;
  height: 16px;
}

.announcement-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.action-btn.danger:hover {
  background: #fef2f2;
  border-color: #ef4444;
  color: #ef4444;
}

.action-btn.secondary {
  background: none;
  border: none;
  color: #6b7280;
}

.action-btn.secondary:hover {
  color: #f97316;
}

.action-btn svg {
  width: 14px;
  height: 14px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #9ca3af;
}

.empty-icon svg {
  width: 100%;
  height: 100%;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  color: #6b7280;
  margin: 0 0 1.5rem 0;
}

/* Responsive */
@media (max-width: 768px) {
  .announcements-view {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .announcement-title-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .announcement-footer {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .announcement-actions {
    width: 100%;
    justify-content: flex-end;
  }
}
</style>
