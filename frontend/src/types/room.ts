export interface Room {
  id: number
  name: string
  building?: string | null
  floor?: number | null
  room_type: 'lecture' | 'lab' | 'computer_lab' | 'multimedia' | 'other'
  capacity: number
  status: 'active' | 'inactive'
  notes?: string | null
  created_at?: string
  updated_at?: string
}

export type RoomFormData = Omit<Room, 'id' | 'created_at' | 'updated_at'>
