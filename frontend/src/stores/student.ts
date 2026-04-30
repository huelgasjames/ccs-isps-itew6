import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { 
  Student, 
  StudentFilter, 
  StudentListResponse, 
  CreateStudentRequest, 
  UpdateStudentRequest,
  BackendCreateStudentRequest,
  DisplayMode,
  StudentStatistics
} from '@/types/student'
import api from '@/services/api'

export const useStudentStore = defineStore('student', () => {
  // State
  const students = ref<Student[]>([])
  const currentStudent = ref<Student | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const filter = ref<StudentFilter>({})
  const displayMode = ref<DisplayMode>('table')
  const currentPage = ref(1)
  const pageSize = ref(10)
  const totalStudents = ref(0)
  const totalPages = ref(0)
  const statistics = ref<StudentStatistics | null>(null)

  // Computed
  const filteredStudents = computed(() => {
    let filtered = [...students.value]

    // Text search
    if (filter.value.search) {
      const search = filter.value.search.toLowerCase()
      filtered = filtered.filter(student => 
        student.personalInfo.firstName.toLowerCase().includes(search) ||
        student.personalInfo.lastName.toLowerCase().includes(search) ||
        student.personalInfo.studentId.toLowerCase().includes(search) ||
        student.personalInfo.email.toLowerCase().includes(search)
      )
    }

    // Skills filter
    if (filter.value.skills && filter.value.skills.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.skills!.some(skillName =>
          student.skills.some(skill =>
            skill.name.toLowerCase().includes(skillName.toLowerCase())
          )
        )
      )
    }

    // Activities filter
    if (filter.value.activities && filter.value.activities.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.activities!.some(activityName =>
          student.activities.some(activity =>
            activity.name.toLowerCase().includes(activityName.toLowerCase())
          )
        )
      )
    }

    // Affiliations filter
    if (filter.value.affiliations && filter.value.affiliations.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.affiliations!.some(affiliationName =>
          student.affiliations.some(affiliation =>
            affiliation.name.toLowerCase().includes(affiliationName.toLowerCase())
          )
        )
      )
    }

    // Academic standing filter
    if (filter.value.academicStanding && filter.value.academicStanding.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.academicStanding!.includes(student.academicStanding.standing)
      )
    }

    // Year level filter
    if (filter.value.yearLevel && filter.value.yearLevel.length > 0) {
      filtered = filtered.filter(student =>
        filter.value.yearLevel!.includes(student.academicStanding.currentYear)
      )
    }

    // GPA range filter
    if (filter.value.gpaRange) {
      filtered = filtered.filter(student =>
        student.academicStanding.currentGPA >= filter.value.gpaRange!.min &&
        student.academicStanding.currentGPA <= filter.value.gpaRange!.max
      )
    }

    // Age range filter
    if (filter.value.ageRange) {
      filtered = filtered.filter(student =>
        student.personalInfo.age >= filter.value.ageRange!.min &&
        student.personalInfo.age <= filter.value.ageRange!.max
      )
    }

    // Violation status filter
    if (filter.value.violationStatus && filter.value.violationStatus.length > 0) {
      filtered = filtered.filter(student =>
        student.violations.some(violation =>
          filter.value.violationStatus!.includes(violation.status)
        )
      )
    }

    return filtered
  })

  const paginatedStudents = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value
    const end = start + pageSize.value
    return filteredStudents.value.slice(start, end)
  })

  // Actions
  const fetchStudents = async (page = 1, limit = 10) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get<StudentListResponse>('/students', {
        params: { page, limit, include: 'skills,affiliations' }
      })
      
      // Handle different response formats
      if (Array.isArray(response.data)) {
        // Direct array response
        students.value = response.data
        totalStudents.value = response.data.length
        totalPages.value = 1
        currentPage.value = 1
      } else if (response.data.students) {
        // Paginated response
        students.value = response.data.students
        totalStudents.value = response.data.total || response.data.students.length
        totalPages.value = response.data.totalPages || 1
        currentPage.value = response.data.page || 1
      } else {
        // Fallback: treat response.data as students array
        students.value = Array.isArray(response.data) ? response.data : []
        totalStudents.value = students.value.length
        totalPages.value = 1
        currentPage.value = 1
      }
    } catch (err: any) {
      console.error('Error fetching students:', err)
      error.value = 'Failed to fetch students from API. Using sample data for demonstration.'
      
      // Generate sample data as fallback
      generateSampleDataForStore()
    } finally {
      loading.value = false
    }
  }

  const generateSampleDataForStore = () => {
  const firstNames = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Jennifer', 'Michael', 'Amanda', 'William', 'Jessica', 'Daniel', 'Ashley', 'Christopher', 'Sophia', 'Matthew', 'Olivia', 'Andrew', 'Emma', 'Joshua', 'Isabella', 'Ryan', 'Mia', 'Kevin', 'Charlotte', 'Tyler', 'Amelia', 'Jason', 'Harper', 'Ethan', 'Evelyn', 'Brandon', 'Abigail', 'Nathan', 'Emily', 'Alexander', 'Madison', 'Jacob', 'Sofia', 'Logan', 'Avery', 'Ethan', 'Aiden', 'Caleb', 'Jackson', 'Mason', 'Liam', 'Noah', 'Lucas', 'Henry', 'Alexander', 'Sebastian', 'Ezra', 'Jack', 'Owen', 'Daniel', 'Matthew', 'Joseph', 'David', 'Samuel', 'Carter', 'Wyatt', 'Jayden', 'John', 'Dylan', 'Luke', 'Gabriel', 'Anthony', 'Isaac', 'Lincoln', 'Christopher', 'Joshua', 'Andrew', 'Mateo', 'Ryan', 'Nathan', 'Aaron', 'Isaiah', 'Thomas', 'Charles', 'Caleb', 'Josiah', 'Christian', 'Jonathan', 'Landon', 'Evan', 'Gavin', 'Connor', 'Adrian', 'Asher', 'Jeremiah', 'Hudson', 'Robert', 'Nicholas', 'Brayden', 'Grayson', 'Eli', 'Ezekiel', 'Dominic', 'Oliver', 'Xavier', 'Jaxon', 'Maverick', 'Kai', 'Santiago', 'Leo', 'Aarav', 'Roman', 'Adam', 'Ronan', 'Emmett', 'Remington', 'Milo', 'Archer', 'Rowan', 'Karter', 'Wesley', 'Jaxson', 'Josiah', 'Elliot', 'Parker', 'Colton', 'Luca', 'Atlas', 'Jasper', 'Declan', 'Kayden', 'Maxwell', 'Ryker', 'Enzo', 'Kingston', 'Bennett', 'Carson', 'Raymond', 'Zion', 'Arlo', 'Theodore', 'Jude', 'Nolan', 'Antonio', 'Myles', 'Elliott', 'Gideon', 'Knox', 'Damon', 'Ace', 'Barrett', 'Amiri', 'Max', 'Javier', 'Silas', 'Cody', 'Beau', 'Amir', 'Adriel', 'Rory', 'Bodhi', 'Emiliano', 'Braxton', 'Khalil', 'Malachi', 'Makai', 'Ronin', 'Finn', 'Zayn', 'Kade', 'Rex', 'Cruz', 'Stellan', 'Joaquin', 'Koa', 'Lorenzo', 'Orion', 'Cassius', 'Armani', 'Frankie', 'Ermias', 'Kairo', 'Legend', 'Raphael', 'Zayne', 'Jesse', 'Sullivan', 'Cameron', 'Graham', 'Felix', 'August', 'River', 'Brooks', 'Bryce', 'Judah', 'Kellan', 'Abel', 'Colby', 'Hayes', 'Salvador', 'Kaden', 'Kamari', 'Solomon', 'Rhys', 'Jerry', 'Ricky', 'Tommy', 'Andre', 'Miguel', 'Hector', 'Sergio', 'Luis', 'Carlos', 'Juan', 'Jorge', 'Martin', 'Adrian', 'Diego', 'Ricardo', 'Antonio', 'Alejandro', 'Manuel', 'Pablo', 'Javier', 'Roberto', 'Pedro', 'Raul', 'Francisco', 'Angel', 'Gabriel', 'Miguel', 'Luis', 'Carlos', 'Jose', 'David', 'Daniel', 'Mario', 'Arturo', 'Rafael', 'Eduardo', 'Victor', 'Alberto', 'Oscar', 'Santiago', 'Andres', 'Marco', 'Fernando', 'Benjamin', 'Samuel', 'Isaac', 'Nathan', 'Caleb', 'Aaron', 'Lucas', 'Henry', 'Owen', 'Julian', 'Levi', 'Christian', 'Eli', 'Aaron', 'Evan', 'Parker', 'Adam', 'Ian', 'Connor', 'Leo', 'Xavier', 'Ryan', 'Colton', 'Angel', 'Adrian', 'Jonathan', 'Carson', 'Hunter', 'Brandon', 'Austin', 'Gavin', 'Nolan', 'Tyler', 'Caleb', 'Lucas', 'Henry', 'Owen', 'Julian', 'Levi', 'Christian', 'Eli', 'Aaron', 'Evan', 'Parker', 'Adam', 'Ian', 'Connor', 'Leo', 'Xavier', 'Ryan', 'Colton', 'Angel', 'Adrian', 'Jonathan', 'Carson', 'Hunter', 'Brandon', 'Austin', 'Gavin', 'Nolan', 'Tyler', 'Caleb']
  const lastNames = ['Doe', 'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young', 'Allen', 'King', 'Wright', 'Lopez', 'Hill', 'Scott', 'Green', 'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter', 'Mitchell', 'Perez', 'Roberts', 'Turner', 'Phillips', 'Campbell', 'Parker', 'Evans', 'Edwards', 'Collins', 'Stewart', 'Sanchez', 'Morris', 'Rogers', 'Reed', 'Cook', 'Morgan', 'Bell', 'Murphy', 'Bailey', 'Rivera', 'Cooper', 'Richardson', 'Cox', 'Howard', 'Ward', 'Torres', 'Peterson', 'Gray', 'Ramirez', 'James', 'Watson', 'Brooks', 'Kelly', 'Sanders', 'Price', 'Bennett', 'Wood', 'Barnes', 'Ross', 'Henderson', 'Coleman', 'Jenkins', 'Perry', 'Powell', 'Long', 'Patterson', 'Hughes', 'Flores', 'Washington', 'Butler', 'Simmons', 'Foster', 'Gonzales', 'Bryant', 'Alexander', 'Russell', 'Griffin', 'Diaz', 'Hayes', 'Myers', 'Ford', 'Hamilton', 'Graham', 'Sullivan', 'Wallace', 'Woods', 'Cole', 'West', 'Jordan', 'Owens', 'Reynolds', 'Fisher', 'Ellis', 'Harrison', 'Gibson', 'Mcdonald', 'Cruz', 'Marshall', 'Ortiz', 'Gomez', 'Murray', 'Freeman', 'Wells', 'Webb', 'Simpson', 'Stevens', 'Tucker', 'Porter', 'Hunter', 'Hicks', 'Crawford', 'Henry', 'Boyd', 'Mason', 'Morales', 'Kennedy', 'Warren', 'Dixon', 'Ramos', 'Reyes', 'Burns', 'Gordon', 'Shaw', 'Holmes', 'Rice', 'Robertson', 'Hunt', 'Black', 'Daniels', 'Palmer', 'Mills', 'Nichols', 'Grant', 'Knight', 'Ferguson', 'Rose', 'Stone', 'Hawkins', 'Dunn', 'Perkins', 'Hudson', 'Spencer', 'Gardner', 'Stephens', 'Payne', 'Pierce', 'Berry', 'Matthews', 'Arnold', 'Wagner', 'Willis', 'Ray', 'Watkins', 'Olson', 'Carroll', 'Duncan', 'Snyder', 'Hart', 'Cunningham', 'Bradley', 'Lane', 'Andrews', 'Ruiz', 'Harper', 'Fox', 'Riley', 'Armstrong', 'Carpenter', 'Weaver', 'Greene', 'Lawrence', 'Elliott', 'Chavez', 'Sims', 'Austin', 'Peters', 'Kelley', 'Franklin', 'Lawson', 'Fields', 'Gutierrez', 'Ryan', 'Schmidt', 'Carr', 'Vasquez', 'Castillo', 'Wheeler', 'Chapman', 'Oliver', 'Montgomery', 'Richards', 'Williamson', 'Johnston', 'Banks', 'Meyer', 'Bauer', 'Fletcher', 'Giles', 'Floyd', 'Hogan', 'Luna', 'Phelps', 'McGuire', 'Allison', 'Bridges', 'Wilkerson', 'Stanley', 'Nguyen', 'George', 'Jacobs', 'Reid', 'Kim', 'Fuller', 'Lynch', 'Dean', 'Gilbert', 'Garza', 'Erickson', 'Vargas', 'Combs', 'Kramer', 'Molina', 'Huffman', 'Kelley', 'Dixon', 'Owens', 'Huffman', 'Kelley', 'Dixon', 'Owens', 'Huffman', 'Kelley', 'Dixon', 'Owens']
  
  const sampleStudents: Student[] = []
  
  for (let i = 0; i < 1000; i++) {
    const year = Math.floor(Math.random() * 4) + 1
    const standings = ['excellent', 'good', 'average', 'probation']
    const standing = standings[Math.floor(Math.random() * standings.length)]
    const gpa = Number((Math.random() * 2 + 2).toFixed(2)) // GPA between 2.0 and 4.0
    
    // Generate skills
    const skillCount = Math.floor(Math.random() * 4) + 1
    const studentSkills: any[] = []
    const availableSkills = ['JavaScript', 'Python', 'Java', 'React', 'Node.js', 'TypeScript', 'HTML/CSS', 'SQL', 'MongoDB', 'Docker', 'Git', 'AWS', 'Machine Learning', 'Leadership', 'Communication']
    
    for (let j = 0; j < skillCount; j++) {
      const skillName = availableSkills[Math.floor(Math.random() * availableSkills.length)]
      const categories = ['technical', 'soft', 'language', 'creative']
      const proficiencies = ['beginner', 'intermediate', 'advanced', 'expert']
      
      studentSkills.push({
        id: (i * 100) + j,
        name: skillName,
        category: categories[Math.floor(Math.random() * categories.length)] as any,
        proficiency: proficiencies[Math.floor(Math.random() * proficiencies.length)] as any,
        yearsExperience: Math.floor(Math.random() * 5) + 1,
        certifications: Math.random() > 0.5 ? [`${skillName} Certification`] : [],
        lastUsed: new Date(2024 - Math.floor(Math.random() * 365), Math.floor(Math.random() * 12) + 1, Math.floor(Math.random() * 28)).toISOString().split('T')[0]
      })
    }
    
    // Generate affiliations
    const affiliationCount = Math.floor(Math.random() * 3)
    const studentAffiliations: any[] = []
    const affiliationNames = ['Computer Science Society', 'Student Council', 'Coding Club', 'Debate Team', 'Sports Club']
    const affiliationTypes = ['student_organization', 'professional', 'academic', 'sports']
    
    for (let k = 0; k < affiliationCount; k++) {
      studentAffiliations.push({
        id: (i * 100) + k,
        name: affiliationNames[Math.floor(Math.random() * affiliationNames.length)],
        type: affiliationTypes[Math.floor(Math.random() * affiliationTypes.length)] as any,
        role: 'Member',
        startDate: new Date(2023, Math.floor(Math.random() * 12), 1).toISOString().split('T')[0]
      })
    }
    
    // Generate violations
    const violationCount = Math.floor(Math.random() * 3)
    const studentViolations: any[] = []
    const violationTypes = ['Academic', 'Behavioral', 'Attendance']
    const violationStatuses = ['pending', 'resolved', 'under_review']
    const severities = ['minor', 'major', 'critical']
    
    for (let l = 0; l < violationCount; l++) {
      studentViolations.push({
        id: (i * 100) + l,
        type: violationTypes[Math.floor(Math.random() * violationTypes.length)],
        severity: severities[Math.floor(Math.random() * severities.length)] as any,
        status: violationStatuses[Math.floor(Math.random() * violationStatuses.length)] as any,
        description: 'Sample violation description',
        date: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString().split('T')[0],
        points: Math.floor(Math.random() * 10) + 1,
        reportedBy: 'System'
      })
    }
    
    sampleStudents.push({
      id: i + 1,
      personalInfo: {
        firstName: firstNames[i] || 'Student',
        lastName: lastNames[i] || `Last${i}`,
        email: `${firstNames[i] || 'student'}.${lastNames[i] || 'name'}@university.edu`,
        phone: `555-${Math.floor(Math.random() * 900) + 100}-${Math.floor(Math.random() * 9000) + 1000}`,
        dateOfBirth: '2000-01-01',
        age: 20,
        gender: 'other',
        address: '123 University St',
        city: 'University City',
        province: 'State',
        postalCode: '12345',
        studentId: `2024${String(i + 1).padStart(4, '0')}`,
        emergencyContact: {
          name: 'Parent Name',
          relationship: 'Parent',
          phone: '555-123-4567'
        }
      },
      academicStanding: {
        currentYear: year,
        currentSemester: 'first' as const,
        currentGPA: gpa,
        totalUnits: 120,
        standing: standing === 'excellent' || standing === 'good' ? 'good' : standing === 'average' ? 'warning' : 'probation',
        advisor: 'Dr. Smith'
      },
      skills: studentSkills,
      activities: [],
      academicHistory: [{
        id: i + 1,
        schoolName: 'University',
        degree: 'Bachelor of Science',
        major: 'Computer Science',
        startDate: '2020-09-01',
        endDate: '2024-05-01',
        gpa: gpa,
        status: 'completed' as const
      }],
      affiliations: studentAffiliations,
      violations: studentViolations,
      createdAt: new Date(2024 - Math.floor(Math.random() * 365), Math.floor(Math.random() * 12) + 1, Math.floor(Math.random() * 28) + 1).toISOString(),
      updatedAt: new Date().toISOString(),
      isActive: true
    })
  }
  
  students.value = sampleStudents
  totalStudents.value = sampleStudents.length
  totalPages.value = 1
  currentPage.value = 1
  console.log('Generated sample data:', sampleStudents.length, 'students')
}

