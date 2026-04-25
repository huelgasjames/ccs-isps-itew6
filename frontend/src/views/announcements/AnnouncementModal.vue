<template>
  <div class="modal-overlay">
    <div class="modal-backdrop" @click="$emit('close')"></div>
    
    <div class="modal-container">
      <form @submit.prevent="handleSubmit" class="modal-form">
        <div class="modal-header">
          <h3 class="modal-title">
            {{ announcement ? 'Edit Announcement' : 'Create New Announcement' }}
          </h3>
          <p class="modal-subtitle">
            {{ announcement ? 'Update the announcement details below.' : 'Fill in the details to create a new announcement.' }}
          </p>
        </div>

        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label class="form-label">
              Title <span class="required">*</span>
            </label>
            <input 
              v-model="form.title"
              type="text" 
              required
              class="form-input"
              placeholder="Enter announcement title"
            />
            <span v-if="errors.title" class="form-error">{{ errors.title }}</span>
          </div>

          <!-- Content -->
          <div class="form-group">
            <label class="form-label">
              Content <span class="required">*</span>
            </label>
            <textarea 
              v-model="form.content"
              required
              rows="6"
              class="form-textarea"
              placeholder="Enter announcement content"
            ></textarea>
            <span v-if="errors.content" class="form-error">{{ errors.content }}</span>
          </div>

          <!-- Image -->
          <div class="form-group">
            <label class="form-label">
              Image
            </label>
            <div class="image-upload-area">
              <input 
                type="file" 
                accept="image/*"
                @change="handleImageChange"
                class="form-input"
              />
              <div v-if="imagePreview" class="image-preview">
                <img 
                  :src="imagePreview" 
                  alt="Preview" 
                  class="preview-img"
                />
              </div>
              <div v-else-if="announcement?.image" class="image-preview">
                <img 
                  :src="getImageUrl(announcement.image)" 
                  alt="Current image" 
                  class="preview-img"
                />
                <p class="current-image-label">Current image</p>
              </div>
            </div>
          </div>

          <!-- Target Type -->
          <div class="form-group">
            <label class="form-label">
              Target Audience <span class="required">*</span>
            </label>
            <select 
              v-model="form.target_type"
              required
              @change="handleTargetTypeChange"
              class="form-select"
            >
              <option value="">Select target audience</option>
              <option value="all">All Users</option>
              <option value="students">Students Only</option>
              <option value="professors">Professors Only</option>
              <option value="specific">Specific Users</option>
            </select>
            <span v-if="errors.target_type" class="form-error">{{ errors.target_type }}</span>
          </div>

          <!-- Specific Users -->
          <div v-if="form.target_type === 'specific'" class="form-group">
            <label class="form-label">
              Select Users <span class="required">*</span>
            </label>
            <div class="users-list">
              <div v-for="user in availableUsers" :key="user.id" class="user-item">
                <input 
                  :id="`user-${user.id}`"
                  v-model="form.target_users"
                  :value="user.id"
                  type="checkbox"
                  class="form-checkbox"
                />
                <label :for="`user-${user.id}`" class="user-label">
                  {{ user.name }} ({{ user.role }})
                </label>
              </div>
            </div>
            <span v-if="errors.target_users" class="form-error">{{ errors.target_users }}</span>
          </div>

          <!-- Status -->
          <div class="form-group">
            <label class="form-label">
              Status <span class="required">*</span>
            </label>
            <div class="radio-group">
              <label class="radio-item">
                <input 
                  v-model="form.status"
                  type="radio" 
                  value="draft"
                  class="form-radio"
                />
                <span class="radio-label">Draft</span>
              </label>
              <label class="radio-item">
                <input 
                  v-model="form.status"
                  type="radio" 
                  value="published"
                  class="form-radio"
                />
                <span class="radio-label">Published</span>
              </label>
            </div>
            <span v-if="errors.status" class="form-error">{{ errors.status }}</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="modal-footer">
          <button 
            type="submit"
            :disabled="submitting"
            class="btn-primary"
          >
            <span v-if="submitting" class="flex items-center">
              <div class="loading-spinner-small"></div>
              Saving...
            </span>
            <span v-else>{{ announcement ? 'Update' : 'Create' }} Announcement</span>
          </button>
          <button 
            type="button"
            @click="$emit('close')"
            class="btn-secondary"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import type { Announcement, CreateAnnouncementData } from '@/services/announcements'
import { announcementService } from '@/services/announcements'

interface User {
  id: number
  name: string
  email: string
  role: string
}

interface Props {
  announcement?: Announcement | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  saved: []
}>()

const submitting = ref(false)
const imagePreview = ref<string | null>(null)
const availableUsers = ref<User[]>([])

const form = reactive<CreateAnnouncementData>({
  title: '',
  content: '',
  target_type: 'all',
  target_users: [],
  status: 'draft'
})

const errors = reactive<Record<string, string>>({})

