<template>
  <div class="student-profiling-view">
    <div class="page-header">
      <h1>Student Profiling</h1>
      <p>Manage and monitor student information</p>
    </div>

    <div class="actions">
      <router-link to="/students/list" class="btn btn-primary btn-small">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        View All Students
      </router-link>
      <button @click="fetchStudents" class="btn btn-info btn-small">
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
      <router-link to="/students/create" class="btn btn-success btn-small">
        Create New Student
      </router-link>
      <ReportGenerator 
        :data="filteredStudentsList" 
        report-title="Students"
        :filters="{
          searchQuery,
          selectedYear,
          selectedStanding,
          selectedSkill,
          selectedAffiliation,
          selectedViolationStatus
        }"
        @generate-report="handleReportGeneration"
      />
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
          <h3>Total Students</h3>
          <p class="stat-number">{{ totalStudents }}</p>
          <span class="stat-change">+{{ newStudentsThisMonth }} this month</span>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>At Risk Students</h3>
          <p class="stat-number">{{ atRiskStudents }}</p>
          <span class="stat-change">{{ ((atRiskStudents / totalStudents) * 100).toFixed(1) }}% of total</span>
        </div>
      </div>

      <div class="stat-card danger">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Violations</h3>
          <p class="stat-number">{{ totalViolations }}</p>
          <span class="stat-change">{{ violationsThisMonth }} this month</span>
        </div>
      </div>
    </div>


    <!-- Year Level Distribution -->
    <div class="analytics-section">
      <h2>Year Level Distribution</h2>
      <div class="year-distribution">
        <div v-for="yearData in yearDistribution" :key="yearData.year" class="year-card">
          <div class="year-header">
            <h3>Year {{ yearData.year }}</h3>
            <span class="year-count">{{ yearData.count }} students</span>
          </div>
          <div class="year-progress">
            <div class="progress-bar" :style="{ width: yearData.percentage + '%' }"></div>
          </div>
          <div class="year-details">
            <span class="year-percentage">{{ yearData.percentage.toFixed(1) }}%</span>
            <span class="year-gpa">Avg GPA: {{ yearData.avgGPA.toFixed(2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number">{{ totalStudents }}</div>
        <div class="stat-label">Total Students</div>
      </div>
      <div class="stat-card probation">
        <div class="stat-number">{{ probationCount }}</div>
        <div class="stat-label">On Probation</div>
      </div>
      <div class="stat-card good">
        <div class="stat-number">{{ goodStandingCount }}</div>
        <div class="stat-label">Good Standing</div>
      </div>
      <div class="stat-card at-risk">
        <div class="stat-number">{{ atRiskCount }}</div>
        <div class="stat-label">At Risk</div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name, email, skills, affiliations, violations, or courses..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="selectedYear" class="filter-select">
          <option value="">All Years</option>
          <option v-for="year in availableYears" :key="year" :value="year">
            Year {{ year }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedStanding" class="filter-select">
          <option value="">All Standing</option>
          <option value="excellent">Excellent</option>
          <option value="good">Good</option>
          <option value="average">Average</option>
          <option value="probation">Probation</option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedSkill" class="filter-select">
          <option value="">All Skills</option>
          <option v-for="skill in availableSkillsList" :key="skill" :value="skill">
            {{ skill }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedAffiliation" class="filter-select">

          
          <option value="">All Affiliations</option>
          <option v-for="affiliation in availableAffiliationsList" :key="affiliation" :value="affiliation">
            {{ affiliation }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="selectedViolationStatus" class="filter-select">
          <option value="">All Violation Status</option>
          <option value="pending">Pending</option>
          <option value="resolved">Resolved</option>
          <option value="appealed">Appealed</option>
        </select>
      </div>
      <button @click="resetFilters" class="btn btn-secondary">
        Reset Filters
      </button>
    </div>

    <!-- Skills Filter Container -->
    <div class="skills-filter-container">
      <div class="skills-header">
        <h3>Filter by Skills</h3>
        <div class="selected-skill-info" v-if="selectedSkill">
          <span class="selected-skill-label">Selected:</span>
          <span class="selected-skill-name">{{ selectedSkill }}</span>
          <button @click="selectedSkill = ''" class="clear-skill-btn">×</button>
        </div>
      </div>
      <div class="skill-tags">
        <button 
          v-for="skill in availableSkillsWithCounts" 
          :key="skill.name"
          @click="toggleSkillFilter(skill.name)"
          :class="['skill-tag', { active: selectedSkill === skill.name }]"
        >
          {{ skill.name }}
          <span class="skill-count">({{ skill.count }})</span>
        </button>
      </div>
    </div>

    <!-- Affiliations Filter Container -->
    <div class="affiliations-filter-container">
      <div class="affiliations-header">
        <h3>Filter by Affiliations</h3>
        <div class="selected-affiliation-info" v-if="selectedAffiliation">
          <span class="selected-affiliation-label">Selected:</span>
          <span class="selected-affiliation-name">{{ selectedAffiliation }}</span>
          <button @click="selectedAffiliation = ''" class="clear-affiliation-btn">×</button>
        </div>
      </div>
      <div class="affiliation-tags">
        <button 
          v-for="affiliation in availableAffiliationsWithCounts" 
          :key="affiliation.name"
          @click="toggleAffiliationFilter(affiliation.name)"
          :class="['affiliation-tag', { active: selectedAffiliation === affiliation.name }]"
        >
          {{ affiliation.name }}
          <span class="affiliation-count">({{ affiliation.count }})</span>
        </button>
      </div>
    </div>

    <!-- Violations Filter Container -->
    <div class="violations-filter-container">
      <div class="violations-header">
        <h3>Filter by Violation Status</h3>
        <div class="selected-violation-info" v-if="selectedViolationStatus">
          <span class="selected-violation-label">Selected:</span>
          <span class="selected-violation-name">{{ formatViolationStatus(selectedViolationStatus) }}</span>
          <button @click="selectedViolationStatus = ''" class="clear-violation-btn">×</button>
        </div>
      </div>
      <div class="violation-tags">
        <button 
          v-for="status in violationStatuses" 
          :key="status.value"
          @click="toggleViolationFilter(status.value)"
          :class="['violation-tag', { active: selectedViolationStatus === status.value }]"
        >
          {{ status.label }}
          <span class="violation-count">({{ getViolationStatusCount(status.value) }})</span>
        </button>
      </div>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="student-table-container">
      <table class="student-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Year Level</th>
            <th>Academic Standing</th>
            <th>Skills</th>
            <th>Affiliations</th>
            <th>Violations</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in filteredStudentsList" :key="student.id">
            <td>{{ student.id }}</td>
            <td>{{ student.personalInfo.firstName }} {{ student.personalInfo.lastName }}</td>
            <td>{{ student.personalInfo.email }}</td>
            <td>{{ student.academicStanding.currentYear }}</td>
            <td>
              <span :class="['status-badge', student.academicStanding.standing]">
                {{ formatStanding(student.academicStanding.standing) }}
              </span>
            </td>
            <td>
              <div class="skills-tags">
                <span v-for="skill in student.skills.slice(0, 2)" :key="skill.id" class="skill-tag">
                  {{ skill.name }}
                </span>
                <span v-if="student.skills.length > 2" class="more-skills">
                  +{{ student.skills.length - 2 }} more
                </span>
              </div>
            </td>
            <td>
              <div class="affiliations-tags">
                <span v-for="affiliation in student.affiliations.slice(0, 2)" :key="affiliation.id" class="affiliation-tag">
                  {{ affiliation.name }}
                </span>
                <span v-if="student.affiliations.length > 2" class="more-affiliations">
                  +{{ student.affiliations.length - 2 }} more
                </span>
              </div>
            </td>
            <td>
              <div class="violations-info">
                <span :class="['violation-count', getViolationClass(student.violations)]">
                  {{ student.violations.length }} violations
                </span>
                <div v-if="student.violations.length > 0" class="violation-types">
                  <span v-for="(violation, index) in student.violations.slice(0, 2)" :key="violation.id" class="violation-type">
                    {{ violation.type }}<span v-if="index < Math.min(student.violations.length - 1, 1)">,</span>
                  </span>
                  <span v-if="student.violations.length > 2" class="more-violations">
                    +{{ student.violations.length - 2 }} more
                  </span>
                </div>
              </div>
            </td>
            <td>
              <router-link :to="`/students/${student.id}`" class="btn btn-sm btn-info">
                View
              </router-link>
              <router-link :to="`/students/${student.id}/edit`" class="btn btn-sm btn-warning">
                Edit
              </router-link>
              <button @click="archiveStudent(student.id)" class="btn btn-sm btn-danger">
                Archive
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Mobile Card View -->
      <div class="mobile-cards-view">
        <div v-for="student in filteredStudentsList" :key="student.id" class="mobile-student-card">
          <div class="mobile-card-header">
            <div class="mobile-student-info">
              <h3>{{ student.personalInfo.firstName }} {{ student.personalInfo.lastName }}</h3>
              <p>{{ student.personalInfo.email }}</p>
              <span :class="['status-badge', student.academicStanding.standing]">
                {{ formatStanding(student.academicStanding.standing) }}
              </span>
            </div>
            <div class="mobile-student-id">
              <strong>ID:</strong> {{ student.id }}
            </div>
          </div>
          
          <div class="mobile-card-body">
            <div class="mobile-detail-row">
              <div class="mobile-detail-label">Year Level:</div>
              <div class="mobile-detail-value">{{ student.academicStanding.currentYear }}</div>
            </div>
            
            <div class="mobile-detail-row">
              <div class="mobile-detail-label">Skills:</div>
              <div class="mobile-detail-value">
                <div class="skills-tags">
                  <span v-for="skill in student.skills.slice(0, 3)" :key="skill.id" class="skill-tag">
                    {{ skill.name }}
                  </span>
                  <span v-if="student.skills.length > 3" class="more-skills">
                    +{{ student.skills.length - 3 }} more
                  </span>
                </div>
              </div>
            </div>
            
            <div class="mobile-detail-row">
              <div class="mobile-detail-label">Affiliations:</div>
              <div class="mobile-detail-value">
                <div class="affiliations-tags">
                  <span v-for="affiliation in student.affiliations.slice(0, 2)" :key="affiliation.id" class="affiliation-tag">
                    {{ affiliation.name }}
                  </span>
                  <span v-if="student.affiliations.length > 2" class="more-affiliations">
                    +{{ student.affiliations.length - 2 }} more
                  </span>
                </div>
              </div>
            </div>
            
            <div class="mobile-detail-row">
              <div class="mobile-detail-label">Violations:</div>
              <div class="mobile-detail-value">
                <div class="violations-info">
                  <span :class="['violation-count', getViolationClass(student.violations)]">
                    {{ student.violations.length }} violations
                  </span>
                  <div v-if="student.violations.length > 0" class="violation-types">
                    <span v-for="(violation, index) in student.violations.slice(0, 2)" :key="violation.id" class="violation-type">
                      {{ violation.type }}<span v-if="index < Math.min(student.violations.length - 1, 1)">,</span>
                    </span>
                    <span v-if="student.violations.length > 2" class="more-violations">
                      +{{ student.violations.length - 2 }} more
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mobile-card-actions">
            <router-link :to="`/students/${student.id}`" class="btn btn-sm btn-info">
              View
            </router-link>
            <router-link :to="`/students/${student.id}/edit`" class="btn btn-sm btn-warning">
              Edit
            </router-link>
            <button @click="archiveStudent(student.id)" class="btn btn-sm btn-danger">
              Archive
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="filteredStudentsList.length === 0" class="no-results">
        No students found matching your criteria.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useStudentStore } from '@/stores/student'
import type { StudentFilter, DisplayMode, Student } from '@/types/student'
import ReportGenerator from '@/components/ReportGenerator.vue'

const router = useRouter()
const studentStore = useStudentStore()

// Local state
const filterPanelOpen = ref(false)
const showCreateModal = ref(false)
const localFilter = ref<StudentFilter>({
  search: '',
  skills: [],
  activities: [],
  affiliations: [],
  violationStatus: [],
  academicStanding: [],
  yearLevel: [],
  gpaRange: { min: 0, max: 4 },
  ageRange: { min: 16, max: 30 }
})

// StudentListView state
const searchQuery = ref('')
const selectedYear = ref('')
const selectedStanding = ref('')
const selectedSkill = ref('')
const selectedAffiliation = ref('')
const selectedViolationStatus = ref('')
const allStudents = computed(() => Array.isArray(studentStore.students) ? studentStore.students : [])

// Available options for filters
const availableSkills = ref([
  'JavaScript', 'Python', 'Java', 'C++', 'PHP', 'React', 'Vue.js', 'Node.js', 
  'HTML/CSS', 'SQL', 'MongoDB', 'Docker', 'Git', 'Machine Learning', 'Data Analysis', 
  'UI/UX Design', 'Laravel', 'Angular', 'TypeScript', 'Flutter', 'Swift', 'Kotlin', 
  'AWS', 'Azure', 'Google Cloud', 'TensorFlow', 'PyTorch', 'Leadership', 'Communication', 'Teamwork'
])
const availableActivities = ref(['basketball', 'volunteer', 'organization', 'sports', 'leadership'])

// Computed lists for filter dropdowns
const availableSkillsList = computed(() => {
  const allSkills = new Set<string>()
  allStudents.value.forEach(student => {
    ;(student.skills || []).forEach(skill => {
      allSkills.add(skill.name)
    })
  })
  return Array.from(allSkills).sort()
})

const availableAffiliationsList = computed(() => {
  const allAffiliations = new Set<string>()
  allStudents.value.forEach(student => {
    ;(student.affiliations || []).forEach(affiliation => {
      allAffiliations.add(affiliation.name)
    })
  })
  return Array.from(allAffiliations).sort()
})

// Available skills with counts for filter containers
const availableSkillsWithCounts = computed(() => {
  const skillCounts = new Map<string, number>()
  
  // Count students for each skill
  allStudents.value.forEach(student => {
    ;(student.skills || []).forEach(skill => {
      skillCounts.set(skill.name, (skillCounts.get(skill.name) || 0) + 1)
    })
  })
  
  // Get all skills
  const allSkills = new Set<string>()
  const hardcodedSkills = [
    'Leadership', 'Database Management', 'Machine Learning',
    'JavaScript', 'Python', 'Java', 'C++', 'PHP', 'React', 'Vue.js', 'Node.js', 
    'HTML/CSS', 'SQL', 'MongoDB', 'Docker', 'Git', 'Data Analysis', 
    'UI/UX Design', 'Laravel', 'Angular', 'TypeScript', 'Flutter', 'Swift', 'Kotlin', 
    'AWS', 'Azure', 'Google Cloud', 'TensorFlow', 'PyTorch', 'Communication', 'Teamwork'
  ]
  
  hardcodedSkills.forEach(skill => allSkills.add(skill))
  allStudents.value.forEach(student => {
    ;(student.skills || []).forEach(skill => allSkills.add(skill.name))
  })
  
  return Array.from(allSkills).map(skillName => ({
    name: skillName,
    count: skillCounts.get(skillName) || 0
  })).sort((a, b) => b.count - a.count)
})

// Available affiliations with counts for filter containers
const availableAffiliationsWithCounts = computed(() => {
  const affiliationCounts = new Map<string, number>()
  
  // Count students for each affiliation
  allStudents.value.forEach(student => {
    ;(student.affiliations || []).forEach(affiliation => {
      affiliationCounts.set(affiliation.name, (affiliationCounts.get(affiliation.name) || 0) + 1)
    })
  })
  
  // Get all affiliations
  const allAffiliations = new Set<string>()
  const hardcodedAffiliations = [
    'Computer Science Society', 'Student Council', 'Coding Club', 'Debate Team', 'Sports Club',
    'Drama Club', 'Music Society', 'Art Club', 'Photography Club', 'Environmental Club',
    'Business Club', 'Engineering Society', 'Medical Club', 'Law Society', 'Literature Club'
  ]
  
  hardcodedAffiliations.forEach(affiliation => allAffiliations.add(affiliation))
  allStudents.value.forEach(student => {
    ;(student.affiliations || []).forEach(affiliation => allAffiliations.add(affiliation.name))
  })
  
  return Array.from(allAffiliations).map(affiliationName => ({
    name: affiliationName,
    count: affiliationCounts.get(affiliationName) || 0
  })).sort((a, b) => b.count - a.count)
})

// Violation statuses for filter
const violationStatuses = ref([
  { value: 'pending', label: 'Pending' },
  { value: 'resolved', label: 'Resolved' },
  { value: 'under_review', label: 'Under Review' },
  { value: 'appealed', label: 'Appealed' }
])

// Computed
const {
  filteredStudents,
  paginatedStudents,
  loading,
  error,
  displayMode,
  currentPage,
  pageSize,
  totalStudents,
  totalPages
} = studentStore

// Access store directly for reactive values
const activeStudents = computed(() => 
  allStudents.value.filter((s: Student) => s?.isActive).length
)

const averageGPA = computed(() => {
  if (allStudents.value.length === 0) return 0
  const total = allStudents.value.reduce((sum: number, student: Student) => sum + (student.academicStanding?.currentGPA || 0), 0)
  return total / allStudents.value.length
})

const totalViolations = computed(() => 
  allStudents.value.reduce((sum: number, student: Student) => sum + (student.violations?.length || 0), 0)
)

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, (currentPage as any).value - 2)
  const end = Math.min((totalPages as any).value, start + 4)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Enhanced Analytics Computed Properties
const atRiskStudents = computed(() => 
  allStudents.value.filter((s: Student) => 
    s.academicStanding?.standing === 'probation' || 
    (s.academicStanding?.currentGPA || 0) < 2.5
  ).length
)

const newStudentsThisMonth = computed(() => {
  const oneMonthAgo = new Date()
  oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1)
  return allStudents.value.filter((s: Student) => 
    new Date(s.createdAt) > oneMonthAgo
  ).length
})

const violationsThisMonth = computed(() => {
  const oneMonthAgo = new Date()
  oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1)
  return allStudents.value.reduce((count: number, student: Student) => {
    const recentViolations = (student.violations || []).filter(v => 
      new Date(v.date) > oneMonthAgo
    )
    return count + recentViolations.length
  }, 0)
})

const gpaTrend = computed(() => {
  // Simulated trend - in real app this would compare with historical data
  return Math.random() * 0.4 - 0.2 // Random between -0.2 and +0.2
})

// Academic Standing Counts
const excellentStandingCount = computed(() => 
  allStudents.value.filter((s: Student) => 
    (s.academicStanding?.currentGPA || 0) >= 3.5
  ).length
)

const goodStandingCount = computed(() => 
  allStudents.value.filter((s: Student) => 
    (s.academicStanding?.currentGPA || 0) >= 3.0 && (s.academicStanding?.currentGPA || 0) < 3.5
  ).length
)

const averageStandingCount = computed(() => 
  allStudents.value.filter((s: Student) => 
    (s.academicStanding?.currentGPA || 0) >= 2.5 && (s.academicStanding?.currentGPA || 0) < 3.0
  ).length
)

const probationCount = computed(() => 
  allStudents.value.filter((s: Student) => 
    (s.academicStanding?.currentGPA || 0) < 2.5
  ).length
)

// StudentListView computed properties
const atRiskCount = computed(() => 
  allStudents.value.filter(student => ['average', 'probation'].includes(student.academicStanding?.standing || '')).length
)

// Available years for filter
const availableYears = computed(() => {
  const years = [...new Set(allStudents.value.map(s => s.academicStanding?.currentYear).filter(Boolean))].sort((a, b) => Number(a) - Number(b))
  return years
})

// Filtered students for StudentListView table
const filteredStudentsList = computed(() => {
  let filtered = allStudents.value

  // Search filter - now includes skills, affiliations, violations, and courses
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(student => {
      // Basic info search
      const basicMatch = 
        (student.personalInfo?.firstName || '').toLowerCase().includes(query) ||
        (student.personalInfo?.lastName || '').toLowerCase().includes(query) ||
        (student.personalInfo?.email || '').toLowerCase().includes(query)
      
      // Skills search
      const skillsMatch = (student.skills || []).some(skill => 
        (skill.name || '').toLowerCase().includes(query) ||
        (skill.category || '').toLowerCase().includes(query)
      )
      
      // Affiliations search
      const affiliationsMatch = (student.affiliations || []).some(affiliation => 
        (affiliation.name || '').toLowerCase().includes(query) ||
        (affiliation.type || '').toLowerCase().includes(query)
      )
      
      // Violations search
      const violationsMatch = (student.violations || []).some(violation => 
        (violation.type || '').toLowerCase().includes(query) ||
        (violation.description || '').toLowerCase().includes(query)
      )
      
      const coursesMatch = (student.enrolledCourses || []).some(course =>
        (course.courseName || '').toLowerCase().includes(query) ||
        (course.courseCode || '').toLowerCase().includes(query)
      )
      
      return basicMatch || skillsMatch || affiliationsMatch || violationsMatch || coursesMatch
    })
  }

  // Year filter
  if (selectedYear.value) {
    filtered = filtered.filter(student => student.academicStanding?.currentYear === Number(selectedYear.value))
  }

  // Standing filter
  if (selectedStanding.value) {
    filtered = filtered.filter(student => student.academicStanding?.standing === selectedStanding.value)
  }
  
  // Skill filter
  if (selectedSkill.value) {
    filtered = filtered.filter(student => 
      (student.skills || []).some(skill => skill.name === selectedSkill.value)
    )
  }
  
  // Affiliation filter
  if (selectedAffiliation.value) {
    filtered = filtered.filter(student => 
      (student.affiliations || []).some(affiliation => affiliation.name === selectedAffiliation.value)
    )
  }
  
  // Violation status filter
  if (selectedViolationStatus.value) {
    filtered = filtered.filter(student => 
      (student.violations || []).some(violation => violation.status === selectedViolationStatus.value)
    )
  }

  return filtered
})

// Year Level Distribution
const yearDistribution = computed(() => {
  const yearGroups = allStudents.value.reduce((groups: any, student: Student) => {
    const year = student.academicStanding?.currentYear ?? 1
    if (!groups[year]) {
      groups[year] = {
        year,
        count: 0,
        totalGPA: 0,
        students: []
      }
    }
    groups[year].count++
    groups[year].totalGPA += student.academicStanding?.currentGPA || 0
    groups[year].students.push(student)
    return groups
  }, {})

  const total = allStudents.value.length
  return Object.values(yearGroups)
    .map((group: any) => ({
      year: group.year,
      count: group.count,
      avgGPA: group.count > 0 ? group.totalGPA / group.count : 0,
      percentage: total > 0 ? (group.count / total) * 100 : 0
    }))
    .sort((a: any, b: any) => a.year - b.year)
})

// Display modes
const displayModes = [
  { value: 'table', label: 'Table', icon: 'Table' },
  { value: 'cards', label: 'Cards', icon: 'Grid' },
  { value: 'compact', label: 'Compact', icon: 'List' }
]

// Quick filters
const quickFilters = [
  { name: 'highGPA', label: 'High GPA (3.5+)', filter: { gpaRange: { min: 3.5, max: 4 } } },
  { name: 'atRisk', label: 'At Risk', filter: { academicStanding: ['probation'] } },
  { name: 'programmers', label: 'Programmers', filter: { skills: ['programming'] } },
  { name: 'athletes', label: 'Athletes', filter: { activities: ['basketball'] } },
  { name: 'firstYear', label: 'First Year', filter: { yearLevel: [1] } },
  { name: 'senior', label: 'Senior Students', filter: { yearLevel: [4] } }
]

// Methods
const toggleFilterPanel = () => {
  filterPanelOpen.value = !filterPanelOpen.value
}

const updateFilter = () => {
  studentStore.setFilter(localFilter.value)
}

const toggleSkill = (skill: string) => {
  const skills = localFilter.value?.skills || []
  const index = skills.indexOf(skill)
  if (index > -1) {
    skills.splice(index, 1)
  } else {
    skills.push(skill)
  }
  localFilter.value = { ...localFilter.value, skills: [...skills] }
}

const toggleActivity = (activity: string) => {
  const activities = localFilter.value?.activities || []
  const index = activities.indexOf(activity)
  if (index > -1) {
    activities.splice(index, 1)
  } else {
    activities.push(activity)
  }
  localFilter.value = { ...localFilter.value, activities: [...activities] }
}

const applyFilters = () => {
  studentStore.setFilter(localFilter.value)
  filterPanelOpen.value = false
}

const clearFilters = () => {
  localFilter.value = {
    search: '',
    skills: [],
    activities: [],
    affiliations: [],
    violationStatus: [],
    academicStanding: [],
    yearLevel: [],
    gpaRange: { min: 0, max: 4 },
    ageRange: { min: 16, max: 30 }
  }
  studentStore.clearFilter()
}

const applyQuickFilter = (filter: any) => {
  Object.assign(localFilter.value, filter.filter)
  applyFilters()
}

const isQuickFilterActive = (filter: any) => {
  return Object.entries(filter.filter).every(([key, value]) => {
    return (localFilter.value as any)[key] === value
  })
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
}

const getGPAClass = (gpa: number) => {
  if (gpa >= 3.5) return 'excellent'
  if (gpa >= 3.0) return 'good'
  if (gpa >= 2.5) return 'average'
  return 'low'
}

const getStandingClass = (standing: string) => {
  switch (standing) {
    case 'excellent': return 'excellent'
    case 'good': return 'good'
    case 'average': return 'average'
    case 'warning': return 'warning'
    case 'probation': return 'probation'
    default: return 'average'
  }
}

const viewStudent = (id: number) => {
  router.push(`/students/${id}`)
}

const editStudent = (id: number) => {
  router.push(`/students/${id}/edit`)
}

const deleteStudent = async (id: number) => {
  const student = allStudents.value.find(s => s.id === id)
  const studentName = student ? `${student.personalInfo.firstName} ${student.personalInfo.lastName}` : 'this student'
  
  if (confirm(`Are you sure you want to archive ${studentName}? This will remove them from the active list but keep their data for records.`)) {
    try {
      await studentStore.deleteStudent(id)
      // Show success message
      alert(`Student archived successfully!\n\nStudent: ${studentName}\nID: ${id}\nArchived at: ${new Date().toLocaleString()}`)
    } catch (error) {
      console.error('Failed to archive student:', error)
      alert('Error: Failed to archive student')
    }
  }
}

const closeCreateModal = () => {
  showCreateModal.value = false
}

const handleCreateStudent = () => {
  // Implementation will be added
  showCreateModal.value = false
}

const fetchStudents = () => {
  studentStore.fetchStudents()
}

const generateSampleData = () => {
  studentStore.generateSampleData()
}

// Filter toggle functions for containers
const toggleSkillFilter = (skillName: string) => {
  if (selectedSkill.value === skillName) {
    selectedSkill.value = '' // Deselect if already selected
  } else {
    selectedSkill.value = skillName // Select new skill
  }
}

const toggleAffiliationFilter = (affiliationName: string) => {
  if (selectedAffiliation.value === affiliationName) {
    selectedAffiliation.value = '' // Deselect if already selected
  } else {
    selectedAffiliation.value = affiliationName // Select new affiliation
  }
}

const toggleViolationFilter = (status: string) => {
  if (selectedViolationStatus.value === status) {
    selectedViolationStatus.value = '' // Deselect if already selected
  } else {
    selectedViolationStatus.value = status // Select new status
  }
}

// Format violation status for display
const formatViolationStatus = (status: string) => {
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

// Get violation status count
const getViolationStatusCount = (status: string) => {
  return allStudents.value.reduce((count, student) => {
    const studentViolations = student.violations || []
    const matchingViolations = studentViolations.filter(violation => violation.status === status)
    return count + matchingViolations.length
  }, 0)
}

const handleReportGeneration = (reportData: any) => {
  console.log('Report generation requested:', reportData)
  // You can add additional logic here if needed
  // For example, logging to analytics, showing notifications, etc.
}

// StudentListView methods
const formatStanding = (standing: string) => {
  return standing.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedYear.value = ''
  selectedStanding.value = ''
  selectedSkill.value = ''
  selectedAffiliation.value = ''
  selectedViolationStatus.value = ''
}

// Helper methods for new columns
const getViolationClass = (violations: any[]) => {
  if (violations.length === 0) return 'no-violations'
  const hasCritical = violations.some(v => v.severity === 'critical')
  const hasMajor = violations.some(v => v.severity === 'major')
  if (hasCritical) return 'critical-violations'
  if (hasMajor) return 'major-violations'
  return 'minor-violations'
}

const archiveStudent = async (studentId: number) => {
  const student = allStudents.value.find(s => s.id === studentId)
  const studentName = student ? `${student.personalInfo.firstName} ${student.personalInfo.lastName}` : 'this student'
  
  if (confirm(`Are you sure you want to archive ${studentName}? This will remove them from the active list but keep their data for records.`)) {
    try {
      await studentStore.deleteStudent(studentId)
      // Show success message
      alert(`Student archived successfully!\n\nStudent: ${studentName}\nID: ${studentId}\nArchived at: ${new Date().toLocaleString()}`)
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Failed to archive student'
      alert(`Error: ${errorMessage}`)
    }
  }
}

// Lifecycle
onMounted(() => {
  fetchStudents()
})
</script>

<style scoped>
.student-profiling-view {
  padding: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: #666;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
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

/* StudentListView Styles */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card.probation {
  border-left-color: #ef4444;
}

.stat-card.good {
  border-left-color: #10b981;
}

.stat-card.at-risk {
  border-left-color: #f59e0b;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
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

.student-table-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.student-table {
  width: 100%;
  border-collapse: collapse;
}

.student-table th,
.student-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.student-table th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.student-table tbody tr:hover {
  background-color: #f9fafb;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.excellent {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.good {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.average {
  background-color: #fed7aa;
  color: #9a3412;
}

.status-badge.probation {
  background-color: #fee2e2;
  color: #991b1b;
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

.stat-card h3 {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1f2937;
}

.stat-card.warning .stat-number {
  color: #f59e0b;
}

/* Enhanced Stat Cards */
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

.stat-card.success .stat-icon {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

.stat-change {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.5rem;
}

.stat-change.positive {
  color: #10b981;
}

.stat-change.negative {
  color: #ef4444;
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

/* Academic Standing Grid */
.standing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.standing-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-left: 4px solid;
}

.standing-card.excellent {
  border-left-color: #10b981;
}

.standing-card.good {
  border-left-color: #3b82f6;
}

.standing-card.average {
  border-left-color: #f59e0b;
}

.standing-card.probation {
  border-left-color: #ef4444;
}

.standing-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.standing-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.75rem;
  color: white;
}

.standing-card.excellent .standing-icon {
  background: #10b981;
}

.standing-card.good .standing-icon {
  background: #3b82f6;
}

.standing-card.average .standing-icon {
  background: #f59e0b;
}

.standing-card.probation .standing-icon {
  background: #ef4444;
}

.standing-header h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1rem;
  font-weight: 600;
}

.standing-stats {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 1rem;
}

.standing-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.standing-percentage {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.standing-progress {
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.standing-card.good .progress-bar {
  background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
}

.standing-card.average .progress-bar {
  background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
}

.standing-card.probation .progress-bar {
  background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
}

/* Year Level Distribution */
.year-distribution {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.year-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #3b82f6;
}

.year-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.year-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
}

.year-count {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.year-progress {
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.year-card .progress-bar {
  background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
}

.year-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.year-percentage {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.year-gpa {
  font-size: 0.875rem;
  color: #1f2937;
  font-weight: 600;
}

/* Student List Section */
.student-list-section {
  margin-top: 2rem;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.list-header h2 {
  margin: 0;
  color: #1f2937;
  font-size: 1.5rem;
}

.list-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-box {
  position: relative;
}

.search-input {
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  width: 300px;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-box svg {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #6b7280;
}

.view-toggle {
  display: flex;
  gap: 0.25rem;
  background: #f3f4f6;
  padding: 0.25rem;
  border-radius: 8px;
}

.view-btn {
  padding: 0.5rem;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  color: #6b7280;
}

.view-btn:hover {
  color: #374151;
}

.view-btn.active {
  background: white;
  color: #3b82f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.view-btn svg {
  width: 20px;
  height: 20px;
}

/* Quick Filters */
.quick-filters-bar {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.quick-filter {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 20px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.quick-filter:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.quick-filter.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

/* Loading and Error States */
.loading-state, .error-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state svg {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin-bottom: 1rem;
}

/* Table Styles */
.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.students-table {
  width: 100%;
  border-collapse: collapse;
}

.students-table th {
  background: #f8fafc;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.students-table td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.student-name-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.student-avatar {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
}

.student-avatar.large {
  width: 48px;
  height: 48px;
  font-size: 1.125rem;
}

.gpa-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.gpa-badge.excellent { background: #10b981; color: white; }
.gpa-badge.good { background: #3b82f6; color: white; }
.gpa-badge.average { background: #f59e0b; color: white; }
.gpa-badge.low { background: #ef4444; color: white; }

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  background: #f3f4f6;
  color: #6b7280;
}

.status-badge.active {
  background: #10b981;
  color: white;
}

.skills-tags {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.skill-tag {
  padding: 0.25rem 0.5rem;
  background: #e5e7eb;
  color: #374151;
  border-radius: 12px;
  font-size: 0.75rem;
}

.more-skills {
  padding: 0.25rem 0.5rem;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 12px;
  font-size: 0.75rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.5rem;
  background: transparent;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  color: #6b7280;
}

.btn-icon:hover {
  background: #f3f4f6;
  color: #374151;
}

.btn-icon.danger:hover {
  background: #fef2f2;
  color: #ef4444;
}

/* Cards View */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.student-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.student-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.student-info h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
}

.student-info p {
  margin: 0.25rem 0 0 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.card-actions {
  margin-left: auto;
}

.card-body {
  padding: 1.5rem;
}

.student-details {
  margin-bottom: 1.5rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.detail-item .label {
  color: #6b7280;
  font-size: 0.875rem;
}

.detail-item .value {
  color: #1f2937;
  font-weight: 500;
}

.skills-section h4 {
  margin: 0 0 0.75rem 0;
  color: #1f2937;
  font-size: 0.875rem;
}

/* Compact View */
.compact-list {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.compact-item {
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e5e7eb;
  cursor: pointer;
  transition: background-color 0.2s;
}

.compact-item:hover {
  background: #f8fafc;
}

.compact-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1rem;
}

.compact-info {
  flex: 1;
}

.compact-info h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1rem;
}

.compact-info p {
  margin: 0.25rem 0 0 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.compact-meta {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.compact-actions {
  display: flex;
  align-items: center;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.pagination-btn {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.pagination-btn:hover:not(:disabled) {
  border-color: #3b82f6;
  color: #3b82f6;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.page-number {
  padding: 0.75rem;
  min-width: 40px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.page-number:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.page-number.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

/* Mobile Card View Styles */
.mobile-cards-view {
  display: none; /* Hidden by default, shown on mobile */
}

.mobile-student-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1rem;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

.mobile-card-header {
  padding: 1rem;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.mobile-student-info h3 {
  margin: 0 0 0.5rem 0;
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
}

.mobile-student-info p {
  margin: 0 0 0.75rem 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.mobile-student-id {
  background: #f3f4f6;
  padding: 0.5rem;
  border-radius: 6px;
  font-size: 0.875rem;
  color: #6b7280;
  white-space: nowrap;
}

.mobile-card-body {
  padding: 1rem;
}

.mobile-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
  gap: 1rem;
}

.mobile-detail-row:last-child {
  margin-bottom: 0;
}

.mobile-detail-label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
  min-width: 100px;
  flex-shrink: 0;
}

.mobile-detail-value {
  flex: 1;
  text-align: right;
}

.affiliations-tags {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.affiliation-tag {
  padding: 0.25rem 0.5rem;
  background: #dbeafe;
  color: #1e40af;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.more-affiliations {
  padding: 0.25rem 0.5rem;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 12px;
  font-size: 0.75rem;
}

.violations-info {
  text-align: right;
}

.violation-count {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  display: inline-block;
}

.violation-count.none {
  background: #f0fdf4;
  color: #16a34a;
}

.violation-count.low {
  background: #fef3c7;
  color: #d97706;
}

.violation-count.moderate {
  background: #fed7aa;
  color: #ea580c;
}

.violation-count.high {
  background: #fecaca;
  color: #dc2626;
}

.violation-types {
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #6b7280;
}

.violation-type {
  margin-right: 0.25rem;
}

.more-violations {
  color: #9ca3af;
}

.mobile-card-actions {
  padding: 1rem;
  background: #f8fafc;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.mobile-card-actions .btn {
  flex: 1;
  min-width: 80px;
  text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
  .student-profiling-view {
    padding: 1rem 0.5rem;
  }

  .page-header {
    padding: 1rem;
    text-align: center;
  }

  .page-header h1 {
    font-size: 1.5rem;
  }

  .actions {
    flex-direction: column;
    gap: 0.5rem;
    padding: 0 1rem;
  }

  .actions .btn {
    width: 100%;
    justify-content: center;
  }

  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 0 1rem;
  }

  .stat-card {
    flex-direction: column;
    text-align: center;
    padding: 1rem;
  }

  .stat-icon {
    margin-bottom: 0.5rem;
  }

  .analytics-section {
    padding: 1rem;
  }

  .year-distribution {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .year-card {
    padding: 1rem;
  }

  .stats-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 0 1rem;
  }

  .filters-container {
    flex-direction: column;
    padding: 1rem;
    gap: 1rem;
  }

  .filter-group {
    width: 100%;
  }

  .filter-select {
    width: 100%;
  }

  .search-box {
    width: 100%;
  }

  .search-input {
    width: 100%;
    font-size: 16px; /* Prevents zoom on iOS */
  }

  .student-table-container {
    overflow-x: auto;
    margin: 0 1rem;
  }

  .student-table {
    min-width: 600px;
    font-size: 0.875rem;
  }

  .student-table th,
  .student-table td {
    padding: 0.5rem;
  }

  .skills-tags {
    flex-direction: column;
    gap: 0.25rem;
  }

  .action-buttons {
    flex-direction: column;
    gap: 0.25rem;
  }

  .btn-icon {
    padding: 0.75rem;
    width: 100%;
    justify-content: center;
  }

  .cards-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 0 1rem;
  }

  .student-card {
    margin-bottom: 1rem;
  }

  .card-header {
    padding: 1rem;
    flex-direction: column;
    text-align: center;
  }

  .card-actions {
    margin-left: 0;
    margin-top: 1rem;
  }

  .card-body {
    padding: 1rem;
  }

  .compact-list {
    margin: 0 1rem;
  }

  .compact-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
  }

  .compact-avatar {
    width: 50px;
    height: 50px;
    font-size: 1.25rem;
  }

  .compact-actions {
    width: 100%;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .pagination {
    flex-direction: column;
    gap: 1rem;
    padding: 0 1rem;
  }

  .page-numbers {
    flex-wrap: wrap;
    justify-content: center;
  }

  .page-number {
    padding: 0.5rem;
    min-width: 36px;
    font-size: 0.875rem;
  }

  .pagination-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }

  /* Hide table on mobile, show cards */
  .student-table {
    display: none;
  }

  .mobile-cards-view {
    display: block;
    padding: 0 1rem;
  }

  .mobile-student-card {
    margin-bottom: 1rem;
  }

  .mobile-card-header {
    padding: 0.75rem;
    flex-direction: column;
    gap: 0.75rem;
  }

  .mobile-student-id {
    align-self: flex-start;
  }

  .mobile-detail-row {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }

  .mobile-detail-label {
    min-width: auto;
  }

  .mobile-detail-value {
    text-align: left;
  }

  .affiliations-tags {
    justify-content: flex-start;
  }

  .violations-info {
    text-align: left;
  }
}

/* Extra small screens (phones) */
@media (max-width: 480px) {
  .student-profiling-view {
    padding: 0.5rem;
  }

  .page-header {
    padding: 0.75rem;
  }

  .page-header h1 {
    font-size: 1.25rem;
  }

  .page-header p {
    font-size: 0.875rem;
  }

  .stats-grid {
    padding: 0 0.5rem;
  }

  .stat-card {
    padding: 0.75rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }

  .analytics-section {
    padding: 0.75rem;
  }

  .year-card {
    padding: 0.75rem;
  }

  .stats-container {
    grid-template-columns: 1fr;
    padding: 0 0.5rem;
  }

  .filters-container {
    padding: 0.75rem;
  }

  .student-table-container {
    margin: 0 0.5rem;
  }

  .student-table {
    min-width: 500px;
    font-size: 0.75rem;
  }

  .student-table th,
  .student-table td {
    padding: 0.375rem;
  }

  .cards-grid {
    padding: 0 0.5rem;
  }

  .card-header {
    padding: 0.75rem;
  }

  .card-body {
    padding: 0.75rem;
  }

  .compact-list {
    margin: 0 0.5rem;
  }

  .compact-item {
    padding: 0.75rem;
  }

  .compact-avatar {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }

  .pagination {
    padding: 0 0.5rem;
  }

  .page-number {
    padding: 0.375rem;
    min-width: 32px;
    font-size: 0.75rem;
  }

  .pagination-btn {
    padding: 0.375rem 0.5rem;
    font-size: 0.75rem;
  }

  .btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }

  .btn-small {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
  }

  .icon-small {
    width: 16px;
    height: 16px;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .stats-container {
    grid-template-columns: repeat(4, 1fr);
  }

  .year-distribution {
    grid-template-columns: repeat(2, 1fr);
  }

  .filters-container {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .filter-group {
    flex: 1;
    min-width: 150px;
  }
}

/* Desktop and Tablet styles - show table, hide mobile cards */
@media (min-width: 769px) {
  .student-table {
    display: table;
  }

  .mobile-cards-view {
    display: none;
  }
}

/* Tablet styles */
@media (min-width: 769px) and (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .year-distribution {
    grid-template-columns: repeat(2, 1fr);
  }

  .cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .filters-container {
    flex-wrap: wrap;
  }

  .filter-group {
    flex: 1;
    min-width: 200px;
  }
}

/* Filter Container Styles */
.skills-filter-container,
.affiliations-filter-container,
.violations-filter-container {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.skills-header,
.affiliations-header,
.violations-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #f3f4f6;
}

.skills-header h3,
.affiliations-header h3,
.violations-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.selected-skill-info,
.selected-affiliation-info,
.selected-violation-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: #f0f9ff;
  border: 1px solid #0ea5e9;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.selected-skill-label,
.selected-affiliation-label,
.selected-violation-label {
  color: #0369a1;
  font-weight: 500;
}

.selected-skill-name,
.selected-affiliation-name,
.selected-violation-name {
  color: #0c4a6e;
  font-weight: 600;
}

.clear-skill-btn,
.clear-affiliation-btn,
.clear-violation-btn {
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.75rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.clear-skill-btn:hover,
.clear-affiliation-btn:hover,
.clear-violation-btn:hover {
  background: #dc2626;
}

.skill-tags,
.affiliation-tags,
.violation-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skill-tag,
.affiliation-tag,
.violation-tag {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.skill-tag:hover,
.affiliation-tag:hover,
.violation-tag:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  transform: translateY(-1px);
}

.skill-tag.active,
.affiliation-tag.active,
.violation-tag.active {
  background: #3b82f6;
  color: white;
  border-color: #2563eb;
}

.skill-tag.active:hover,
.affiliation-tag.active:hover,
.violation-tag.active:hover {
  background: #2563eb;
}

.skill-count,
.affiliation-count,
.violation-count {
  font-size: 0.75rem;
  opacity: 0.7;
  font-weight: 500;
}

.skill-tag.active .skill-count,
.affiliation-tag.active .affiliation-count,
.violation-tag.active .violation-count {
  opacity: 0.9;
}

/* Responsive design for filter containers */
@media (max-width: 768px) {
  .skills-filter-container,
  .affiliations-filter-container,
  .violations-filter-container {
    padding: 1rem;
  }
  
  .skills-header,
  .affiliations-header,
  .violations-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .selected-skill-info,
  .selected-affiliation-info,
  .selected-violation-info {
    align-self: stretch;
    justify-content: center;
  }
  
  .skill-tags,
  .affiliation-tags,
  .violation-tags {
    justify-content: center;
  }
}
</style>
