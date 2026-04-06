<template>
  <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <span class="bc-prompt">$</span>
      <router-link to="/dashboard" class="bc-item">HOME</router-link>
      <span class="bc-sep">›</span>
      <router-link to="/announcements" class="bc-item">ANNOUNCEMENTS</router-link>
      <span class="bc-sep">›</span>
      <span class="bc-current">{{ isEdit ? 'Edit' : 'Create' }} Announcement</span>
    </div>

    <!-- Page Header -->
    <div class="section-header">
      <div>
        <h2 class="section-title">{{ isEdit ? 'Edit Announcement' : 'Create New Announcement' }}</h2>
        <p class="section-subtitle">
          {{ isEdit ? 'Update announcement details and content' : 'Create a new announcement for the community' }}
        </p>
      </div>
      <div class="header-actions">
        <button class="action-btn" @click="$router.go(-1)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
          Back
        </button>
      </div>
    </div>

    <!-- Form -->
    <div class="form-container">
      <form @submit.prevent="handleSubmit" class="announcement-form">
        <!-- Basic Information -->
        <div class="form-section">
          <h3 class="section-title">Basic Information</h3>
          
          <div class="form-group">
            <label class="form-label">Title *</label>
            <input 
              v-model="formData.title" 
              type="text" 
              class="form-input" 
              placeholder="Enter announcement title"
              required
            >
            <span v-if="errors.title" class="error-message">{{ errors.title }}</span>
          </div>

          <div class="form-group">
            <label class="form-label">Type *</label>
            <select v-model="formData.type" class="form-select" required>
              <option value="">Select type</option>
              <option value="System">System</option>
              <option value="Academic">Academic</option>
              <option value="Event">Event</option>
              <option value="Feature">Feature</option>
              <option value="Policy">Policy</option>
              <option value="General">General</option>
            </select>
            <span v-if="errors.type" class="error-message">{{ errors.type }}</span>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Priority *</label>
              <select v-model="formData.priority" class="form-select" required>
                <option value="">Select priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
              <span v-if="errors.priority" class="error-message">{{ errors.priority }}</span>
            </div>

            <div class="form-group">
              <label class="form-label">Status *</label>
              <select v-model="formData.status" class="form-select" required>
                <option value="">Select status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="scheduled">Scheduled</option>
              </select>
              <span v-if="errors.status" class="error-message">{{ errors.status }}</span>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="form-section">
          <h3 class="section-title">Content</h3>
          
          <div class="form-group">
            <label class="form-label">Excerpt *</label>
            <textarea 
              v-model="formData.excerpt" 
              class="form-textarea" 
              rows="3" 
              placeholder="Brief summary of the announcement"
              required
            ></textarea>
            <span class="form-hint">{{ formData.excerpt.length }}/200 characters</span>
            <span v-if="errors.excerpt" class="error-message">{{ errors.excerpt }}</span>
          </div>

          <div class="form-group">
            <label class="form-label">Full Content *</label>
            <div class="editor-toolbar">
              <button type="button" class="toolbar-btn" @click="insertText('**', '**')" title="Bold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                  <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                </svg>
              </button>
              <button type="button" class="toolbar-btn" @click="insertText('*', '*')" title="Italic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="19" y1="4" x2="10" y2="4"/>
                  <line x1="14" y1="20" x2="5" y2="20"/>
                  <line x1="15" y1="4" x2="9" y2="20"/>
                </svg>
              </button>
              <button type="button" class="toolbar-btn" @click="insertText('### ', '')" title="Heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 12h16"/>
                  <path d="M4 18h7"/>
                </svg>
              </button>
              <button type="button" class="toolbar-btn" @click="insertText('- ', '')" title="List">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="8" y1="6" x2="21" y2="6"/>
                  <line x1="8" y1="12" x2="21" y2="12"/>
                  <line x1="8" y1="18" x2="21" y2="18"/>
                  <line x1="3" y1="6" x2="3.01" y2="6"/>
                  <line x1="3" y1="12" x2="3.01" y2="12"/>
                  <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
              </button>
              <button type="button" class="toolbar-btn" @click="insertText('[', '](url)')" title="Link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
              </button>
            </div>
            <textarea 
              v-model="formData.content" 
              class="form-textarea editor" 
              rows="12" 
              placeholder="Write the full announcement content here..."
              required
            ></textarea>
            <span v-if="errors.content" class="error-message">{{ errors.content }}</span>
          </div>
        </div>

        <!-- Publishing Options -->
        <div class="form-section">
          <h3 class="section-title">Publishing Options</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Publish Date</label>
              <input 
                v-model="formData.publish_date" 
                type="datetime-local" 
                class="form-input"
              >
              <span v-if="errors.publish_date" class="error-message">{{ errors.publish_date }}</span>
            </div>

            <div class="form-group">
              <label class="form-label">Expiry Date</label>
              <input 
                v-model="formData.expires_at" 
                type="datetime-local" 
                class="form-input"
              >
              <span class="form-hint">Leave empty if announcement doesn't expire</span>
              <span v-if="errors.expires_at" class="error-message">{{ errors.expires_at }}</span>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Target Audience</label>
            <div class="checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="formData.target_all" @change="handleTargetAll">
                <span>All Users</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" v-model="formData.target_students" :disabled="formData.target_all" @change="handleTargetStudents">
                <span>Students</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" v-model="formData.target_professors" :disabled="formData.target_all" @change="handleTargetProfessors">
                <span>Professors</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" v-model="formData.target_admins" :disabled="formData.target_all" @change="handleTargetAdmins">
                <span>Administrators</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Attachments</label>
            <div class="file-upload">
              <input 
                type="file" 
                ref="fileInput" 
                multiple 
                @change="handleFileUpload"
                class="file-input"
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp,.bmp,.tiff,.svg,.txt,.zip,.rar"
              >
              <button type="button" class="upload-btn" @click="fileInput?.click()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="17 8 12 3 7 8"/>
                  <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Choose Files
              </button>
              <span class="file-hint">PDF, Word, Excel, PowerPoint, Images (Max 100MB)</span>
            </div>
            <div v-if="formData.attachments.length > 0" class="file-list">
              <div v-for="(file, index) in formData.attachments" :key="index" class="file-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span>{{ file.name }}</span>
                <button type="button" @click="removeFile(index)" class="remove-btn">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="button" class="btn secondary" @click="$router.go(-1)">
            Cancel
          </button>
          <button type="button" class="btn secondary" @click="saveDraft" v-if="!isEdit">
            Save Draft
          </button>
          <button type="submit" class="btn primary" :disabled="submitting">
            {{ submitting ? 'Saving...' : (isEdit ? 'Update Announcement' : 'Publish Announcement') }}
          </button>
        </div>
      </form>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

