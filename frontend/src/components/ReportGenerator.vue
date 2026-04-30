<template>
  <div class="report-generator">
    <!-- Report Generation Button -->
    <button @click="showReportModal = true" class="btn btn-report">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14,2 14,8 20,8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10,9 9,9 8,9"/>
      </svg>
      Generate Report
    </button>

    <!-- Report Modal -->
    <div v-if="showReportModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Generate {{ reportTitle }} Report</h2>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>

        <div class="modal-body">
          <!-- Report Type Selection -->
          <div class="form-group">
            <label>Report Type</label>
            <select v-model="selectedReportType" class="form-select">
              <option value="summary">Summary Report</option>
              <option value="detailed">Detailed Report</option>
              <option value="analytics">Analytics Report</option>
              <option value="custom">Custom Report</option>
            </select>
          </div>

          <!-- Data Filters Info -->
          <div class="filters-info">
            <h3>Current Filters Applied:</h3>
            <div v-if="hasActiveFilters" class="active-filters">
              <div v-if="props.filters.searchQuery" class="filter-tag">
                Search: "{{ props.filters.searchQuery }}"
              </div>
              <div v-if="props.filters.selectedYear" class="filter-tag">
                Year: {{ props.filters.selectedYear }}
              </div>
              <div v-if="props.filters.selectedStanding" class="filter-tag">
                Standing: {{ formatStanding(props.filters.selectedStanding) }}
              </div>
              <div v-if="props.filters.selectedSkill" class="filter-tag">
                Skill: {{ props.filters.selectedSkill }}
              </div>
              <div v-if="props.filters.selectedAffiliation" class="filter-tag">
                Affiliation: {{ props.filters.selectedAffiliation }}
              </div>
              <div v-if="props.filters.selectedViolationStatus" class="filter-tag">
                Violation Status: {{ props.filters.selectedViolationStatus }}
              </div>
            </div>
            <div v-else class="no-filters">
              No filters applied - report will include all data
            </div>
          </div>

          <!-- Custom Report Options -->
          <div v-if="selectedReportType === 'custom'" class="custom-options">
            <h3>Custom Report Options</h3>
            <div class="checkbox-group">
              <label v-for="option in customOptions" :key="option.key" class="checkbox-label">
                <input 
                  type="checkbox" 
                  v-model="selectedCustomOptions" 
                  :value="option.key"
                  class="checkbox"
                />
                {{ option.label }}
              </label>
            </div>
          </div>

          <!-- Export Format -->
          <div class="form-group">
            <label>Export Format</label>
            <div class="format-options">
              <label v-for="format in exportFormats" :key="format.value" class="radio-label">
                <input 
                  type="radio" 
                  v-model="selectedFormat" 
                  :value="format.value"
                  class="radio"
                />
                {{ format.label }}
              </label>
            </div>
          </div>

          <!-- Report Preview -->
          <div class="preview-section">
            <h3>Report Preview</h3>
            <div class="preview-content">
              <div class="preview-stats">
                <div class="preview-stat">
                  <span class="stat-label">Total Records:</span>
                  <span class="stat-value">{{ filteredData.length }}</span>
                </div>
                <div class="preview-stat">
                  <span class="stat-label">Report Type:</span>
                  <span class="stat-value">{{ formatReportType(selectedReportType) }}</span>
                </div>
                <div class="preview-stat">
                  <span class="stat-label">Format:</span>
                  <span class="stat-value">{{ selectedFormat }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="closeModal" class="btn btn-secondary">Cancel</button>
          <button @click="generateReport" class="btn btn-primary" :disabled="isGenerating">
            <span v-if="isGenerating">Generating...</span>
            <span v-else>Generate Report</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Report Preview Modal -->
    <div v-if="showPreviewModal" class="modal-overlay" @click="closePreview">
      <div class="modal-content large" @click.stop>
        <div class="modal-header">
          <h2>Report Preview</h2>
          <button @click="closePreview" class="close-btn">&times;</button>
        </div>

        <div class="modal-body">
          <div class="report-preview" v-html="reportPreview"></div>
        </div>

        <div class="modal-footer">
          <button @click="closePreview" class="btn btn-secondary">Close</button>
          <button @click="downloadReport" class="btn btn-success">
            Download {{ selectedFormat }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, defineProps, defineEmits, withDefaults } from 'vue'
import api from '@/services/api'

// Props
interface Props {
  data: any[]
  reportTitle: string
  fetchFromAPI?: boolean
  filters?: {
    searchQuery?: string
    selectedYear?: string
    selectedStanding?: string
    selectedSkill?: string
    selectedAffiliation?: string
    selectedViolationStatus?: string
  }
}

const props = withDefaults(defineProps<Props>(), {
  fetchFromAPI: false,
  filters: () => ({
    searchQuery: '',
    selectedYear: '',
    selectedStanding: '',
    selectedSkill: '',
    selectedAffiliation: '',
    selectedViolationStatus: ''
  })
})

// Emits
const emit = defineEmits<{
  generateReport: [reportData: any]
}>()

// State
const showReportModal = ref(false)
const showPreviewModal = ref(false)
const selectedReportType = ref('summary')
const selectedFormat = ref('pdf')
const selectedCustomOptions = ref<string[]>([])
const isGenerating = ref(false)
const reportPreview = ref('')

// Options
const exportFormats = [
  { value: 'pdf', label: 'PDF Document' },
  { value: 'csv', label: 'CSV Spreadsheet' },
  { value: 'excel', label: 'Excel Workbook' },
  { value: 'html', label: 'HTML Web Page' }
]

const customOptions = [
  { key: 'personalInfo', label: 'Personal Information' },
  { key: 'academicStanding', label: 'Academic Standing' },
  { key: 'skills', label: 'Skills & Competencies' },
  { key: 'affiliations', label: 'Affiliations & Organizations' },
  { key: 'violations', label: 'Violation Records' },
  { key: 'courses', label: 'Enrolled Courses' },
  { key: 'analytics', label: 'Analytics & Statistics' }
]

// Computed
const hasActiveFilters = computed(() => {
  return Object.values(props.filters).some(value => value && value !== '')
})

const filteredData = computed(() => {
  // This should match the filtered data from the parent component
  return props.data
})

// Function to fetch real API data
const fetchAPIData = async (reportType: string, filters: any) => {
  try {
    const response = await api.get(`/reports/${reportType}`, {
      params: filters
    })
    return response.data
  } catch (error) {
    console.error('Error fetching API data:', error)
    throw error
  }
}

// Methods
const closeModal = () => {
  showReportModal.value = false
  selectedReportType.value = 'summary'
  selectedFormat.value = 'pdf'
  selectedCustomOptions.value = []
}

const closePreview = () => {
  showPreviewModal.value = false
  reportPreview.value = ''
}

const formatStanding = (standing: string) => {
  return standing.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatReportType = (type: string) => {
  const types: Record<string, string> = {
    summary: 'Summary Report',
    detailed: 'Detailed Report',
    analytics: 'Analytics Report',
    custom: 'Custom Report'
  }
  return types[type] || type
}

const generateReport = async () => {
  isGenerating.value = true
  
  try {
    let reportData: any
    
    // Try to fetch from API if not in demo mode
    const token = localStorage.getItem('auth_token')
    const isDemo = token ? token.startsWith('demo-') : false
    
    if (!isDemo && props.fetchFromAPI) {
      try {
        const apiData = await fetchAPIData(selectedReportType.value, props.filters)
        reportData = {
          type: selectedReportType.value,
          format: selectedFormat.value,
          filters: props.filters,
          data: apiData,
          customOptions: selectedCustomOptions.value,
          timestamp: new Date().toISOString(),
          title: props.reportTitle,
          dataSource: 'API'
        }
      } catch (apiError) {
        console.warn('API fetch failed, using local data:', apiError)
        // Fallback to local data
        reportData = {
          type: selectedReportType.value,
          format: selectedFormat.value,
          filters: props.filters,
          data: filteredData.value,
          customOptions: selectedCustomOptions.value,
          timestamp: new Date().toISOString(),
          title: props.reportTitle,
          dataSource: 'Local'
        }
      }
    } else {
      // Use local/demo data
      reportData = {
        type: selectedReportType.value,
        format: selectedFormat.value,
        filters: props.filters,
        data: filteredData.value,
        customOptions: selectedCustomOptions.value,
        timestamp: new Date().toISOString(),
        title: props.reportTitle,
        dataSource: isDemo ? 'Demo' : 'Local'
      }
    }

    // Generate preview
    reportPreview.value = generateReportPreview(reportData)

    // Show preview modal
    showPreviewModal.value = true
    showReportModal.value = false

    // Emit event to parent
    emit('generateReport', reportData)
  } catch (error) {
    console.error('Error generating report:', error)
    alert('Error generating report. Please try again.')
  } finally {
    isGenerating.value = false
  }
}

const generateReportPreview = (reportData: any) => {
  const { type, data, filters, title } = reportData
  
  let html = `
    <div class="report-preview-content">
      <div class="report-header">
        <h1>${title} - ${formatReportType(type)}</h1>
        <p>Generated on: ${new Date().toLocaleString()}</p>
        <p>Total Records: ${data.length}</p>
      </div>
  `

  // Add filters section if active
  if (hasActiveFilters.value) {
    html += `
      <div class="report-filters">
        <h2>Applied Filters</h2>
        <ul>
    `
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value && value !== '') {
        html += `<li><strong>${key}:</strong> ${value}</li>`
      }
    })
    
    html += `
        </ul>
      </div>
    `
  }

  // Add content based on report type
  switch (type) {
    case 'summary':
      html += generateSummaryReport(data)
      break
    case 'detailed':
      html += generateDetailedReport(data)
      break
    case 'analytics':
      html += generateAnalyticsReport(data)
      break
    case 'custom':
      html += generateCustomReport(data, selectedCustomOptions.value)
      break
  }

  html += `</div>`
  return html
}

