<template>
  <div class="login-container">
    <!-- Animated background -->
    <div class="bg-grid"></div>
    <div class="bg-scanlines"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Left Panel -->
    <div class="login-left">
      <div class="left-content">
        <div class="hex-logo">
          <div class="hex-ring hex-ring-outer"></div>
          <div class="hex-ring hex-ring-mid"></div>
          <div class="hex-inner">
            <img src="/image-removebg-preview (1).png" alt="University Logo" class="logo-img" />
          </div>
        </div>
        <div class="left-text">
          <div class="sys-tag">SYS://CCS.EDU.PH</div>
          <h1>College of<br /><span class="accent">Computing</span><br />Studies</h1>
          <div class="divider-line"></div>
          <p class="subtitle">Department Management System</p>
          <div class="status-bar">
            <span class="status-dot"></span>
            <span class="status-text">SYSTEM ONLINE — v1.0</span>
          </div>
        </div>
        <div class="corner-deco tl"></div>
        <div class="corner-deco tr"></div>
        <div class="corner-deco bl"></div>
        <div class="corner-deco br"></div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="login-right">
      <div class="login-form-container">
        <div class="form-header">
          <img src="/ccs-white-removebg-preview.png" alt="CCS Logo" class="form-logo" />
          <div class="form-title-block">
            <div class="form-eyebrow">ENTER YOUR PINNACLE ACCOUNT</div>
            <h2 class="form-title">DEPARTMENT LOGIN</h2>
          </div>
        </div>

        <div class="terminal-line">
          <span class="terminal-prompt">$</span>
          <span class="terminal-cmd" id="typewriter"></span>
          <span class="cursor-blink">▋</span>
        </div>

        <form @submit.prevent="handleLogin" novalidate class="login-form">
          <div class="field-group">
            <label class="field-label">Username</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input
                v-model="form.email"
                type="text"
                class="field-input"
                placeholder="e.g. 2001746"
                autocomplete="off"
                required
              />
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">Password</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input
                v-model="form.password"
                type="password"
                class="field-input"
                placeholder="••••••••••"
                required
              />
            </div>
          </div>

          <div class="form-meta">
            <a href="#" class="forgot-link">FORGOT CREDENTIALS?</a>
          </div>

          <div v-if="error" class="error-alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="alert-icon"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ error }}
          </div>

          <button type="submit" :disabled="loading" class="submit-btn">
            <span v-if="loading" class="btn-spinner"></span>
            <span v-if="loading">AUTHENTICATING...</span>
            <span v-else>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="btn-icon"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
              LOGIN
            </span>
          </button>
        </form>

        <div class="form-footer">
          <span>AUTHORIZED PERSONNEL ONLY</span>
          <span class="footer-dot">◆</span>
          <span>CCS // {{ currentYear }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const currentYear = new Date().getFullYear()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await authStore.login(form.value)
    router.push('/dashboard')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Authentication failed. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const commands = ['initializing system...', 'loading protocols...', 'awaiting credentials_']
  let ci = 0, ci2 = 0
  const el = document.getElementById('typewriter')
  const type = () => {
    if (!el) return
    const cmd = commands[ci % commands.length]
    if (ci2 <= cmd.length) {
      el.textContent = cmd.slice(0, ci2++)
      setTimeout(type, 55)
    } else {
      setTimeout(() => { ci++; ci2 = 0; type() }, 1800)
    }
  }
  type()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.login-container {
  min-height: 100vh;
  display: flex;
  background: #f8f9fa;
  font-family: 'Rajdhani', sans-serif;
  position: relative;
  overflow: hidden;
}

/* ── Backgrounds ── */
.bg-grid {
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(253, 126, 20, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(253, 126, 20, 0.04) 1px, transparent 1px);
  background-size: 40px 40px;
}

.bg-scanlines {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background: repeating-linear-gradient(
    0deg,
    transparent,
    transparent 2px,
    rgba(0,0,0,0.03) 2px,
    rgba(0,0,0,0.03) 4px
  );
}

.orb {
  position: fixed; border-radius: 50%; filter: blur(80px); z-index: 0; pointer-events: none;
}
.orb-1 {
  width: 400px; height: 400px;
  background: radial-gradient(circle, rgba(253, 126, 20, 0.08) 0%, transparent 70%);
  top: -100px; left: -100px;
  animation: orbFloat 8s ease-in-out infinite alternate;
}
.orb-2 {
  width: 300px; height: 300px;
  background: radial-gradient(circle, rgba(255, 146, 43, 0.06) 0%, transparent 70%);
  bottom: -80px; right: 30%;
  animation: orbFloat 11s ease-in-out infinite alternate-reverse;
}
@keyframes orbFloat {
  from { transform: translate(0, 0); }
  to { transform: translate(40px, 30px); }
}

