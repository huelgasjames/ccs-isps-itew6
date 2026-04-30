import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService, type User, type LoginCredentials } from '@/services/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isStudent = computed(() => user.value?.role === 'student')
  const isProfessor = computed(() => user.value?.role === 'professor')
  
  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    try {
      const response = await authService.login(credentials)
      user.value = response.user
      token.value = response.token
      
      localStorage.setItem('auth_token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))
      
      return response
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await authService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
    }
  }

  const getDashboardRoute = (role: string) => {
    switch (role) {
      case 'admin':
        return '/dashboard' // Future: '/admin/dashboard'
      case 'professor':
        return '/faculty/dashboard'
      case 'student':
        return '/student/dashboard'
      default:
        return '/dashboard'
    }
  }

  const initAuth = () => {
    const storedToken = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('user')
    
    console.log('Initializing auth...')
    console.log('Token found:', !!storedToken)
    console.log('User found:', !!storedUser)
    
    if (storedToken && storedUser) {
      token.value = storedToken
      try {
        user.value = JSON.parse(storedUser)
        console.log('Auth initialized successfully for user:', user.value?.name, 'Role:', user.value?.role)
      } catch (error) {
        console.error('Error parsing stored user:', error)
        logout()
      }
    } else {
      console.log('No stored authentication found')
    }
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    isStudent,
    isProfessor,
    login,
    logout,
    initAuth,
    getDashboardRoute
  }
})
