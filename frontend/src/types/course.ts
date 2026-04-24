export interface Course {
  id: number
  courseCode: string
  courseName: string
  description: string
  credits: number
  department: string
  semester: string
  academicYear: string
  instructor?: string
  schedule?: string
  room?: string
  maxStudents: number
  currentStudents: number
  status: 'active' | 'inactive' | 'archived'
  prerequisites?: string[]
  createdAt: string
  updatedAt: string
}

export interface CourseFormData {
  courseCode: string
  courseName: string
  description: string
  credits: number
  department: string
  semester: string
  academicYear: string
  instructor: string
  schedule: string
  room: string
  maxStudents: number
  status: 'active' | 'inactive' | 'archived'
  prerequisites: string[]
}

export interface CourseFilter {
  search: string
  department: string
  semester: string
  status: string
  academicYear: string
}

export interface CourseAnalytics {
  totalCourses: number
  activeCourses: number
  inactiveCourses: number
  archivedCourses: number
  totalDepartments: number
  averageCredits: number
  totalStudents: number
  coursesByDepartment: DepartmentCourseCount[]
  coursesBySemester: SemesterCourseCount[]
}

export interface DepartmentCourseCount {
  department: string
  count: number
  percentage: number
}

export interface SemesterCourseCount {
  semester: string
  count: number
  percentage: number
}
