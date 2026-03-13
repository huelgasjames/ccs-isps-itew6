<template>
  <div class="login-container">
    <!-- Main Content - Centered like Facebook -->
    <div class="login-center">
      <!-- Left Side - Branding (like Facebook left side) -->
      <div class="brand-section">
        <div class="logo-block">
          <div class="logo-circle">
            <img src="/image-removebg-preview (1).png" alt="CCS Logo" class="logo-img" />
          </div>
        </div>
        <h1 class="brand-title">College of <span class="accent">Computing</span> Studies</h1>
        <p class="brand-tagline">Comprehensive Profiling and Management System</p>
        <div class="brand-stats">
          <div class="stat-item">
            <span class="stat-number">SYS</span>
            <span class="stat-label">ONLINE</span>
          </div>
          <div class="stat-separator">|</div>
          <div class="stat-item">
            <span class="stat-number">v2.0</span>
            <span class="stat-label">BUILD</span>
          </div>
        </div>
      </div>

      <!-- Right Side - Login Card (like Facebook login card) -->
      <div class="login-card">
        <div class="card-header">
          <div class="card-eyebrow">CCS DEPARTMENT</div>
          <h2 class="card-title">Welcome Back</h2>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="input-group">
            <input
              v-model="form.email"
              type="text"
              class="login-input"
              placeholder="Email or Student ID"
              autocomplete="off"
              required
            />
          </div>

          <div class="input-group">
            <input
              v-model="form.password"
              type="password"
              class="login-input"
              placeholder="Password"
              required
            />
          </div>

          <button type="submit" :disabled="loading" class="login-btn">
            <span v-if="loading" class="btn-spinner"></span>
            <span v-else>Log In</span>
          </button>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>
        </form>
      </div>
    </div>

    <!-- Footer -->
    <div class="login-footer">
      <div class="footer-content">
        <span class="footer-brand">CCS</span>
        <span class="footer-dot">•</span>
        <span>Comprehensive Profiling System</span>
        <span class="footer-dot">•</span>
        <span>{{ currentYear }}</span>
      </div>
      <button @click="themeStore.toggleTheme()" class="theme-toggle">
        <svg v-if="themeStore.isDark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="1" x2="12" y2="3"/>
          <line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
          <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/>
          <line x1="21" y1="12" x2="23" y2="12"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()
const currentYear = new Date().getFullYear()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  if (!form.value.email.trim() || !form.value.password.trim()) {
    error.value = 'Please enter both email and password.'
    return
  }

  loading.value = true
  error.value = ''
  try {
    const credentials = {
      email: form.value.email.trim(),
      password: form.value.password
    }
    
    await authStore.login(credentials)
    router.push('/dashboard')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Invalid email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.login-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8f9fa;
  font-family: 'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  position: relative;
}

.login-center {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 80px;
  padding: 40px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.brand-section {
  flex: 1;
  max-width: 500px;
  text-align: left;
}

.logo-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.15), rgba(255, 146, 43, 0.1));
  border: 2px solid rgba(253, 126, 20, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.logo-img {
  width: 50px;
  height: 50px;
  object-fit: contain;
}

.brand-title {
  font-size: 2.8rem;
  font-weight: 700;
  color: #333;
  line-height: 1.2;
  margin-bottom: 12px;
}

.brand-title .accent {
  color: #fd7e14;
}

.brand-tagline {
  font-size: 1.15rem;
  color: #666;
  margin-bottom: 30px;
  line-height: 1.5;
}

.login-card {
  width: 400px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  padding: 25px;
  border: 1px solid #e9ecef;
}

.card-header {
  text-align: center;
  margin-bottom: 25px;
}

.card-eyebrow {
  font-size: 0.7rem;
  color: #fd7e14;
  letter-spacing: 0.2em;
  font-weight: 600;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.card-title {
  font-size: 1.6rem;
  font-weight: 700;
  color: #333;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.login-input {
  width: 100%;
  height: 52px;
  padding: 14px 16px;
  font-size: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f8f9fa;
  color: #333;
  outline: none;
  transition: all 0.2s;
  font-family: inherit;
}

.login-input:focus {
  border-color: #fd7e14;
  background: #fff;
  box-shadow: 0 0 0 2px rgba(253, 126, 20, 0.15);
}

.login-input::placeholder {
  color: #999;
}

.login-btn {
  width: 100%;
  height: 48px;
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 5px;
}

.login-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #e56a0a, #fd7e14);
  box-shadow: 0 4px 15px rgba(253, 126, 20, 0.4);
  transform: translateY(-1px);
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-spinner {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-message {
  background: #fff5f5;
  border: 1px solid #feb2b2;
  color: #c53030;
  padding: 12px;
  border-radius: 8px;
  font-size: 0.9rem;
  text-align: center;
}

.login-footer {
  padding: 25px;
  text-align: center;
  border-top: 1px solid #e9ecef;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
}

.footer-content {
  color: #888;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.footer-brand {
  font-weight: 700;
  color: #fd7e14;
  letter-spacing: 0.1em;
}

.footer-dot {
  color: rgba(253, 126, 20, 0.5);
}

.theme-toggle {
  width: 36px;
  height: 36px;
  background: transparent;
  border: 1px solid rgba(253, 126, 20, 0.3);
  border-radius: 50%;
  color: #fd7e14;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.theme-toggle:hover {
  background: rgba(253, 126, 20, 0.1);
  border-color: #fd7e14;
}

.theme-toggle svg {
  width: 18px;
  height: 18px;
}

@media (max-width: 900px) {
  .login-center {
    flex-direction: column;
    gap: 40px;
    padding: 30px 20px;
  }

  .brand-section {
    text-align: center;
    max-width: 100%;
  }

  .logo-block {
    display: flex;
    justify-content: center;
  }

  .login-card {
    width: 100%;
    max-width: 400px;
  }
}

/* Dark Mode Styles */
.dark .login-container {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.dark .brand-title {
  color: #f9fafb;
}

.dark .brand-tagline {
  color: #9ca3af;
}

.dark .login-card {
  background: #1e293b;
  border-color: #334155;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
}

.dark .card-title {
  color: #f9fafb;
}

.dark .login-input {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .login-input:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.dark .login-btn {
  background: #f97316;
}

.dark .login-btn:hover {
  background: #ea580c;
}

.dark .login-footer {
  border-top-color: #334155;
}

.dark .footer-content {
  color: #9ca3af;
}

.dark .theme-toggle {
  border-color: rgba(249, 115, 22, 0.3);
  color: #f97316;
}

.dark .theme-toggle:hover {
  background: rgba(249, 115, 22, 0.1);
  border-color: #f97316;
}

.dark .error-message {
  background: rgba(239, 68, 68, 0.1);
  border-color: rgba(239, 68, 68, 0.3);
  color: #f87171;
}

@media (max-width: 480px) {
  .brand-title {
    font-size: 2rem;
  }

  .login-card {
    padding: 20px;
  }
}
</style>