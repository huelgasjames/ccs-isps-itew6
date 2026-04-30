// localStorage helper functions for offline fallback
export interface OfflineOperation {
  id: string
  type: 'CREATE' | 'UPDATE' | 'DELETE'
  endpoint: string
  data: any
  timestamp: number
  synced: boolean
}

export interface OfflineStatus {
  isOnline: boolean
  isOfflineMode: boolean
  pendingSyncCount: number
}

class OfflineService {
  private readonly STORAGE_KEYS = {
    ANNOUNCEMENTS: 'offline_announcements',
    STUDENTS: 'offline_students',
    PENDING_OPERATIONS: 'offline_pending_operations',
    STATUS: 'offline_status'
  }

  // Check online status
  isOnline(): boolean {
    return navigator.onLine
  }

  // Get offline status
  getStatus(): OfflineStatus {
    const pendingOps = this.getPendingOperations()
    return {
      isOnline: this.isOnline(),
      isOfflineMode: !this.isOnline(),
      pendingSyncCount: pendingOps.filter(op => !op.synced).length
    }
  }

  // localStorage helpers for announcements
  getLocalAnnouncements(): any[] {
    try {
      const data = localStorage.getItem(this.STORAGE_KEYS.ANNOUNCEMENTS)
      return data ? JSON.parse(data) : []
    } catch {
      return []
    }
  }

  setLocalAnnouncements(announcements: any[]): void {
    localStorage.setItem(this.STORAGE_KEYS.ANNOUNCEMENTS, JSON.stringify(announcements))
  }

  // localStorage helpers for students
  getLocalStudents(): any[] {
    try {
      const data = localStorage.getItem(this.STORAGE_KEYS.STUDENTS)
      return data ? JSON.parse(data) : []
    } catch {
      return []
    }
  }

  setLocalStudents(students: any[]): void {
    localStorage.setItem(this.STORAGE_KEYS.STUDENTS, JSON.stringify(students))
  }

  // Pending operations management
  getPendingOperations(): OfflineOperation[] {
    try {
      const data = localStorage.getItem(this.STORAGE_KEYS.PENDING_OPERATIONS)
      return data ? JSON.parse(data) : []
    } catch {
      return []
    }
  }