// TypeScript interfaces
interface Announcement {
  id?: number
  title: string
  type: string
  priority: 'low' | 'medium' | 'high' | 'urgent'
  status: 'draft' | 'published' | 'scheduled' | 'archived'
  excerpt: string
  content: string
  publish_date?: string
  expires_at?: string
  target_all: boolean
  target_students: boolean
  target_professors: boolean
  target_admins: boolean
  attachments: File[]
}

interface FormErrors {
  title?: string
  type?: string
  priority?: string
  status?: string
  excerpt?: string
  content?: string
  publish_date?: string
  expires_at?: string
}

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const isEdit = computed(() => route.name === 'EditAnnouncement')
const announcementId = computed(() => route.params.id)

const submitting = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)

const formData = ref<Announcement>({
  title: '',
  type: '',
  priority: 'medium',
  status: 'draft',
  excerpt: '',
  content: '',
  publish_date: '',
  expires_at: '',
  target_all: true,
  target_students: false,
  target_professors: false,
  target_admins: false,
  attachments: []
})

const errors = ref<FormErrors>({})

const handleSubmit = async () => {
  try {
    submitting.value = true
    errors.value = {}

    // Validate form
    if (!formData.value.title.trim()) {
      errors.value.title = 'Title is required'
    }
    if (!formData.value.type) {
      errors.value.type = 'Type is required'
    }
    if (!formData.value.priority) {
      errors.value.priority = 'Priority is required'
    }
    if (!formData.value.status) {
      errors.value.status = 'Status is required'
    }
    if (!formData.value.excerpt.trim()) {
      errors.value.excerpt = 'Excerpt is required'
    }
    if (!formData.value.content.trim()) {
      errors.value.content = 'Content is required'
    }

    if (Object.keys(errors.value).length > 0) {
      return
    }

    const payload: any = { ...formData.value }
    
    // Handle target audience
    if (payload.target_all) {
      payload.target_students = true
      payload.target_professors = true
      payload.target_admins = true
    }

    // Remove attachments from payload as they'll be sent separately
    const attachments = payload.attachments
    delete payload.attachments

    // Create FormData for file upload
    const formDataToSend = new FormData()
    
    // Add all form fields
    Object.keys(payload).forEach(key => {
      if (payload[key] !== null && payload[key] !== undefined) {
        // Convert boolean values to strings for FormData
        const value = typeof payload[key] === 'boolean' ? (payload[key] ? '1' : '0') : payload[key]
        formDataToSend.append(key, value)
      }
    })
    
    // Add files
    if (attachments && attachments.length > 0) {
      attachments.forEach((file: File) => {
        formDataToSend.append('attachments[]', file)
      })
    }

    if (isEdit.value) {
      await api.put(`/announcements/${announcementId.value}`, formDataToSend)
      router.push(`/announcements/${announcementId.value}`)
    } else {
      const response = await api.post('/announcements', formDataToSend)
      router.push(`/announcements/${response.data.id}`)
    }
  } catch (error: any) {
    console.error('Error saving announcement:', error)
    
    // Show detailed error if available
    if (error.response?.data?.errors) {
      console.log('Validation errors:', error.response.data.errors)
      const errorMessages = Object.values(error.response.data.errors).flat()
      alert(`Validation failed: ${errorMessages.join(', ')}`)
    } else if (error.response?.data?.message) {
      alert(`Error: ${error.response.data.message}`)
    } else {
      alert('Failed to save announcement. Please try again.')
    }
  } finally {
    submitting.value = false
  }
}

