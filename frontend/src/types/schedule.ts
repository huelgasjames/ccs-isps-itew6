export interface Schedule {
  id: number
  course_id: number
  professor_id: number
  section: string
  room: string
  room_type: 'lecture' | 'lab' | 'computer_lab' | 'multimedia'
  day_of_week: 'Monday' | 'Tuesday' | 'Wednesday' | 'Thursday' | 'Friday' | 'Saturday'
  start_time: string
  end_time: string
  academic_year: string
  semester: 'First Semester' | 'Second Semester' | 'Summer'
  max_capacity: number
  current_enrollment: number
  status: 'active' | 'inactive' | 'cancelled'
  notes?: string | null
  course?: { id: number; course_code: string; course_name: string }
  professor?: { id: number; first_name: string; last_name: string; email: string }
}

export type ScheduleFormData = Omit<Schedule, 'id' | 'course' | 'professor'>