  addPendingOperation(operation: Omit<OfflineOperation, 'id' | 'timestamp' | 'synced'>): void {
    const operations = this.getPendingOperations()
    const newOp: OfflineOperation = {
      ...operation,
      id: `${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
      timestamp: Date.now(),
      synced: false
    }
    operations.push(newOp)
    localStorage.setItem(this.STORAGE_KEYS.PENDING_OPERATIONS, JSON.stringify(operations))
  }

  markOperationAsSynced(operationId: string): void {
    const operations = this.getPendingOperations()
    const index = operations.findIndex(op => op.id === operationId)
    if (index !== -1 && operations[index]) {
      operations[index].synced = true
      localStorage.setItem(this.STORAGE_KEYS.PENDING_OPERATIONS, JSON.stringify(operations))
    }
  }

  clearSyncedOperations(): void {
    const operations = this.getPendingOperations()
    const unsynced = operations.filter(op => !op.synced)
    localStorage.setItem(this.STORAGE_KEYS.PENDING_OPERATIONS, JSON.stringify(unsynced))
  }

  // Dummy data for offline mode
  getDummyAnnouncements(): any[] {
    return [
      {
        id: 1,
        title: 'System Maintenance Notice',
        content: 'The system will undergo maintenance on Sunday from 2 AM to 6 AM.',
        type: 'System',
        priority: 'high',
        status: 'published',
        user_id: 1,
        user: { id: 1, name: 'Admin User', email: 'admin@example.com', role: 'admin' },
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      },
      {
        id: 2,
        title: 'New Course Registration',
        content: 'Registration for Fall 2026 courses is now open.',
        type: 'Academic',
        priority: 'medium',
        status: 'published',
        user_id: 2,
        user: { id: 2, name: 'Prof. Smith', email: 'smith@example.com', role: 'professor' },
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
    ]
  }

  getDummyStudents(): any[] {
    const firstNames = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Jennifer', 'Michael', 'Amanda', 'William', 'Jessica', 'Daniel', 'Ashley', 'Christopher', 'Sophia', 'Matthew', 'Olivia', 'Andrew', 'Emma', 'Joshua', 'Isabella', 'Ryan', 'Mia', 'Kevin', 'Charlotte', 'Tyler', 'Amelia', 'Jason', 'Harper', 'Ethan', 'Evelyn', 'Brandon', 'Abigail', 'Nathan', 'Emily', 'Alexander', 'Madison', 'Jacob', 'Sofia', 'Logan', 'Avery', 'Ethan', 'Aiden', 'Caleb', 'Jackson', 'Mason', 'Liam', 'Noah', 'Lucas', 'Henry', 'Alexander', 'Sebastian', 'Ezra', 'Jack', 'Owen', 'Daniel', 'Matthew', 'Joseph', 'David', 'Samuel', 'Carter', 'Wyatt', 'Jayden', 'John', 'Dylan', 'Luke', 'Gabriel', 'Anthony', 'Isaac', 'Lincoln', 'Christopher', 'Joshua', 'Andrew', 'Mateo', 'Ryan', 'Nathan', 'Aaron', 'Isaiah', 'Thomas', 'Charles', 'Caleb', 'Josiah', 'Christian', 'Jonathan', 'Landon', 'Evan', 'Gavin', 'Connor', 'Adrian', 'Asher', 'Jeremiah', 'Hudson', 'Robert', 'Nicholas', 'Brayden', 'Grayson', 'Eli', 'Ezekiel', 'Dominic', 'Oliver', 'Xavier', 'Jaxon', 'Maverick', 'Kai', 'Santiago', 'Leo', 'Aarav', 'Roman', 'Adam', 'Ronan', 'Emmett', 'Remington', 'Milo', 'Archer', 'Rowan', 'Karter', 'Wesley', 'Jaxson', 'Josiah', 'Elliot', 'Parker', 'Colton', 'Luca', 'Atlas', 'Jasper', 'Declan', 'Kayden', 'Maxwell', 'Ryker', 'Enzo', 'Kingston', 'Bennett', 'Carson', 'Raymond', 'Zion', 'Arlo', 'Theodore', 'Jude', 'Nolan', 'Antonio', 'Myles', 'Elliott', 'Gideon', 'Knox', 'Damon', 'Ace', 'Barrett', 'Amiri', 'Max', 'Javier', 'Silas', 'Cody', 'Beau', 'Amir', 'Adriel', 'Rory', 'Bodhi', 'Emiliano', 'Braxton', 'Khalil', 'Malachi', 'Makai', 'Ronin', 'Finn', 'Zayn', 'Kade', 'Rex', 'Cruz', 'Stellan', 'Joaquin', 'Koa', 'Lorenzo', 'Orion', 'Cassius', 'Armani', 'Frankie', 'Ermias', 'Kairo', 'Legend', 'Raphael', 'Zayne', 'Jesse', 'Sullivan', 'Cameron', 'Graham', 'Felix', 'August', 'River', 'Brooks', 'Bryce', 'Judah', 'Kellan', 'Abel', 'Colby', 'Hayes', 'Salvador', 'Kaden', 'Kamari', 'Solomon', 'Rhys', 'Jerry', 'Ricky', 'Tommy', 'Andre', 'Miguel', 'Hector', 'Sergio', 'Luis', 'Carlos', 'Juan', 'Jorge', 'Martin', 'Adrian', 'Diego', 'Ricardo', 'Antonio', 'Alejandro', 'Manuel', 'Pablo', 'Javier', 'Roberto', 'Pedro', 'Raul', 'Francisco', 'Angel', 'Gabriel', 'Miguel', 'Luis', 'Carlos', 'Jose', 'David', 'Daniel', 'Mario', 'Arturo', 'Rafael', 'Eduardo', 'Victor', 'Alberto', 'Oscar', 'Santiago', 'Andres', 'Marco', 'Fernando', 'Benjamin', 'Samuel', 'Isaac', 'Nathan', 'Caleb', 'Aaron', 'Lucas', 'Henry', 'Owen', 'Julian', 'Levi', 'Christian', 'Eli', 'Aaron', 'Evan', 'Parker', 'Adam', 'Ian', 'Connor', 'Leo', 'Xavier', 'Ryan', 'Colton', 'Angel', 'Adrian', 'Jonathan', 'Carson', 'Hunter', 'Brandon', 'Austin', 'Gavin', 'Nolan', 'Tyler', 'Caleb', 'Lucas', 'Henry', 'Owen', 'Julian', 'Levi', 'Christian', 'Eli', 'Aaron', 'Evan', 'Parker', 'Adam', 'Ian', 'Connor', 'Leo', 'Xavier', 'Ryan', 'Colton', 'Angel', 'Adrian', 'Jonathan', 'Carson', 'Hunter', 'Brandon', 'Austin', 'Gavin', 'Nolan', 'Tyler', 'Caleb']
    
    const lastNames = ['Doe', 'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young', 'Allen', 'King', 'Wright', 'Lopez', 'Hill', 'Scott', 'Green', 'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter', 'Mitchell', 'Perez', 'Roberts', 'Turner', 'Phillips', 'Campbell', 'Parker', 'Evans', 'Edwards', 'Collins', 'Stewart', 'Sanchez', 'Morris', 'Rogers', 'Reed', 'Cook', 'Morgan', 'Bell', 'Murphy', 'Bailey', 'Rivera', 'Cooper', 'Richardson', 'Cox', 'Howard', 'Ward', 'Torres', 'Peterson', 'Gray', 'Ramirez', 'James', 'Watson', 'Brooks', 'Kelly', 'Sanders', 'Price', 'Bennett', 'Wood', 'Barnes', 'Ross', 'Henderson', 'Coleman', 'Jenkins', 'Perry', 'Powell', 'Long', 'Patterson', 'Hughes', 'Flores', 'Washington', 'Butler', 'Simmons', 'Foster', 'Gonzales', 'Bryant', 'Alexander', 'Russell', 'Griffin', 'Diaz', 'Hayes', 'Myers', 'Ford', 'Hamilton', 'Graham', 'Sullivan', 'Wallace', 'Woods', 'Cole', 'West', 'Jordan', 'Owens', 'Reynolds', 'Fisher', 'Ellis', 'Harrison', 'Gibson', 'Mcdonald', 'Cruz', 'Marshall', 'Ortiz', 'Gomez', 'Murray', 'Freeman', 'Wells', 'Webb', 'Simpson', 'Stevens', 'Tucker', 'Porter', 'Hunter', 'Hicks', 'Crawford', 'Henry', 'Boyd', 'Mason', 'Morales', 'Kennedy', 'Warren', 'Dixon', 'Ramos', 'Reyes', 'Burns', 'Gordon', 'Shaw', 'Holmes', 'Rice', 'Robertson', 'Hunt', 'Black', 'Daniels', 'Palmer', 'Mills', 'Nichols', 'Grant', 'Knight', 'Ferguson', 'Rose', 'Stone', 'Hawkins', 'Dunn', 'Perkins', 'Hudson', 'Spencer', 'Gardner', 'Stephens', 'Payne', 'Pierce', 'Berry', 'Matthews', 'Arnold', 'Wagner', 'Willis', 'Ray', 'Watkins', 'Olson', 'Carroll', 'Duncan', 'Snyder', 'Hart', 'Cunningham', 'Bradley', 'Lane', 'Andrews', 'Ruiz', 'Harper', 'Fox', 'Riley', 'Armstrong', 'Carpenter', 'Weaver', 'Greene', 'Lawrence', 'Elliott', 'Chavez', 'Sims', 'Austin', 'Peters', 'Kelley', 'Franklin', 'Lawson', 'Fields', 'Gutierrez', 'Ryan', 'Schmidt', 'Carr', 'Vasquez', 'Castillo', 'Wheeler', 'Chapman', 'Oliver', 'Montgomery', 'Richards', 'Williamson', 'Johnston', 'Banks', 'Meyer', 'Bauer', 'Fletcher', 'Giles', 'Floyd', 'Hogan', 'Luna', 'Phelps', 'McGuire', 'Allison', 'Bridges', 'Wilkerson', 'Stanley', 'Nguyen', 'George', 'Jacobs', 'Reid', 'Kim', 'Fuller', 'Lynch', 'Dean', 'Gilbert', 'Garza', 'Erickson', 'Vargas', 'Combs', 'Kramer', 'Molina', 'Huffman', 'Kelley', 'Dixon', 'Owens', 'Huffman', 'Kelley', 'Dixon', 'Owens', 'Huffman', 'Kelley', 'Dixon', 'Owens']
    
    const students = []
    
    // Generate 1000 students
    for (let i = 0; i < 1000; i++) {
      const firstName = firstNames[i % firstNames.length] || 'Student'
      const lastName = lastNames[i % lastNames.length] || `Last${i}`
      const year = Math.floor(Math.random() * 4) + 1
      const standings = ['excellent', 'good', 'average', 'probation']
      const standing = standings[Math.floor(Math.random() * standings.length)]
      const gpa = Number((Math.random() * 2 + 2).toFixed(2))
      
      students.push({
        id: i + 1,
        personalInfo: {
          firstName: firstName,
          lastName: lastName,
          middleName: 'Generated',
          studentId: `2024${String(i + 1).padStart(4, '0')}`,
          email: `${firstName.toLowerCase()}.${lastName.toLowerCase()}@university.edu`,
          phone: `+1-555-${String(Math.floor(Math.random() * 900) + 100).padStart(3, '0')}-${String(Math.floor(Math.random() * 9000) + 1000).padStart(4, '0')}`,
          dateOfBirth: `${2000 + Math.floor(Math.random() * 5)}-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}`,
          age: Math.floor(Math.random() * 10) + 18,
          gender: Math.random() > 0.5 ? 'male' : 'female',
          address: `${Math.floor(Math.random() * 9999) + 1} University Ave`,
          city: 'Academic City',
          province: 'Education State',
          postalCode: String(Math.floor(Math.random() * 99999) + 10000),
          emergencyContact: {
            name: 'Parent Name',
            relationship: 'Parent',
            phone: `+1-555-${String(Math.floor(Math.random() * 900) + 100).padStart(3, '0')}-${String(Math.floor(Math.random() * 9000) + 1000).padStart(4, '0')}`
          }
        },
        academicHistory: [
          {
            id: i + 1,
            schoolName: 'High School of Science',
            degree: 'High School Diploma',
            major: 'General Studies',
            startDate: '2017-08-01',
            endDate: '2021-06-15',
            gpa: gpa,
            honors: Math.random() > 0.7 ? ['Honor Roll'] : [],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: year,
          currentSemester: Math.random() > 0.5 ? 'first' : 'second',
          currentGPA: gpa,
          totalUnits: Math.floor(Math.random() * 60) + 60,
          standing: standing === 'excellent' || standing === 'good' ? 'good' : standing === 'average' ? 'warning' : 'probation',
          advisor: `Dr. ${['Smith', 'Johnson', 'Williams', 'Brown', 'Jones'][Math.floor(Math.random() * 5)]}`
        },
        activities: [],
        violations: Math.random() > 0.8 ? [{
          id: 1,
          type: 'academic',
          description: 'Late assignment submission',
          date: '2023-10-15',
          severity: 'minor',
          status: 'resolved',
          consequence: 'Warning'
        }] : [],
        skills: [],
        affiliations: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      })
    }
    
    return students
  }

  initializeDummyData(): void {
    if (this.getLocalAnnouncements().length === 0) {
      this.setLocalAnnouncements(this.getDummyAnnouncements())
    }
    if (this.getLocalStudents().length === 0) {
      this.setLocalStudents(this.getDummyStudents())
    }
  }

  getLocalProfessors(): any[] {
    const stored = localStorage.getItem('professors')
    return stored ? JSON.parse(stored) : this.getDummyProfessors()
  }

  getLocalViolations(): any[] {
    const stored = localStorage.getItem('violations')
    return stored ? JSON.parse(stored) : this.getDummyViolations()
  }

  getLocalCourses(): any[] {
    const stored = localStorage.getItem('courses')
    return stored ? JSON.parse(stored) : this.getDummyCourses()
  }

  getLocalEvents(): any[] {
    const stored = localStorage.getItem('events')
    return stored ? JSON.parse(stored) : this.getDummyEvents()
  }

  getLocalRooms(): any[] {
    const stored = localStorage.getItem('rooms')
    return stored ? JSON.parse(stored) : this.getDummyRooms()
  }

  getLocalSchedules(): any[] {
    const stored = localStorage.getItem('schedules')
    return stored ? JSON.parse(stored) : this.getDummySchedules()
  }

  getLocalSyllabi(): any[] {
    const stored = localStorage.getItem('syllabi')
    return stored ? JSON.parse(stored) : this.getDummySyllabi()
  }

  getDummyProfessors(): any[] {
    const professors = []
    const departments = ['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Engineering', 'Business', 'Arts']
    const firstNames = ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams', 'Dr. Brown', 'Dr. Davis', 'Dr. Miller', 'Dr. Wilson', 'Dr. Moore', 'Dr. Taylor', 'Dr. Anderson']
    const lastNames = ['Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson', 'Garcia', 'Martinez', 'Robinson']
    
    for (let i = 0; i < 20; i++) {
      professors.push({
        id: i + 1,
        firstName: firstNames[i % firstNames.length],
        lastName: lastNames[i % lastNames.length],
        email: `professor${i + 1}@university.edu`,
        department: departments[i % departments.length],
        specialization: `Specialization ${i + 1}`,
        hireDate: `${2010 + Math.floor(Math.random() * 10)}-09-01`,
        status: Math.random() > 0.1 ? 'active' : 'inactive',
        courses: Math.floor(Math.random() * 5) + 1,
        rating: (Math.random() * 2 + 3).toFixed(1),
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return professors
  }

  getDummyViolations(): any[] {
    const violations = []
    const types = ['Academic Dishonesty', 'Attendance Policy', 'Code of Conduct', 'Plagiarism', 'Late Submission', 'Disruptive Behavior']
    const severities = ['minor', 'moderate', 'major', 'severe']
    
    for (let i = 0; i < 50; i++) {
      violations.push({
        id: i + 1,
        studentId: Math.floor(Math.random() * 1000) + 1,
        type: types[i % types.length],
        description: `Violation description ${i + 1}`,
        date: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        severity: severities[Math.floor(Math.random() * severities.length)],
        status: Math.random() > 0.3 ? 'resolved' : 'pending',
        consequence: `Consequence ${i + 1}`,
        reportedBy: `Professor ${Math.floor(Math.random() * 20) + 1}`,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return violations
  }

  getDummyCourses(): any[] {
    const courses = []
    const subjects = ['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Engineering', 'Business', 'Arts', 'Literature', 'History']
    const levels = ['100', '200', '300', '400']
    
    for (let i = 0; i < 30; i++) {
      courses.push({
        id: i + 1,
        code: `${subjects[i % subjects.length].substring(0, 3).toUpperCase()}${levels[i % levels.length]}${Math.floor(Math.random() * 99) + 1}`,
        name: `Course ${i + 1}: ${subjects[i % subjects.length]} Fundamentals`,
        description: `Description for course ${i + 1}`,
        credits: Math.floor(Math.random() * 4) + 1,
        level: levels[i % levels.length],
        department: subjects[i % subjects.length],
        professorId: Math.floor(Math.random() * 20) + 1,
        semester: ['Fall', 'Spring', 'Summer'][i % 3],
        year: 2024,
        capacity: Math.floor(Math.random() * 50) + 20,
        enrolled: Math.floor(Math.random() * 40) + 10,
        status: Math.random() > 0.1 ? 'active' : 'inactive',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return courses
  }

  getDummyEvents(): any[] {
    const events = []
    const types = ['Academic', 'Sports', 'Cultural', 'Workshop', 'Seminar', 'Conference']
    
    for (let i = 0; i < 25; i++) {
      const startDate = new Date(Date.now() + Math.random() * 90 * 24 * 60 * 60 * 1000)
      events.push({
        id: i + 1,
        title: `Event ${i + 1}: ${types[i % types.length]} Activity`,
        description: `Description for event ${i + 1}`,
        type: types[i % types.length],
        startDate: startDate.toISOString(),
        endDate: new Date(startDate.getTime() + Math.random() * 8 * 60 * 60 * 1000).toISOString(),
        location: `Location ${i + 1}`,
        organizer: `Organizer ${i + 1}`,
        capacity: Math.floor(Math.random() * 200) + 50,
        registered: Math.floor(Math.random() * 150) + 20,
        status: Math.random() > 0.2 ? 'upcoming' : 'completed',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return events
  }

  getDummyRooms(): any[] {
    const rooms = []
    const buildings = ['Science Building', 'Engineering Hall', 'Business Tower', 'Arts Center', 'Library']
    const types = ['Lecture Hall', 'Laboratory', 'Seminar Room', 'Computer Lab', 'Conference Room']
    
    for (let i = 0; i < 40; i++) {
      rooms.push({
        id: i + 1,
        number: `Room ${Math.floor(Math.random() * 900) + 100}`,
        building: buildings[i % buildings.length],
        type: types[i % types.length],
        capacity: Math.floor(Math.random() * 100) + 20,
        equipment: ['Projector', 'Whiteboard', 'Computer', 'Sound System'].slice(0, Math.floor(Math.random() * 4) + 1),
        floor: Math.floor(Math.random() * 5) + 1,
        status: Math.random() > 0.1 ? 'available' : 'maintenance',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return rooms
  }

  getDummySchedules(): any[] {
    const schedules = []
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
    const times = ['08:00', '10:00', '13:00', '15:00', '17:00']
    
    for (let i = 0; i < 60; i++) {
      schedules.push({
        id: i + 1,
        courseId: Math.floor(Math.random() * 30) + 1,
        professorId: Math.floor(Math.random() * 20) + 1,
        roomId: Math.floor(Math.random() * 40) + 1,
        day: days[i % days.length],
        startTime: times[i % times.length],
        endTime: times[(i % times.length) + 1] || '19:00',
        semester: 'Fall 2024',
        status: Math.random() > 0.1 ? 'active' : 'cancelled',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return schedules
  }

  getDummySyllabi(): any[] {
    const syllabi = []
    const courses = ['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology']
    
    for (let i = 0; i < 20; i++) {
      syllabi.push({
        id: i + 1,
        courseId: Math.floor(Math.random() * 30) + 1,
        title: `Syllabus for ${courses[i % courses.length]} Course ${i + 1}`,
        description: `Course description and objectives for syllabus ${i + 1}`,
        objectives: [`Objective ${i + 1}.1`, `Objective ${i + 1}.2`, `Objective ${i + 1}.3`],
        topics: [`Topic ${i + 1}.1`, `Topic ${i + 1}.2`, `Topic ${i + 1}.3`, `Topic ${i + 1}.4`],
        gradingPolicy: {
          assignments: '30%',
          midterm: '30%',
          final: '40%'
        },
        attendancePolicy: 'Mandatory attendance required',
        officeHours: 'MW 14:00-16:00',
        status: Math.random() > 0.1 ? 'published' : 'draft',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
    }
    return syllabi
  }
}

export const offlineService = new OfflineService()
export default offlineService