const generateSummaryReport = (data: any[]) => {
  // Generate summary statistics
  const totalStudents = data.length
  const avgGPA = data.reduce((sum, student) => sum + (student.academicStanding?.currentGPA || 0), 0) / totalStudents
  const atRiskCount = data.filter(s => s.academicStanding?.standing === 'probation').length
  const totalViolations = data.reduce((sum, student) => sum + (student.violations?.length || 0), 0)

  return `
    <div class="summary-report">
      <h2>Summary Statistics</h2>
      <div class="stats-grid">
        <div class="stat-item">
          <h3>${totalStudents}</h3>
          <p>Total Students</p>
        </div>
        <div class="stat-item">
          <h3>${avgGPA.toFixed(2)}</h3>
          <p>Average GPA</p>
        </div>
        <div class="stat-item">
          <h3>${atRiskCount}</h3>
          <p>At Risk Students</p>
        </div>
        <div class="stat-item">
          <h3>${totalViolations}</h3>
          <p>Total Violations</p>
        </div>
      </div>
    </div>
  `
}

const generateDetailedReport = (data: any[]) => {
  let html = `
    <div class="detailed-report">
      <h2>Detailed Student Information</h2>
      <table class="report-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Year</th>
            <th>GPA</th>
            <th>Standing</th>
            <th>Skills</th>
            <th>Affiliations</th>
            <th>Violations</th>
          </tr>
        </thead>
        <tbody>
  `

  data.forEach(student => {
    const skills = (student.skills || []).slice(0, 3).map((s: any) => s.name).join(', ')
    const affiliations = (student.affiliations || []).slice(0, 2).map((a: any) => a.name).join(', ')
    const violationCount = student.violations?.length || 0

    html += `
      <tr>
        <td>${student.id}</td>
        <td>${student.personalInfo?.firstName} ${student.personalInfo?.lastName}</td>
        <td>${student.personalInfo?.email}</td>
        <td>${student.academicStanding?.currentYear}</td>
        <td>${student.academicStanding?.currentGPA || 'N/A'}</td>
        <td>${formatStanding(student.academicStanding?.standing || '')}</td>
        <td>${skills || 'None'}</td>
        <td>${affiliations || 'None'}</td>
        <td>${violationCount}</td>
      </tr>
    `
  })

  html += `
        </tbody>
      </table>
    </div>
  `

  return html
}

