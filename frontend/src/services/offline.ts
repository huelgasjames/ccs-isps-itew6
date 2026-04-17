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
    return [
      {
        id: 1,
        personalInfo: {
          firstName: 'John',
          lastName: 'Doe',
          middleName: 'Michael',
          studentId: '2021-001',
          email: 'john.doe@university.edu',
          phone: '+1-555-0123',
          dateOfBirth: '2000-05-15',
          age: 23,
          gender: 'male',
          address: '123 University Ave',
          city: 'Academic City',
          province: 'Education State',
          postalCode: '12345',
          emergencyContact: {
            name: 'Jane Doe',
            relationship: 'Mother',
            phone: '+1-555-0124'
          }
        },
        academicHistory: [
          {
            id: 1,
            schoolName: 'High School of Science',
            degree: 'High School Diploma',
            major: 'General Studies',
            startDate: '2017-08-01',
            endDate: '2021-06-15',
            gpa: 3.8,
            honors: ['Honor Roll', 'Science Club President'],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: 3,
          currentSemester: 'second',
          currentGPA: 3.7,
          totalUnits: 90,
          standing: 'good',
          advisor: 'Dr. Sarah Johnson'
        },
        activities: [
          {
            id: 1,
            name: 'Computer Science Club',
            type: 'organization',
            role: 'Member',
            startDate: '2021-09-01',
            description: 'Active participant in coding competitions and workshops',
            achievements: ['Hackathon Winner 2022'],
            level: 'local'
          }
        ],
        violations: [],
        skills: [
          {
            id: 1,
            name: 'JavaScript',
            category: 'technical',
            proficiency: 'advanced',
            certifications: ['JavaScript Certification'],
            yearsExperience: 3,
            lastUsed: new Date().toISOString().split('T')[0]
          },
          {
            id: 2,
            name: 'Leadership',
            category: 'soft',
            proficiency: 'intermediate',
            yearsExperience: 2
          }
        ],
        affiliations: [
          {
            id: 1,
            name: 'Student Government',
            type: 'student_organization',
            role: 'Class Representative',
            startDate: '2022-01-01',
            position: 'Representative',
            description: 'Representing student interests in department meetings'
          }
        ],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      },
      {
        id: 2,
        personalInfo: {
          firstName: 'Jane',
          lastName: 'Smith',
          middleName: 'Elizabeth',
          studentId: '2021-002',
          email: 'jane.smith@university.edu',
          phone: '+1-555-0125',
          dateOfBirth: '2000-08-22',
          age: 23,
          gender: 'female',
          address: '456 College Blvd',
          city: 'Scholar Town',
          province: 'Learning Province',
          postalCode: '67890',
          emergencyContact: {
            name: 'Robert Smith',
            relationship: 'Father',
            phone: '+1-555-0126'
          }
        },
        academicHistory: [
          {
            id: 2,
            schoolName: 'Arts Academy',
            degree: 'High School Diploma',
            major: 'Fine Arts',
            startDate: '2017-08-01',
            endDate: '2021-06-15',
            gpa: 3.9,
            honors: ['Valedictorian', 'Art Award'],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: 3,
          currentSemester: 'second',
          currentGPA: 3.9,
          totalUnits: 92,
          standing: 'good',
          advisor: 'Prof. Michael Brown'
        },
        activities: [
          {
            id: 2,
            name: 'Drama Club',
            type: 'organization',
            role: 'President',
            startDate: '2021-09-01',
            description: 'Leading drama productions and performances',
            achievements: ['Best Director 2023'],
            level: 'regional'
          }
        ],
        violations: [],
        skills: [
          {
            id: 3,
            name: 'Public Speaking',
            category: 'soft',
            proficiency: 'expert',
            yearsExperience: 4
          },
          {
            id: 4,
            name: 'Digital Art',
            category: 'creative',
            proficiency: 'advanced',
            yearsExperience: 3
          }
        ],
        affiliations: [
          {
            id: 2,
            name: 'Art Students Association',
            type: 'student_organization',
            role: 'Treasurer',
            startDate: '2022-01-01',
            position: 'Treasurer',
            description: 'Managing association finances and events'
          }
        ],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      },
      {
        id: 3,
        personalInfo: {
          firstName: 'Carlos',
          lastName: 'Rodriguez',
          middleName: 'Miguel',
          studentId: '2020-003',
          email: 'carlos.rodriguez@university.edu',
          phone: '+1-555-0130',
          dateOfBirth: '1999-12-10',
          age: 24,
          gender: 'male',
          address: '789 Campus Drive',
          city: 'Tech Valley',
          province: 'Innovation State',
          postalCode: '54321',
          emergencyContact: {
            name: 'Maria Rodriguez',
            relationship: 'Sister',
            phone: '+1-555-0131'
          }
        },
        academicHistory: [
          {
            id: 3,
            schoolName: 'STEM High School',
            degree: 'High School Diploma',
            major: 'Engineering',
            startDate: '2016-08-01',
            endDate: '2020-06-15',
            gpa: 3.6,
            honors: ['Math Olympiad', 'Robotics Team Captain'],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: 4,
          currentSemester: 'first',
          currentGPA: 3.5,
          totalUnits: 110,
          standing: 'good',
          advisor: 'Dr. Emily Chen'
        },
        activities: [
          {
            id: 3,
            name: 'Robotics Club',
            type: 'organization',
            role: 'Vice President',
            startDate: '2020-09-01',
            description: 'Building and programming competition robots',
            achievements: ['Regional Robotics Champion 2022'],
            level: 'national'
          }
        ],
        violations: [],
        skills: [
          {
            id: 5,
            name: 'Python',
            category: 'technical',
            proficiency: 'expert',
            certifications: ['Python Professional Certification'],
            yearsExperience: 4,
            lastUsed: new Date().toISOString().split('T')[0]
          },
          {
            id: 6,
            name: 'Machine Learning',
            category: 'technical',
            proficiency: 'intermediate',
            yearsExperience: 2
          }
        ],
        affiliations: [
          {
            id: 3,
            name: 'Engineering Society',
            type: 'student_organization',
            role: 'Member',
            startDate: '2020-01-01',
            position: 'Member',
            description: 'Professional development and networking for engineering students'
          }
        ],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      },
      {
        id: 4,
        personalInfo: {
          firstName: 'Aisha',
          lastName: 'Patel',
          middleName: 'Rani',
          studentId: '2022-004',
          email: 'aisha.patel@university.edu',
          phone: '+1-555-0140',
          dateOfBirth: '2003-03-25',
          age: 21,
          gender: 'female',
          address: '321 Scholar Lane',
          city: 'Knowledge Heights',
          province: 'Academic District',
          postalCode: '98765',
          emergencyContact: {
            name: 'Raj Patel',
            relationship: 'Brother',
            phone: '+1-555-0141'
          }
        },
        academicHistory: [
          {
            id: 4,
            schoolName: 'International Baccalaureate School',
            degree: 'IB Diploma',
            major: 'Business Studies',
            startDate: '2019-08-01',
            endDate: '2022-06-15',
            gpa: 4.0,
            honors: ['IB Full Diploma', 'Debate Team Captain'],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: 2,
          currentSemester: 'second',
          currentGPA: 3.8,
          totalUnits: 60,
          standing: 'excellent',
          advisor: 'Prof. David Lee'
        },
        activities: [
          {
            id: 4,
            name: 'Business Club',
            type: 'organization',
            role: 'Secretary',
            startDate: '2022-09-01',
            description: 'Organizing business case competitions and workshops',
            achievements: ['Business Plan Competition Winner 2023'],
            level: 'international'
          }
        ],
        violations: [],
        skills: [
          {
            id: 7,
            name: 'Financial Analysis',
            category: 'business',
            proficiency: 'advanced',
            certifications: ['Financial Modeling Certificate'],
            yearsExperience: 2,
            lastUsed: new Date().toISOString().split('T')[0]
          },
          {
            id: 8,
            name: 'Public Speaking',
            category: 'soft',
            proficiency: 'advanced',
            yearsExperience: 3
          }
        ],
        affiliations: [
          {
            id: 4,
            name: 'International Student Association',
            type: 'student_organization',
            role: 'Cultural Coordinator',
            startDate: '2022-01-01',
            position: 'Cultural Coordinator',
            description: 'Promoting cultural diversity and international student support'
          }
        ],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      },
      {
        id: 5,
        personalInfo: {
          firstName: 'Marcus',
          lastName: 'Johnson',
          middleName: 'James',
          studentId: '2021-005',
          email: 'marcus.johnson@university.edu',
          phone: '+1-555-0150',
          dateOfBirth: '2001-07-18',
          age: 22,
          gender: 'male',
          address: '555 Athletic Ave',
          city: 'Sports City',
          province: 'Championship State',
          postalCode: '13579',
          emergencyContact: {
            name: 'Linda Johnson',
            relationship: 'Mother',
            phone: '+1-555-0151'
          }
        },
        academicHistory: [
          {
            id: 5,
            schoolName: 'Sports Academy',
            degree: 'High School Diploma',
            major: 'Physical Education',
            startDate: '2017-08-01',
            endDate: '2021-06-15',
            gpa: 3.2,
            honors: ['Athletic Scholarship', 'Team MVP'],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: 3,
          currentSemester: 'second',
          currentGPA: 3.3,
          totalUnits: 88,
          standing: 'average',
          advisor: 'Dr. Susan Martinez'
        },
        activities: [
          {
            id: 5,
            name: 'Basketball Team',
            type: 'sports',
            role: 'Team Captain',
            startDate: '2021-09-01',
            description: 'Leading the university basketball team',
            achievements: ['League MVP 2023', 'Championship Team'],
            level: 'national'
          }
        ],
        violations: [
          {
            id: 1,
            type: 'academic',
            description: 'Late assignment submission',
            date: '2023-10-15',
            severity: 'minor',
            status: 'resolved',
            consequence: 'Warning'
          }
        ],
        skills: [
          {
            id: 9,
            name: 'Basketball',
            category: 'sports',
            proficiency: 'expert',
            yearsExperience: 8,
            lastUsed: new Date().toISOString().split('T')[0]
          },
          {
            id: 10,
            name: 'Team Leadership',
            category: 'soft',
            proficiency: 'advanced',
            yearsExperience: 3
          }
        ],
        affiliations: [
          {
            id: 5,
            name: 'Athletic Department',
            type: 'department',
            role: 'Student Athlete Representative',
            startDate: '2021-01-01',
            position: 'Representative',
            description: 'Representing student athletes in department meetings'
          }
        ],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      },
      {
        id: 6,
        personalInfo: {
          firstName: 'Sophia',
          lastName: 'Kim',
          middleName: 'Min',
          studentId: '2023-006',
          email: 'sophia.kim@university.edu',
          phone: '+1-555-0160',
          dateOfBirth: '2004-11-30',
          age: 19,
          gender: 'female',
          address: '888 Innovation Plaza',
          city: 'Future Town',
          province: 'Digital State',
          postalCode: '24680',
          emergencyContact: {
            name: 'Jin Kim',
            relationship: 'Father',
            phone: '+1-555-0161'
          }
        },
        academicHistory: [
          {
            id: 6,
            schoolName: 'Digital Learning Academy',
            degree: 'High School Diploma',
            major: 'Computer Science',
            startDate: '2019-08-01',
            endDate: '2023-06-15',
            gpa: 3.95,
            honors: ['Valedictorian', 'Coding Competition Winner'],
            status: 'completed'
          }
        ],
        academicStanding: {
          currentYear: 1,
          currentSemester: 'second',
          currentGPA: 3.85,
          totalUnits: 30,
          standing: 'excellent',
          advisor: 'Dr. Alex Thompson'
        },
        activities: [
          {
            id: 6,
            name: 'AI Research Group',
            type: 'research',
            role: 'Research Assistant',
            startDate: '2023-09-01',
            description: 'Assisting in artificial intelligence research projects',
            achievements: ['Published Research Paper'],
            level: 'international'
          }
        ],
        violations: [],
        skills: [
          {
            id: 11,
            name: 'React',
            category: 'technical',
            proficiency: 'advanced',
            certifications: ['React Developer Certificate'],
            yearsExperience: 2,
            lastUsed: new Date().toISOString().split('T')[0]
          },
          {
            id: 12,
            name: 'Data Science',
            category: 'technical',
            proficiency: 'intermediate',
            yearsExperience: 1
          },
          {
            id: 13,
            name: 'Korean Language',
            category: 'language',
            proficiency: 'native',
            yearsExperience: 19
          }
        ],
        affiliations: [
          {
            id: 6,
            name: 'Women in Tech',
            type: 'student_organization',
            role: 'Member',
            startDate: '2023-01-01',
            position: 'Member',
            description: 'Supporting women in technology fields'
          }
        ],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        isActive: true
      }
    ]
  }

  initializeDummyData(): void {
    if (this.getLocalAnnouncements().length === 0) {
      this.setLocalAnnouncements(this.getDummyAnnouncements())
    }
    if (this.getLocalStudents().length === 0) {
      this.setLocalStudents(this.getDummyStudents())
    }
  }
}

export const offlineService = new OfflineService()
export default offlineService
