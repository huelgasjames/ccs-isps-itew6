<template>
  <div class="dashboard-root">
    <!-- ── Main Content ── -->
    <main class="main-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb-bar">
        <span class="bc-prompt">$</span>
        <router-link to="/faculty/dashboard" class="bc-item">HOME</router-link>
        <span class="bc-sep">›</span>
        <span class="bc-current">FACULTY DASHBOARD</span>
      </div>

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="welcome-left">
          <div class="welcome-avatar">{{ user?.name?.charAt(0) ?? 'F' }}</div>
          <div>
            <h1 class="welcome-name">Welcome, <span class="accent">{{ user?.name }}</span></h1>
            <p class="welcome-sub">
              <span class="tag-chip">Professor</span>
              <span class="tag-chip">{{ facultyInfo?.department || 'CCS Department' }}</span>
              <span class="tag-chip">{{ facultyInfo?.designation || 'Assistant Professor' }}</span>
              <span v-if="isDemoMode" class="tag-chip demo-mode">Demo Mode</span>
            </p>
          </div>
        </div>
        <div class="welcome-right">
          <div class="time-display">
            <div class="time-value">{{ currentTime }}</div>
            <div class="time-date">{{ currentDate }}</div>
          </div>
        </div>
      </div>

      <!-- Announcements Section -->
      <div class="panel mb-section">
        <div class="panel-header">
          <div class="panel-title">Latest Announcements</div>
          <div class="header-actions">
            <router-link to="/announcements" class="text-sm text-blue-600 hover:text-blue-800">View All</router-link>
            <button 
              @click="$router.push('/announcements/create')"
              class="action-btn"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 4v16m8-8H4"/>
              </svg>
              Create Announcement
            </button>
          </div>
        </div>
        <div class="panel-body">
          <!-- Loading State -->
          <div v-if="loadingAnnouncements" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          </div>
          
          <!-- Announcements List -->
          <div v-else-if="announcements.length > 0" class="space-y-4">
            <div 
              v-for="announcement in announcements" 
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
                  
                  <button 
                    v-if="!announcement.is_viewed"
                    @click="markAnnouncementAsViewed(announcement.id)"
                    class="mark-read-btn"
                  >
                    Mark as read
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-else class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <p class="text-gray-500 text-sm">No announcements available</p>
          </div>
        </div>
      </div>

      <!-- Faculty Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="3" width="20" height="14" rx="2"/>
              <line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Total Courses</div>
            <div class="stat-value">{{ facultyStats.totalCourses }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ facultyStats.totalStudents }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Pending Reports</div>
            <div class="stat-value">{{ facultyStats.pendingReports }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11H3v10h6V11z"/>
              <path d="M15 3H9v18h6V3z"/>
              <path d="M21 7h-6v14h6V7z"/>
            </svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Today's Classes</div>
            <div class="stat-value">{{ facultyStats.todayClasses }}</div>
          </div>
        </div>
      </div>

      <!-- Today's Schedule & Recent Activities -->
      <div class="two-col-grid">
        <!-- Today's Schedule -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">Today's Schedule</div>
          </div>
          <div class="panel-body">
            <div v-if="todaySchedule.length === 0" class="text-center py-8 text-gray-500">
              <p>No classes scheduled for today</p>
            </div>
            <div v-else class="space-y-3">
              <div v-for="schedule in todaySchedule" :key="schedule.id" class="schedule-item">
                <div class="schedule-time">
                  <div class="time-start">{{ schedule.startTime }}</div>
                  <div class="time-end">{{ schedule.endTime }}</div>
                </div>
                <div class="schedule-info">
                  <div class="schedule-course">{{ schedule.course }}</div>
                  <div class="schedule-room">{{ schedule.room }} • {{ schedule.section }}</div>
                </div>
                <div class="schedule-status" :class="schedule.status">
                  {{ schedule.status }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">Recent Activities</div>
          </div>
          <div class="panel-body">
            <div v-if="recentActivities.length === 0" class="text-center py-8 text-gray-500">
              <p>No recent activities</p>
            </div>
            <div v-else class="space-y-3">
              <div v-for="activity in recentActivities" :key="activity.id" class="activity-row">
                <div class="activity-dot" :style="`--dot-color: ${activity.color}`"></div>
                <div class="activity-info">
                  <div class="activity-title">{{ activity.title }}</div>
                  <div class="activity-sub">{{ activity.description }}</div>
                </div>
                <div class="activity-time">{{ activity.time }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="panel mb-section">
        <div class="panel-header">
          <div class="panel-title">Quick Actions</div>
        </div>
        <div class="panel-body">
          <div class="actions-grid">
            <router-link to="/announcements/create" class="action-card green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
              </svg>
              <span>Create Announcement</span>
            </router-link>
            <router-link to="/courses" class="action-card blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
              </svg>
              <span>Manage Courses</span>
            </router-link>
            <router-link to="/students" class="action-card amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
              <span>View Students</span>
            </router-link>
            <router-link to="/profile" class="action-card purple">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span>My Profile</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Student Performance Overview -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">Student Performance Overview</div>
          <router-link to="/students" class="text-sm text-blue-600 hover:text-blue-800">View Details</router-link>
        </div>
        <div class="panel-body">
          <div class="performance-grid">
            <div class="performance-item">
              <div class="performance-label">Excellent (90-100%)</div>
              <div class="performance-bar">
                <div class="performance-fill excellent" :style="{ width: performanceData.excellent + '%' }"></div>
              </div>
              <div class="performance-count">{{ performanceData.excellentCount }} students</div>
            </div>
            <div class="performance-item">
              <div class="performance-label">Good (80-89%)</div>
              <div class="performance-bar">
                <div class="performance-fill good" :style="{ width: performanceData.good + '%' }"></div>
              </div>
              <div class="performance-count">{{ performanceData.goodCount }} students</div>
            </div>
            <div class="performance-item">
              <div class="performance-label">Fair (70-79%)</div>
              <div class="performance-bar">
                <div class="performance-fill fair" :style="{ width: performanceData.fair + '%' }"></div>
              </div>
              <div class="performance-count">{{ performanceData.fairCount }} students</div>
            </div>
            <div class="performance-item">
              <div class="performance-label">Needs Improvement (<70%)</div>
              <div class="performance-bar">
                <div class="performance-fill poor" :style="{ width: performanceData.poor + '%' }"></div>
              </div>
              <div class="performance-count">{{ performanceData.poorCount }} students</div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import { announcementService, type Announcement } from '@/services/announcements'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()
const user = computed(() => authStore.user)
const isDemoMode = computed(() => authStore.isDemoMode)

const now = ref(new Date())
let timer: any
const currentTime = computed(() => now.value.toLocaleTimeString('en-US', { hour12: false }))
const currentDate = computed(() => now.value.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }))

const announcements = ref<Announcement[]>([])
const loadingAnnouncements = ref(true)

const facultyInfo = ref({
  department: 'College of Computing Studies',
  designation: 'Assistant Professor'
})

const facultyStats = ref({
  totalCourses: 4,
  totalStudents: 156,
  pendingReports: 2,
  todayClasses: 3
})

const todaySchedule = ref([
  {
    id: 1,
    startTime: '7:30 AM',
    endTime: '9:00 AM',
    course: 'Web Development',
    room: 'Lab 301',
    section: 'BSIT-3A',
    status: 'completed'
  },
  {
    id: 2,
    startTime: '10:00 AM',
    endTime: '11:30 AM',
    course: 'Database Systems',
    room: 'Room 205',
    section: 'BSIT-2B',
    status: 'ongoing'
  },
  {
    id: 3,
    startTime: '2:00 PM',
    endTime: '3:30 PM',
    course: 'Data Structures',
    room: 'Lab 302',
    section: 'BSIT-1A',
    status: 'upcoming'
  }
])

const recentActivities = ref([
  {
    id: 1,
    title: 'Graded Assignment',
    description: 'Web Development - Project 2 (BSIT-3A)',
    time: '1 hour ago',
    color: '#10b981'
  },
  {
    id: 2,
    title: 'Posted Announcement',
    description: 'Midterm Exam Schedule',
    time: '3 hours ago',
    color: '#3b82f6'
  },
  {
    id: 3,
    title: 'Submitted Report',
    description: 'Monthly Progress Report',
    time: '1 day ago',
    color: '#8b5cf6'
  }
])

const performanceData = ref({
  excellent: 35,
  excellentCount: 55,
  good: 40,
  goodCount: 62,
  fair: 20,
  fairCount: 31,
  poor: 5,
  poorCount: 8
})

const fetchAnnouncements = async () => {
  try {
    loadingAnnouncements.value = true
    const allAnnouncements = await announcementService.getAnnouncements()
    
    if (!Array.isArray(allAnnouncements)) {
      console.warn('API did not return an array for announcements:', allAnnouncements)
      announcements.value = []
      return
    }
    
    announcements.value = allAnnouncements
      .filter(announcement => {
        if (announcement.status !== 'published') return false
        
        if (!authStore.user) return false
        
        if (announcement.target_type === 'all') return true
        if (announcement.target_type === 'professors') return true
        if (announcement.target_type === 'specific' && announcement.target_users) {
          return announcement.target_users.includes(authStore.user.id)
        }
        
        return false
      })
      .slice(0, 3)
  } catch (error) {
    console.error('Error fetching announcements:', error)
    announcements.value = []
  } finally {
    loadingAnnouncements.value = false
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
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
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

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.src = '/placeholder-announcement.jpg'
}

onMounted(() => {
  fetchAnnouncements()
  timer = setInterval(() => { now.value = new Date() }, 1000)
})

onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Root & Background ── */
.dashboard-root {
  min-height: 100vh;
  background: #ffffff;
  font-family: 'Inter', sans-serif;
  transition: background-color 0.3s;
}

.dark .dashboard-root {
  background: #0f172a;
}

/* ── Main Content ── */
.main-content { position: relative; z-index: 1; padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

/* Breadcrumb */
.breadcrumb-bar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.bc-prompt {
  color: #f97316;
  font-family: 'Inter', monospace;
}

.bc-item {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.bc-item:hover {
  color: #f97316;
}

.bc-sep {
  color: #d1d5db;
}

.bc-current {
  color: #374151;
  font-weight: 500;
}

/* Welcome Banner */
.welcome-banner {
  position: relative; padding: 1.5rem 1.5rem;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px; margin-bottom: 1.5rem;
  display: flex; align-items: center; justify-content: space-between; gap: 2rem;
}

.dark .welcome-banner {
  background: #1e293b;
  border-color: #334155;
}
.welcome-left { display: flex; align-items: center; gap: 1rem; }
.welcome-avatar {
  width: 48px; height: 48px; border-radius: 50%;
  background: linear-gradient(135deg, #10b981, #059669);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; font-weight: 600; color: white;
  flex-shrink: 0;
}
.welcome-name { font-size: 1.5rem; font-weight: 600; color: #111827; margin-bottom: 4px; }
.welcome-name .accent { color: #10b981; }
.welcome-sub { display: flex; gap: 8px; flex-wrap: wrap; }
.tag-chip {
  font-size: 0.75rem; padding: 4px 10px; border-radius: 4px;
  background: #f3f4f6; border: 1px solid #e5e7eb;
  color: #6b7280;
}
.tag-chip.demo-mode {
  background: #fef3c7; border-color: #f59e0b; color: #d97706;
}
.welcome-right { flex-shrink: 0; }
.time-display {
  text-align: right;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px; padding: 10px 14px;
}
.time-value { font-size: 1.25rem; font-weight: 600; color: #111827; }
.time-date { font-size: 0.75rem; color: #6b7280; margin-top: 2px; }

/* Panel Styles */
.panel {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.dark .panel {
  background: #1e293b;
  border-color: #334155;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.dark .panel-header {
  border-bottom-color: #334155;
}

.panel-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
}

.dark .panel-title {
  color: #f9fafb;
}

.panel-body {
  padding: 1.5rem;
}

.mb-section {
  margin-bottom: 1.5rem;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 6px;
  background: #3b82f6;
  border: none;
  color: white;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background: #2563eb;
}

.action-btn svg {
  width: 16px;
  height: 16px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.dark .stat-card {
  background: #1e293b;
  border-color: #334155;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon.amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.stat-body {
  flex: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.dark .stat-label {
  color: #9ca3af;
}

.stat-value {
  font-size: 1.875rem;
  font-weight: 700;
  color: #111827;
}

.dark .stat-value {
  color: #f9fafb;
}

/* Two Column Grid */
.two-col-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .two-col-grid {
    grid-template-columns: 1fr;
  }
}

/* Schedule Item */
.schedule-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 0.75rem;
}

.dark .schedule-item {
  border-color: #374151;
}

.schedule-time {
  text-align: center;
  min-width: 60px;
  flex-shrink: 0;
}

.time-start {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
}

.dark .time-start {
  color: #f9fafb;
}

.time-end {
  font-size: 0.75rem;
  color: #6b7280;
}

.dark .time-end {
  color: #9ca3af;
}

.schedule-info {
  flex: 1;
}

.schedule-course {
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.25rem;
}

.dark .schedule-course {
  color: #f9fafb;
}

.schedule-room {
  font-size: 0.75rem;
  color: #6b7280;
}

.dark .schedule-room {
  color: #9ca3af;
}

.schedule-status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
  flex-shrink: 0;
}

.schedule-status.completed {
  background: #10b981;
  color: white;
}

.schedule-status.ongoing {
  background: #3b82f6;
  color: white;
}

.schedule-status.upcoming {
  background: #f59e0b;
  color: white;
}

/* Activity Row */
.activity-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.dark .activity-row {
  border-bottom-color: #374151;
}

.activity-row:last-child {
  border-bottom: none;
}

.activity-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--dot-color);
  flex-shrink: 0;
}

.activity-info {
  flex: 1;
}

.activity-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.25rem;
}

.dark .activity-title {
  color: #f9fafb;
}

.activity-sub {
  font-size: 0.75rem;
  color: #6b7280;
}

.dark .activity-sub {
  color: #9ca3af;
}

.activity-time {
  font-size: 0.75rem;
  color: #9ca3af;
}

/* Actions Grid */
.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  color: white;
  font-weight: 500;
  transition: transform 0.2s, box-shadow 0.2s;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-card.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.action-card.green { background: linear-gradient(135deg, #10b981, #059669); }
.action-card.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
.action-card.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.action-card svg {
  width: 24px;
  height: 24px;
}

/* Performance Grid */
.performance-grid {
  display: grid;
  gap: 1rem;
}

.performance-item {
  display: grid;
  grid-template-columns: 150px 1fr 80px;
  align-items: center;
  gap: 1rem;
}

.performance-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
}

.dark .performance-label {
  color: #f9fafb;
}

.performance-bar {
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
}

.dark .performance-bar {
  background: #374151;
}

.performance-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.performance-fill.excellent { background: #10b981; }
.performance-fill.good { background: #3b82f6; }
.performance-fill.fair { background: #f59e0b; }
.performance-fill.poor { background: #ef4444; }

.performance-count {
  font-size: 0.75rem;
  color: #6b7280;
  text-align: right;
}

.dark .performance-count {
  color: #9ca3af;
}

/* Announcement Styles */
.announcement-item {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  transition: border-color 0.2s;
}

.dark .announcement-item {
  border-color: #374151;
}

.announcement-item.unread {
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.02);
}

.dark .announcement-item.unread {
  background: rgba(59, 130, 246, 0.05);
}

.announcement-header {
  margin-bottom: 0.75rem;
}

.announcement-icon {
  width: 32px;
  height: 32px;
  background: #f3f4f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
}

.dark .announcement-icon {
  background: #374151;
  color: #9ca3af;
}

.announcement-title {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.dark .announcement-title {
  color: #f9fafb;
}

.announcement-author {
  font-size: 0.875rem;
  color: #6b7280;
}

.dark .announcement-author {
  color: #9ca3af;
}

.announcement-time {
  font-size: 0.75rem;
  color: #9ca3af;
}

.unread-badge {
  background: #3b82f6;
  color: white;
  font-size: 0.75rem;
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 500;
}

.announcement-content {
  color: #374151;
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.dark .announcement-content {
  color: #d1d5db;
}

.announcement-image img {
  border-radius: 6px;
  margin-bottom: 0.75rem;
}

.announcement-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.75rem;
  border-top: 1px solid #f3f4f6;
}

.dark .announcement-footer {
  border-top-color: #374151;
}

.mark-read-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.mark-read-btn:hover {
  background: #2563eb;
}
</style>
