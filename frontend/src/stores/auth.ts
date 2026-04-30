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
  const isDemoMode = computed(() => token.value?.startsWith('demo-') || false)
  
  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    try {
      // Check if this is a demo login attempt
      const demoCredentials = [
        'admin@ccsd.edu',
        'faculty@ccsd.edu',
        'student@ccsd.edu',
        'registrar@ccsd.edu',
        'counselor@ccsd.edu',
        'parent@ccsd.edu',
        'demo@ccsd.edu'
      ]
      
      const demoPasswords: Record<string, string> = {
        'admin@ccsd.edu': 'demopass',
        'faculty@ccsd.edu': 'demopass',
        'student@ccsd.edu': 'demopass',
        'registrar@ccsd.edu': 'demopass',
        'counselor@ccsd.edu': 'demopass',
        'parent@ccsd.edu': 'demopass',
        'demo@ccsd.edu': 'demopass'
      }
      
      if (demoCredentials.includes(credentials.email) && demoPasswords[credentials.email] === credentials.password) {
        // Demo authentication - handle client-side
        let role = 'admin'
        let name = 'Demo User'
        
        if (credentials.email === 'admin@ccsd.edu') {
          role = 'admin'
          name = 'System Administrator'
        } else if (credentials.email === 'faculty@ccsd.edu') {
          role = 'professor'
          name = 'Faculty Member'
        } else if (credentials.email === 'student@ccsd.edu') {
          role = 'student'
          name = 'Student'
        } else if (credentials.email === 'registrar@ccsd.edu') {
          role = 'admin'
          name = 'Registrar'
        } else if (credentials.email === 'counselor@ccsd.edu') {
          role = 'admin'
          name = 'Counselor'
        } else if (credentials.email === 'parent@ccsd.edu') {
          role = 'student'
          name = 'Parent'
        } else if (credentials.email === 'demo@ccsd.edu') {
          role = 'admin'
          name = 'Demo User'
        }
        
        // Create demo token
        const demoToken = `demo-${role}-${Date.now()}`
        const demoUser = {
          id: 1,
          name: name,
          email: credentials.email,
          role: role as 'admin' | 'student' | 'professor',
          department: 'CCS Department',
          permissions: ['full_demo']
        }
        
        // Set auth state
        user.value = demoUser
        token.value = demoToken
        
        // Store in localStorage
        localStorage.setItem('auth_token', demoToken)
        localStorage.setItem('demo_user', JSON.stringify(demoUser))
        
        // Initialize demo data
        import('@/services/offline').then(({ offlineService }) => {
          offlineService.initializeDummyData()
        })
        
        return {
          user: demoUser,
          token: demoToken,
          role: role
        }
      }
      
      // Normal backend authentication
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
      localStorage.removeItem('demo_user')
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
    
    console.log('Initializing auth...')
    console.log('Token found:', !!storedToken)
    
    if (storedToken) {
      token.value = storedToken
      
      // Check if it's a demo token
      const isDemo = storedToken.startsWith('demo-')
      
      if (isDemo) {
        // Load demo user from localStorage
        const storedDemoUser = localStorage.getItem('demo_user')
        if (storedDemoUser) {
          try {
            const demoUser = JSON.parse(storedDemoUser)
            user.value = {
              id: demoUser.id,
              name: demoUser.name,
              email: demoUser.email,
              role: demoUser.role as 'admin' | 'student' | 'professor',
              department: demoUser.department || 'CCS Department'
            }
            console.log('Demo auth initialized successfully for user:', user.value?.name, 'Role:', user.value?.role)
          } catch (error) {
            console.error('Error parsing stored demo user:', error)
            logout()
          }
        }
      } else {
        // Load regular user from localStorage
        const storedUser = localStorage.getItem('user')
        if (storedUser) {
          try {
            user.value = JSON.parse(storedUser)
            console.log('Auth initialized successfully for user:', user.value?.name, 'Role:', user.value?.role)
          } catch (error) {
            console.error('Error parsing stored user:', error)
            logout()
          }
        }
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
