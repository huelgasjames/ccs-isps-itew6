export interface Announcement {
  id: number
  title: string
  excerpt: string
  content: string
  type: string
  priority: 'low' | 'medium' | 'high' | 'urgent'
  status: 'draft' | 'published' | 'scheduled' | 'archived'
  author: string
  publish_date: string
  expires_at: string | null
  views: number
  likes: number
  comments: number
  target_all: boolean
  target_students: boolean
  target_professors: boolean
  target_admins: boolean
  user_id: number | null
  created_at: string
  updated_at: string
  attachments?: AnnouncementAttachment[]
}

export interface AnnouncementAttachment {
  id: number
  announcement_id: number
  filename: string
  original_name: string
  mime_type: string
  file_size: number
  file_path: string
  created_at: string
  updated_at: string
  url?: string
  name?: string
  size?: number
}

export interface AnnouncementComment {
  id: number
  announcement_id: number
  user_id: number
  content: string
  created_at: string
  updated_at: string
  canDelete: boolean
  author: string
}

export interface FormErrors {
  title?: string
  type?: string
  priority?: string
  status?: string
  excerpt?: string
  content?: string
  publish_date?: string
  expires_at?: string
}
