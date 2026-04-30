<template>
  <div class="course-list-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Courses Management</h1>
          <p>Manage CCS courses from 101 to 112</p>
        </div>
        <div class="header-actions">
          <button @click="courseStore.generateSampleData" class="btn btn-secondary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M12 2v20M17 7l-5-5-5 5M17 17l-5 5-5-5"/>
            </svg>
            Generate Sample Data
          </button>
          <div class="export-dropdown">
            <button @click="toggleExportMenu" class="btn btn-success btn-small" :disabled="generatingPDF">
              {{ generatingPDF ? '⏳ Generating...' : '📊 Export' }}
            </button>
            <div v-if="showExportMenu" class="export-menu">
              <button @click="exportCourseListPDF" class="export-item">
                📄 Course List PDF
              </button>
              <button @click="exportStatisticsPDF" class="export-item">
                📈 Statistics Report PDF
              </button>
              <button @click="exportFilteredCoursesPDF" class="export-item">
                🔍 Filtered Courses PDF
              </button>
            </div>
          </div>
          <button @click="courseStore.fetchCourses" class="btn btn-info btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M23 4v6h-6M1 20v-6h6"/>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Refresh
          </button>
          <router-link to="/courses/create" class="btn btn-primary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            Create New Course
          </router-link>
        </div>
      </div>
    </div>

    <!-- Top Statistics -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6 4l8 8v8H6z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Courses</h3>
          <div class="stat-number">{{ courseStore.analytics.totalCourses }}</div>
          <span class="stat-change">Across all departments</span>
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
          <h3>Active Courses</h3>
          <div class="stat-number">{{ courseStore.analytics.activeCourses }}</div>
          <span class="stat-change">Currently running</span>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Departments</h3>
          <div class="stat-number">{{ courseStore.analytics.totalDepartments }}</div>
          <span class="stat-change">Academic departments</span>
        </div>
      </div>

      <div class="stat-card danger">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Students</h3>
          <div class="stat-number">{{ courseStore.analytics.totalStudents }}</div>
          <span class="stat-change">Enrolled students</span>
        </div>
      </div>
    </div>

    <!-- Department Distribution -->
    <div class="analytics-section">
      <h2>Department Distribution</h2>
      <div class="department-distribution">
        <div 
          v-for="dept in courseStore.analytics.coursesByDepartment" 
          :key="dept.department"
          class="dept-card"
        >
          <div class="dept-header">
            <h3>{{ dept.department }}</h3>
            <span class="dept-count">{{ dept.count }} courses</span>
          </div>
          <div class="dept-progress">
            <div class="progress-bar" :style="{ width: dept.percentage + '%' }"></div>
          </div>
          <div class="dept-details">
            <span class="dept-percentage">{{ dept.percentage.toFixed(1) }}%</span>
            <span class="dept-employment">Active courses</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="courseStore.filter.search"
          type="text"
          placeholder="Search courses by code, name, instructor..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="courseStore.filter.department" class="filter-select">
          <option value="">All Departments</option>
          <option v-for="dept in courseStore.availableDepartments" :key="dept" :value="dept">
            {{ dept }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="courseStore.filter.semester" class="filter-select">
          <option value="">All Semesters</option>
          <option v-for="semester in courseStore.availableSemesters" :key="semester" :value="semester">
            {{ semester }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="courseStore.filter.status" class="filter-select">
          <option value="">All Status</option>
          <option v-for="status in courseStore.availableStatuses" :key="status" :value="status">
            {{ status.charAt(0).toUpperCase() + status.slice(1) }}
          </option>
        </select>
      </div>
      <button @click="courseStore.resetFilter" class="btn btn-secondary btn-sm">
        Reset Filters
      </button>
    </div>

    <!-- Course Table -->
    <div class="course-table-container">
      <div v-if="courseStore.loading" class="loading">
        <div class="spinner"></div>
        <p>Loading courses...</p>
      </div>
      
      <div v-else-if="courseStore.error" class="error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/>
          <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <h3>Error loading courses</h3>
        <p>{{ courseStore.error }}</p>
      </div>

      <div v-else class="table-wrapper">
        <table class="course-table">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Credits</th>
              <th>Department</th>
              <th>Semester</th>
              <th>Instructor</th>
              <th>Students</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="course in courseStore.filteredCourses" :key="course.id">
              <td>
                <span class="course-code">{{ course.courseCode }}</span>
              </td>
              <td>
                <span class="course-name">{{ course.courseName }}</span>
              </td>
              <td>
                <span class="credits-badge">{{ course.credits }}</span>
              </td>
              <td>
                <span class="department-badge">{{ course.department }}</span>
              </td>
              <td>
                <span class="semester-badge">{{ course.semester }}</span>
              </td>
              <td>
                <span class="instructor-name">{{ course.instructor || 'TBA' }}</span>
              </td>
              <td>
                <div class="enrollment-info">
                  <span class="student-count">{{ course.currentStudents }}/{{ course.maxStudents }}</span>
                  <div class="enrollment-bar">
                    <div 
                      class="enrollment-fill" 
                      :class="getEnrollmentStatusClass(course.currentStudents, course.maxStudents)"
                      :style="{ width: (course.currentStudents / course.maxStudents * 100) + '%' }"
                    ></div>
                  </div>
                  <span 
                    class="enrollment-status" 
                    :class="getEnrollmentStatusClass(course.currentStudents, course.maxStudents)"
                  >
                    {{ getEnrollmentStatus(course.currentStudents, course.maxStudents) }}
                  </span>
                </div>
              </td>
              <td>
                <span :class="['status-badge', getStatusClass(course.status)]">
                  {{ course.status.charAt(0).toUpperCase() + course.status.slice(1) }}
                </span>
              </td>
              <td>
                <router-link :to="`/courses/${course.id}`" class="btn btn-info btn-small">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  View
                </router-link>
                <router-link :to="`/courses/${course.id}/edit`" class="btn btn-warning btn-small">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </router-link>
                <button @click="archiveCourse(course.id)" class="btn btn-danger btn-small">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
                    <path d="M21 8v13H3V8"/>
                    <rect x="1" y="3" width="22" height="5" rx="1" ry="1"/>
                  </svg>
                  Archive
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="courseStore.filteredCourses.length === 0" class="no-results">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
          <h3>No courses found</h3>
          <p>No courses match your current filters. Try adjusting your search criteria.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useCourseStore } from '@/stores/course'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'

const courseStore = useCourseStore()
const showExportMenu = ref(false)
const generatingPDF = ref(false)

const archiveCourse = async (courseId: number) => {
  if (confirm('Are you sure you want to archive this course? This will hide it from the active course list.')) {
    const success = await courseStore.archiveCourse(courseId)
    if (success) {
      alert('Course archived successfully!')
    } else {
      alert('Failed to archive course. Please try again.')
    }
  }
}

const getStatusClass = (status: string) => {
  if (status === 'active') return 'active'
  if (status === 'inactive') return 'inactive'
  return 'archived'
}

const getEnrollmentStatusClass = (current: number, max: number) => {
  const percentage = (current / max) * 100
  if (percentage >= 100) return 'full'
  if (percentage >= 80) return 'almost-full'
  if (percentage >= 60) return 'moderate'
  return 'available'
}

const getEnrollmentStatus = (current: number, max: number) => {
  const percentage = (current / max) * 100
  if (percentage >= 100) return 'Full'
  if (percentage >= 80) return 'Almost Full'
  if (percentage >= 60) return 'Moderate'
  return 'Available'
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
async function exportCourseListPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Complete Course List Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Total Courses: ${courseStore.courses.length}`, pageWidth / 2, 37, { align: 'center' })
    
    // Table headers
    const headers = ['Code', 'Name', 'Department', 'Credits', 'Semester', 'Year']
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
    
    courseStore.courses.forEach((course, index) => {
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
        course.courseCode,
        course.courseName,
        course.department,
        course.credits.toString(),
        course.semester,
        course.academicYear
      ]
      
      rowData.forEach((data, dataIndex) => {
        const text = data.length > 15 ? data.substring(0, 15) + '...' : data
        pdf.text(text, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Course Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`course-list-${Date.now()}.pdf`)
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
    pdf.text('Course Statistics Report', pageWidth / 2, 20, { align: 'center' })
    
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
    
    const totalCourses = courseStore.courses.length
    const totalCredits = courseStore.courses.reduce((sum, course) => sum + course.credits, 0)
    const departments = [...new Set(courseStore.courses.map(c => c.department))]
    
    const stats = [
      { label: 'Total Courses', value: totalCourses },
      { label: 'Total Credits', value: totalCredits },
      { label: 'Total Departments', value: departments.length }
    ]
    
    stats.forEach(stat => {
      pdf.text(`${stat.label}: ${stat.value}`, 30, currentY)
      currentY += 10
    })
    
    // Department distribution
    currentY += 15
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Department Distribution', 20, currentY)
    
    currentY += 10
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    const deptGroups = courseStore.courses.reduce((groups: any, course: any) => {
      const dept = course.department
      if (!groups[dept]) groups[dept] = { count: 0, credits: 0 }
      groups[dept].count++
      groups[dept].credits += course.credits
      return groups
    }, {})
    
    Object.entries(deptGroups).forEach(([dept, data]: [string, any]) => {
      pdf.text(`${dept}: ${data.count} courses (${data.credits} credits)`, 30, currentY)
      currentY += 8
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Course Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`course-statistics-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating statistics PDF:', error)
    alert('Failed to generate statistics PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

async function exportFilteredCoursesPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Filtered Courses Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Filtered Courses: ${courseStore.filteredCourses.length} of ${courseStore.courses.length}`, pageWidth / 2, 37, { align: 'center' })
    
    // Active filters
    let filterText = 'Active Filters: '
    if (courseStore.filter.search) filterText += `Search: "${courseStore.filter.search}" `
    if (courseStore.filter.department) filterText += `Department: ${courseStore.filter.department} `
    if (courseStore.filter.semester) filterText += `Semester: ${courseStore.filter.semester} `
    
    if (filterText === 'Active Filters: ') filterText = 'No active filters'
    
    pdf.text(filterText, pageWidth / 2, 44, { align: 'center' })
    
    // Table
    const headers = ['Code', 'Name', 'Department', 'Credits', 'Semester', 'Year']
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
    
    courseStore.filteredCourses.forEach((course, index) => {
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
        course.courseCode,
        course.courseName,
        course.department,
        course.credits.toString(),
        course.semester,
        course.academicYear
      ]
      
      rowData.forEach((data, dataIndex) => {
        pdf.text(data, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    pdf.save(`filtered-courses-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating filtered courses PDF:', error)
    alert('Failed to generate filtered courses PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  courseStore.fetchCourses()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

</script>

<style scoped>
.course-list-view {
  padding: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-left h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.header-left p {
  color: #6b7280;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

/* Export dropdown */
.export-dropdown {
  position: relative;
  display: inline-block;
}

.export-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  min-width: 200px;
  margin-top: 0.5rem;
}

.export-item {
  display: block;
  width: 100%;
  padding: 0.75rem 1rem;
  text-align: left;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 0.875rem;
  color: #374151;
  transition: background-color 0.2s;
}

.export-item:hover {
  background-color: #f3f4f6;
}

.export-item:first-child {
  border-radius: 0.5rem 0.5rem 0 0;
}

.export-item:last-child {
  border-radius: 0 0 0.5rem 0.5rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  border: none;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

.icon-small {
  width: 16px;
  height: 16px;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-secondary {
  background-color: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background-color: #4b5563;
}

.btn-info {
  background-color: #0ea5e9;
  color: white;
}

.btn-info:hover {
  background-color: #0284c7;
}

.btn-warning {
  background-color: #f59e0b;
  color: white;
}

.btn-warning:hover {
  background-color: #d97706;
}

.btn-danger {
  background-color: #ef4444;
  color: white;
}

.btn-danger:hover {
  background-color: #dc2626;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-card.primary {
  border-left: 4px solid #3b82f6;
}

.stat-card.success {
  border-left: 4px solid #10b981;
}

.stat-card.warning {
  border-left: 4px solid #f59e0b;
}

.stat-card.danger {
  border-left: 4px solid #ef4444;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.stat-card.primary .stat-icon {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.stat-card.success .stat-icon {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card.warning .stat-icon {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card.danger .stat-icon {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-content h3 {
  margin: 0 0 0.5rem 0;
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.stat-change {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
}

/* Analytics Sections */
.analytics-section {
  margin: 3rem 0;
}

.analytics-section h2 {
  margin: 0 0 1.5rem 0;
  color: #1f2937;
  font-size: 1.5rem;
  font-weight: 600;
}

/* Department Distribution */
.department-distribution {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.dept-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #3b82f6;
}

.dept-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.dept-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
}

.dept-count {
  color: #6b7280;
  font-size: 0.875rem;
}

.dept-progress {
  background: #f3f4f6;
  height: 8px;
  border-radius: 4px;
  margin-bottom: 0.75rem;
  overflow: hidden;
}

.progress-bar {
  background: linear-gradient(90deg, #3b82f6, #2563eb);
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.dept-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dept-percentage {
  font-weight: 600;
  color: #1f2937;
}

.dept-employment {
  font-size: 0.75rem;
  color: #6b7280;
}

/* Filters */
.filters-container {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 250px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-group {
  min-width: 150px;
}

.filter-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  background-color: white;
  transition: border-color 0.2s;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Course Table */
.course-table-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table-wrapper {
  overflow-x: auto;
}

.course-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1000px;
}

.course-table th,
.course-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.course-table th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.course-table tbody tr:hover {
  background-color: #f9fafb;
}

.course-code {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #1f2937;
  background: #f3f4f6;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.course-name {
  font-weight: 500;
  color: #374151;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.credits-badge {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 0.875rem;
}

.department-badge {
  background-color: #dbeafe;
  color: #1e40af;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.semester-badge {
  background-color: #f3f4f6;
  color: #374151;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.instructor-name {
  color: #374151;
  font-size: 0.875rem;
}

.student-count {
  font-size: 0.875rem;
  color: #6b7280;
  display: block;
  margin-bottom: 0.25rem;
}

.enrollment-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.enrollment-bar {
  width: 100%;
  height: 6px;
  background: #f3f4f6;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 0.25rem;
}

.enrollment-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.enrollment-fill.available {
  background: linear-gradient(90deg, #10b981, #059669);
}

.enrollment-fill.moderate {
  background: linear-gradient(90deg, #f59e0b, #d97706);
}

.enrollment-fill.almost-full {
  background: linear-gradient(90deg, #f97316, #ea580c);
}

.enrollment-fill.full {
  background: linear-gradient(90deg, #ef4444, #dc2626);
}

.enrollment-status {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.125rem 0.5rem;
  border-radius: 9999px;
  text-align: center;
}

.enrollment-status.available {
  background-color: #d1fae5;
  color: #065f46;
}

.enrollment-status.moderate {
  background-color: #fef3c7;
  color: #92400e;
}

.enrollment-status.almost-full {
  background-color: #fed7aa;
  color: #9a3412;
}

.enrollment-status.full {
  background-color: #fee2e2;
  color: #991b1b;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.active {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.inactive {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.archived {
  background-color: #f3f4f6;
  color: #374151;
}

/* States */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  text-align: center;
  color: #6b7280;
}

.error svg {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin-bottom: 1rem;
}

.error h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.error p {
  color: #6b7280;
  margin: 0 0 1.5rem 0;
}

.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  text-align: center;
  color: #6b7280;
}

.no-results svg {
  width: 48px;
  height: 48px;
  color: #9ca3af;
  margin-bottom: 1rem;
}

.no-results h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.no-results p {
  color: #6b7280;
  margin: 0;
  max-width: 400px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .course-list-view {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .header-actions {
    width: 100%;
    justify-content: flex-start;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .department-distribution {
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
  
  .course-table {
    font-size: 0.75rem;
  }
  
  .course-table th,
  .course-table td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 480px) {
  .header-left h1 {
    font-size: 1.5rem;
  }
  
  .btn {
    font-size: 0.75rem;
  }
  
  .btn-small {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
  }
  
  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.7rem;
  }
  
  .stat-card {
    flex-direction: column;
    text-align: center;
  }
  
  .stat-icon {
    margin: 0 auto 1rem auto;
  }
}
</style>
