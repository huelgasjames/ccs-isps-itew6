<template>
  <div class="schedule-list-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Schedules Management</h1>
          <p>Manage CCS course schedules and timetables</p>
        </div>
        <div class="header-actions">
          <button @click="scheduleStore.fetchSchedules" class="btn btn-info btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M23 4v6h-6M1 20v-6h6"/>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Refresh
          </button>
          <div class="export-dropdown">
            <button @click="toggleExportMenu" class="btn btn-success btn-small" :disabled="generatingPDF">
              {{ generatingPDF ? '⏳ Generating...' : '📊 Export' }}
            </button>
            <div v-if="showExportMenu" class="export-menu">
              <button @click="exportScheduleListPDF" class="export-item">
                📄 Schedule List PDF
              </button>
              <button @click="exportStatisticsPDF" class="export-item">
                📈 Statistics Report PDF
              </button>
              <button @click="exportFilteredSchedulesPDF" class="export-item">
                🔍 Filtered Schedules PDF
              </button>
            </div>
          </div>
          <router-link 
            v-if="authStore.isAdmin" 
            to="/schedules/create" 
            class="btn btn-primary btn-small"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            Create New Schedule
          </router-link>
        </div>
      </div>
    </div>

    <!-- Top Statistics -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Schedules</h3>
          <div class="stat-number">{{ scheduleStore.schedules.length }}</div>
          <span class="stat-change">All course schedules</span>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M16 12l-4 4-4-4"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Active Schedules</h3>
          <div class="stat-number">{{ activeSchedulesCount }}</div>
          <span class="stat-change">Currently running</span>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Rooms Used</h3>
          <div class="stat-number">{{ uniqueRoomsCount }}</div>
          <span class="stat-change">Different rooms</span>
        </div>
      </div>

      <div class="stat-card danger">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Students</h3>
          <div class="stat-number">{{ totalStudentsCount }}</div>
          <span class="stat-change">Enrolled students</span>
        </div>
      </div>
    </div>

    <!-- Day Distribution -->
    <div class="analytics-section">
      <h2>Schedule Distribution by Day</h2>
      <div class="day-distribution">
        <div 
          v-for="day in analytics.schedulesByDay" 
          :key="day.day"
          class="day-card"
        >
          <div class="day-header">
            <h3>{{ day.day }}</h3>
            <span class="day-count">{{ day.count }} schedules</span>
          </div>
          <div class="day-progress">
            <div class="progress-bar" :style="{ width: day.percentage + '%' }"></div>
          </div>
          <div class="day-details">
            <span class="day-percentage">{{ day.percentage.toFixed(1) }}%</span>
            <span class="day-usage">Active schedules</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="filter.search"
          type="text"
          placeholder="Search schedules by course, section, room..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="filter.day" class="filter-select">
          <option value="">All Days</option>
          <option v-for="day in availableDays" :key="day" :value="day">
            {{ day }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="filter.room_type" class="filter-select">
          <option value="">All Room Types</option>
          <option v-for="type in availableRoomTypes" :key="type" :value="type">
            {{ type.charAt(0).toUpperCase() + type.slice(1) }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="filter.status" class="filter-select">
          <option value="">All Status</option>
          <option v-for="status in availableStatuses" :key="status" :value="status">
            {{ status.charAt(0).toUpperCase() + status.slice(1) }}
          </option>
        </select>
      </div>
      <button @click="resetFilter" class="btn btn-secondary btn-sm">
        Reset Filters
      </button>
    </div>

    <!-- Schedule Table -->
    <div class="schedule-table-container">
      <div v-if="scheduleStore.loading" class="loading">
        <div class="spinner"></div>
        <p>Loading schedules...</p>
      </div>
      
      <div v-else-if="scheduleStore.error" class="error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/>
          <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <h3>Error loading schedules</h3>
        <p>{{ scheduleStore.error }}</p>
      </div>

      <div v-else class="table-wrapper">
        <table class="schedule-table">
          <thead>
            <tr>
              <th>Course</th>
              <th>Section</th>
              <th>Room</th>
              <th>Day</th>
              <th>Time</th>
              <th>Capacity</th>
              <th>Status</th>
              <th v-if="authStore.isAdmin">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="schedule in filteredSchedules" :key="schedule.id">
              <td>
                <span class="course-code">{{ schedule.course?.course_code || schedule.course_id }}</span>
              </td>
              <td>
                <span class="section-name">{{ schedule.section }}</span>
              </td>
              <td>
                <span class="room-badge">{{ schedule.room }}</span>
              </td>
              <td>
                <span class="day-badge">{{ schedule.day_of_week }}</span>
              </td>
              <td>
                <span class="time-range">{{ schedule.start_time }} - {{ schedule.end_time }}</span>
              </td>
              <td>
                <div class="capacity-info">
                  <span class="capacity-count">{{ schedule.current_enrollment || 0 }}/{{ schedule.max_capacity }}</span>
                  <div class="capacity-bar">
                    <div 
                      class="capacity-fill" 
                      :class="getCapacityStatusClass(schedule.current_enrollment || 0, schedule.max_capacity)"
                      :style="{ width: getCapacityPercentage(schedule.current_enrollment || 0, schedule.max_capacity) + '%' }"
                    ></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="status-badge" :class="schedule.status">
                  {{ schedule.status }}
                </span>
              </td>
              <td v-if="authStore.isAdmin" class="actions-cell">
                <router-link :to="`/schedules/${schedule.id}/edit`" class="action-btn edit">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </router-link>
                <button class="action-btn delete" @click="removeSchedule(schedule.id)">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useScheduleStore } from '@/stores/schedule'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'

const authStore = useAuthStore()
const scheduleStore = useScheduleStore()
const showExportMenu = ref(false)
const generatingPDF = ref(false)

// Computed properties for statistics
const activeSchedulesCount = computed(() => 
  scheduleStore.schedules.filter(s => s.status === 'active').length
)

const uniqueRoomsCount = computed(() => 
  new Set(scheduleStore.schedules.map(s => s.room)).size
)

const totalStudentsCount = computed(() => 
  scheduleStore.schedules.reduce((total, s) => total + (s.current_enrollment || 0), 0)
)

// Analytics computed properties
const analytics = computed(() => {
  const dayGroups = scheduleStore.schedules.reduce((groups, schedule) => {
    const day = schedule.day_of_week
    if (!groups[day]) {
      groups[day] = { day, count: 0, percentage: 0 }
    }
    groups[day].count++
    return groups
  }, {} as Record<string, { day: string; count: number; percentage: number }>)

  const total = scheduleStore.schedules.length
  Object.values(dayGroups).forEach(group => {
    group.percentage = total > 0 ? (group.count / total) * 100 : 0
  })

  return {
    schedulesByDay: Object.values(dayGroups)
  }
})

// Filter and search computed properties
const filter = computed(() => ({
  search: '',
  day: '',
  room_type: '',
  status: ''
}))

const availableDays = computed(() => 
  [...new Set(scheduleStore.schedules.map(s => s.day_of_week))]
)

const availableRoomTypes = computed(() => 
  [...new Set(scheduleStore.schedules.map(s => s.room_type))]
)

const availableStatuses = computed(() => 
  [...new Set(scheduleStore.schedules.map(s => s.status))]
)

const filteredSchedules = computed(() => {
  let filtered = scheduleStore.schedules

  if (filter.value.search) {
    const search = filter.value.search.toLowerCase()
    filtered = filtered.filter(s => 
      (s.course?.course_code || s.course_id.toString()).toLowerCase().includes(search) ||
      s.section.toLowerCase().includes(search) ||
      s.room.toLowerCase().includes(search)
    )
  }

  if (filter.value.day) {
    filtered = filtered.filter(s => s.day_of_week === filter.value.day)
  }

  if (filter.value.room_type) {
    filtered = filtered.filter(s => s.room_type === filter.value.room_type)
  }

  if (filter.value.status) {
    filtered = filtered.filter(s => s.status === filter.value.status)
  }

  return filtered
})

// Helper methods
const getCapacityPercentage = (current: number, max: number) => {
  return max > 0 ? Math.min((current / max) * 100, 100) : 0
}

const getCapacityStatusClass = (current: number, max: number) => {
  const percentage = getCapacityPercentage(current, max)
  if (percentage >= 90) return 'danger'
  if (percentage >= 75) return 'warning'
  return 'success'
}

const resetFilter = () => {
  // Reset filter values
  filter.value.search = ''
  filter.value.day = ''
  filter.value.room_type = ''
  filter.value.status = ''
}

const removeSchedule = async (id: number) => {
  if (!confirm('Delete this schedule?')) return
  await scheduleStore.deleteSchedule(id)
}

// Export menu functions
function toggleExportMenu() {
  showExportMenu.value = !showExportMenu.value
}

function handleClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement
  if (!target.closest('.export-dropdown')) {
    showExportMenu.value = false
  }
}

