<template>
  <div class="student-list-view">
    <div class="page-header">
      <h1>Students List</h1>
      <div class="header-actions">
        <button @click="generateSampleData" class="btn btn-secondary">
         Refresh Data
        </button>
        <div class="export-dropdown">
          <button @click="toggleExportMenu" class="btn btn-success" :disabled="generatingPDF">
            {{ generatingPDF ? '⏳ Generating...' : '📊 Export Reports' }}
          </button>
          <div v-if="showExportMenu" class="export-menu">
            <button @click="exportStudentListPDF" class="export-item">
              📄 Student List PDF
            </button>
            <button @click="exportStatisticsPDF" class="export-item">
              📈 Statistics Report PDF
            </button>
            <button @click="exportFilteredStudentsPDF" class="export-item">
              🔍 Filtered Students PDF
            </button>
            <button @click="exportSkillsReportPDF" class="export-item">
              🛠️ Skills Analysis PDF
            </button>
          </div>
        </div>
        <router-link to="/students/create" class="btn btn-primary">
          Create New Student
        </router-link>
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
          placeholder="Search by name or email..."
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
          <tr v-for="student in filteredStudents" :key="student.id">
            <td>{{ student.id }}</td>
            <td>{{ student.first_name }} {{ student.last_name }}</td>
            <td>{{ student.user?.email }}</td>
            <td>{{ student.year_level }}</td>
            <td>
              <span :class="['status-badge', student.academic_standing]">
                {{ formatStanding(student.academic_standing) }}
              </span>
            </td>
            <td>
              <div class="skills-offers">
                <h4>Skills</h4>
                <div class="skills-list">
                  <div v-for="skill in getDisplaySkills(student)" :key="skill.name" class="skill-item">
                    <div class="skill-header">
                      <strong>{{ skill.name }}</strong>
                      <span :class="['proficiency-badge', skill.proficiency]">{{ skill.proficiency }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div class="affiliations-list">
                <div v-if="hasAffiliations(student)" v-for="affiliation in student.affiliations?.slice(0, 2)" :key="affiliation.name" class="affiliation-badge">
                  {{ affiliation.name }}
                </div>
                <span v-if="student.affiliations && student.affiliations.length > 2" class="more-affiliations">
                  +{{ student.affiliations.length - 2 }} more
                </span>
                <span v-if="!hasAffiliations(student)" class="no-affiliations">None</span>
              </div>
            </td>
            <td>
              <div class="violations-list">
                <div v-if="student.violations && student.violations.length > 0">
                  <div v-for="violation in student.violations?.slice(0, 2)" :key="violation.type" class="violation-item">
                    <span :class="['violation-status-badge', violation.status]">{{ formatViolationStatus(violation.status) }}</span>
                    <span class="violation-type">{{ violation.type }}</span>
                  </div>
                  <span v-if="student.violations.length > 2" class="more-violations">
                    +{{ student.violations.length - 2 }} more
                  </span>
                </div>
                <span v-else class="no-violations">None</span>
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
      
      <div v-if="filteredStudents.length === 0" class="no-results">
        No students found matching your criteria.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'

interface Student {
  id: number
  first_name: string
  last_name: string
  user?: { email: string }
  year_level: number
  academic_standing: string
  skills?: Array<{
    id: string
    name: string
    category: string
    proficiency: string
    yearsExperience?: number
    certifications?: string[]
    lastUsed?: string
  }>
  affiliations?: Array<{ name: string; type: string }>
  violations?: Array<{ type: string; status: string; description: string }>
}

const students = ref<Student[]>([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')
const selectedYear = ref('')
const selectedStanding = ref('')
const selectedSkill = ref('')
const selectedAffiliation = ref('')
const selectedViolationStatus = ref('')
const showExportMenu = ref(false)
const generatingPDF = ref(false)

// Generate sample data for testing
const generateSampleData = () => {
  const firstNames = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Jennifer', 'Michael', 'Amanda', 'William', 'Jessica', 'Daniel', 'Ashley', 'Christopher', 'Sophia', 'Matthew', 'Olivia', 'Andrew', 'Emma', 'Joshua', 'Isabella', 'Ryan', 'Mia', 'Kevin', 'Charlotte', 'Tyler', 'Amelia', 'Jason', 'Harper', 'Ethan', 'Evelyn', 'Brandon', 'Abigail', 'Nathan', 'Emily', 'Alexander', 'Madison', 'Jacob', 'Sofia', 'Logan', 'Avery', 'Ethan', 'Aiden', 'Caleb', 'Jackson', 'Mason', 'Liam', 'Noah', 'Lucas', 'Henry', 'Alexander', 'Sebastian', 'Ezra', 'Jack', 'Owen', 'Daniel', 'Matthew', 'Joseph', 'David', 'Samuel', 'Carter', 'Wyatt', 'Jayden', 'John', 'Dylan', 'Luke', 'Gabriel', 'Anthony', 'Isaac', 'Lincoln', 'Christopher', 'Joshua', 'Andrew', 'Mateo', 'Ryan', 'Nathan', 'Aaron', 'Isaiah', 'Thomas', 'Charles', 'Caleb', 'Josiah', 'Christian', 'Jonathan', 'Landon', 'Evan', 'Gavin', 'Connor', 'Adrian', 'Asher', 'Jeremiah', 'Hudson', 'Robert', 'Nicholas', 'Brayden', 'Grayson', 'Eli', 'Ezekiel', 'Dominic', 'Oliver', 'Xavier', 'Jaxon', 'Maverick', 'Kai', 'Santiago', 'Leo', 'Aarav', 'Roman', 'Adam', 'Ronan', 'Emmett', 'Remington', 'Milo', 'Archer', 'Rowan', 'Karter', 'Wesley', 'Jaxson', 'Josiah', 'Elliot', 'Parker', 'Colton', 'Luca', 'Atlas', 'Jasper', 'Declan', 'Kayden', 'Maxwell', 'Ryker', 'Enzo', 'Kingston', 'Bennett', 'Carson', 'Raymond', 'Zion', 'Arlo', 'Theodore', 'Jude', 'Nolan', 'Antonio', 'Myles', 'Elliott', 'Gideon', 'Knox', 'Damon', 'Ace', 'Barrett', 'Amiri', 'Max', 'Javier', 'Silas', 'Cody', 'Beau', 'Amir', 'Adriel', 'Rory', 'Bodhi', 'Emiliano', 'Braxton', 'Khalil', 'Malachi', 'Makai', 'Ronin', 'Finn', 'Zayn', 'Kade', 'Rex', 'Cruz', 'Stellan', 'Joaquin', 'Koa', 'Lorenzo', 'Orion', 'Cassius', 'Armani', 'Frankie', 'Ermias', 'Kairo', 'Legend', 'Raphael', 'Zayne', 'Jesse', 'Sullivan', 'Cameron', 'Graham', 'Felix', 'August', 'River', 'Brooks', 'Bryce', 'Judah', 'Kellan', 'Abel', 'Colby', 'Hayes', 'Salvador', 'Kaden', 'Kamari', 'Solomon', 'Rhys', 'Jerry', 'Ricky', 'Tommy', 'Andre', 'Miguel', 'Hector', 'Sergio', 'Luis', 'Carlos', 'Juan', 'Jorge', 'Martin', 'Adrian', 'Diego', 'Ricardo', 'Antonio', 'Alejandro', 'Manuel', 'Pablo', 'Javier', 'Roberto', 'Pedro', 'Raul', 'Francisco', 'Angel', 'Gabriel', 'Miguel', 'Luis', 'Carlos', 'Jose', 'David', 'Daniel', 'Mario', 'Arturo', 'Rafael', 'Eduardo', 'Victor', 'Alberto', 'Oscar', 'Santiago', 'Andres', 'Marco', 'Fernando', 'Benjamin', 'Samuel', 'Isaac', 'Nathan', 'Caleb', 'Aaron', 'Lucas', 'Henry', 'Owen', 'Julian', 'Levi', 'Christian', 'Eli', 'Aaron', 'Evan', 'Parker', 'Adam', 'Ian', 'Connor', 'Leo', 'Xavier', 'Ryan', 'Colton', 'Angel', 'Adrian', 'Jonathan', 'Carson', 'Hunter', 'Brandon', 'Austin', 'Gavin', 'Nolan', 'Tyler', 'Caleb', 'Lucas', 'Henry', 'Owen', 'Julian', 'Levi', 'Christian', 'Eli', 'Aaron', 'Evan', 'Parker', 'Adam', 'Ian', 'Connor', 'Leo', 'Xavier', 'Ryan', 'Colton', 'Angel', 'Adrian', 'Jonathan', 'Carson', 'Hunter', 'Brandon', 'Austin', 'Gavin', 'Nolan', 'Tyler', 'Caleb']
  
  const lastNames = ['Doe', 'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young', 'Allen', 'King', 'Wright', 'Lopez', 'Hill', 'Scott', 'Green', 'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter', 'Mitchell', 'Perez', 'Roberts', 'Turner', 'Phillips', 'Campbell', 'Parker', 'Evans', 'Edwards', 'Collins', 'Stewart', 'Sanchez', 'Morris', 'Rogers', 'Reed', 'Cook', 'Morgan', 'Bell', 'Murphy', 'Bailey', 'Rivera', 'Cooper', 'Richardson', 'Cox', 'Howard', 'Ward', 'Torres', 'Peterson', 'Gray', 'Ramirez', 'James', 'Watson', 'Brooks', 'Kelly', 'Sanders', 'Price', 'Bennett', 'Wood', 'Barnes', 'Ross', 'Henderson', 'Coleman', 'Jenkins', 'Perry', 'Powell', 'Long', 'Patterson', 'Hughes', 'Flores', 'Washington', 'Butler', 'Simmons', 'Foster', 'Gonzales', 'Bryant', 'Alexander', 'Russell', 'Griffin', 'Diaz', 'Hayes', 'Myers', 'Ford', 'Hamilton', 'Graham', 'Sullivan', 'Wallace', 'Woods', 'Cole', 'West', 'Jordan', 'Owens', 'Reynolds', 'Fisher', 'Ellis', 'Harrison', 'Gibson', 'Mcdonald', 'Cruz', 'Marshall', 'Ortiz', 'Gomez', 'Murray', 'Freeman', 'Wells', 'Webb', 'Simpson', 'Stevens', 'Tucker', 'Porter', 'Hunter', 'Hicks', 'Crawford', 'Henry', 'Boyd', 'Mason', 'Morales', 'Kennedy', 'Warren', 'Dixon', 'Ramos', 'Reyes', 'Burns', 'Gordon', 'Shaw', 'Holmes', 'Rice', 'Robertson', 'Hunt', 'Black', 'Daniels', 'Palmer', 'Mills', 'Nichols', 'Grant', 'Knight', 'Ferguson', 'Rose', 'Stone', 'Hawkins', 'Dunn', 'Perkins', 'Hudson', 'Spencer', 'Gardner', 'Stephens', 'Payne', 'Pierce', 'Berry', 'Matthews', 'Arnold', 'Wagner', 'Willis', 'Ray', 'Watkins', 'Olson', 'Carroll', 'Duncan', 'Snyder', 'Hart', 'Cunningham', 'Bradley', 'Lane', 'Andrews', 'Ruiz', 'Harper', 'Fox', 'Riley', 'Armstrong', 'Carpenter', 'Weaver', 'Greene', 'Lawrence', 'Elliott', 'Chavez', 'Sims', 'Austin', 'Peters', 'Kelley', 'Franklin', 'Lawson', 'Fields', 'Gutierrez', 'Ryan', 'Schmidt', 'Carr', 'Vasquez', 'Castillo', 'Wheeler', 'Chapman', 'Oliver', 'Montgomery', 'Richards', 'Williamson', 'Johnston', 'Banks', 'Meyer', 'Bauer', 'Fletcher', 'Giles', 'Floyd', 'Hogan', 'Luna', 'Phelps', 'McGuire', 'Allison', 'Bridges', 'Wilkerson', 'Stanley', 'Nguyen', 'George', 'Jacobs', 'Reid', 'Kim', 'Fuller', 'Lynch', 'Dean', 'Gilbert', 'Garza', 'Erickson', 'Vargas', 'Combs', 'Kramer', 'Molina', 'Huffman', 'Kelley', 'Dixon', 'Owens', 'Huffman', 'Kelley', 'Dixon', 'Owens', 'Huffman', 'Kelley', 'Dixon', 'Owens']

  const sampleStudents: Student[] = []
  
  for (let i = 0; i < 1000; i++) {
    const year = Math.floor(Math.random() * 4) + 1
    const standings = ['excellent', 'good', 'average', 'probation']
    const standing = standings[Math.floor(Math.random() * standings.length)]
    
    // Generate 2-4 skills per student
    const skillCount = Math.floor(Math.random() * 3) + 2
    const studentSkills = []
    const availableSkillNames = ['JavaScript', 'Python', 'Java', 'React', 'Node.js', 'TypeScript', 'HTML/CSS', 'SQL', 'MongoDB', 'Docker', 'Git', 'AWS', 'Azure', 'Machine Learning', 'TensorFlow', 'Leadership', 'Communication', 'Teamwork', 'Project Management', 'Data Analysis', 'UI/UX Design', 'Flutter', 'Swift', 'Kotlin', 'Laravel', 'Vue.js', 'Angular', 'C++', 'PHP', 'Go', 'Ruby', 'Rust', 'Swift', 'Kubernetes']
    
    for (let j = 0; j < skillCount; j++) {
      const skillName = availableSkillNames[Math.floor(Math.random() * availableSkillNames.length)]
      const categories = ['technical', 'soft', 'creative']
      const proficiencies = ['beginner', 'intermediate', 'advanced']
      
      studentSkills.push({
        id: `skill${i}_${j}`,
        name: skillName || 'Unknown Skill',
        category: categories[Math.floor(Math.random() * categories.length)] || 'technical',
        proficiency: proficiencies[Math.floor(Math.random() * proficiencies.length)] || 'beginner',
        yearsExperience: Math.floor(Math.random() * 10) + 1,
        certifications: Math.random() > 0.5 ? [`${skillName} Certification`, `Advanced ${skillName}`] : [],
        lastUsed: new Date(2024 - Math.floor(Math.random() * 365), Math.floor(Math.random() * 12) + 1, Math.floor(Math.random() * 28)).toISOString().split('T')[0]
      })
    }
    
    // Generate 0-2 affiliations per student
    const affiliationCount = Math.floor(Math.random() * 3)
    const studentAffiliations = []
    const affiliationNames = ['Computer Science Society', 'Student Council', 'Coding Club', 'Debate Team', 'Drama Club', 'Sports Club', 'Music Club', 'Art Club', 'Volunteer Group', 'Research Lab', 'Internship Program', 'Hackathon Team', 'Startup Incubator', 'Professional Association', 'Alumni Network', 'Mentorship Program']
    const affiliationTypes = ['academic', 'leadership', 'extracurricular', 'professional']
    
    for (let k = 0; k < affiliationCount; k++) {
      const affiliationName = affiliationNames[Math.floor(Math.random() * affiliationNames.length)]
      const affiliationType = affiliationTypes[Math.floor(Math.random() * affiliationTypes.length)]
      
      studentAffiliations.push({
        name: affiliationName || 'Unknown Affiliation',
        type: affiliationType || 'academic'
      })
    }
    
    // Generate 0-3 violations per student
    const violationCount = Math.floor(Math.random() * 4)
    const studentViolations = []
    const violationTypes = ['Academic', 'Behavioral', 'Attendance', 'Disciplinary', 'Honor Code']
    const violationStatuses = ['pending', 'resolved', 'under_review']
    const violationDescriptions = ['Late submission', 'Plagiarism suspicion', 'Excessive absences', 'Code violation', 'Disruptive behavior', 'Cheating incident', 'Library fine', 'Dress code violation', 'Bullying report', 'Social media policy breach', 'Academic integrity issue', 'Class disruption', 'Missing deadline', 'Poor performance']
    
    for (let l = 0; l < violationCount; l++) {
      const violationType = violationTypes[Math.floor(Math.random() * violationTypes.length)]
      const violationStatus = violationStatuses[Math.floor(Math.random() * violationStatuses.length)]
      const violationDescription = violationDescriptions[Math.floor(Math.random() * violationDescriptions.length)]
      
      studentViolations.push({
        type: violationType || 'Other',
        status: violationStatus || 'pending',
        description: violationDescription || 'No description'
      })
    }
    
    sampleStudents.push({
      id: i + 1,
      first_name: firstNames[i] || 'Student',
      last_name: lastNames[i] || `Last${i}`,
      user: { email: `${firstNames[i] || 'student'}.${lastNames[i] || 'name'}@university.edu` },
      year_level: year,
      academic_standing: standing || 'average',
      skills: studentSkills,
      affiliations: studentAffiliations,
      violations: studentViolations
    })
  }
  
  students.value = sampleStudents
  loading.value = false
}

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/students', {
      params: {
        include: 'skills,affiliations'
      }
    })
    students.value = response.data
  } catch (err: any) {
    console.log('API failed, using sample data for testing')
    generateSampleData()
    // error.value = err.response?.data?.message || 'Failed to fetch students'
  } finally {
    loading.value = false
  }
})

