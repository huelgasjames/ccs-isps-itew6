import api from './api'

export interface LoginCredentials {
  email: string
  password: string
}

export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'student' | 'professor'
  department?: string
  created_at?: string
  updated_at?: string
}

export interface AuthResponse {
  user: User
  token: string
  role: string
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    console.log('Sending login request:', credentials)
    try {
      const response = await api.post('/login', credentials)
      console.log('Login response:', response)
      return response.data
    } catch (error: any) {
      console.error('Login error:', error.response?.data || error)
      throw error
    }
  },

  async logout(): Promise<void> {
    await api.post('/logout')
  },

  async getCurrentUser(): Promise<User> {
    const response = await api.get('/me')
    return response.data
  }
}
