<template>
  <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <span class="bc-prompt">$</span>
      <router-link to="/dashboard" class="bc-item">HOME</router-link>
      <span class="bc-sep">›</span>
      <router-link to="/announcements" class="bc-item">ANNOUNCEMENTS</router-link>
      <span class="bc-sep">›</span>
      <span class="bc-current">{{ announcement?.title || 'Loading...' }}</span>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading announcement...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <h3>Announcement Not Found</h3>
      <p>{{ error }}</p>
      <button class="btn primary" @click="$router.push('/announcements')">
        Back to Announcements
      </button>
    </div>

    <!-- Announcement Content -->
    <div v-else-if="announcement" class="announcement-detail">
      <!-- Header -->
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
        <div class="announcement-actions" v-if="isAdmin">
          <button @click="editAnnouncement" class="action-btn-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button @click="deleteAnnouncement" class="action-btn-icon danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Title and Basic Info -->
      <div class="announcement-title-section">
        <h1 class="announcement-title">{{ announcement.title }}</h1>
        <div class="announcement-info">
          <div class="info-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>{{ announcement.author }}</span>
          </div>
          <div class="info-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Published: {{ formatDate(announcement.publish_date) }}</span>
          </div>
          <div v-if="announcement.expires_at" class="info-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Expires: {{ formatDate(announcement.expires_at) }}</span>
          </div>
          <div class="info-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <span>{{ announcement.views || 0 }} views</span>
          </div>
        </div>
      </div>

      <!-- Excerpt -->
      <div class="announcement-excerpt">
        <p>{{ announcement.excerpt }}</p>
      </div>

      <!-- Content -->
      <div class="announcement-content">
        <div class="content-body" v-html="formatContent(announcement.content)"></div>
      </div>

      <!-- Attachments -->
      <div v-if="announcement.attachments && announcement.attachments.length > 0" class="announcement-attachments">
        <h3 class="section-title">Attachments</h3>
        <div class="attachment-list">
          <a 
            v-for="(attachment, index) in announcement.attachments" 
            :key="index"
            :href="attachment.url" 
            :download="attachment.name"
            class="attachment-item"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <div class="attachment-info">
              <span class="attachment-name">{{ attachment.name }}</span>
              <span class="attachment-size">{{ formatFileSize(attachment.size || 0) }}</span>
            </div>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- Engagement -->
      <div class="announcement-engagement">
        <div class="engagement-stats">
          <button class="engagement-btn" @click="toggleLike" :class="{ active: isLiked }">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            <span>{{ announcement.likes || 0 }}</span>
          </button>
          <button class="engagement-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            <span>{{ announcement.comments || 0 }}</span>
          </button>
          <button class="engagement-btn" @click="shareAnnouncement">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="18" cy="5" r="3"/>
              <circle cx="6" cy="12" r="3"/>
              <circle cx="18" cy="19" r="3"/>
              <line x1="8.59" y1="13.41" x2="15.42" y2="6.58"/>
              <line x1="15.41" y1="17.59" x2="8.59" y2="10.41"/>
            </svg>
            <span>Share</span>
          </button>
        </div>
        <div class="engagement-actions">
          <button class="btn secondary" @click="printAnnouncement">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 6 2 18 2 18 9"/>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
              <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Print
          </button>
        </div>
      </div>

      <!-- Comments Section -->
      <div class="comments-section">
        <h3 class="section-title">Comments ({{ announcement.comments || 0 }})</h3>
        
        <!-- Add Comment -->
        <div class="add-comment">
          <textarea 
            v-model="newComment" 
            placeholder="Add a comment..." 
            class="comment-input"
            rows="3"
          ></textarea>
          <div class="comment-actions">
            <span class="comment-hint">{{ newComment.length }}/500 characters</span>
            <button class="btn primary" @click="addComment" :disabled="!newComment.trim()">
              Post Comment
            </button>
          </div>
        </div>

        <!-- Comments List -->
        <div class="comments-list">
          <div v-for="comment in comments" :key="comment.id" class="comment-item">
            <div class="comment-avatar">
              {{ (comment.author || 'Anonymous')?.charAt(0)?.toUpperCase() || '?' }}
            </div>
            <div class="comment-content">
              <div class="comment-header">
                <span class="comment-author">{{ comment.author || 'Anonymous' }}</span>
                <span class="comment-date">{{ formatDate(comment.created_at) }}</span>
              </div>
              <p class="comment-text">{{ comment.content }}</p>
              <div class="comment-actions">
                <button class="comment-action">Reply</button>
                <button v-if="comment.canDelete" class="comment-action danger">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import type { Announcement } from '@/types/announcement'