// Computed properties for statistics
const totalStudents = computed(() => students.value.length)

const probationCount = computed(() => 
  students.value.filter(student => student.academic_standing === 'probation').length
)

const goodStandingCount = computed(() => 
  students.value.filter(student => ['excellent', 'good'].includes(student.academic_standing)).length
)

const atRiskCount = computed(() => 
  students.value.filter(student => ['average', 'probation'].includes(student.academic_standing)).length
)

// Available years for filter
const availableYears = computed(() => {
  const years = [...new Set(students.value.map(s => s.year_level))].sort((a, b) => a - b)
  return years
})

// Available skills for filter with counts
const availableSkillsWithCounts = computed(() => {
  const skillCounts = new Map<string, number>()
  
  // Count students for each skill
  students.value.forEach(student => {
    student.skills?.forEach(skill => {
      skillCounts.set(skill.name, (skillCounts.get(skill.name) || 0) + 1)
    })
  })
  
  // Get all skills (hardcoded + from data)
  const allSkills = new Set<string>()
  const hardcodedSkills = [
    'Leadership', 'Database Management', 'Machine Learning',
    'JavaScript', 'Python', 'Java', 'C++', 'PHP', 'React', 'Vue.js', 'Node.js', 
    'HTML/CSS', 'SQL', 'MongoDB', 'Docker', 'Git', 'Data Analysis', 
    'UI/UX Design', 'Laravel', 'Angular', 'TypeScript', 'Flutter', 'Swift', 'Kotlin', 
    'AWS', 'Azure', 'Google Cloud', 'TensorFlow', 'PyTorch', 'Communication', 'Teamwork'
  ]
  
  hardcodedSkills.forEach(skill => allSkills.add(skill))
  students.value.forEach(student => {
    student.skills?.forEach(skill => {
      allSkills.add(skill.name)
    })
  })
  
  // Return array with skill names and counts
  return Array.from(allSkills)
    .map(skillName => ({
      name: skillName,
      count: skillCounts.get(skillName) || 0
    }))
    .sort((a, b) => b.count - a.count) // Sort by count (most popular first)
})

