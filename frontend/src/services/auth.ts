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
}

export interface AuthResponse {
  user: User
  token: string
  role: string
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post('/login', credentials)
    return response.data
  },

  async logout(): Promise<void> {
    await api.post('/logout')
  },

  async getCurrentUser(): Promise<User> {
    const response = await api.get('/me')
    return response.data
  }
}
