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
  const isDemoMode = computed(() => token.value ? token.value.startsWith('demo-') : false)

  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    try {
      // Map usernames to emails for demo accounts
      const emailMap: Record<string, string> = {
        'admin': 'admin@ccs.edu',
        'professor': 'professor@ccs.edu', 
        'student': 'student@ccs.edu',
        'maria': 'maria@ccs.edu'
      }
      
      // Convert username to email if needed
      const normalizedCredentials = {
        ...credentials,
        email: emailMap[credentials.email] || credentials.email
      }
      
      // Check for hardcoded demo credentials for different roles
      const demoCredentials = [
        // Admin credentials - username only
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
        // Admin credentials - full email
        {
          email: 'admin@ccs.edu',
          password: 'password',
          user: {
            id: 1,
            email: 'admin@ccs.edu',
            name: 'System Administrator',
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
        },
              ]

      // Check if credentials match any demo account (using original input)
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
      const response = await authService.login(normalizedCredentials)
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
        return '/dashboard' // Future: '/professor/dashboard'
      case 'student':
        return '/dashboard' // Future: '/student/dashboard'
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
    isDemoMode,
    login,
    logout,
    initAuth,
    getDashboardRoute
  }
})