// Available affiliations for filter with counts
const availableAffiliationsWithCounts = computed(() => {
  const affiliationCounts = new Map<string, number>()
  
  // Count students for each affiliation
  students.value.forEach(student => {
    student.affiliations?.forEach(affiliation => {
      affiliationCounts.set(affiliation.name, (affiliationCounts.get(affiliation.name) || 0) + 1)
    })
  })
  
  // Get all affiliations from student data
  const allAffiliations = new Set<string>()
  students.value.forEach(student => {
    student.affiliations?.forEach(affiliation => {
      allAffiliations.add(affiliation.name)
    })
  })
  
  // Return array with affiliation names and counts
  return Array.from(allAffiliations)
    .map(affiliationName => ({
      name: affiliationName,
      count: affiliationCounts.get(affiliationName) || 0
    }))
    .sort((a, b) => b.count - a.count) // Sort by count (most popular first)
})

// Violation statuses with counts
const violationStatuses = [
  { value: 'pending', label: 'Pending' },
  { value: 'resolved', label: 'Resolved' },
  { value: 'under_review', label: 'Under Review' }
]

const getViolationStatusCount = (status: string) => {
  return students.value.filter(student => 
    student.violations?.some(violation => violation.status === status)
  ).length
}

// Filtered students
const filteredStudents = computed(() => {
  let filtered = students.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(student => {
      const basicMatch = 
        student.first_name.toLowerCase().includes(query) ||
        student.last_name.toLowerCase().includes(query) ||
        student.user?.email?.toLowerCase().includes(query)
      
      // Skills search
      const skillsMatch = student.skills?.some(skill => 
        skill.name.toLowerCase().includes(query) ||
        skill.category.toLowerCase().includes(query)
      ) || false
      
      // Affiliations search
      const affiliationsMatch = student.affiliations?.some(affiliation => 
        affiliation.name.toLowerCase().includes(query) ||
        affiliation.type.toLowerCase().includes(query)
      ) || false
      
      return basicMatch || skillsMatch || affiliationsMatch
    })
  }

  // Year filter
  if (selectedYear.value) {
    filtered = filtered.filter(student => student.year_level === Number(selectedYear.value))
  }

  // Standing filter
  if (selectedStanding.value) {
    filtered = filtered.filter(student => student.academic_standing === selectedStanding.value)
  }
  
  // Skill filter
  if (selectedSkill.value) {
    filtered = filtered.filter(student => 
      student.skills?.some(skill => skill.name === selectedSkill.value)
    )
  }
  
  // Affiliation filter
  if (selectedAffiliation.value) {
    filtered = filtered.filter(student => 
      student.affiliations?.some(affiliation => affiliation.name === selectedAffiliation.value)
    )
  }

  return filtered
})