const saveDraft = async () => {
  formData.value.status = 'draft'
  await handleSubmit()
}

const handleTargetAll = () => {
  if (formData.value.target_all) {
    formData.value.target_students = false
    formData.value.target_professors = false
    formData.value.target_admins = false
  }
}

const handleTargetStudents = () => {
  if (!formData.value.target_all) {
    // Handle individual checkbox logic
  }
}

const handleTargetProfessors = () => {
  if (!formData.value.target_all) {
    // Handle individual checkbox logic
  }
}

const handleTargetAdmins = () => {
  if (!formData.value.target_all) {
    // Handle individual checkbox logic
  }
}

const insertText = (before: string, after: string = '') => {
  const textarea = document.querySelector('.editor') as HTMLTextAreaElement
  if (!textarea) return
  
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const text = textarea.value
  const selectedText = text.substring(start, end)
  
  const newText = text.substring(0, start) + before + selectedText + after + text.substring(end)
  formData.value.content = newText
  
  // Set cursor position
  setTimeout(() => {
    textarea.focus()
    textarea.setSelectionRange(start + before.length, start + before.length + selectedText.length)
  }, 0)
}

const handleFileUpload = (event: Event) => {
  const files = Array.from((event.target as HTMLInputElement).files || [])
  
  // Check file sizes
  const maxSize = 100 * 1024 * 1024 // 100MB
  for (const file of files) {
    if (file.size > maxSize) {
      alert(`File ${file.name} exceeds 100MB limit`)
      return
    }
  }
  
  formData.value.attachments.push(...files)
}

const removeFile = (index: number) => {
  formData.value.attachments.splice(index, 1)
}

const loadAnnouncement = async () => {
  try {
    const response = await api.get(`/announcements/${announcementId.value}`)
    const announcement = response.data
    
    formData.value = {
      title: announcement.title,
      type: announcement.type,
      priority: announcement.priority,
      status: announcement.status,
      excerpt: announcement.excerpt,
      content: announcement.content,
      publish_date: announcement.publish_date ? new Date(announcement.publish_date).toISOString().slice(0, 16) : '',
      expires_at: announcement.expires_at ? new Date(announcement.expires_at).toISOString().slice(0, 16) : '',
      target_all: announcement.target_all || false,
      target_students: announcement.target_students || false,
      target_professors: announcement.target_professors || false,
      target_admins: announcement.target_admins || false,
      attachments: announcement.attachments || []
    }
  } catch (error) {
    console.error('Error loading announcement:', error)
    router.push('/announcements')
  }
}

