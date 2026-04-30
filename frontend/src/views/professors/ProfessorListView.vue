<template>
  <div class="professor-profiling-view">
    <div class="page-header">
      <h1>Professor Profiling</h1>
      <p>Manage and monitor professor information</p>
    </div>

    <div class="actions">
      
      <button @click="fetchProfessors" class="btn btn-info btn-small">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
          <path d="M23 4v6h-6M1 20v-6h6"/>
          <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
        </svg>
        Refresh Data
      </button>
      <button @click="generateSampleData" class="btn btn-secondary btn-small">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
          <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
        </svg>
        Generate Sample Data
      </button>
      <router-link to="/professors/create" class="btn btn-success btn-small">
        Create New Professor
      </router-link>
      <div class="export-dropdown">
        <button @click="toggleExportMenu" class="btn btn-primary btn-small" :disabled="generatingPDF">
          {{ generatingPDF ? '⏳ Generating...' : '📊 Export' }}
        </button>
        <div v-if="showExportMenu" class="export-menu">
          <button @click="exportProfessorListPDF" class="export-item">
            📄 Professor List PDF
          </button>
          <button @click="exportStatisticsPDF" class="export-item">
            📈 Statistics Report PDF
          </button>
          <button @click="exportFilteredProfessorsPDF" class="export-item">
            🔍 Filtered Professors PDF
          </button>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Professors</h3>
          <p class="stat-number">{{ totalProfessors }}</p>
          <span class="stat-change">+{{ newProfessorsThisMonth }} this month</span>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Part-time Professors</h3>
          <p class="stat-number">{{ partTimeProfessors }}</p>
          <span class="stat-change">{{ ((partTimeProfessors / totalProfessors) * 100).toFixed(1) }}% of total</span>
        </div>
      </div>

      <div class="stat-card danger">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Departments</h3>
          <p class="stat-number">{{ totalDepartments }}</p>
          <span class="stat-change">{{ averageProfessorsPerDepartment.toFixed(1) }} avg per dept</span>
        </div>
      </div>
    </div>

    <!-- Department Distribution -->
    <div class="analytics-section">
      <h2>Department Distribution</h2>
      <div class="department-distribution">
        <div v-for="deptData in departmentDistribution" :key="deptData.department" class="dept-card">
          <div class="dept-header">
            <h3>{{ deptData.department }}</h3>
            <span class="dept-count">{{ deptData.count }} professors</span>
          </div>
          <div class="dept-progress">
            <div class="progress-bar" :style="{ width: deptData.percentage + '%' }"></div>
          </div>
          <div class="dept-details">
            <span class="dept-percentage">{{ deptData.percentage.toFixed(1) }}%</span>
            <span class="dept-employment">{{ deptData.fullTime }} full-time, {{ deptData.partTime }} part-time</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Employment Type Analytics -->
    <div class="analytics-section">
      <h2>Employment Type Analytics</h2>
      <div class="employment-grid">
        <div class="employment-card full-time">
          <div class="employment-header">
            <div class="employment-icon"> FT</div>
            <h4>Full-time</h4>
          </div>
          <div class="employment-stats">
            <div class="employment-number">{{ fullTimeProfessors }}</div>
            <div class="employment-percentage">{{ ((fullTimeProfessors / totalProfessors) * 100).toFixed(1) }}%</div>
          </div>
          <div class="employment-progress">
            <div class="progress-bar" :style="{ width: ((fullTimeProfessors / totalProfessors) * 100) + '%' }"></div>
          </div>
        </div>

        <div class="employment-card part-time">
          <div class="employment-header">
            <div class="employment-icon"> PT</div>
            <h4>Part-time</h4>
          </div>
          <div class="employment-stats">
            <div class="employment-number">{{ partTimeProfessors }}</div>
            <div class="employment-percentage">{{ ((partTimeProfessors / totalProfessors) * 100).toFixed(1) }}%</div>
          </div>
          <div class="employment-progress">
            <div class="progress-bar" :style="{ width: ((partTimeProfessors / totalProfessors) * 100) + '%' }"></div>
          </div>
        </div>

        <div class="employment-card contract">
          <div class="employment-header">
            <div class="employment-icon"> C</div>
            <h4>Contract</h4>
          </div>
          <div class="employment-stats">
            <div class="employment-number">{{ contractProfessors }}</div>
            <div class="employment-percentage">{{ ((contractProfessors / totalProfessors) * 100).toFixed(1) }}%</div>
          </div>
          <div class="employment-progress">
            <div class="progress-bar" :style="{ width: ((contractProfessors / totalProfessors) * 100) + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number">{{ totalProfessors }}</div>
        <div class="stat-label">Total Professors</div>
      </div>
      <div class="stat-card full-time">
        <div class="stat-number">{{ fullTimeProfessors }}</div>
        <div class="stat-label">Full-time</div>
      </div>
      <div class="stat-card part-time">
        <div class="stat-number">{{ partTimeProfessors }}</div>
        <div class="stat-label">Part-time</div>
      </div>
      <div class="stat-card contract">
        <div class="stat-number">{{ contractProfessors }}</div>
        <div class="stat-label">Contract</div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name or email..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="selectedDepartment" class="filter-select">
          <option value="">All Departments</option>
          <option v-for="dept in availableDepartments" :key="dept" :value="dept">
            {{ dept }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedEmployment" class="filter-select">
          <option value="">All Employment Types</option>
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Contract">Contract</option>
        </select>
      </div>
      <button @click="resetFilters" class="btn btn-secondary">
        Reset Filters
      </button>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="professor-table-container">
      <table class="professor-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Employment Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="professor in filteredProfessors" :key="professor.id">
            <td>{{ professor.id }}</td>
            <td>{{ professor.first_name }} {{ professor.last_name }}</td>
            <td>{{ professor.email }}</td>
            <td>{{ professor.department }}</td>
            <td>
              <span :class="['status-badge', getEmploymentClass(professor.employment_type)]">
                {{ professor.employment_type }}
              </span>
            </td>
            <td>
              <router-link :to="`/professors/${professor.id}`" class="btn btn-sm btn-info">
                View
              </router-link>
              <router-link :to="`/professors/${professor.id}/edit`" class="btn btn-sm btn-warning">
                Edit
              </router-link>
              <button @click="archiveProfessor(professor.id)" class="btn btn-sm btn-danger">
                Archive
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="filteredProfessors.length === 0" class="no-results">
        No professors found matching your criteria.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import axios from 'axios'
  import jsPDF from 'jspdf'
  import html2canvas from 'html2canvas'

  interface Professor {
    id: number
    first_name: string
    last_name: string
    email: string
    department: string
    employment_type: string
    role: string
    createdAt?: string
  }

  const professors = ref<Professor[]>([])
  const loading = ref(true)
  const error = ref('')

  // Filter state
  const searchQuery = ref('')
  const selectedDepartment = ref('')
  const selectedEmployment = ref('')
  const showExportMenu = ref(false)
  const generatingPDF = ref(false)

  // Computed properties
  const totalProfessors = computed(() => professors.value.length)

  const fullTimeProfessors = computed(() => 
    professors.value.filter(p => p.employment_type === 'Full-time').length
  )

  const partTimeProfessors = computed(() => 
    professors.value.filter(p => p.employment_type === 'Part-time').length
  )

  const contractProfessors = computed(() => 
    professors.value.filter(p => p.employment_type === 'Contract').length
  )

  const totalDepartments = computed(() => {
    const departments = [...new Set(professors.value.map(p => p.department))]
    return departments.length
  })

  const averageProfessorsPerDepartment = computed(() => {
    return totalDepartments.value > 0 ? totalProfessors.value / totalDepartments.value : 0
  })

  const newProfessorsThisMonth = computed(() => {
    const oneMonthAgo = new Date()
    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1)
    return professors.value.filter(p => 
      p.createdAt && new Date(p.createdAt) > oneMonthAgo
    ).length
  })

  // Department distribution
  const departmentDistribution = computed(() => {
    const deptGroups = professors.value.reduce((groups: any, professor: Professor) => {
      const dept = professor.department
      if (!groups[dept]) {
        groups[dept] = {
          department: dept,
          count: 0,
          fullTime: 0,
          partTime: 0,
          contract: 0
        }
      }
      groups[dept].count++
      if (professor.employment_type === 'Full-time') groups[dept].fullTime++
      else if (professor.employment_type === 'Part-time') groups[dept].partTime++
      else if (professor.employment_type === 'Contract') groups[dept].contract++
      return groups
    }, {})

    const total = professors.value.length
    return Object.values(deptGroups)
      .map((group: any) => ({
        department: group.department,
        count: group.count,
        fullTime: group.fullTime,
        partTime: group.partTime,
        contract: group.contract,
        percentage: total > 0 ? (group.count / total) * 100 : 0
      }))
      .sort((a: any, b: any) => b.count - a.count)
  })

  // Available departments for filter
  const availableDepartments = computed(() => {
    const departments = [...new Set(professors.value.map(p => p.department))].sort()
    return departments
  })

  // Filtered professors
  const filteredProfessors = computed(() => {
    let filtered = professors.value

    // Search filter
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(professor =>
        professor.first_name.toLowerCase().includes(query) ||
        professor.last_name.toLowerCase().includes(query) ||
        professor.email.toLowerCase().includes(query)
      )
    }

    // Department filter
    if (selectedDepartment.value) {
      filtered = filtered.filter(professor => professor.department === selectedDepartment.value)
    }

    // Employment filter
    if (selectedEmployment.value) {
      filtered = filtered.filter(professor => professor.employment_type === selectedEmployment.value)
    }

    return filtered
  })

  // Methods
  const fetchProfessors = async () => {
    loading.value = true
    error.value = ''
    try {
      const response = await axios.get('http://127.0.0.1:8000/api/professors')
      professors.value = response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch professors'
    } finally {
      loading.value = false
    }
  }

  const generateSampleData = () => {
    const firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Jennifer', 'William', 'Jessica', 'Daniel', 'Ashley', 'Christopher', 'Sophia', 'Matthew', 'Olivia', 'Andrew', 'Emma', 'Joshua', 'Isabella', 'Ryan', 'Mia', 'Kevin', 'Charlotte', 'Tyler', 'Amelia', 'Jason', 'Harper', 'Ethan', 'Evelyn', 'Brandon', 'Abigail', 'Nathan', 'Alexander', 'Madison', 'Jacob', 'Sofia', 'Logan', 'Avery', 'Ethan', 'Aiden', 'Caleb', 'Jackson', 'Mason', 'Liam', 'Noah', 'Lucas', 'Henry', 'Sebastian', 'Ezra', 'Jack', 'Owen']
  
  const lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young', 'Allen', 'King', 'Wright', 'Lopez', 'Hill', 'Scott', 'Green', 'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter', 'Mitchell', 'Perez', 'Roberts', 'Turner', 'Phillips', 'Campbell', 'Parker', 'Evans', 'Edwards', 'Collins']
  
  const departments = ['Information Technology', 'Software Development', 'Computer Science', 'Network Administration', 'Information Security', 'Database Administration', 'Cloud Computing', 'IT Support', 'Systems Analysis', 'Web Development', 'Cybersecurity', 'Data Science', 'IT Project Management', 'Business Information Systems', 'Enterprise Architecture']
  const employmentTypes = ['Full-time', 'Part-time', 'Contract']
  const roles = ['IT Professor', 'Software Engineering Professor', 'Computer Science Professor', 'Network Systems Professor', 'Cybersecurity Professor', 'Data Science Professor', 'IT Lecturer', 'Software Development Instructor', 'Systems Analyst Professor', 'Cloud Computing Professor', 'Database Systems Professor', 'Web Technologies Professor', 'Information Systems Professor', 'IT Architecture Professor', 'Digital Technologies Professor']
  
  const sampleProfessors: Professor[] = []
  
  for (let i = 0; i < 50; i++) {
    sampleProfessors.push({
      id: i + 1,
      first_name: firstNames[i % firstNames.length],
      last_name: lastNames[i % lastNames.length],
      email: `${firstNames[i % firstNames.length].toLowerCase()}.${lastNames[i % lastNames.length].toLowerCase()}@university.edu`,
      department: departments[Math.floor(Math.random() * departments.length)],
      employment_type: employmentTypes[Math.floor(Math.random() * employmentTypes.length)],
      role: roles[Math.floor(Math.random() * roles.length)],
      createdAt: new Date(Date.now() - Math.floor(Math.random() * 365) * 24 * 60 * 60 * 1000).toISOString()
    })
  }
  
  professors.value = sampleProfessors
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedDepartment.value = ''
  selectedEmployment.value = ''
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

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  fetchProfessors()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// PDF Export Functions
async function exportProfessorListPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Complete Professor List Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Total Professors: ${totalProfessors.value}`, pageWidth / 2, 37, { align: 'center' })
    
    // Table headers
    const headers = ['ID', 'Name', 'Email', 'Department', 'Employment Type']
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
    
    professors.value.forEach((professor, index) => {
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
        professor.id.toString(),
        `${professor.first_name} ${professor.last_name}`,
        professor.email,
        professor.department,
        professor.employment_type
      ]
      
      rowData.forEach((data, dataIndex) => {
        const text = data.length > 15 ? data.substring(0, 15) + '...' : data
        pdf.text(text, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Professor Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`professor-list-${Date.now()}.pdf`)
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
    pdf.text('Professor Statistics Report', pageWidth / 2, 20, { align: 'center' })
    
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
      { label: 'Total Professors', value: totalProfessors.value },
      { label: 'Full-time Professors', value: fullTimeProfessors.value },
      { label: 'Part-time Professors', value: partTimeProfessors.value },
      { label: 'Contract Professors', value: contractProfessors.value },
      { label: 'Total Departments', value: totalDepartments.value }
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
    
    departmentDistribution.value.forEach(dept => {
      pdf.text(`${dept.department}: ${dept.count} professors (${dept.percentage.toFixed(1)}%)`, 30, currentY)
      currentY += 8
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Professor Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`professor-statistics-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating statistics PDF:', error)
    alert('Failed to generate statistics PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

async function exportFilteredProfessorsPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Filtered Professors Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Filtered Professors: ${filteredProfessors.value.length} of ${totalProfessors.value}`, pageWidth / 2, 37, { align: 'center' })
    
    // Active filters
    let filterText = 'Active Filters: '
    if (searchQuery.value) filterText += `Search: "${searchQuery.value}" `
    if (selectedDepartment.value) filterText += `Department: ${selectedDepartment.value} `
    if (selectedEmployment.value) filterText += `Employment: ${selectedEmployment.value} `
    
    if (filterText === 'Active Filters: ') filterText = 'No active filters'
    
    pdf.text(filterText, pageWidth / 2, 44, { align: 'center' })
    
    // Table
    const headers = ['ID', 'Name', 'Email', 'Department', 'Employment Type']
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
    
    filteredProfessors.value.forEach((professor, index) => {
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
        professor.id.toString(),
        `${professor.first_name} ${professor.last_name}`,
        professor.email,
        professor.department,
        professor.employment_type
      ]
      
      rowData.forEach((data, dataIndex) => {
        pdf.text(data, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    pdf.save(`filtered-professors-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating filtered professors PDF:', error)
    alert('Failed to generate filtered professors PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

const archiveProfessor = async (professorId: number) => {
  const professor = professors.value.find(p => p.id === professorId)
  const professorName = professor ? `${professor.first_name} ${professor.last_name}` : 'this professor'
  
  if (confirm(`Are you sure you want to archive ${professorName}? This will remove them from the active list but keep their data for records.`)) {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/professors/${professorId}`)
      alert(`Professor archived successfully!\n\nProfessor: ${professorName}\nID: ${professorId}\nArchived at: ${new Date().toLocaleString()}`)
      // Remove from local list
      professors.value = professors.value.filter(professor => professor.id !== professorId)
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Failed to archive professor'
      alert(`Error: ${errorMessage}`)
    }
  }
}

function getEmploymentClass(type: string) {
  if (type === 'Full-time') return 'full-time'
  if (type === 'Part-time') return 'part-time'
  return 'contract'
}
</script>

<style scoped>
.professor-profiling-view {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.page-header p {
  color: #6b7280;
  margin: 0;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
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
}

.btn-small {
  padding: 0.5rem 1rem;
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

.btn-success {
  background-color: #10b981;
  color: white;
}

.btn-success:hover {
  background-color: #059669;
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

.dark .data-row:hover {
  background: rgba(249, 115, 22, 0.04);
}

.dark .data-row td {
  color: #f9fafb;
}

.dark .page-btn {
  background: rgba(30, 41, 59, 0.6);
  border-color: rgba(249, 115, 22, 0.15);
  color: rgba(249, 115, 22, 0.5);
}

.dark .page-btn:hover:not(:disabled) {
  border-color: rgba(249, 115, 22, 0.45);
  color: #f97316;
  background: rgba(249, 115, 22, 0.08);
}

.dark .page-btn.active {
  background: rgba(249, 115, 22, 0.14);
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
}

.dark .pagination-bar {
  border-top-color: rgba(249, 115, 22, 0.07);
}

.dark .loading-state,
.dark .empty-state {
  color: rgba(249, 115, 22, 0.4);
}

/* Responsive */
@media (max-width: 900px) {
  .filter-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .filter-grid { grid-template-columns: 1fr; }
  .topbar-title { display: none; }
  .prof-actions { flex-direction: column; }
  .row-btn span { display: none; }
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
  margin-bottom: 1rem;
}

.stat-card.primary .stat-icon {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.stat-card.warning .stat-icon {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card.danger .stat-icon {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-content {
  text-align: center;
}

.stat-content h3 {
  margin: 0 0 0.5rem 0;
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1f2937;
}

.stat-change {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.5rem;
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

/* Employment Type Grid */
.employment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.employment-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-left: 4px solid;
}

.employment-card.full-time {
  border-left-color: #10b981;
}

.employment-card.part-time {
  border-left-color: #f59e0b;
}

.employment-card.contract {
  border-left-color: #ef4444;
}

.employment-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.employment-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 0.875rem;
  color: white;
}

.employment-card.full-time .employment-icon {
  background: #10b981;
}

.employment-card.part-time .employment-icon {
  background: #f59e0b;
}

.employment-card.contract .employment-icon {
  background: #ef4444;
}

.employment-header h4 {
  margin: 0;
  color: #1f2937;
}

.employment-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.employment-number {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
}

.employment-percentage {
  font-size: 0.875rem;
  color: #6b7280;
}

.employment-progress {
  background: #f3f4f6;
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
}

/* Stats Container */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card.full-time {
  border-left-color: #10b981;
}

.stat-card.part-time {
  border-left-color: #f59e0b;
}

.stat-card.contract {
  border-left-color: #ef4444;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
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

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

.professor-table-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.professor-table {
  width: 100%;
  border-collapse: collapse;
}

.professor-table th,
.professor-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.professor-table th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.professor-table tbody tr:hover {
  background-color: #f9fafb;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.full-time {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.part-time {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.contract {
  background-color: #fef3c7;
  color: #92400e;
}

.no-results {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1rem;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1rem;
}

.error {
  text-align: center;
  padding: 3rem;
  color: #ef4444;
  font-size: 1rem;
}
</style>