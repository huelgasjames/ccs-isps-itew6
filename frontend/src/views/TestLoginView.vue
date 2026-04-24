<template>
  <div class="test-login">
    <h1>Admin Login Test</h1>
    <div class="test-info">
      <p><strong>Admin Credentials:</strong></p>
      <p>Email: admin@ccs.edu</p>
      <p>Password: password</p>
    </div>
    
    <div class="test-steps">
      <h3>Test Steps:</h3>
      <ol>
        <li>Go to <router-link to="/login">/login</router-link></li>
        <li>Enter admin@ccs.edu and password</li>
        <li>Click "Log In"</li>
        <li>Should redirect to <router-link to="/dashboard">/dashboard</router-link></li>
        <li>Dashboard should show "Administrator" role</li>
      </ol>
    </div>
    
    <div class="current-auth">
      <h3>Current Authentication Status:</h3>
      <p><strong>Is Authenticated:</strong> {{ authStore.isAuthenticated }}</p>
      <p><strong>User:</strong> {{ authStore.user?.name }} ({{ authStore.user?.role }})</p>
      <p><strong>Is Admin:</strong> {{ authStore.isAdmin }}</p>
      <p><strong>Is Demo Mode:</strong> {{ authStore.isDemoMode }}</p>
    </div>
    
    <div class="actions">
      <button @click="testLogin" class="test-btn">Test Admin Login</button>
      <button @click="testLogout" class="test-btn logout">Logout</button>
      <router-link to="/dashboard" class="test-btn">Go to Dashboard</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const testLogin = async () => {
  try {
    await authStore.login({
      email: 'admin@ccs.edu',
      password: 'password'
    })
    console.log('Login successful!')
    router.push('/dashboard')
  } catch (error) {
    console.error('Login failed:', error)
    alert('Login failed: ' + (error as any).response?.data?.message || 'Unknown error')
  }
}

const testLogout = async () => {
  await authStore.logout()
  console.log('Logged out')
}
</script>

<style scoped>
.test-login {
  padding: 2rem;
  max-width: 800px;
  margin: 0 auto;
}

.test-info, .test-steps, .current-auth {
  background: #f9fafb;
  padding: 1.5rem;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #e5e7eb;
}

.test-info p, .test-steps li, .current-auth p {
  margin: 0.5rem 0;
}

.actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.test-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
  border: 1px solid #3b82f6;
  background: #3b82f6;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.test-btn:hover {
  background: #2563eb;
}

.test-btn.logout {
  background: #ef4444;
  border-color: #ef4444;
}

.test-btn.logout:hover {
  background: #dc2626;
}
</style>