onMounted(() => {
  if (isEdit.value) {
    loadAnnouncement()
  }
  
  // Set default publish date to now
  if (!isEdit.value) {
    formData.value.publish_date = new Date().toISOString().slice(0, 16)
  }
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
}
.action-btn:hover { background: #f9fafb; border-color: #f97316; color: #f97316; }
.action-btn svg { width: 16px; height: 16px; }

/* Form Container */
.form-container {
  max-width: 800px; margin: 0 auto;
}
.announcement-form { display: flex; flex-direction: column; gap: 2rem; }

/* Form Sections */
.form-section {
  background: white; border: 1px solid #e5e7eb; border-radius: 8px;
  padding: 1.5rem;
}
.form-section .section-title {
  font-size: 1.125rem; font-weight: 600; color: #111827;
  margin: 0 0 1rem 0; padding-bottom: 0.5rem;
  border-bottom: 1px solid #f3f4f6;
}

/* Form Groups */
.form-group {
  display: flex; flex-direction: column; gap: 6px;
  margin-bottom: 1rem;
}
.form-row {
  display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
}
.form-label {
  font-size: 0.875rem; font-weight: 500; color: #374151;
}
.form-input, .form-select, .form-textarea {
  padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;
  font-size: 0.875rem; transition: border-color 0.2s;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
  outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}
.form-textarea { resize: vertical; }
.form-hint {
  font-size: 0.75rem; color: #6b7280;
}
.error-message {
  font-size: 0.75rem; color: #dc2626;
}

/* Editor Toolbar */
.editor-toolbar {
  display: flex; gap: 4px; padding: 8px;
  background: #f9fafb; border: 1px solid #d1d5db; border-bottom: none;
  border-radius: 6px 6px 0 0;
}
.toolbar-btn {
  padding: 6px 8px; background: white; border: 1px solid #d1d5db;
  border-radius: 4px; cursor: pointer; transition: all 0.2s;
}
.toolbar-btn:hover { background: #f3f4f6; border-color: #f97316; }
.toolbar-btn svg { width: 16px; height: 16px; }
.editor {
  border-radius: 0 0 6px 6px; border-top: none;
}

/* Checkbox Group */
.checkbox-group {
  display: flex; flex-wrap: wrap; gap: 1rem;
}
.checkbox-label {
  display: flex; align-items: center; gap: 6px;
  font-size: 0.875rem; color: #374151; cursor: pointer;
}
.checkbox-label input[type="checkbox"] {
  width: 16px; height: 16px;
}
.checkbox-label:has(input:disabled) {
  color: #9ca3af; cursor: not-allowed;
}

/* File Upload */
.file-upload {
  display: flex; flex-direction: column; gap: 8px;
}
.file-input { display: none; }
.upload-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 16px; background: white; border: 2px dashed #d1d5db;
  border-radius: 6px; color: #6b7280; cursor: pointer;
  transition: all 0.2s;
}
.upload-btn:hover { border-color: #f97316; color: #f97316; }
.upload-btn svg { width: 16px; height: 16px; }

/* File List */
.file-list {
  display: flex; flex-direction: column; gap: 8px; margin-top: 8px;
}
.file-item {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px; background: #f9fafb; border: 1px solid #e5e7eb;
  border-radius: 6px;
}
.file-item svg { width: 16px; height: 16px; color: #6b7280; }
.file-item span { flex: 1; font-size: 0.875rem; }
.remove-btn {
  padding: 4px; background: none; border: none; border-radius: 4px;
  color: #6b7280; cursor: pointer; transition: all 0.2s;
}
.remove-btn:hover { background: #fee2e2; color: #dc2626; }
.remove-btn svg { width: 14px; height: 14px; }

/* Form Actions */
.form-actions {
  display: flex; justify-content: flex-end; gap: 1rem;
  padding: 1.5rem 0;
}
.btn {
  padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s; border: none;
}
.btn.primary { background: #f97316; color: white; }
.btn.primary:hover { background: #ea580c; }
.btn.primary:disabled { background: #fbbf24; cursor: not-allowed; }
.btn.secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.btn.secondary:hover { background: #e5e7eb; }

/* Responsive */
@media (max-width: 768px) {
  .form-row { grid-template-columns: 1fr; }
  .section-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .form-actions { flex-direction: column; }
  .checkbox-group { flex-direction: column; }
}
</style>
