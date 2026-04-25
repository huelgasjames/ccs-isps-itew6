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
      path: '/student/dashboard',
      name: 'student-dashboard',
      component: () => import('../views/StudentDashboardView.vue'),
      meta: { requiresAuth: true, roles: ['student'] }
    },
    {
      path: '/faculty/dashboard',
      name: 'faculty-dashboard',
      component: () => import('../views/FacultyDashboardView.vue'),
      meta: { requiresAuth: true, roles: ['professor'] }
    },
    {
      path: '/students',
      name: 'students',
      component: () => import('../views/students/StudentProfilingView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/students/list',
      name: 'student-list',
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
      name: 'student-profile',
      component: () => import('../views/students/StudentProfileView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/students/:id/detail',
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
      path: '/announcements',
      name: 'announcements',
      component: () => import('../views/AnnouncementsView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/announcements/create',
      name: 'announcement-create',
      component: () => import('../views/announcements/AnnouncementFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/announcements/:id',
      name: 'announcement-detail',
      component: () => import('../views/announcements/AnnouncementDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/announcements/:id/edit',
      name: 'announcement-edit',
      component: () => import('../views/announcements/AnnouncementFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'professor'] }
    },
    {
      path: '/events',
      name: 'events',
      component: () => import('../views/EventsView.vue'),
      meta: { requiresAuth: true }
    },
    // Academic Management Routes
    {
      path: '/courses',
      name: 'courses',
      component: () => import('../views/courses/CourseListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/courses/create',
      name: 'course-create',
      component: () => import('../views/courses/CourseFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/courses/:id',
      name: 'course-detail',
      component: () => import('../views/courses/CourseDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/courses/:id/edit',
      name: 'course-edit',
      component: () => import('../views/courses/CourseFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/syllabi',
      name: 'syllabi',
      component: () => import('../views/syllabi/SyllabusListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/syllabi/create',
      name: 'syllabus-create',
      component: () => import('../views/syllabi/SyllabusFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/syllabi/:id',
      name: 'syllabus-detail',
      component: () => import('../views/syllabi/SyllabusDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/syllabi/:id/edit',
      name: 'syllabus-edit',
      component: () => import('../views/syllabi/SyllabusFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/rooms',
      name: 'rooms',
      component: () => import('../views/rooms/RoomListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/rooms/create',
      name: 'room-create',
      component: () => import('../views/rooms/RoomFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/rooms/:id/edit',
      name: 'room-edit',
      component: () => import('../views/rooms/RoomFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/schedules',
      name: 'schedules',
      component: () => import('../views/schedules/ScheduleListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/schedules/create',
      name: 'schedule-create',
      component: () => import('../views/schedules/ScheduleFormView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] }
    },
    {
      path: '/schedules/:id/edit',
      name: 'schedule-edit',
      component: () => import('../views/schedules/ScheduleFormView.vue'),
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
    {
      path: '/test-login',
      name: 'test-login',
      component: () => import('../views/TestLoginView.vue')
    }
  ]
})

router.beforeEach((to, from) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return '/login'
  }
  
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    const role = authStore.user?.role || 'student'
    const dashboardRoute = role === 'admin' ? '/dashboard' : 
                          role === 'professor' ? '/faculty/dashboard' : 
                          '/student/dashboard'
    return dashboardRoute
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
