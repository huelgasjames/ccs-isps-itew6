import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/DashboardView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/students',
      name: 'students',
      component: () => import('../views/students/StudentListView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/students/create',
      name: 'student-create',
      component: () => import('../views/students/StudentFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/students/:id',
      name: 'student-detail',
      component: () => import('../views/students/StudentDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/students/:id/edit',
      name: 'student-edit',
      component: () => import('../views/students/StudentFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/professors',
      name: 'professors',
      component: () => import('../views/professors/ProfessorListView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/professors/create',
      name: 'professor-create',
      component: () => import('../views/professors/ProfessorFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/professors/:id',
      name: 'professor-detail',
      component: () => import('../views/professors/ProfessorDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/professors/:id/edit',
      name: 'professor-edit',
      component: () => import('../views/professors/ProfessorFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/violations',
      name: 'violations',
      component: () => import('../views/violations/ViolationListView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/violations/create',
      name: 'violation-create',
      component: () => import('../views/violations/ViolationFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/violations/:id',
      name: 'violation-detail',
      component: () => import('../views/violations/ViolationDetailView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/violations/:id/edit',
      name: 'violation-edit',
      component: () => import('../views/violations/ViolationFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('../views/ProfileView.vue'),
      meta: { requiresAuth: true }
    },
  ],
})

router.beforeEach((to, from) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return '/login'
  }
  
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return '/dashboard'
  }
  
  if (to.meta.roles && Array.isArray(to.meta.roles)) {
    const userRole = authStore.user?.role
    if (!userRole || !(to.meta.roles as string[]).includes(userRole)) {
      return '/dashboard'
    }
  }
  
  return true
})

export default router
