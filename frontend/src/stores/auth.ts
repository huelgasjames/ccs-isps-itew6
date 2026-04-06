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
      // Check for hardcoded demo credentials for different roles
      const demoCredentials = [
        // Admin credentials
        {
          email: 'admin',
          password: 'password',
          user: {
            id: 1,
            email: 'admin@ccs.edu',
            name: 'Demo Admin',
            role: 'admin' as const,
            department: 'College of Computing Studies',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          },
          token: 'demo-admin-token-' + Date.now()
        },
        // Professor credentials
        {
          email: 'professor',
          password: 'password',
          user: {
            id: 2,
            email: 'professor@ccs.edu',
            name: 'Demo Professor',
            role: 'professor' as const,
            department: 'College of Computing Studies',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          },
          token: 'demo-professor-token-' + Date.now()
        },
        // Student credentials
        {
          email: 'student',
          password: 'password',
          user: {
            id: 3,
            email: 'student@ccs.edu',
            name: 'Demo Student',
            role: 'student' as const,
            department: 'College of Computing Studies',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          },
          token: 'demo-student-token-' + Date.now()
        },
        // Maria - Specific student account
        {
          email: 'maria',
          password: 'password',
          user: {
            id: 4,
            email: 'maria@ccs.edu',
            name: 'Maria Santos',
            role: 'student' as const,
            department: 'College of Computing Studies',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          },
          token: 'demo-maria-token-' + Date.now()
        }
      ]

      // Check if credentials match any demo account
      const demoAccount = demoCredentials.find(
        cred => cred.email === credentials.email && cred.password === credentials.password
      )

      if (demoAccount) {
        user.value = demoAccount.user
        token.value = demoAccount.token
        
        localStorage.setItem('auth_token', demoAccount.token)
        localStorage.setItem('user', JSON.stringify(demoAccount.user))
        
        return { user: demoAccount.user, token: demoAccount.token }
      }
      
      // Real backend login only
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

  const initAuth = () => {
    const storedToken = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('user')
    
    if (storedToken && storedUser) {
      token.value = storedToken
      try {
        user.value = JSON.parse(storedUser)
      } catch (error) {
        console.error('Error parsing stored user:', error)
        logout()
      }
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
    initAuth
  }
})