import type { AnnouncementComment } from '@/types/announcement'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.isAdmin)

const announcementId = computed(() => Number(route.params.id))

const loading = ref(true)
const error = ref('')
const announcement = ref<Announcement | null>(null)
const comments = ref<AnnouncementComment[]>([])
const newComment = ref('')
const isLiked = ref(false)

const fetchAnnouncement = async () => {
  try {
    loading.value = true
    const response = await api.get(`/announcements/${announcementId.value}`)
    announcement.value = response.data
    
    // Fetch comments
    try {
      const commentsResponse = await api.get(`/announcements/${announcementId.value}/comments`)
      comments.value = commentsResponse.data
    } catch (err) {
      // Mock comments for development
      comments.value = [
        {
          id: 1,
          announcement_id: 1,
          user_id: 1,
          content: 'Thank you for this important information!',
          created_at: '2024-03-15T10:30:00Z',
          updated_at: '2024-03-15T10:30:00Z',
          canDelete: false,
          author: 'John Doe'
        },
        {
          id: 2,
          announcement_id: 1,
          user_id: 2,
          content: 'This is very helpful. When will the maintenance be completed?',
          created_at: '2024-03-15T11:15:00Z',
          updated_at: '2024-03-15T11:15:00Z',
          canDelete: false,
          author: 'Jane Smith'
        }
      ]
    }
  } catch (err) {
    console.error('Error fetching announcement:', err)
    error.value = 'Announcement not found or you do not have permission to view it.'
    
    // Mock data for development
    announcement.value = {
      id: 1,
      title: 'System Maintenance Scheduled',
      excerpt: 'The system will undergo scheduled maintenance this weekend. Please save your work and log out before the maintenance period.',
      content: `# System Maintenance Scheduled

Dear Users,

We will be performing **scheduled system maintenance** this weekend to improve performance and security.

## Maintenance Schedule
- **Start**: Saturday, March 20, 2024 at 11:00 PM
- **End**: Sunday, March 21, 2024 at 3:00 AM
- **Duration**: 4 hours

## What to Expect
During the maintenance period:
- All services will be temporarily unavailable
- You will not be able to access the system
- Data will be safely backed up before maintenance begins

## Action Required
1. **Save all work** before Saturday evening
2. **Log out** of the system before 11:00 PM
3. **Plan accordingly** for the downtime

## Support
If you have any questions or concerns, please contact the IT support team.

Thank you for your cooperation!`,
      author: 'System Administrator',
      priority: 'high',
      status: 'published',
      type: 'System',
      publish_date: '2024-03-15T10:00:00Z',
      expires_at: '2024-03-20T23:59:59Z',
      views: 156,
      likes: 23,
      comments: 2,
      target_all: true,
      target_students: true,
      target_professors: true,
      target_admins: true,
      user_id: 1,
      created_at: '2024-03-15T10:00:00Z',
      updated_at: '2024-03-15T10:00:00Z',
      attachments: [
        {
          id: 1,
          announcement_id: 1,
          filename: 'maintenance_guide.pdf',
          original_name: 'Maintenance_Guide.pdf',
          mime_type: 'application/pdf',
          file_size: 1024000,
          file_path: 'storage/announcements/maintenance_guide.pdf',
          created_at: '2024-03-15T10:00:00Z',
          updated_at: '2024-03-15T10:00:00Z',
          url: '#',
          name: 'Maintenance_Guide.pdf',
          size: 1024000
        }
      ]
    }
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatContent = (content: string) => {
  if (!content) return ''
  // Simple markdown to HTML conversion
  return content
    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
    .replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>')
    .replace(/\*(.*)\*/gim, '<em>$1</em>')
    .replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2">$1</a>')
    .replace(/^- (.*$)/gim, '<li>$1</li>')
    .replace(/\n/gim, '<br>')
}

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const editAnnouncement = () => {
  if (!announcement.value) return
  router.push(`/announcements/${announcementId.value}/edit`)
}

const deleteAnnouncement = async () => {
  if (!announcement.value) return
  
  if (confirm(`Are you sure you want to delete "${announcement.value.title}"?`)) {
    try {
      await api.delete(`/announcements/${announcementId.value}`)
      router.push('/announcements')
    } catch (error) {
      console.error('Error deleting announcement:', error)
      alert('Failed to delete announcement. Please try again.')
    }
  }
}

const toggleLike = () => {
  if (!announcement.value) return
  isLiked.value = !isLiked.value
  // Update like count
  if (isLiked.value) {
    announcement.value.likes++
  } else {
    announcement.value.likes--
  }
}

const shareAnnouncement = () => {
  if (navigator.share) {
    navigator.share({
      title: announcement.value?.title,
      text: announcement.value?.excerpt,
      url: window.location.href
    })
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href)
    alert('Link copied to clipboard!')
  }
}

const printAnnouncement = () => {
  window.print()
}

const addComment = async () => {
  if (!newComment.value.trim()) return
  
  try {
    const response = await api.post(`/announcements/${announcementId.value}/comments`, {
      content: newComment.value
    })
    
    comments.value.unshift(response.data)
    if (announcement.value) {
      announcement.value.comments++
    }
    newComment.value = ''
  } catch (error) {
    console.error('Error adding comment:', error)
    
    // Mock comment for development
    const mockComment: AnnouncementComment = {
      id: Date.now(),
      announcement_id: Number(announcementId.value),
      user_id: authStore.user?.id || 1,
      content: newComment.value,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      canDelete: true,
      author: authStore.user?.name || 'Current User'
    }
    comments.value.unshift(mockComment)
    if (announcement.value) {
      announcement.value.comments++
    }
    newComment.value = ''
  }
}

onMounted(() => {
  fetchAnnouncement()
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

/* Error State */
.error-state {
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
  padding: 3rem; text-align: center;
}
.error-state svg { width: 64px; height: 64px; color: #dc2626; }
.error-state h3 { font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0; }
.error-state p { color: #6b7280; margin: 0; }
.btn {
  padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s; border: none;
}
.btn.primary { background: #f97316; color: white; }
.btn.primary:hover { background: #ea580c; }
.btn.secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.btn.secondary:hover { background: #e5e7eb; }

/* Announcement Detail */
.announcement-detail {
  max-width: 800px; margin: 0 auto;
}

/* Header */
.announcement-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 1.5rem;
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
}
.action-btn-icon:hover { background: #f3f4f6; color: #374151; }
.action-btn-icon.danger:hover { background: #fee2e2; color: #dc2626; }
.action-btn-icon svg { width: 16px; height: 16px; }

/* Title Section */
.announcement-title-section {
  margin-bottom: 1.5rem;
}
.announcement-title {
  font-size: 2rem; font-weight: 700; color: #111827;
  margin: 0 0 1rem 0; line-height: 1.2;
}
.announcement-info {
  display: flex; gap: 1.5rem; flex-wrap: wrap;
}
.info-item {
  display: flex; align-items: center; gap: 6px;
  font-size: 0.875rem; color: #6b7280;
}
.info-item svg { width: 16px; height: 16px; }

/* Content */
.announcement-excerpt {
  background: #f9fafb; border-left: 4px solid #f97316;
  padding: 1rem; margin-bottom: 1.5rem; border-radius: 0 8px 8px 0;
}
.announcement-excerpt p {
  font-size: 1.125rem; color: #374151; margin: 0;
  font-weight: 500;
}

.announcement-content {
  margin-bottom: 2rem;
}
.content-body {
  font-size: 1rem; line-height: 1.7; color: #374151;
}
.content-body h1, .content-body h2, .content-body h3 {
  margin: 1.5rem 0 1rem 0; color: #111827;
}
.content-body h1 { font-size: 1.875rem; }
.content-body h2 { font-size: 1.5rem; }
.content-body h3 { font-size: 1.25rem; }
.content-body strong { font-weight: 600; color: #111827; }
.content-body em { font-style: italic; }
.content-body a { color: #f97316; text-decoration: none; }
.content-body a:hover { text-decoration: underline; }

/* Attachments */
.announcement-attachments {
  margin-bottom: 2rem;
}
.section-title {
  font-size: 1.25rem; font-weight: 600; color: #111827;
  margin: 0 0 1rem 0;
}
.attachment-list {
  display: flex; flex-direction: column; gap: 8px;
}
.attachment-item {
  display: flex; align-items: center; gap: 12px;
  padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb;
  border-radius: 8px; text-decoration: none; color: #374151;
  transition: all 0.2s;
}
.attachment-item:hover { background: #f3f4f6; border-color: #f97316; }
.attachment-item svg:first-child { width: 20px; height: 20px; color: #6b7280; }
.attachment-info { flex: 1; }
.attachment-name { font-size: 0.875rem; font-weight: 500; display: block; }
.attachment-size { font-size: 0.75rem; color: #6b7280; }
.attachment-item svg:last-child { width: 16px; height: 16px; color: #6b7280; }

/* Engagement */
.announcement-engagement {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 0; border-top: 1px solid #f3f4f6;
  margin-bottom: 2rem;
}
.engagement-stats {
  display: flex; gap: 1rem;
}
.engagement-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 12px; background: none; border: 1px solid #e5e7eb;
  border-radius: 6px; color: #6b7280; cursor: pointer;
  transition: all 0.2s; font-size: 0.875rem;
}
.engagement-btn:hover { background: #f3f4f6; border-color: #f97316; color: #f97316; }
.engagement-btn.active { background: #f97316; border-color: #f97316; color: white; }
.engagement-btn svg { width: 16px; height: 16px; }
.engagement-actions { display: flex; gap: 8px; }

/* Comments */
.comments-section {
  border-top: 1px solid #f3f4f6; padding-top: 2rem;
}
.add-comment {
  margin-bottom: 2rem;
}
.comment-input {
  width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px;
  font-size: 0.875rem; resize: vertical; margin-bottom: 8px;
}
.comment-input:focus {
  outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}
.comment-actions {
  display: flex; justify-content: space-between; align-items: center;
}
.comment-hint { font-size: 0.75rem; color: #6b7280; }

.comments-list {
  display: flex; flex-direction: column; gap: 1rem;
}
.comment-item {
  display: flex; gap: 12px;
}
.comment-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: #f97316; color: white; display: flex;
  align-items: center; justify-content: center; font-weight: 600;
  flex-shrink: 0;
}
.comment-content {
  flex: 1;
}
.comment-header {
  display: flex; gap: 1rem; align-items: center; margin-bottom: 4px;
}
.comment-author { font-weight: 600; color: #111827; }
.comment-date { font-size: 0.75rem; color: #6b7280; }
.comment-text { color: #374151; line-height: 1.5; margin: 0 0 8px 0; }
.comment-actions {
  display: flex; gap: 1rem;
}
.comment-action {
  font-size: 0.75rem; color: #6b7280; background: none; border: none;
  cursor: pointer; padding: 4px; border-radius: 4px; transition: all 0.2s;
}
.comment-action:hover { background: #f3f4f6; color: #374151; }
.comment-action.danger:hover { background: #fee2e2; color: #dc2626; }

/* Responsive */
@media (max-width: 768px) {
  .announcement-header { flex-direction: column; gap: 1rem; }
  .announcement-title { font-size: 1.5rem; }
  .announcement-info { flex-direction: column; gap: 0.5rem; }
  .announcement-engagement { flex-direction: column; gap: 1rem; align-items: flex-start; }
  .engagement-stats { width: 100%; justify-content: space-between; }
}

@media print {
  .announcement-header, .announcement-engagement, .comments-section {
    display: none;
  }
}
</style>
