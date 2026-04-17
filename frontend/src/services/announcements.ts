import api, { apiWithFallback } from './api'

export interface Announcement {
  id: number
  title: string
  content: string
  image?: string
  user_id: number
  status: 'draft' | 'published'
  target_users: number[] | null
  target_type: 'all' | 'students' | 'professors' | 'specific'
  created_at: string
  updated_at: string
  user?: {
    id: number
    name: string
    email: string
    role: string
  }
  views?: AnnouncementView[]
  view_count?: number
  is_viewed?: boolean
}

export interface AnnouncementView {
  id: number
  announcement_id: number
  user_id: number
  viewed_at: string
  user?: {
    id: number
    name: string
    email: string
    role: string
  }
}

export interface CreateAnnouncementData {
  title: string
  content: string
  image?: File
  target_type: 'all' | 'students' | 'professors' | 'specific'
  target_users?: number[]
  status: 'draft' | 'published'
}

class AnnouncementService {
  // Original methods (keep existing functionality)
  async getAnnouncements(): Promise<Announcement[]> {
    const response = await api.get('/announcements')
    return response.data
  }

  async getAnnouncement(id: number): Promise<Announcement> {
    const response = await api.get(`/announcements/${id}`)
    return response.data
  }

  // Offline-enabled methods
  async getAnnouncementsOffline(): Promise<Announcement[]> {
    const response = await apiWithFallback.get('/announcements')
    return response.data
  }

  async getAnnouncementOffline(id: number): Promise<Announcement> {
    const response = await apiWithFallback.get(`/announcements/${id}`)
    return response.data
  }

  async createAnnouncement(data: CreateAnnouncementData): Promise<Announcement> {
    const formData = new FormData()
    
    formData.append('title', data.title)
    formData.append('content', data.content)
    formData.append('target_type', data.target_type)
    formData.append('status', data.status)
    
    if (data.image) {
      formData.append('image', data.image)
    }
    
    if (data.target_users && data.target_users.length > 0) {
      formData.append('target_users', JSON.stringify(data.target_users))
    }

    const response = await api.post('/announcements', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  }

  async updateAnnouncement(id: number, data: Partial<CreateAnnouncementData>): Promise<Announcement> {
    const formData = new FormData()
    
    if (data.title) formData.append('title', data.title)
    if (data.content) formData.append('content', data.content)
    if (data.target_type) formData.append('target_type', data.target_type)
    if (data.status) formData.append('status', data.status)
    
    if (data.image) {
      formData.append('image', data.image)
    }
    
    if (data.target_users) {
      formData.append('target_users', JSON.stringify(data.target_users))
    }

    const response = await api.post(`/announcements/${id}?_method=PUT`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  }

  async deleteAnnouncement(id: number): Promise<void> {
    await api.delete(`/announcements/${id}`)
  }

  // Offline CRUD operations
  async createAnnouncementOffline(data: Omit<CreateAnnouncementData, 'image'>): Promise<Announcement> {
    const response = await apiWithFallback.post('/announcements', data)
    return response.data
  }

  async updateAnnouncementOffline(id: number, data: Partial<CreateAnnouncementData>): Promise<Announcement> {
    const response = await apiWithFallback.put(`/announcements/${id}`, data)
    return response.data
  }

  async deleteAnnouncementOffline(id: number): Promise<void> {
    await apiWithFallback.delete(`/announcements/${id}`)
  }

  async markAsViewed(announcementId: number): Promise<void> {
    await api.post(`/announcements/${announcementId}/view`)
  }

  async getAnnouncementViews(announcementId: number): Promise<AnnouncementView[]> {
    const response = await api.get(`/announcements/${announcementId}/views`)
    return response.data
  }

  async publishAnnouncement(id: number): Promise<Announcement> {
    const response = await api.patch(`/announcements/${id}/publish`)
    return response.data
  }

  async unpublishAnnouncement(id: number): Promise<Announcement> {
    const response = await api.patch(`/announcements/${id}/unpublish`)
    return response.data
  }

  // Additional offline CRUD operations
  async markAsViewedOffline(announcementId: number): Promise<void> {
    await apiWithFallback.post(`/announcements/${announcementId}/view`)
  }

  async getAnnouncementViewsOffline(announcementId: number): Promise<AnnouncementView[]> {
    const response = await apiWithFallback.get(`/announcements/${announcementId}/views`)
    return response.data
  }

  async publishAnnouncementOffline(id: number): Promise<Announcement> {
    const response = await apiWithFallback.patch(`/announcements/${id}/publish`)
    return response.data
  }

  async unpublishAnnouncementOffline(id: number): Promise<Announcement> {
    const response = await apiWithFallback.patch(`/announcements/${id}/unpublish`)
    return response.data
  }
}

export const announcementService = new AnnouncementService()
export default announcementService