// PDF Export Functions
async function exportScheduleListPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Complete Schedule List Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Total Schedules: ${scheduleStore.schedules.length}`, pageWidth / 2, 37, { align: 'center' })
    
    // Table headers
    const headers = ['Course', 'Section', 'Professor', 'Room', 'Day', 'Time', 'Status']
    const startY = 50
    const rowHeight = 8
    const colWidth = (pageWidth - 40) / headers.length
    
    pdf.setFontSize(10)
    pdf.setFont('helvetica', 'bold')
    headers.forEach((header, index) => {
      pdf.text(header, 20 + (index * colWidth), startY)
    })
    
    // Table data
    pdf.setFont('helvetica', 'normal')
    let currentY = startY + rowHeight
    
    scheduleStore.schedules.forEach((schedule, index) => {
      if (currentY > pdf.internal.pageSize.getHeight() - 20) {
        pdf.addPage()
        currentY = 20
        
        // Re-add headers on new page
        pdf.setFont('helvetica', 'bold')
        headers.forEach((header, headerIndex) => {
          pdf.text(header, 20 + (headerIndex * colWidth), currentY)
        })
        pdf.setFont('helvetica', 'normal')
        currentY += rowHeight
      }
      
      const rowData = [
        schedule.course?.course_code || `Course ${schedule.course_id}`,
        schedule.section,
        schedule.professor ? `${schedule.professor.first_name} ${schedule.professor.last_name}` : `Professor ${schedule.professor_id}`,
        schedule.room,
        schedule.day_of_week,
        `${schedule.start_time} - ${schedule.end_time}`,
        schedule.status
      ]
      
      rowData.forEach((data, dataIndex) => {
        const text = data.length > 15 ? data.substring(0, 15) + '...' : data
        pdf.text(text, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Schedule Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`schedule-list-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating PDF:', error)
    alert('Failed to generate PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

async function exportStatisticsPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('p', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Schedule Statistics Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    
    // Statistics
    let currentY = 50
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Overall Statistics', 20, currentY)
    
    currentY += 15
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    const stats = [
      { label: 'Total Schedules', value: scheduleStore.schedules.length },
      { label: 'Active Schedules', value: activeSchedulesCount.value },
      { label: 'Unique Rooms', value: uniqueRoomsCount.value },
      { label: 'Total Students', value: totalStudentsCount.value }
    ]
    
    stats.forEach(stat => {
      pdf.text(`${stat.label}: ${stat.value}`, 30, currentY)
      currentY += 10
    })
    
    // Day distribution
    currentY += 15
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Schedule Distribution by Day', 20, currentY)
    
    currentY += 10
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    analytics.value.schedulesByDay.forEach(dayData => {
      pdf.text(`${dayData.day}: ${dayData.count} schedules (${dayData.percentage.toFixed(1)}%)`, 30, currentY)
      currentY += 8
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Schedule Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`schedule-statistics-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating statistics PDF:', error)
    alert('Failed to generate statistics PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

async function exportFilteredSchedulesPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Filtered Schedules Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Filtered Schedules: ${filteredSchedules.value.length} of ${scheduleStore.schedules.length}`, pageWidth / 2, 37, { align: 'center' })
    
    // Active filters
    let filterText = 'Active Filters: '
    if (filter.value.search) filterText += `Search: "${filter.value.search}" `
    if (filter.value.day) filterText += `Day: ${filter.value.day} `
    if (filter.value.room_type) filterText += `Room Type: ${filter.value.room_type} `
    if (filter.value.status) filterText += `Status: ${filter.value.status} `
    
    if (filterText === 'Active Filters: ') filterText = 'No active filters'
    
    pdf.text(filterText, pageWidth / 2, 44, { align: 'center' })
    
    // Table
    const headers = ['Course', 'Section', 'Professor', 'Room', 'Day', 'Time', 'Status']
    const startY = 55
    const rowHeight = 8
    const colWidth = (pageWidth - 40) / headers.length
    
    pdf.setFontSize(10)
    pdf.setFont('helvetica', 'bold')
    headers.forEach((header, index) => {
      pdf.text(header, 20 + (index * colWidth), startY)
    })
    
    // Table data
    pdf.setFont('helvetica', 'normal')
    let currentY = startY + rowHeight
    
    filteredSchedules.value.forEach((schedule, index) => {
      if (currentY > pdf.internal.pageSize.getHeight() - 20) {
        pdf.addPage()
        currentY = 20
        
        // Re-add headers on new page
        pdf.setFont('helvetica', 'bold')
        headers.forEach((header, headerIndex) => {
          pdf.text(header, 20 + (headerIndex * colWidth), currentY)
        })
        pdf.setFont('helvetica', 'normal')
        currentY += rowHeight
      }
      
      const rowData = [
        schedule.course?.course_code || `Course ${schedule.course_id}`,
        schedule.section,
        schedule.professor ? `${schedule.professor.first_name} ${schedule.professor.last_name}` : `Professor ${schedule.professor_id}`,
        schedule.room,
        schedule.day_of_week,
        `${schedule.start_time} - ${schedule.end_time}`,
        schedule.status
      ]
      
      rowData.forEach((data, dataIndex) => {
        pdf.text(data, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    pdf.save(`filtered-schedules-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating filtered schedules PDF:', error)
    alert('Failed to generate filtered schedules PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  scheduleStore.fetchSchedules()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.schedule-list-view {
  min-height: 100vh;
  background: #f8f9fa;
  font-family: 'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding: 40px;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, #fff, #f8f9fa);
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(253, 126, 20, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.header-left h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-bottom: 8px;
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.header-left p {
  color: #6b7280;
  font-size: 1.1rem;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  color: white;
  box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(253, 126, 20, 0.4);
}

.btn-info {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-info:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
}

.btn-secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
}

.btn-small {
  padding: 10px 16px;
  font-size: 0.9rem;
}

.btn-sm {
  padding: 8px 12px;
  font-size: 0.85rem;
}

.icon-small {
  width: 16px;
  height: 16px;
}

/* Statistics Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-start), var(--color-end));
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
}

.stat-card.primary {
  --color-start: #3b82f6;
  --color-end: #2563eb;
}

.stat-card.success {
  --color-start: #10b981;
  --color-end: #059669;
}

.stat-card.warning {
  --color-start: #f59e0b;
  --color-end: #d97706;
}

.stat-card.danger {
  --color-start: #ef4444;
  --color-end: #dc2626;
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  background: linear-gradient(135deg, var(--color-start), var(--color-end));
  color: white;
}

.stat-icon svg {
  width: 28px;
  height: 28px;
}

.stat-content h3 {
  font-size: 0.9rem;
  color: #6b7280;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-bottom: 4px;
  line-height: 1;
}

.stat-change {
  font-size: 0.85rem;
  color: #9ca3af;
  font-weight: 500;
}

/* Analytics Section */
.analytics-section {
  background: white;
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.analytics-section h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.day-distribution {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.day-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.day-card:hover {
  background: #f3f4f6;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.day-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.day-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
}

.day-count {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 500;
}

.day-progress {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  margin-bottom: 12px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #fd7e14, #ff922b);
  border-radius: 4px;
  transition: width 0.5s ease;
}

.day-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.day-percentage {
  font-size: 1rem;
  font-weight: 700;
  color: #fd7e14;
}

.day-usage {
  font-size: 0.8rem;
  color: #9ca3af;
}

/* Filters */
.filters-container {
  background: white;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
  display: flex;
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 300px;
}

.search-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
}

.search-input:focus {
  outline: none;
  border-color: #fd7e14;
  background: white;
  box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.1);
}

.filter-group {
  min-width: 150px;
}

.filter-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #fd7e14;
  background: white;
  box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.1);
}

/* Table */
.schedule-table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.loading, .error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #fd7e14;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error svg {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin-bottom: 16px;
}

.error h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
}

.table-wrapper {
  overflow-x: auto;
}
.schedule-table th {
  background: #f8f9fa;
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e5e7eb;
}

.schedule-table td {
  padding: 16px;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.95rem;
}

.schedule-table tbody tr {
  transition: all 0.2s ease;
}

.schedule-table tbody tr:hover {
  background: #f8f9fa;
}

.course-code {
  font-weight: 700;
  color: #1a1a1a;
  background: #fef3c7;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.85rem;
}

.section-name {
  font-weight: 600;
  color: #374151;
}

.room-badge {
  background: #dbeafe;
  color: #1e40af;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.day-badge {
  background: #dcfce7;
  color: #166534;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.time-range {
  background: #f3f4f6;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 500;
  color: #374151;
  border: 1px solid #e5e7eb;
}

.capacity-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.capacity-count {
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
}

.capacity-bar {
  width: 100px;
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
}

.capacity-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.capacity-fill.success {
  background: linear-gradient(90deg, #10b981, #059669);
}

.capacity-fill.warning {
  background: linear-gradient(90deg, #f59e0b, #d97706);
}

.capacity-fill.danger {
  background: linear-gradient(90deg, #ef4444, #dc2626);
}

.status-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-badge.active {
  background: #dcfce7;
  color: #166534;
}

.status-badge.inactive {
  background: #f3f4f6;
  color: #6b7280;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

.actions-cell {
  display: flex;
  gap: 8px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 500;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn svg {
  width: 16px;
  height: 16px;
}

.action-btn.edit {
  background: #eff6ff;
  color: #2563eb;
}

.action-btn.edit:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}

.action-btn.delete {
  background: #fef2f2;
  color: #dc2626;
}

.action-btn.delete:hover {
  background: #fee2e2;
  transform: translateY(-1px);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Dark mode */
.dark .schedule-list-view {
  background: #0f172a;
}

.dark .page-header {
  background: linear-gradient(135deg, #1e293b, #334155);
  border-color: rgba(253, 126, 20, 0.2);
}

.dark .header-left h1 {
  background: linear-gradient(135deg, #f97316, #fb923c);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.dark .header-left p {
  color: #9ca3af;
}

.dark .stat-card {
  background: #1e293b;
  border-color: #334155;
}

.dark .stat-content h3 {
  color: #9ca3af;
}

.dark .stat-number {
  color: #f9fafb;
}

.dark .stat-change {
  color: #6b7280;
}

.dark .analytics-section {
  background: #1e293b;
  border-color: #334155;
}

.dark .analytics-section h2 {
  color: #f9fafb;
}

.dark .day-card {
  background: #374151;
  border-color: #4b5563;
}

.dark .day-card:hover {
  background: #4b5563;
}

.dark .day-header h3 {
  color: #f9fafb;
}

.dark .day-count {
  color: #9ca3af;
}

.dark .day-progress {
  background: #4b5563;
}

.dark .filters-container {
  background: #1e293b;
  border-color: #334155;
}

.dark .search-input,
.dark .filter-select {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .search-input:focus,
.dark .filter-select:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.dark .schedule-table-container {
  background: #1e293b;
  border-color: #334155;
}

.dark .schedule-table th {
  background: #374151;
  color: #f9fafb;
  border-color: #4b5563;
}

.dark .schedule-table td {
  border-color: #4b5563;
  color: #e5e7eb;
}

.dark .schedule-table tbody tr:hover {
  background: #374151;
}

.dark .course-code {
  background: rgba(249, 115, 22, 0.2);
  color: #fbbf24;
}

.dark .section-name {
  color: #e5e7eb;
}

.dark .room-badge {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
}

.dark .day-badge {
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
}

.dark .time-range {
  background: #374151;
  color: #e5e7eb;
  border-color: #4b5563;
}

.dark .capacity-count {
  color: #e5e7eb;
}

.dark .capacity-bar {
  background: #4b5563;
}

.dark .status-badge.active {
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
}

.dark .status-badge.inactive {
  background: rgba(107, 114, 128, 0.2);
  color: #9ca3af;
}

.dark .status-badge.cancelled {
  background: rgba(239, 68, 68, 0.2);
  color: #f87171;
}

/* Responsive */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .day-distribution {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .schedule-list-view {
    padding: 20px;
  }
  
  .page-header {
    padding: 24px;
    flex-direction: column;
    align-items: stretch;
    gap: 20px;
  }
  
  .header-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .header-left h1 {
    font-size: 2rem;
  }
  
  .header-actions {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .day-distribution {
    grid-template-columns: 1fr;
  }
  
  .filters-container {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-box {
    min-width: auto;
  }
  
  .filter-group {
    min-width: auto;
  }
  
  .schedule-table th,
  .schedule-table td {
    padding: 12px 8px;
    font-size: 0.85rem;
  }
  
  .actions-cell {
    flex-direction: column;
    gap: 4px;
  }
}
</style>