const validateForm = () => {
  const newErrors: Record<string, string> = {}

  if (!form.title.trim()) {
    newErrors.title = 'Title is required'
  }

  if (!form.content.trim()) {
    newErrors.content = 'Content is required'
  }

  if (!form.target_type) {
    newErrors.target_type = 'Target audience is required'
  }

  if (form.target_type === 'specific' && (!form.target_users || form.target_users.length === 0)) {
    newErrors.target_users = 'Please select at least one user'
  }

  if (!form.status) {
    newErrors.status = 'Status is required'
  }

  Object.assign(errors, newErrors)
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) return

  try {
    submitting.value = true

    if (props.announcement) {
      await announcementService.updateAnnouncement(props.announcement.id, form)
    } else {
      await announcementService.createAnnouncement(form)
    }

    emit('saved')
  } catch (error) {
    console.error('Error saving announcement:', error)
  } finally {
    submitting.value = false
  }
}

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  } else {
    form.image = undefined
    imagePreview.value = null
  }
}

const handleTargetTypeChange = () => {
  if (form.target_type !== 'specific') {
    form.target_users = []
  }
}

const getImageUrl = (imagePath: string) => {
  if (!imagePath) return '/placeholder-announcement.jpg'
  if (imagePath.startsWith('http')) return imagePath
  return `http://127.0.0.1:8000/${imagePath}`
}

const loadUsers = async () => {
  try {
    // This would typically be an API call to get users
    // For now, we'll use a mock implementation
    availableUsers.value = [
      { id: 1, name: 'System Administrator', email: 'admin@ccs.edu', role: 'admin' },
      { id: 2, name: 'Juan Dela Cruz', email: 'juan.professor@ccs.edu', role: 'professor' },
      { id: 3, name: 'Maria Student', email: 'maria.student@ccs.edu', role: 'student' },
      { id: 4, name: 'Jose Student', email: 'jose.student@ccs.edu', role: 'student' },
    ]
  } catch (error) {
    console.error('Error loading users:', error)
  }
}

// Initialize form when announcement prop changes
watch(() => props.announcement, (announcement) => {
  if (announcement) {
    form.title = announcement.title
    form.content = announcement.content
    form.target_type = announcement.target_type
    form.target_users = announcement.target_users || []
    form.status = announcement.status
    form.image = undefined
    imagePreview.value = null
  } else {
    form.title = ''
    form.content = ''
    form.target_type = 'all'
    form.target_users = []
    form.status = 'draft'
    form.image = undefined
    imagePreview.value = null
  }
}, { immediate: true })

onMounted(() => {
  loadUsers()
})
</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.modal-container {
  position: relative;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.dark .modal-container {
  background: #1e293b;
  border: 1px solid #334155;
}

.modal-form {
  display: flex;
  flex-direction: column;
}

/* Modal Header */
.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.dark .modal-header {
  border-color: #334155;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.5rem;
}

.dark .modal-title {
  color: #f9fafb;
}

.modal-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
}

.dark .modal-subtitle {
  color: #9ca3af;
}

/* Modal Body */
.modal-body {
  padding: 1.5rem;
  flex: 1;
}

/* Form Groups */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.dark .form-label {
  color: #d1d5db;
}

.required {
  color: #ef4444;
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  color: #374151;
  transition: all 0.2s;
}

.dark .form-input,
.dark .form-textarea,
.dark .form-select {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

.form-error {
  display: block;
  font-size: 0.75rem;
  color: #ef4444;
  margin-top: 0.25rem;
}

/* Image Upload */
.image-upload-area > * + * {
  margin-top: 0.5rem;
}

.image-preview {
  margin-top: 0.5rem;
}

.preview-img {
  height: 128px;
  width: 100%;
  object-fit: cover;
  border-radius: 8px;
}

.current-image-label {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.dark .current-image-label {
  color: #9ca3af;
}

/* Users List */
.users-list {
  max-height: 160px;
  overflow-y: auto;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 0.5rem;
}

.dark .users-list {
  background: #374151;
  border-color: #4b5563;
}

.user-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.user-item:last-child {
  margin-bottom: 0;
}

.form-checkbox {
  width: 1rem;
  height: 1rem;
  color: #f97316;
  border-color: #d1d5db;
  border-radius: 4px;
}

.user-label {
  margin-left: 0.5rem;
  font-size: 0.875rem;
  color: #374151;
  cursor: pointer;
}

.dark .user-label {
  color: #f9fafb;
}

/* Radio Group */
.radio-group {
  display: flex;
  gap: 1rem;
}

.radio-item {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.form-radio {
  width: 1rem;
  height: 1rem;
  color: #f97316;
  border-color: #d1d5db;
}

.radio-label {
  margin-left: 0.5rem;
  font-size: 0.875rem;
  color: #374151;
}

.dark .radio-label {
  color: #f9fafb;
}

/* Modal Footer */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.dark .modal-footer {
  border-color: #334155;
}

/* Buttons */
.btn-primary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  background: #f97316;
  color: white;
  border: none;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #ea580c;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  background: white;
  color: #374151;
  border: 1px solid #d1d5db;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.dark .btn-secondary {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #f97316;
  color: #f97316;
}

.dark .btn-secondary:hover {
  background: #4b5563;
  border-color: #f97316;
  color: #f97316;
}

/* Loading Spinner */
.loading-spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 640px) {
  .modal-overlay {
    padding: 0.5rem;
  }
  
  .modal-container {
    max-height: 95vh;
  }
  
  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 1rem;
  }
  
  .modal-footer {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
}
</style>