/* ── Left Panel ── */
.login-left {
  flex: 1;
  display: flex; align-items: center; justify-content: center;
  position: relative; z-index: 1;
  padding: 3rem;
  border-right: 1px solid rgba(253, 126, 20, 0.15);
  background: linear-gradient(160deg, rgba(248, 249, 250, 0.9) 0%, rgba(233, 236, 239, 0.95) 100%);
}

.left-content {
  position: relative;
  max-width: 380px;
  width: 100%;
}

/* Corner decorations */
.corner-deco {
  position: absolute; width: 20px; height: 20px;
  border-color: rgba(253, 126, 20, 0.5); border-style: solid;
}
.corner-deco.tl { top: -20px; left: -20px; border-width: 2px 0 0 2px; }
.corner-deco.tr { top: -20px; right: -20px; border-width: 2px 2px 0 0; }
.corner-deco.bl { bottom: -20px; left: -20px; border-width: 0 0 2px 2px; }
.corner-deco.br { bottom: -20px; right: -20px; border-width: 0 2px 2px 0; }

/* Hex logo */
.hex-logo {
  position: relative; width: 130px; height: 130px;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 2.5rem;
}

.hex-ring {
  position: absolute; border-radius: 50%;
  border: 1px solid rgba(253, 126, 20, 0.3);
}
.hex-ring-outer {
  inset: 0;
  animation: spinRing 12s linear infinite;
  border-style: dashed;
  border-color: rgba(253, 126, 20, 0.2);
}
.hex-ring-mid {
  inset: 12px;
  animation: spinRing 8s linear infinite reverse;
  border-color: rgba(255, 146, 43, 0.3);
}
@keyframes spinRing {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.hex-inner {
  width: 80px; height: 80px; border-radius: 50%;
  background: rgba(248, 249, 250, 0.9);
  border: 1px solid rgba(253, 126, 20, 0.5);
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 20px rgba(253, 126, 20, 0.2), inset 0 0 20px rgba(253, 126, 20, 0.05);
  z-index: 1;
}
.logo-img { width: 55px; height: 55px; object-fit: contain; filter: brightness(1.2); }

/* Left text */
.sys-tag {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.72rem; color: rgba(253, 126, 20, 0.6);
  letter-spacing: 0.15em; margin-bottom: 1rem;
}
.left-text h1 {
  font-size: 2.6rem; line-height: 1.1; font-weight: 700;
  color: #333333; letter-spacing: 0.02em;
}
.left-text h1 .accent {
  color: transparent;
  background: linear-gradient(90deg, #fd7e14, #ff922b);
  -webkit-background-clip: text; background-clip: text;
}
.divider-line {
  width: 60px; height: 2px;
  background: linear-gradient(90deg, #fd7e14, transparent);
  margin: 1.2rem 0;
}
.subtitle {
  font-size: 1rem; color: rgba(51, 51, 51, 0.6);
  font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase;
  margin-bottom: 2rem;
}
.status-bar {
  display: flex; align-items: center; gap: 8px;
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.7rem; color: rgba(253, 126, 20, 0.5);
}
.status-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #fd7e14;
  box-shadow: 0 0 8px #fd7e14;
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; } 50% { opacity: 0.4; }
}

/* ── Right Panel ── */
.login-right {
  width: 460px; min-width: 420px;
  display: flex; align-items: center; justify-content: center;
  padding: 3rem 2.5rem;
  background: rgba(248, 249, 250, 0.97);
  position: relative; z-index: 1;
}

.login-form-container { width: 100%; }

/* Form header */
.form-header {
  display: flex; align-items: center; gap: 1rem;
  margin-bottom: 1.8rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid rgba(253, 126, 20, 0.1);
}
.form-logo {
  width: 56px; height: 56px; object-fit: contain;
  filter: drop-shadow(0 0 8px rgba(253, 126, 20, 0.4));
}
.form-eyebrow {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.65rem; color: rgba(253, 126, 20, 0.5);
  letter-spacing: 0.2em; margin-bottom: 4px;
}
.form-title {
  font-size: 1.4rem; font-weight: 700;
  color: #333333; letter-spacing: 0.15em;
}

/* Terminal line */
.terminal-line {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.78rem;
  color: rgba(253, 126, 20, 0.5);
  margin-bottom: 1.8rem;
  padding: 8px 12px;
  background: rgba(253, 126, 20, 0.03);
  border: 1px solid rgba(253, 126, 20, 0.08);
  border-radius: 4px;
  display: flex; align-items: center; gap: 6px;
}
.terminal-prompt { color: #fd7e14; }
.cursor-blink {
  color: rgba(253, 126, 20, 0.7);
  animation: blink 1s step-end infinite;
}
@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

/* Fields */
.field-group { margin-bottom: 1.4rem; }
.field-label {
  display: block;
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.65rem; color: rgba(253, 126, 20, 0.5);
  letter-spacing: 0.2em; margin-bottom: 8px;
}
.field-wrap {
  position: relative;
}
.field-icon {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px; color: rgba(253, 126, 20, 0.4);
  pointer-events: none;
}
.field-input {
  width: 100%; height: 50px;
  background: rgba(233, 236, 239, 0.6);
  border: 1px solid rgba(253, 126, 20, 0.2);
  border-radius: 4px;
  color: #333333;
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.9rem;
  padding: 0 16px 0 44px;
  transition: all 0.2s;
  outline: none;
}
.field-input::placeholder { color: rgba(153, 153, 153, 0.3); }
.field-input:focus {
  border-color: rgba(253, 126, 20, 0.6);
  background: rgba(248, 249, 250, 0.7);
  box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.08), inset 0 0 20px rgba(253, 126, 20, 0.03);
}

.form-meta { text-align: right; margin-bottom: 1.5rem; }
.forgot-link {
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.65rem; color: rgba(253, 126, 20, 0.6);
  letter-spacing: 0.1em; text-decoration: none;
  transition: color 0.2s;
}
.forgot-link:hover { color: rgba(253, 126, 20, 0.9); }

/* Error */
.error-alert {
  display: flex; align-items: center; gap: 10px;
  background: rgba(255, 50, 50, 0.07);
  border: 1px solid rgba(255, 50, 50, 0.3);
  color: #ff8888;
  border-radius: 4px; padding: 12px 14px;
  font-size: 0.85rem; margin-bottom: 1.2rem;
}
.alert-icon { width: 16px; height: 16px; flex-shrink: 0; }

/* Submit */
.submit-btn {
  width: 100%; height: 52px;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.15), rgba(255, 146, 43, 0.1));
  border: 1px solid rgba(253, 126, 20, 0.4);
  border-radius: 4px;
  color: #fd7e14;
  font-family: 'Rajdhani', sans-serif;
  font-size: 1rem; font-weight: 700;
  letter-spacing: 0.2em;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  transition: all 0.25s;
  position: relative; overflow: hidden;
}
.submit-btn::before {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.1), transparent);
  opacity: 0; transition: opacity 0.25s;
}
.submit-btn:hover:not(:disabled) {
  border-color: rgba(253, 126, 20, 0.8);
  color: #fff;
  box-shadow: 0 0 25px rgba(253, 126, 20, 0.2), inset 0 0 20px rgba(253, 126, 20, 0.05);
}
.submit-btn:hover:not(:disabled)::before { opacity: 1; }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-icon { width: 16px; height: 16px; }
.btn-spinner {
  width: 16px; height: 16px; border-radius: 50%;
  border: 2px solid rgba(253, 126, 20, 0.2);
  border-top-color: #fd7e14;
  animation: spinRing 0.7s linear infinite;
}

/* Footer */
.form-footer {
  text-align: center; margin-top: 2rem;
  font-family: 'Share Tech Mono', monospace;
  font-size: 0.6rem; color: rgba(153, 153, 153, 0.3);
  letter-spacing: 0.15em;
  display: flex; align-items: center; justify-content: center; gap: 10px;
}
.footer-dot { color: rgba(253, 126, 20, 0.3); }

/* ── Responsive ── */
@media (max-width: 900px) {
  .login-container { flex-direction: column; }
  .login-left { border-right: none; border-bottom: 1px solid rgba(253, 126, 20, 0.15); padding: 2.5rem 2rem; }
  .login-right { width: 100%; min-width: unset; padding: 2.5rem 1.5rem; }
  .left-text h1 { font-size: 2rem; }
}
</style>