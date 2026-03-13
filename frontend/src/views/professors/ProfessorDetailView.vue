<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/professors" class="inline-flex items-center text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300 mr-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Professors
            </router-link>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Professor Details</h1>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              :to="`/professors/${professor?.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 dark:focus:ring-offset-gray-800"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit Professor
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

        <!-- Professor Details -->
        <div v-else-if="professor" class="space-y-6">
          <!-- Professor Info Card -->
          <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Professor Information</h3>
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.first_name }} {{ professor.middle_name }} {{ professor.last_name }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Professor ID</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.professor_unique_id }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.email }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Number</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.contact_number || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Age</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.age }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Birthday</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.birthday }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Blood Type</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.blood_type || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.department }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Type</dt>
                  <dd class="mt-1">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      professor.employment_type === 'Full-time' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' :
                      professor.employment_type === 'Part-time' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                      'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                    ]">
                      {{ professor.employment_type }}
                    </span>
                  </dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.role || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Application Date</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.application_date || 'N/A' }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Professional Information -->
          <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Professional Information</h3>
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Educational Attainment</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.educational_attainment || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Experience</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.experience || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-2">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Courses Handled</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.courses_handled || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Organization</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.organization || 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ professor.address || 'N/A' }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="text-red-600 dark:text-red-400 text-lg font-semibold mb-4">Error loading professor data</div>
          <p class="text-gray-600 dark:text-gray-400 mb-4">{{ error }}</p>
          <router-link
            to="/professors"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600"
          >
            Back to Professors
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

const professor = ref<any>(null)
const loading = ref(true)
const error = ref('')

const fetchProfessor = async () => {
  try {
    const response = await api.get(`/professors/${route.params.id}`)
    professor.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load professor data'
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  fetchProfessor()
})
</script>
