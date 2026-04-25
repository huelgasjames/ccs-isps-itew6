<template>
  <div class="announcement-card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 mb-4">
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-start mb-4">
        <div class="flex-1">
          <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ announcement.title }}</h3>
          <div class="flex items-center text-sm text-gray-500 space-x-4">
            <span class="flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              {{ announcement.user?.name }}
            </span>
            <span class="flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {{ formatDate(announcement.created_at) }}
            </span>
            <span class="flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              {{ announcement.view_count || 0 }} views
            </span>
          </div>
        </div>
        <div class="flex flex-col items-end space-y-2">
          <span :class="getStatusClass(announcement.status)" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ announcement.status }}
          </span>
          <span :class="getTargetTypeClass(announcement.target_type)" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ getTargetTypeLabel(announcement.target_type) }}
          </span>
        </div>
      </div>

      <!-- Image -->
      <div v-if="announcement.image" class="mb-4">
        <img 
          :src="getImageUrl(announcement.image)" 
          :alt="announcement.title"
          class="w-full h-48 object-cover rounded-lg"
          @error="handleImageError"
        />
      </div>

      <!-- Content -->
      <div class="text-gray-700 mb-4">
        <p v-if="!showFullContent" class="line-clamp-3">{{ announcement.content }}</p>
        <p v-else>{{ announcement.content }}</p>
        
        <button 
          v-if="announcement.content.length > 150"
          @click="toggleContent"
          class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2"
        >
          {{ showFullContent ? 'Show less' : 'Read more' }}
        </button>
      </div>

      <!-- Actions -->
      <div class="flex justify-between items-center pt-4 border-t border-gray-200">
        <div class="flex space-x-2">
          <button 
            v-if="!announcement.is_viewed && canView"
            @click="markAsViewed"
            class="flex items-center px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Mark as Read
          </button>
          
          <span v-if="announcement.is_viewed" class="flex items-center px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-md">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Read
          </span>
        </div>

        <div class="flex space-x-2">
          <button 
            v-if="canEdit"
            @click="$emit('edit', announcement)"
            class="flex items-center px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </button>
          
          <button 
            v-if="canDelete"
            @click="$emit('delete', announcement)"
            class="flex items-center px-3 py-1 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete
          </button>
          
          <button 
            @click="showViews = !showViews"
            class="flex items-center px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Views ({{ announcement.view_count || 0 }})
          </button>
        </div>
      </div>

      <!-- Views List -->
      <div v-if="showViews && views.length > 0" class="mt-4 pt-4 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-900 mb-2">Viewed by:</h4>
        <div class="space-y-2">
          <div v-for="view in views" :key="view.id" class="flex items-center justify-between text-sm">
            <div class="flex items-center">
              <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                <span class="text-xs font-medium text-gray-600">{{ view.user?.name?.charAt(0) }}</span>
              </div>
              <span class="text-gray-700">{{ view.user?.name }}</span>
              <span class="text-gray-500 ml-2">({{ view.user?.role }})</span>
            </div>
            <span class="text-gray-500">{{ formatDate(view.viewed_at) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Announcement, AnnouncementView } from '@/services/announcements'
import { announcementService } from '@/services/announcements'
import { useAuthStore } from '@/stores/auth'

interface Props {
  announcement: Announcement
  canEdit?: boolean
  canDelete?: boolean
  canView?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  canEdit: false,
  canDelete: false,
  canView: true
})

const emit = defineEmits<{
  edit: [announcement: Announcement]
  delete: [announcement: Announcement]
  viewed: [announcementId: number]
}>()

const authStore = useAuthStore()
const showFullContent = ref(false)
const showViews = ref(false)
const views = ref<AnnouncementView[]>([])

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getImageUrl = (imagePath: string) => {
  if (!imagePath) return '/placeholder-announcement.jpg'
  if (imagePath.startsWith('http')) return imagePath
  return `http://127.0.0.1:8000/${imagePath}`
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.src = '/placeholder-announcement.jpg'
}

const toggleContent = () => {
  showFullContent.value = !showFullContent.value
}

const getStatusClass = (status: string) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-800'
    case 'draft':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getTargetTypeClass = (targetType: string) => {
  switch (targetType) {
    case 'all':
      return 'bg-blue-100 text-blue-800'
    case 'students':
      return 'bg-purple-100 text-purple-800'
    case 'professors':
      return 'bg-orange-100 text-orange-800'
    case 'specific':
      return 'bg-pink-100 text-pink-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getTargetTypeLabel = (targetType: string) => {
  switch (targetType) {
    case 'all':
      return 'All Users'
    case 'students':
      return 'Students'
    case 'professors':
      return 'Professors'
    case 'specific':
      return 'Specific Users'
    default:
      return targetType
  }
}

const markAsViewed = async () => {
  try {
    await announcementService.markAsViewed(props.announcement.id)
    props.announcement.is_viewed = true
    props.announcement.view_count = (props.announcement.view_count || 0) + 1
    emit('viewed', props.announcement.id)
  } catch (error) {
    console.error('Error marking announcement as viewed:', error)
  }
}

const loadViews = async () => {
  try {
    views.value = await announcementService.getAnnouncementViews(props.announcement.id)
  } catch (error) {
    console.error('Error loading views:', error)
  }
}

onMounted(() => {
  if (props.announcement.view_count && props.announcement.view_count > 0) {
    loadViews()
  }
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
