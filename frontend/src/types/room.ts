export interface Room {
  id: number
  name: string
  room_code: string
  type: 'lecture' | 'lab' | 'computer_lab' | 'multimedia' | 'conference'
  capacity: number
  floor?: number | null
  building?: string | null
  description?: string | null
  status: 'available' | 'maintenance' | 'occupied' | 'unavailable'
  equipment?: string[] | null
  hourly_rate?: number | null
  created_at?: string
  updated_at?: string
}

export type RoomFormData = Omit<Room, 'id' | 'created_at' | 'updated_at'>