const generateAnalyticsReport = (data: any[]) => {
  // Generate analytics insights
  const yearDistribution = data.reduce((acc, student) => {
    const year = student.academicStanding?.currentYear || 1
    acc[year] = (acc[year] || 0) + 1
    return acc
  }, {} as Record<number, number>)

  const standingDistribution = data.reduce((acc, student) => {
    const standing = student.academicStanding?.standing || 'unknown'
    acc[standing] = (acc[standing] || 0) + 1
    return acc
  }, {} as Record<string, number>)

  let html = `
    <div class="analytics-report">
      <h2>Analytics & Insights</h2>
      
      <div class="analytics-section">
        <h3>Year Level Distribution</h3>
        <div class="chart-data">
  `

  Object.entries(yearDistribution).forEach(([year, count]) => {
    const percentage = ((count / data.length) * 100).toFixed(1)
    html += `
      <div class="chart-item">
        <span class="label">Year ${year}:</span>
        <span class="value">${count} (${percentage}%)</span>
      </div>
    `
  })

  html += `
        </div>
      </div>
      
      <div class="analytics-section">
        <h3>Academic Standing Distribution</h3>
        <div class="chart-data">
  `

  Object.entries(standingDistribution).forEach(([standing, count]) => {
    const percentage = ((count / data.length) * 100).toFixed(1)
    html += `
      <div class="chart-item">
        <span class="label">${formatStanding(standing)}:</span>
        <span class="value">${count} (${percentage}%)</span>
      </div>
    `
  })

  html += `
        </div>
      </div>
    </div>
  `

  return html
}

