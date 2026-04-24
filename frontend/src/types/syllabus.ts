export interface Syllabus {
  id: number
  courseId: number
  professorId: number
  academicYear: string
  semester: string
  courseDescription: string
  learningObjectives: string
  topicsOutline: string
  assessmentMethods: string
  gradingPolicy: string
  requiredMaterials: string
  classPolicies: string
  filePath?: string
  approved: boolean
  approvedAt?: string
  approvedBy?: number
  createdAt: string
  updatedAt: string
  course?: {
    id: number
    courseCode: string
    courseName: string
  }
  professor?: {
    id: number
    firstName: string
    lastName: string
  }
  approvedByUser?: {
    id: number
    name: string
  }
}

export interface SyllabusFormData {
  courseId: number
  professorId: number
  academicYear: string
  semester: string
  courseDescription: string
  learningObjectives: string
  topicsOutline: string
  assessmentMethods: string
  gradingPolicy: string
  requiredMaterials: string
  classPolicies: string
  approved?: boolean
}

export interface SyllabusFilter {
  courseId?: number
  professorId?: number
  academicYear?: string
  semester?: string
  approved?: boolean
  search?: string
}

export interface SyllabusStats {
  totalSyllabi: number
  approvedSyllabi: number
  pendingSyllabi: number
  rejectedSyllabi: number
  byAcademicYear: Record<string, number>
  bySemester: Record<string, number>
  byDepartment: Record<string, number>
}
