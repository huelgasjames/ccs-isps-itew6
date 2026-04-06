<template>
  <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <span class="bc-prompt">$</span>
      <router-link to="/dashboard" class="bc-item">HOME</router-link>
      <span class="bc-sep">›</span>
      <span class="bc-current">ANNOUNCEMENTS</span>
    </div>

    <!-- Page Header -->
    <div class="section-header">
      <div>
        <h2 class="section-title">Announcements</h2>
        <p class="section-subtitle">Manage and view system announcements</p>
      </div>
      <div class="header-actions">
        <button class="action-btn" @click="fetchAnnouncements">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
          </svg>
          Refresh
        </button>
        <button v-if="isAdmin" class="action-btn primary" @click="$router.push('/announcements/create')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          New Announcement
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-panel">
      <div class="filter-group">
        <label class="filter-label">Search</label>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search announcements..." 
          class="filter-input"
          @input="filterAnnouncements"
        >
      </div>
      <div class="filter-group">
        <label class="filter-label">Status</label>
        <select v-model="statusFilter" class="filter-select" @change="filterAnnouncements">
          <option value="">All Status</option>
          <option value="published">Published</option>
          <option value="draft">Draft</option>
          <option value="scheduled">Scheduled</option>
          <option value="archived">Archived</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label">Priority</label>
        <select v-model="priorityFilter" class="filter-select" @change="filterAnnouncements">
          <option value="">All Priorities</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>
      </div>
    </div>

    <!-- Announcements List -->
    <div class="announcements-container">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading announcements...</p>
      </div>

      <div v-else-if="filteredAnnouncements.length === 0" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
        <h3>No announcements found</h3>
        <p>Try adjusting your filters or create a new announcement.</p>
        <button v-if="isAdmin" class="btn primary" @click="$router.push('/announcements/create')">
          Create Announcement
        </button>
      </div>

      <div v-else class="announcements-list">
        <div 
          v-for="announcement in filteredAnnouncements" 
          :key="announcement.id" 
          class="announcement-card"
          :class="[announcement.priority, announcement.status]"
        >
          <div class="announcement-header">
            <div class="announcement-meta">
              <span class="priority-badge" :class="announcement.priority">
                {{ announcement.priority.toUpperCase() }}
              </span>
              <span class="status-badge" :class="announcement.status">
                {{ announcement.status }}
              </span>
              <span class="announcement-type">{{ announcement.type }}</span>
            </div>
            <div class="announcement-actions">
              <button v-if="isAdmin" @click="editAnnouncement(announcement)" class="action-btn-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>
              <button v-if="isAdmin" @click="deleteAnnouncement(announcement)" class="action-btn-icon danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="announcement-content">
            <h3 class="announcement-title">{{ announcement.title }}</h3>
            <p class="announcement-excerpt">{{ announcement.excerpt }}</p>
            <div class="announcement-details">
              <div class="detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>{{ announcement.author }}</span>
              </div>
              <div class="detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>{{ formatDate(announcement.publish_date) }}</span>
              </div>
              <div v-if="announcement.expires_at" class="detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>Expires: {{ formatDate(announcement.expires_at) }}</span>
              </div>
            </div>
          </div>

          <div class="announcement-footer">
            <div class="announcement-stats">
              <span class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                {{ announcement.views || 0 }} views
              </span>
              <span class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                {{ announcement.comments || 0 }} comments
              </span>
            </div>
            <button @click="viewAnnouncement(announcement)" class="btn secondary">
              Read More
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import type { Announcement } from '@/types/announcement'

const router = useRouter()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.isAdmin)

const announcements = ref<Announcement[]>([])
const loading = ref(true)
const searchQuery = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')

const filteredAnnouncements = computed(() => {
  let filtered = Array.isArray(announcements.value) ? announcements.value : []

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(announcement => 
      announcement.title.toLowerCase().includes(query) ||
      announcement.excerpt.toLowerCase().includes(query) ||
      announcement.author.toLowerCase().includes(query)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(announcement => announcement.status === statusFilter.value)
  }

  if (priorityFilter.value) {
    filtered = filtered.filter(announcement => announcement.priority === priorityFilter.value)
  }

  return filtered.sort((a, b) => new Date(b.publish_date).getTime() - new Date(a.publish_date).getTime())
})