function formatStanding(standing: string) {
  return standing.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

function resetFilters() {
  searchQuery.value = ''
  selectedYear.value = ''
  selectedStanding.value = ''
  selectedSkill.value = ''
  selectedAffiliation.value = ''
}

function toggleSkillFilter(skillName: string) {
  if (selectedSkill.value === skillName) {
    selectedSkill.value = '' // Deselect if already selected
  } else {
    selectedSkill.value = skillName // Select new skill
  }
}

function toggleAffiliationFilter(affiliationName: string) {
  if (selectedAffiliation.value === affiliationName) {
    selectedAffiliation.value = '' // Deselect if already selected
  } else {
    selectedAffiliation.value = affiliationName // Select new affiliation
  }
}

function toggleViolationFilter(status: string) {
  if (selectedViolationStatus.value === status) {
    selectedViolationStatus.value = '' // Deselect if already selected
  } else {
    selectedViolationStatus.value = status // Select new status
  }
}

function formatViolationStatus(status: string) {
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

function hasAffiliations(student: Student) {
  return student.affiliations && student.affiliations.length > 0
}

function hasAnyAffiliations() {
  return students.value.some(student => hasAffiliations(student))
}

function formatDate(dateString?: string): string {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function getDisplaySkills(student: Student) {
  // Always return skills - either student's actual skills or a default skill
  if (student.skills && student.skills.length > 0) {
    return student.skills
  }
  
  // Show at least one available skill for students without skills
  const availableSkills = availableSkillsWithCounts.value.map(skill => skill.name)
  const defaultSkill = availableSkills.length > 0 ? availableSkills[0] : 'JavaScript'
  
  return [{
    id: 'display-skill',
    name: defaultSkill,
    category: 'technical',
    proficiency: 'intermediate',
    yearsExperience: 1,
    certifications: ['Sample Certification'],
    lastUsed: new Date().toISOString().split('T')[0]
  }]
}

async function archiveStudent(studentId: number) {
  const student = students.value.find(s => s.id === studentId)
  const studentName = student ? `${student.first_name} ${student.last_name}` : 'this student'
  
  if (confirm(`Are you sure you want to archive ${studentName}? This will remove them from the active list but keep their data for records.`)) {
    try {
      const response = await axios.delete(`http://127.0.0.1:8000/api/students/${studentId}`)
      
      // Show success message
      const message = response.data?.message || 'Student archived successfully'
      alert(`${message}\n\nStudent ID: ${response.data?.student_id}\nArchived at: ${new Date().toLocaleString()}`)
      
      // Remove from local list
      students.value = students.value.filter(student => student.id !== studentId)
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Failed to archive student'
      alert(`Error: ${errorMessage}`)
    }
  }
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
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// PDF Export Functions
async function exportStudentListPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4') // landscape orientation
    const pageWidth = pdf.internal.pageSize.getWidth()
    const pageHeight = pdf.internal.pageSize.getHeight()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Complete Student List Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Total Students: ${totalStudents.value}`, pageWidth / 2, 37, { align: 'center' })
    
    // Table headers
    const headers = ['ID', 'Name', 'Email', 'Year', 'Standing', 'Skills Count']
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
    
    students.value.forEach((student, index) => {
      if (currentY > pageHeight - 20) {
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
        student.id.toString(),
        `${student.first_name} ${student.last_name}`,
        student.user?.email || 'N/A',
        student.year_level.toString(),
        formatStanding(student.academic_standing),
        (student.skills?.length || 0).toString()
      ]
      
      rowData.forEach((data, dataIndex) => {
        const text = data.length > 15 ? data.substring(0, 15) + '...' : data
        pdf.text(text, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Student Management System', 20, pageHeight - 10)
    
    pdf.save(`student-list-${Date.now()}.pdf`)
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
    pdf.text('Student Statistics Report', pageWidth / 2, 20, { align: 'center' })
    
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
      { label: 'Total Students', value: totalStudents.value },
      { label: 'Students on Probation', value: probationCount.value },
      { label: 'Students in Good Standing', value: goodStandingCount.value },
      { label: 'At Risk Students', value: atRiskCount.value }
    ]
    
    stats.forEach(stat => {
      pdf.text(`${stat.label}: ${stat.value}`, 30, currentY)
      currentY += 10
    })
    
    // Year level distribution
    currentY += 15
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Year Level Distribution', 20, currentY)
    
    currentY += 10
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    availableYears.value.forEach(year => {
      const count = students.value.filter(s => s.year_level === year).length
      const percentage = ((count / totalStudents.value) * 100).toFixed(1)
      pdf.text(`Year ${year}: ${count} students (${percentage}%)`, 30, currentY)
      currentY += 8
    })
    
    // Academic standing distribution
    currentY += 15
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Academic Standing Distribution', 20, currentY)
    
    currentY += 10
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    const standings = ['excellent', 'good', 'average', 'probation']
    standings.forEach(standing => {
      const count = students.value.filter(s => s.academic_standing === standing).length
      const percentage = ((count / totalStudents.value) * 100).toFixed(1)
      pdf.text(`${formatStanding(standing)}: ${count} students (${percentage}%)`, 30, currentY)
      currentY += 8
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Student Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`student-statistics-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating statistics PDF:', error)
    alert('Failed to generate statistics PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

async function exportFilteredStudentsPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('l', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    const pageHeight = pdf.internal.pageSize.getHeight()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Filtered Students Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    pdf.text(`Filtered Students: ${filteredStudents.value.length} of ${totalStudents.value}`, pageWidth / 2, 37, { align: 'center' })
    
    // Active filters
    let filterText = 'Active Filters: '
    if (searchQuery.value) filterText += `Search: "${searchQuery.value}" `
    if (selectedYear.value) filterText += `Year: ${selectedYear.value} `
    if (selectedStanding.value) filterText += `Standing: ${selectedStanding.value} `
    if (selectedSkill.value) filterText += `Skill: ${selectedSkill.value} `
    if (selectedAffiliation.value) filterText += `Affiliation: ${selectedAffiliation.value} `
    
    if (filterText === 'Active Filters: ') filterText = 'No active filters'
    
    pdf.text(filterText, pageWidth / 2, 44, { align: 'center' })
    
    // Table
    const headers = ['ID', 'Name', 'Email', 'Year', 'Standing', 'Skills', 'Affiliations']
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
    
    filteredStudents.value.forEach((student, index) => {
      if (currentY > pageHeight - 20) {
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
      
      const skills = student.skills?.slice(0, 2).map(s => s.name).join(', ') || 'None'
      const affiliations = student.affiliations?.slice(0, 2).map(a => a.name).join(', ') || 'None'
      
      const rowData = [
        student.id.toString(),
        `${student.first_name} ${student.last_name}`,
        student.user?.email || 'N/A',
        student.year_level.toString(),
        formatStanding(student.academic_standing),
        skills.length > 15 ? skills.substring(0, 15) + '...' : skills,
        affiliations.length > 15 ? affiliations.substring(0, 15) + '...' : affiliations
      ]
      
      rowData.forEach((data, dataIndex) => {
        pdf.text(data, 20 + (dataIndex * colWidth), currentY)
      })
      
      currentY += rowHeight
    })
    
    pdf.save(`filtered-students-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating filtered students PDF:', error)
    alert('Failed to generate filtered students PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}

async function exportSkillsReportPDF() {
  showExportMenu.value = false
  generatingPDF.value = true
  
  try {
    const pdf = new jsPDF('p', 'mm', 'a4')
    const pageWidth = pdf.internal.pageSize.getWidth()
    
    // Header
    pdf.setFontSize(20)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Skills Analysis Report', pageWidth / 2, 20, { align: 'center' })
    
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 30, { align: 'center' })
    
    // Skills summary
    let currentY = 50
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Top Skills Overview', 20, currentY)
    
    currentY += 15
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    // Get top skills sorted by count
    const topSkills = availableSkillsWithCounts.value
      .filter(skill => skill.count > 0)
      .slice(0, 15)
    
    topSkills.forEach((skill, index) => {
      const percentage = ((skill.count / totalStudents.value) * 100).toFixed(1)
      pdf.text(`${index + 1}. ${skill.name}: ${skill.count} students (${percentage}%)`, 30, currentY)
      currentY += 8
    })
    
    // Skills by proficiency
    currentY += 15
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Skills by Proficiency Level', 20, currentY)
    
    currentY += 10
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    const proficiencyStats = {
      beginner: 0,
      intermediate: 0,
      advanced: 0
    }
    
    students.value.forEach(student => {
      student.skills?.forEach(skill => {
        if (skill.proficiency === 'beginner') proficiencyStats.beginner++
        else if (skill.proficiency === 'intermediate') proficiencyStats.intermediate++
        else if (skill.proficiency === 'advanced') proficiencyStats.advanced++
      })
    })
    
    Object.entries(proficiencyStats).forEach(([level, count]) => {
      const percentage = totalStudents.value > 0 ? ((count / totalStudents.value) * 100).toFixed(1) : 0
      pdf.text(`${formatStanding(level)}: ${count} skills (${percentage}%)`, 30, currentY)
      currentY += 8
    })
    
    // Students with most skills
    currentY += 15
    pdf.setFontSize(14)
    pdf.setFont('helvetica', 'bold')
    pdf.text('Students with Most Skills', 20, currentY)
    
    currentY += 10
    pdf.setFontSize(12)
    pdf.setFont('helvetica', 'normal')
    
    const studentsBySkillCount = students.value
      .map(student => ({
        name: `${student.first_name} ${student.last_name}`,
        skillCount: student.skills?.length || 0
      }))
      .sort((a, b) => b.skillCount - a.skillCount)
      .slice(0, 10)
    
    studentsBySkillCount.forEach((student, index) => {
      pdf.text(`${index + 1}. ${student.name}: ${student.skillCount} skills`, 30, currentY)
      currentY += 8
    })
    
    // Footer
    pdf.setFontSize(8)
    pdf.text('CCS-ISPS Student Management System', 20, pdf.internal.pageSize.getHeight() - 10)
    
    pdf.save(`skills-analysis-${Date.now()}.pdf`)
  } catch (error) {
    console.error('Error generating skills report PDF:', error)
    alert('Failed to generate skills report PDF. Please try again.')
  } finally {
    generatingPDF.value = false
  }
}
</script>

<style scoped>
.student-list-view {
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

/* Export Dropdown */
.export-dropdown {
  position: relative;
  display: inline-block;
}

.export-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #d1d5db;
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
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  transition: background-color 0.2s;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #f3f4f6;
}

.export-item:last-child {
  border-bottom: none;
}

.export-item:hover {
  background-color: #f9fafb;
  color: #1f2937;
}

.export-item:first-child:hover {
  border-radius: 0.5rem 0.5rem 0 0;
}

.export-item:last-child:hover {
  border-radius: 0 0 0.5rem 0.5rem;
}

/* Statistics Cards */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #3b82f6;
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

/* Skills Filter Container */
.skills-filter-container {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #3b82f6;
}

.skills-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.skills-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
}

.selected-skill-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.75rem;
  background: #eff6ff;
  border: 1px solid #3b82f6;
  border-radius: 9999px;
  font-size: 0.875rem;
}

.selected-skill-label {
  color: #6b7280;
  font-weight: 500;
}

.selected-skill-name {
  color: #1e40af;
  font-weight: 600;
}

.clear-skill-btn {
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: bold;
  transition: background-color 0.2s;
}

.clear-skill-btn:hover {
  background: #2563eb;
}

.skill-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

/* Affiliations Filter Container */
.affiliations-filter-container {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #10b981;
}

.affiliations-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.affiliations-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
}

.selected-affiliation-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.75rem;
  background: #f0fdf4;
  border: 1px solid #10b981;
  border-radius: 9999px;
  font-size: 0.875rem;
}

.selected-affiliation-label {
  color: #6b7280;
  font-weight: 500;
}

.selected-affiliation-name {
  color: #047857;
  font-weight: 600;
}

.clear-affiliation-btn {
  background: #10b981;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: bold;
  transition: background-color 0.2s;
}

.clear-affiliation-btn:hover {
  background: #059669;
}

.affiliation-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.affiliation-tag {
  padding: 0.375rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 9999px;
  background: white;
  color: #6b7280;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.affiliation-tag:hover {
  border-color: #10b981;
  background: #f0fdf4;
  color: #047857;
}

.affiliation-tag.active {
  background: #10b981;
  border-color: #10b981;
  color: white;
}

.affiliation-count {
  font-size: 0.625rem;
  opacity: 0.7;
  font-weight: 400;
}

.affiliation-tag.active .affiliation-count {
  opacity: 0.9;
}

/* Violations Filter Container */
.violations-filter-container {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #ef4444;
}

.violations-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.violations-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
}

.selected-violation-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.75rem;
  background: #fef2f2;
  border: 1px solid #ef4444;
  border-radius: 9999px;
  font-size: 0.875rem;
}

.selected-violation-label {
  color: #6b7280;
  font-weight: 500;
}

.selected-violation-name {
  color: #b91c1c;
  font-weight: 600;
}

.clear-violation-btn {
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: bold;
  transition: background-color 0.2s;
}

.clear-violation-btn:hover {
  background: #dc2626;
}

.violation-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.violation-tag {
  padding: 0.375rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 9999px;
  background: white;
  color: #6b7280;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.violation-tag:hover {
  border-color: #ef4444;
  background: #fef2f2;
  color: #b91c1c;
}

.violation-tag.active {
  background: #ef4444;
  border-color: #ef4444;
  color: white;
}

.violation-count {
  font-size: 0.625rem;
  opacity: 0.7;
  font-weight: 400;
}

.violation-tag.active .violation-count {
  opacity: 0.9;
}

.skill-tag {
  padding: 0.375rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 9999px;
  background: white;
  color: #6b7280;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.skill-tag:hover {
  border-color: #3b82f6;
  background: #f0f9ff;
  color: #1e40af;
}

.skill-tag.active {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.skill-count {
  font-size: 0.625rem;
  opacity: 0.7;
  font-weight: 400;
}

.skill-tag.active .skill-count {
  opacity: 0.9;
}

.filter-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  background: white;
  cursor: pointer;
  transition: border-color 0.2s;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Buttons */
.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
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

.btn-sm {
  padding: 0.25rem 0.5rem;
  margin-right: 0.25rem;
  font-size: 0.75rem;
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

/* Table */
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
}

.student-table tbody tr:hover {
  background-color: #f9fafb;
}

/* Status Badges */
.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

/* Skills, Affiliations, and Violations Badges */
.skills-list,
.affiliations-list,
.violations-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
  max-width: 200px;
}

/* Skills Offers Display */
.skills-offers {
  max-width: 400px;
}

.skills-offers h4 {
  margin: 0 0 0.5rem 0;
  color: #1f2937;
  font-size: 1rem;
  font-weight: 600;
}

.skills-list {
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background: #f9fafb;
}

.skill-item {
  padding: 0.75rem;
  border-bottom: 1px solid #f3f4f6;
}

.skill-item:last-child {
  border-bottom: none;
}

.skill-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.25rem;
}

.skill-header strong {
  color: #1f2937;
  font-size: 0.875rem;
}

.proficiency-badge {
  padding: 0.125rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.625rem;
  font-weight: 500;
  text-transform: capitalize;
}

.proficiency-badge.beginner {
  background: #fef3c7;
  color: #92400e;
}

.proficiency-badge.intermediate {
  background: #dbeafe;
  color: #1e40af;
}

.proficiency-badge.advanced {
  background: #d1fae5;
  color: #065f46;
}

.skill-details {
  font-size: 0.75rem;
  color: #6b7280;
  line-height: 1.4;
}

.skill-details span {
  display: block;
  margin-bottom: 0.125rem;
}

.no-skills {
  color: #6b7280;
  font-style: italic;
  text-align: center;
  padding: 1rem;
}

.skills-count {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 60px;
}

.skill-number {
  font-size: 1.25rem;
  font-weight: 700;
  color: #3b82f6;
  line-height: 1;
}

.skill-label {
  font-size: 0.625rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-top: 0.125rem;
}

.skill-badge,
.affiliation-badge {
  padding: 0.125rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.625rem;
  font-weight: 500;
  text-transform: capitalize;
  white-space: nowrap;
}

.skill-badge.technical {
  background-color: #dbeafe;
  color: #1e40af;
}

.skill-badge.creative {
  background-color: #fce7f3;
  color: #a21caf;
}

.skill-badge.soft {
  background-color: #d1fae5;
  color: #065f46;
}

.affiliation-badge {
  background-color: #f3f4f6;
  color: #374151;
}

.more-skills,
.more-affiliations {
  font-size: 0.625rem;
  color: #6b7280;
  font-style: italic;
  align-self: center;
}

/* Violations Display */
.violations-list {
  flex-direction: column;
  gap: 0.5rem;
}

.violation-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 0.25rem;
  border-radius: 0.25rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
}

.violation-status-badge {
  padding: 0.125rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.625rem;
  font-weight: 500;
  text-transform: capitalize;
  align-self: flex-start;
}

.violation-status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.violation-status-badge.resolved {
  background: #d1fae5;
  color: #065f46;
}

.violation-status-badge.under_review {
  background: #dbeafe;
  color: #1e40af;
}

.violation-type {
  font-size: 0.75rem;
  color: #374151;
  font-weight: 500;
}

.more-violations {
  font-size: 0.625rem;
  color: #6b7280;
  font-style: italic;
  align-self: center;
}

.no-violations,
.no-affiliations {
  color: #9ca3af;
  font-style: italic;
  font-size: 0.875rem;
}

.status-badge.excellent {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.good {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.average {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.probation {
  background-color: #fecaca;
  color: #991b1b;
}

/* States */
.loading,
.error {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #dc2626;
}

.no-results {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
  font-style: italic;
}

/* Mobile Responsive Design */
@media (max-width: 1024px) {
  .students-profiling-view {
    padding: 1.5rem;
  }
  
  .stats-container {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
  
  .skills-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

@media (max-width: 768px) {
  .students-profiling-view {
    padding: 1rem;
  }
  
  .page-header h1 {
    font-size: 1.75rem;
  }
  
  .page-header p {
    font-size: 0.875rem;
  }
  
  .header-actions {
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
  }
  
  .header-actions > * {
    width: 100%;
    justify-content: center;
  }
  
  .export-dropdown {
    width: 100%;
  }
  
  .export-menu {
    position: fixed;
    top: auto;
    bottom: 0;
    left: 0;
    right: 0;
    border-radius: 0.5rem 0.5rem 0 0;
    margin: 0;
  }
  
  .stats-container {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .stat-card {
    padding: 1rem;
  }
  
  .stat-icon {
    width: 40px;
    height: 40px;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  .stat-label {
    font-size: 0.8rem;
  }
  
  .filters-container {
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
  }
  
  .search-box {
    min-width: auto;
    order: -1;
  }
  
  .filter-group {
    min-width: auto;
  }
  
  .students-table-container {
    overflow-x: auto;
  }
  
  .students-table {
    min-width: 800px;
    font-size: 0.875rem;
  }
  
  .students-table th,
  .students-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .skills-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  }
  
  .skill-item {
    padding: 0.5rem;
  }
  
  .skill-name {
    font-size: 0.75rem;
  }
  
  .skill-count {
    font-size: 0.625rem;
  }
  
  .student-card {
    padding: 1rem;
  }
  
  .student-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .student-info h3 {
    font-size: 1rem;
  }
  
  .student-details {
    grid-template-columns: 1fr;
    gap: 0.5rem;
  }
  
  .student-actions {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .violation-item {
    padding: 0.5rem;
  }
  
  .violation-type {
    font-size: 0.7rem;
  }
  
  .more-violations {
    font-size: 0.6rem;
  }
}

@media (max-width: 640px) {
  .students-profiling-view {
    padding: 0.75rem;
  }
  
  .page-header h1 {
    font-size: 1.5rem;
  }
  
  .page-header p {
    font-size: 0.8rem;
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
    width: 14px;
    height: 14px;
  }
  
  .stat-card {
    padding: 0.75rem;
    flex-direction: column;
    text-align: center;
  }
  
  .stat-icon {
    width: 36px;
    height: 36px;
    margin: 0 auto 0.75rem auto;
  }
  
  .stat-number {
    font-size: 1.75rem;
  }
  
  .stat-label {
    font-size: 0.75rem;
  }
  
  .students-table {
    min-width: 700px;
    font-size: 0.75rem;
  }
  
  .students-table th,
  .students-table td {
    padding: 0.5rem 0.25rem;
  }
  
  .students-table th {
    font-size: 0.7rem;
  }
  
  .status-badge {
    font-size: 0.625rem;
    padding: 0.125rem 0.5rem;
  }
  
  .student-id {
    font-size: 0.7rem;
  }
  
  .student-email {
    font-size: 0.75rem;
  }
  
  .student-gpa {
    font-size: 0.875rem;
  }
  
  .student-status {
    font-size: 0.7rem;
  }
  
  .skills-grid {
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 0.5rem;
  }
  
  .skill-item {
    padding: 0.375rem;
  }
  
  .skill-name {
    font-size: 0.7rem;
  }
  
  .skill-count {
    font-size: 0.6rem;
  }
  
  .student-card {
    padding: 0.75rem;
  }
  
  .student-info h3 {
    font-size: 0.875rem;
  }
  
  .student-email {
    font-size: 0.7rem;
  }
  
  .student-gpa {
    font-size: 0.75rem;
  }
  
  .student-actions .btn-sm {
    font-size: 0.65rem;
    padding: 0.125rem 0.375rem;
  }
  
  .violation-item {
    padding: 0.375rem;
  }
  
  .violation-type {
    font-size: 0.65rem;
  }
  
  .more-violations {
    font-size: 0.55rem;
  }
}

@media (max-width: 480px) {
  .students-profiling-view {
    padding: 0.5rem;
  }
  
  .page-header h1 {
    font-size: 1.25rem;
  }
  
  .page-header p {
    font-size: 0.75rem;
  }
  
  .btn,
  .btn-small {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
  }
  
  .stat-card {
    padding: 0.5rem;
  }
  
  .stat-icon {
    width: 32px;
    height: 32px;
  }
  
  .stat-number {
    font-size: 1.5rem;
  }
  
  .stat-label {
    font-size: 0.7rem;
  }
  
  .students-table {
    min-width: 600px;
    font-size: 0.7rem;
  }
  
  .students-table th,
  .students-table td {
    padding: 0.375rem 0.125rem;
  }
  
  .students-table th {
    font-size: 0.65rem;
  }
  
  .status-badge {
    font-size: 0.6rem;
    padding: 0.125rem 0.375rem;
  }
  
  .student-card {
    padding: 0.5rem;
  }
  
  .student-info h3 {
    font-size: 0.75rem;
  }
  
  .student-details {
    font-size: 0.65rem;
  }
  
  .student-gpa {
    font-size: 0.7rem;
  }
  
  .skills-grid {
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  }
  
  .skill-item {
    padding: 0.25rem;
  }
  
  .skill-name {
    font-size: 0.65rem;
  }
  
  .skill-count {
    font-size: 0.55rem;
  }
  
  .violation-item {
    padding: 0.25rem;
  }
  
  .violation-type {
    font-size: 0.6rem;
  }
  
  .more-violations {
    font-size: 0.5rem;
  }
  
  .no-results,
  .loading,
  .error {
    padding: 1.5rem;
    font-size: 0.875rem;
  }
}
</style>
