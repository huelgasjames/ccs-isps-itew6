<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/students" class="inline-flex items-center text-orange-600 hover:text-orange-900 mr-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Students
            </router-link>
            <h1 class="text-xl font-semibold text-gray-900">Student Details</h1>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              :to="`/students/${student?.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit Student
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600"></div>
        </div>

        <!-- Student Details -->
        <div v-else-if="student" class="space-y-6">
          <!-- Student Info Card -->
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Student Information</h3>
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.student_unique_id }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Email</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.email }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.contact_number || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Program</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.program }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Year Level</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.year_level }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Section</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ student.section }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Academic Status</dt>
                  <dd class="mt-1">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      student.academic_status === 'Regular' ? 'bg-orange-100 text-orange-800' :
                      student.academic_status === 'Probation' ? 'bg-yellow-100 text-yellow-800' :
                      student.academic_status === 'Suspended' ? 'bg-red-100 text-red-800' :
                      'bg-gray-100 text-gray-800'
                    ]">
                      {{ student.academic_status }}
                    </span>
                  </dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">At Risk Status</dt>
                  <dd class="mt-1">
                    <span v-if="student.is_at_risk" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                      ⚠️ At Risk
                    </span>
                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                      ✓ Normal
                    </span>
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Skills, Talents, Sports, Certificates -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Skills -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Skills</h3>
                <div v-if="student.skills && student.skills.length > 0" class="space-y-2">
                  <div v-for="skill in student.skills" :key="skill.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">{{ skill.skill_name }}</span>
                    <span class="text-xs text-gray-500">Skill</span>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-500">No skills recorded</p>
              </div>
            </div>

            <!-- Talents -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Talents</h3>
                <div v-if="student.talents && student.talents.length > 0" class="space-y-2">
                  <div v-for="talent in student.talents" :key="talent.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">{{ talent.talent_name }}</span>
                    <span class="text-xs text-gray-500">Talent</span>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-500">No talents recorded</p>
              </div>
            </div>

            <!-- Sports -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Sports</h3>
                <div v-if="student.sports && student.sports.length > 0" class="space-y-2">
                  <div v-for="sport in student.sports" :key="sport.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">{{ sport.sport_name }}</span>
                    <span class="text-xs text-gray-500">Sport</span>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-500">No sports recorded</p>
              </div>
            </div>

            <!-- Certificates -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Certificates</h3>
                <div v-if="student.certificates && student.certificates.length > 0" class="space-y-2">
                  <div v-for="certificate in student.certificates" :key="certificate.id" class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-900">{{ certificate.certificate_name }}</span>
                      <span class="text-xs text-gray-500">{{ certificate.date_received }}</span>
                    </div>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-500">No certificates recorded</p>
              </div>
            </div>
          </div>

          <!-- Violations -->
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Violations</h3>
              <div v-if="student.violations && student.violations.length > 0" class="space-y-2">
                <div v-for="violation in student.violations" :key="violation.id" class="p-4 border rounded-lg" :class="[
                  violation.status === 'Pending' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50'
                ]">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center">
                        <h4 class="text-sm font-medium text-gray-900">{{ violation.violation_type }}</h4>
                        <span :class="[
                          'ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                          violation.status === 'Pending' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                        ]">
                          {{ violation.status }}
                        </span>
                      </div>
                      <p class="mt-1 text-sm text-gray-600">{{ violation.description }}</p>
                      <p class="mt-1 text-xs text-gray-500">Date: {{ violation.date_committed }}</p>
                      <p v-if="violation.sanction" class="mt-1 text-xs text-gray-500">Sanction: {{ violation.sanction }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <p v-else class="text-sm text-gray-500">No violations recorded</p>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="text-red-600 text-lg font-semibold mb-4">Error loading student data</div>
          <p class="text-gray-600 mb-4">{{ error }}</p>
          <router-link
            to="/students"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700"
          >
            Back to Students
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const student = ref<any>(null)
const loading = ref(true)
const error = ref('')

const fetchStudent = async () => {
  try {
    const response = await api.get(`/students/${route.params.id}`)
    student.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load student data'
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  fetchStudent()
})
</script>