const generateCustomReport = (data: any[], options: string[]) => {
  let html = `
    <div class="custom-report">
      <h2>Custom Report</h2>
      <p>Selected sections: ${options.join(', ')}</p>
  `

  if (options.includes('personalInfo')) {
    html += generatePersonalInfoSection(data)
  }

  if (options.includes('academicStanding')) {
    html += generateAcademicStandingSection(data)
  }

  if (options.includes('skills')) {
    html += generateSkillsSection(data)
  }

  if (options.includes('affiliations')) {
    html += generateAffiliationsSection(data)
  }

  if (options.includes('violations')) {
    html += generateViolationsSection(data)
  }

  if (options.includes('analytics')) {
    html += generateAnalyticsReport(data)
  }

  html += `</div>`
  return html
}

const generatePersonalInfoSection = (data: any[]) => {
  return `
    <div class="report-section">
      <h3>Personal Information</h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
          </tr>
        </thead>
        <tbody>
          ${data.map(student => `
            <tr>
              <td>${student.id}</td>
              <td>${student.personalInfo?.firstName || 'N/A'}</td>
              <td>${student.personalInfo?.lastName || 'N/A'}</td>
              <td>${student.personalInfo?.email || 'N/A'}</td>
              <td>${student.personalInfo?.phone || 'N/A'}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `
}

const generateAcademicStandingSection = (data: any[]) => {
  return `
    <div class="report-section">
      <h3>Academic Standing</h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Current Year</th>
            <th>Current GPA</th>
            <th>Standing</th>
          </tr>
        </thead>
        <tbody>
          ${data.map(student => `
            <tr>
              <td>${student.id}</td>
              <td>${student.personalInfo?.firstName} ${student.personalInfo?.lastName}</td>
              <td>${student.academicStanding?.currentYear || 'N/A'}</td>
              <td>${student.academicStanding?.currentGPA || 'N/A'}</td>
              <td>${formatStanding(student.academicStanding?.standing || '')}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `
}

const generateSkillsSection = (data: any[]) => {
  return `
    <div class="report-section">
      <h3>Skills & Competencies</h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Skills</th>
          </tr>
        </thead>
        <tbody>
          ${data.map(student => `
            <tr>
              <td>${student.id}</td>
              <td>${student.personalInfo?.firstName} ${student.personalInfo?.lastName}</td>
              <td>${(student.skills || []).map((s: any) => s.name).join(', ') || 'None'}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `
}

const generateAffiliationsSection = (data: any[]) => {
  return `
    <div class="report-section">
      <h3>Affiliations & Organizations</h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Affiliations</th>
          </tr>
        </thead>
        <tbody>
          ${data.map(student => `
            <tr>
              <td>${student.id}</td>
              <td>${student.personalInfo?.firstName} ${student.personalInfo?.lastName}</td>
              <td>${(student.affiliations || []).map((a: any) => a.name).join(', ') || 'None'}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `
}

const generateViolationsSection = (data: any[]) => {
  return `
    <div class="report-section">
      <h3>Violation Records</h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Violation Count</th>
            <th>Violation Types</th>
          </tr>
        </thead>
        <tbody>
          ${data.map(student => {
            const violations = student.violations || []
            const violationTypes = violations.map((v: any) => v.type).join(', ') || 'None'
            return `
              <tr>
                <td>${student.id}</td>
                <td>${student.personalInfo?.firstName} ${student.personalInfo?.lastName}</td>
                <td>${violations.length}</td>
                <td>${violationTypes}</td>
              </tr>
            `
          }).join('')}
        </tbody>
      </table>
    </div>
  `
}


const downloadCSV = (data: any[], title: string, type: string) => {
  // CSV download implementation
  let csv = `${title} - ${formatReportType(type)}\n`
  csv += `Generated on: ${new Date().toLocaleString()}\n`
  csv += `Total Records: ${data.length}\n\n`

  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${title.replace(/\s+/g, '_')}_report.csv`
  a.click()
  window.URL.revokeObjectURL(url)
}

const downloadPDF = (data: any[], title: string, type: string) => {
  // PDF download implementation (would need a library like jsPDF)
  alert('PDF download would be implemented here. For now, try CSV or HTML format.')
}

const downloadExcel = (data: any[], title: string, type: string) => {
  // Excel download implementation (would need a library like xlsx)
  alert('Excel download would be implemented here. For now, try CSV format.')
}

const downloadHTML = (data: any[], title: string, type: string) => {
  // HTML download implementation
  const htmlContent = reportPreview.value
  const blob = new Blob([htmlContent], { type: 'text/html' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${title.replace(/\s+/g, '_')}_report.html`
  a.click()
  window.URL.revokeObjectURL(url)
}

const downloadReport = () => {
  const data = filteredData.value
  const title = props.reportTitle
  const format = selectedFormat.value
  
  switch (format) {
    case 'pdf':
      downloadPDF(data, title, format)
      break
    case 'csv':
      downloadCSV(data, title, format)
      break
    case 'excel':
      downloadExcel(data, title, format)
      break
    case 'html':
      downloadHTML(data, title, format)
      break
    default:
      alert('Unsupported format. Please select PDF, CSV, Excel, or HTML.')
  }
}
</script>

<style scoped>
.report-generator {
  display: inline-block;
}

.btn-report {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-report:hover {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  transform: translateY(-1px);
}

.icon {
  width: 20px;
  height: 20px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 1rem;
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-content.large {
  max-width: 900px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  color: #1f2937;
  font-size: 1.5rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0.5rem;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  background: white;
}

.filters-info {
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.filters-info h3 {
  margin: 0 0 0.75rem 0;
  color: #374151;
  font-size: 1rem;
}

.active-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.filter-tag {
  background: #3b82f6;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.no-filters {
  color: #6b7280;
  font-style: italic;
}

.custom-options {
  margin-bottom: 1.5rem;
}

.custom-options h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1rem;
}

.checkbox-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.75rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.checkbox {
  accent-color: #3b82f6;
}

.format-options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}

.radio-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.radio {
  accent-color: #3b82f6;
}

.preview-section {
  margin-top: 1.5rem;
}

.preview-section h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1rem;
}

.preview-content {
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
}

.preview-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.preview-stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: white;
  border-radius: 0.25rem;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
}

.stat-value {
  font-weight: 600;
  color: #1f2937;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
}

.report-preview {
  max-height: 60vh;
  overflow-y: auto;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 0.5rem;
}

/* Report preview styles */
.report-preview-content {
  font-family: Arial, sans-serif;
  line-height: 1.6;
}

.report-header {
  text-align: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.report-header h1 {
  margin: 0 0 0.5rem 0;
  color: #1f2937;
}

.report-header p {
  margin: 0.25rem 0;
  color: #6b7280;
}

.report-filters {
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 0.5rem;
}

.report-filters h2 {
  margin: 0 0 1rem 0;
  color: #374151;
}

.report-filters ul {
  margin: 0;
  padding-left: 1.5rem;
}

.report-filters li {
  margin-bottom: 0.5rem;
}

.summary-report h2,
.detailed-report h2,
.analytics-report h2,
.custom-report h2 {
  margin: 0 0 1.5rem 0;
  color: #1f2937;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-item {
  text-align: center;
  padding: 1rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

.stat-item h3 {
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
  color: #3b82f6;
}

.stat-item p {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}

.report-table th,
.report-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.report-table th {
  background: #f8fafc;
  font-weight: 600;
  color: #374151;
}

.report-table tbody tr:hover {
  background: #f8fafc;
}

.analytics-section {
  margin-bottom: 2rem;
}

.analytics-section h3 {
  margin: 0 0 1rem 0;
  color: #374151;
}

.chart-data {
  display: grid;
  gap: 0.5rem;
}

.chart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.25rem;
}

.chart-item .label {
  color: #374151;
  font-weight: 500;
}

.chart-item .value {
  color: #6b7280;
}

.report-section {
  margin-bottom: 2rem;
}

.report-section h3 {
  margin: 0 0 1rem 0;
  color: #374151;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
    margin: 1rem;
  }

  .checkbox-group,
  .format-options {
    grid-template-columns: 1fr;
  }

  .preview-stats {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