const fetchStudentById = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get<Student>(`/students/${id}`)
      currentStudent.value = response.data
      return response.data
    } catch (err) {
      error.value = 'Failed to fetch student'
      console.error('Error fetching student:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const createStudent = async (studentData: CreateStudentRequest | BackendCreateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post<Student>('/students', studentData)
      students.value.unshift(response.data)
      totalStudents.value += 1
      return response.data
    } catch (err) {
      error.value = 'Failed to create student'
      console.error('Error creating student:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStudent = async (id: number, updates: UpdateStudentRequest) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.put<Student>(`/students/${id}`, updates)
      
      // Update in array
      const index = students.value.findIndex(s => s.id === id)
      if (index !== -1) {
        students.value[index] = response.data
      }
      
      // Update current student if it's the same
      if (currentStudent.value?.id === id) {
        currentStudent.value = response.data
      }
      
      return response.data
    } catch (err) {
      error.value = 'Failed to update student'
      console.error('Error updating student:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteStudent = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await api.delete(`/students/${id}`)
      
      // Remove from array
      const index = students.value.findIndex(s => s.id === id)
      if (index !== -1) {
        students.value.splice(index, 1)
      }
      
      // Clear current student if it's the same
      if (currentStudent.value?.id === id) {
        currentStudent.value = null
      }
      
      totalStudents.value -= 1
    } catch (err) {
      error.value = 'Failed to delete student'
      console.error('Error deleting student:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const addSkill = async (studentId: number, skill: Omit<Student['skills'][0], 'id'>) => {
    try {
      const response = await api.post(`/students/${studentId}/skills`, skill)
      
      // Update local state
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills.push(response.data)
      }
      
      if (currentStudent.value?.id === studentId) {
        currentStudent.value.skills.push(response.data)
      }
      
      return response.data
    } catch (err) {
      error.value = 'Failed to add skill'
      console.error('Error adding skill:', err)
      throw err
    }
  }

  const removeSkill = async (studentId: number, skillId: number) => {
    try {
      await api.delete(`/students/${studentId}/skills/${skillId}`)
      
      // Update local state
      const student = students.value.find(s => s.id === studentId)
      if (student) {
        student.skills = student.skills.filter(s => s.id !== skillId)
      }
      
      if (currentStudent.value?.id === studentId) {
        currentStudent.value.skills = currentStudent.value.skills.filter(s => s.id !== skillId)
      }
    } catch (err) {
      error.value = 'Failed to remove skill'
      console.error('Error removing skill:', err)
      throw err
    }
  }

  const setFilter = (newFilter: StudentFilter) => {
    filter.value = { ...filter.value, ...newFilter }
  }

  const clearFilter = () => {
    filter.value = {}
  }

  // ... (rest of the code remains the same)

  return {
    // State
    students,
    currentStudent,
    loading,
    error,
    filter,
    displayMode,
    currentPage,
    pageSize,
    totalStudents,
    totalPages,
    statistics,
    
    // Computed
    filteredStudents,
    paginatedStudents,
    
    // Actions
    fetchStudents,
    fetchStudentById,
    createStudent,
    updateStudent,
    deleteStudent,
    addSkill,
    removeSkill,
    setFilter,
    clearFilter,
    generateSampleData: generateSampleDataForStore
  }
})