const fetchAnnouncements = async () => {
  try {
    loading.value = true
    const response = await api.get('/announcements')
    announcements.value = response.data
  } catch (error) {
    console.error('Error fetching announcements:', error)
    // Mock data for development
    announcements.value = [
      {
        id: 1,
        title: 'System Maintenance Scheduled',
        excerpt: 'The system will undergo scheduled maintenance this weekend. Please save your work and log out before the maintenance period.',
        content: 'Full content of the system maintenance announcement...',
        author: 'System Administrator',
        priority: 'high',
        status: 'published',
        type: 'System',
        publish_date: '2024-03-15T10:00:00Z',
        expires_at: '2024-03-20T23:59:59Z',
        views: 156,
        likes: 23,
        comments: 12,
        target_all: true,
        target_students: true,
        target_professors: true,
        target_admins: true,
        user_id: 1,
        created_at: '2024-03-15T10:00:00Z',
        updated_at: '2024-03-15T10:00:00Z'
      },
      {
        id: 2,
        title: 'New Academic Calendar Released',
        excerpt: 'The academic calendar for the upcoming semester has been released. Please check the important dates and deadlines.',
        content: 'Full content of the academic calendar announcement...',
        author: 'Academic Affairs',
        priority: 'medium',
        status: 'published',
        type: 'Academic',
        publish_date: '2024-03-14T14:30:00Z',
        expires_at: '2024-04-30T23:59:59Z',
        views: 89,
        likes: 15,
        comments: 5,
        target_all: true,
        target_students: true,
        target_professors: true,
        target_admins: true,
        user_id: 2,
        created_at: '2024-03-14T14:30:00Z',
        updated_at: '2024-03-14T14:30:00Z'
      },
      {
        id: 3,
        title: 'Student Portal Update',
        excerpt: 'New features have been added to the student portal including improved navigation and faster loading times.',
        content: 'Full content of the student portal update announcement...',
        author: 'IT Department',
        priority: 'low',
        status: 'published',
        type: 'Feature',
        publish_date: '2024-03-13T09:15:00Z',
        expires_at: null,
        views: 234,
        likes: 45,
        comments: 8,
        target_all: true,
        target_students: true,
        target_professors: true,
        target_admins: true,
        user_id: 3,
        created_at: '2024-03-13T09:15:00Z',
        updated_at: '2024-03-13T09:15:00Z'
      }
    ]
  } finally {
    loading.value = false
  }
}

