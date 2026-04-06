// Core Student Information
export interface PersonalInformation {
  firstName: string
  lastName: string
  middleName?: string
  studentId: string
  email: string
  phone: string
  dateOfBirth: string
  age: number
  gender: 'male' | 'female' | 'other'
  address: string
  city: string
  province: string
  postalCode: string
  emergencyContact: {
    name: string
    relationship: string
    phone: string
  }
}

// Academic History
export interface AcademicHistory {
  id: number
  schoolName: string
  degree: string
  major: string
  startDate: string
  endDate?: string
  gpa?: number
  honors?: string[]
  status: 'completed' | 'ongoing' | 'transferred'
}

// Current Academic Standing
export interface AcademicStanding {
  currentYear: number
  currentSemester: 'first' | 'second' | 'summer'
  currentGPA: number
  totalUnits: number
  standing: 'good' | 'warning' | 'probation'
  advisor: string
}

// Non-Academic Activities
export interface NonAcademicActivity {
  id: number
  name: string
  type: 'sports' | 'organization' | 'volunteer' | 'competition' | 'leadership' | 'other'
  role: string
  startDate: string
  endDate?: string
  description: string
  achievements?: string[]
  level: 'local' | 'regional' | 'national' | 'international'
}

// Violations
export interface Violation {
  id: number
  type: string
  severity: 'minor' | 'major' | 'critical'
  description: string
  date: string
  status: 'pending' | 'resolved' | 'under_review'
  sanctions?: string[]
  points: number
  reportedBy: string
}

// Skills
export interface Skill {
  id: number
  name: string
  category: 'technical' | 'soft' | 'language' | 'creative' | 'sports' | 'other'
  proficiency: 'beginner' | 'intermediate' | 'advanced' | 'expert'
  certifications?: string[]
  yearsExperience?: number
  lastUsed?: string
}

// Affiliations
export interface Affiliation {
  id: number
  name: string
  type: 'student_organization' | 'professional' | 'religious' | 'community' | 'academic' | 'other'
  role: string
  startDate: string
  endDate?: string
  position?: string
  description?: string
}

// Main Student Profile Interface
export interface Student {
  id: number
  personalInfo: PersonalInformation
  academicHistory: AcademicHistory[]
  academicStanding: AcademicStanding
  activities: NonAcademicActivity[]
  violations: Violation[]
  skills: Skill[]
  affiliations: Affiliation[]
  createdAt: string
  updatedAt: string
  profileImage?: string
  isActive: boolean
}

// Filter and Query Types
export interface StudentFilter {
  search?: string
  skills?: string[]
  activities?: string[]
  affiliations?: string[]
  violationStatus?: string[]
  academicStanding?: string[]
  yearLevel?: number[]
  gpaRange?: {
    min: number
    max: number
  }
  ageRange?: {
    min: number
    max: number
  }
}

// API Response Types
export interface StudentListResponse {
  students: Student[]
  total: number
  page: number
  limit: number
  totalPages: number
}

export interface CreateStudentRequest {
  personalInfo: PersonalInformation
  academicStanding: AcademicStanding
}

export interface UpdateStudentRequest extends Partial<Student> {
  id: number
}

// Display Options
export type DisplayMode = 'table' | 'cards' | 'compact'

// Statistics
export interface StudentStatistics {
  totalStudents: number
  activeStudents: number
  averageGPA: number
  topSkills: { name: string; count: number }[]
  commonActivities: { name: string; count: number }[]
  violationCount: number
}