const filterAnnouncements = () => {
  // Triggered by v-model changes
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'No expiry'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const viewAnnouncement = (announcement: Announcement) => {
  router.push(`/announcements/${announcement.id}`)
}

const editAnnouncement = (announcement: Announcement) => {
  router.push(`/announcements/${announcement.id}/edit`)
}

const deleteAnnouncement = async (announcement: Announcement) => {
  if (confirm(`Are you sure you want to delete "${announcement.title}"?`)) {
    try {
      await api.delete(`/announcements/${announcement.id}`)
      await fetchAnnouncements()
    } catch (error) {
      console.error('Error deleting announcement:', error)
      alert('Failed to delete announcement. Please try again.')
    }
  }
}

onMounted(() => {
  fetchAnnouncements()
})
</script>

<style scoped>
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

/* Section Header */
.section-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 1.5rem;
}
.section-title { font-size: 1.5rem; font-weight: 600; color: #111827; margin-bottom: 4px; }
.section-subtitle { font-size: 0.875rem; color: #6b7280; margin: 0; }
.header-actions { display: flex; gap: 8px; }
.action-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 6px;
  background: white; border: 1px solid #e5e7eb;
  color: #374151; font-size: 0.875rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}
.action-btn:hover { background: #f9fafb; border-color: #f97316; color: #f97316; transform: translateY(-1px); }
.action-btn:active { transform: translateY(0); }
.action-btn.primary { background: #f97316; border-color: #f97316; color: white; }
.action-btn.primary:hover { background: #ea580c; transform: translateY(-1px); }
.action-btn.primary:active { transform: translateY(0); }
.action-btn svg { width: 16px; height: 16px; }

/* Filters */
.filters-panel {
  display: flex; gap: 1rem; padding: 1rem; background: #f9fafb;
  border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 1.5rem;
}
.filter-group { display: flex; flex-direction: column; gap: 4px; }
.filter-label { font-size: 0.75rem; font-weight: 500; color: #374151; }
.filter-input, .filter-select {
  padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px;
  font-size: 0.875rem; min-width: 200px;
}
.filter-input:focus, .filter-select:focus { outline: none; border-color: #f97316; }

/* Loading State */
.loading-state {
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
  padding: 3rem; text-align: center;
}
.spinner {
  width: 32px; height: 32px; border: 3px solid #e5e7eb;
  border-top: 3px solid #f97316; border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* Empty State */
.empty-state {
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
  padding: 3rem; text-align: center;
}
.empty-state svg { width: 64px; height: 64px; color: #d1d5db; }
.empty-state h3 { font-size: 1.25rem; font-weight: 600; color: #374151; margin: 0; }
.empty-state p { color: #6b7280; margin: 0; }
.btn {
  padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s; border: none;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}
.btn.primary { background: #f97316; color: white; }
.btn.primary:hover { background: #ea580c; transform: translateY(-1px); }
.btn.primary:active { transform: translateY(0); }
.btn.secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.btn.secondary:hover { background: #e5e7eb; transform: translateY(-1px); }
.btn.secondary:active { transform: translateY(0); }

/* Announcements List */
.announcements-list {
  display: flex; flex-direction: column; gap: 1rem;
}

/* Announcement Card */
.announcement-card {
  background: white; border: 1px solid #e5e7eb; border-radius: 8px;
  padding: 1.5rem; transition: all 0.2s;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}
.announcement-card:hover { border-color: #f97316; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transform: translateY(-2px); }
.announcement-card:active { transform: translateY(0); }

/* Priority Colors */
.announcement-card.urgent { border-left: 4px solid #dc2626; }
.announcement-card.high { border-left: 4px solid #f59e0b; }
.announcement-card.medium { border-left: 4px solid #3b82f6; }
.announcement-card.low { border-left: 4px solid #10b981; }

/* Status Colors */
.announcement-card.draft { opacity: 0.7; background: #f9fafb; }
.announcement-card.scheduled { background: #fffbeb; }
.announcement-card.archived { opacity: 0.5; }

/* Announcement Header */
.announcement-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 1rem;
}
.announcement-meta {
  display: flex; gap: 8px; flex-wrap: wrap;
}
.priority-badge, .status-badge, .announcement-type {
  padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 500;
}
.priority-badge.urgent { background: #fee2e2; color: #dc2626; }
.priority-badge.high { background: #fef3c7; color: #d97706; }
.priority-badge.medium { background: #dbeafe; color: #2563eb; }
.priority-badge.low { background: #d1fae5; color: #059669; }
.status-badge.published { background: #d1fae5; color: #059669; }
.status-badge.draft { background: #f3f4f6; color: #6b7280; }
.status-badge.scheduled { background: #fef3c7; color: #d97706; }
.status-badge.archived { background: #e5e7eb; color: #6b7280; }
.announcement-type { background: #ede9fe; color: #7c3aed; }
.announcement-actions { display: flex; gap: 4px; }
.action-btn-icon {
  padding: 6px; background: none; border: none; border-radius: 4px;
  color: #6b7280; cursor: pointer; transition: all 0.2s;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}
.action-btn-icon:hover { background: #f3f4f6; color: #374151; transform: scale(1.1); }
.action-btn-icon:active { transform: scale(1); }
.action-btn-icon.danger:hover { background: #fee2e2; color: #dc2626; }
.action-btn-icon svg { width: 16px; height: 16px; }

/* Announcement Content */
.announcement-content { margin-bottom: 1rem; }
.announcement-title { font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0 0 0.5rem 0; }
.announcement-excerpt { color: #6b7280; line-height: 1.5; margin: 0 0 1rem 0; }
.announcement-details { display: flex; gap: 1rem; flex-wrap: wrap; }
.detail-item {
  display: flex; align-items: center; gap: 4px;
  font-size: 0.875rem; color: #6b7280;
}
.detail-item svg { width: 14px; height: 14px; }

/* Announcement Footer */
.announcement-footer {
  display: flex; justify-content: space-between; align-items: center;
  padding-top: 1rem; border-top: 1px solid #f3f4f6;
}
.announcement-stats { display: flex; gap: 1rem; }
.stat-item {
  display: flex; align-items: center; gap: 4px;
  font-size: 0.75rem; color: #9ca3af;
}
.stat-item svg { width: 14px; height: 14px; }

/* Responsive */
@media (max-width: 1024px) {
  .announcements-list {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .filters-panel {
    flex-wrap: wrap;
  }
  
  .filter-group {
    flex: 1;
    min-width: 200px;
  }
}

@media (max-width: 768px) {
  .filters-panel { 
    flex-direction: column; 
  }
  
  .filter-input, .filter-select { 
    min-width: 100%; 
  }
  
  .section-header { 
    flex-direction: column; 
    align-items: flex-start; 
    gap: 1rem; 
  }
  
  .announcement-header { 
    flex-direction: column; 
    gap: 1rem; 
  }
  
  .announcement-footer { 
    flex-direction: column; 
    gap: 1rem; 
    align-items: flex-start; 
  }
  
  .announcement-card {
    padding: 1rem;
  }
  
  .announcement-title {
    font-size: 1rem;
  }
  
  .announcement-excerpt {
    font-size: 0.875rem;
  }
}

@media (max-width: 640px) {
  .breadcrumb-bar {
    flex-wrap: wrap;
    gap: 4px;
  }
  
  .announcement-meta {
    flex-direction: column;
    gap: 4px;
    align-items: flex-start;
  }
  
  .announcement-details {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .announcement-stats {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .announcement-card {
    padding: 0.75rem;
  }
  
  .priority-badge, .status-badge, .announcement-type {
    font-size: 0.65rem;
    padding: 2px 6px;
  }
}

@media (max-width: 480px) {
  .section-title {
    font-size: 1.25rem;
  }
  
  .section-subtitle {
    font-size: 0.8rem;
  }
  
  .action-btn {
    padding: 6px 12px;
    font-size: 0.8rem;
  }
  
  .announcement-card {
    padding: 0.5rem;
  }
  
  .empty-state {
    padding: 2rem 1rem;
  }
  
  .empty-state svg {
    width: 48px;
    height: 48px;
  }
  
  .empty-state h3 {
    font-size: 1.125rem;
  }
  
  .empty-state p {
    font-size: 0.8rem;
  }
}
</style>
