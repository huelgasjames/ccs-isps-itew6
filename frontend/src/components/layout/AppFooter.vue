<template>
  <footer class="app-footer">
    <div class="footer-content">
      <!-- Footer Main -->
      <div class="footer-main">
        <div class="footer-section">
          <h4 class="footer-title">System Information</h4>
          <div class="footer-info">
            <div class="info-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>Version 2.0.1</span>
            </div>
            <div class="info-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="10" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <span>Secure Connection</span>
            </div>
            <div class="info-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
              </svg>
              <span>System Status: Operational</span>
            </div>
          </div>
        </div>

        <div class="footer-section">
          <h4 class="footer-title">Quick Links</h4>
          <div class="footer-links">
            <router-link to="/dashboard" class="footer-link">Dashboard</router-link>
            <router-link to="/students" class="footer-link" v-if="isAdmin">Students</router-link>
            <router-link to="/professors" class="footer-link" v-if="isAdmin">Professors</router-link>
            <router-link to="/violations" class="footer-link" v-if="isAdmin">Violations</router-link>
            <router-link to="/events" class="footer-link">Events</router-link>
            <router-link to="/profile" class="footer-link">Profile</router-link>
          </div>
        </div>

        <div class="footer-section">
          <h4 class="footer-title">Resources</h4>
          <div class="footer-links">
            <a href="#" class="footer-link" @click.prevent="showHelp">Help Center</a>
            <a href="#" class="footer-link" @click.prevent="showDocumentation">Documentation</a>
            <a href="#" class="footer-link" @click.prevent="showSupport">Support</a>
            <a href="#" class="footer-link" @click.prevent="showAPI">API Reference</a>
            <a href="#" class="footer-link" @click.prevent="showChangelog">Changelog</a>
          </div>
        </div>

        <div class="footer-section">
          <h4 class="footer-title">Contact</h4>
          <div class="footer-contact">
            <div class="contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <span>support@ccs.edu</span>
            </div>
            <div class="contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              <span>+63 (2) 1234 5678</span>
            </div>
            <div class="contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <span>CCS Building, University Campus</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <div class="footer-left">
          <p class="copyright">
            © {{ currentYear }} College of Computer Studies. All rights reserved.
          </p>
          <div class="footer-legal">
            <a href="#" class="legal-link" @click.prevent="showPrivacy">Privacy Policy</a>
            <span class="separator">•</span>
            <a href="#" class="legal-link" @click.prevent="showTerms">Terms of Service</a>
            <span class="separator">•</span>
            <a href="#" class="legal-link" @click.prevent="showCookies">Cookie Policy</a>
          </div>
        </div>

        <div class="footer-right">
          <div class="social-links">
            <a href="#" class="social-link" title="Facebook">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a href="#" class="social-link" title="Twitter">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a href="#" class="social-link" title="LinkedIn">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
            </a>
            <a href="#" class="social-link" title="GitHub">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const isAdmin = computed(() => authStore.isAdmin)

const currentYear = computed(() => new Date().getFullYear())

// Footer action handlers
const showHelp = () => {
  console.log('Opening help center...')
  // Implement help center modal or navigation
}

const showDocumentation = () => {
  console.log('Opening documentation...')
  // Implement documentation modal or navigation
}

const showSupport = () => {
  console.log('Opening support...')
  // Implement support modal or navigation
}

const showAPI = () => {
  console.log('Opening API reference...')
  // Implement API documentation modal or navigation
}

const showChangelog = () => {
  console.log('Opening changelog...')
  // Implement changelog modal or navigation
}

const showPrivacy = () => {
  console.log('Opening privacy policy...')
  // Implement privacy policy modal or navigation
}

const showTerms = () => {
  console.log('Opening terms of service...')
  // Implement terms of service modal or navigation
}

const showCookies = () => {
  console.log('Opening cookie policy...')
  // Implement cookie policy modal or navigation
}
</script>

<style scoped>
.app-footer {
  background: #ffffff;
  border-top: 1px solid #e5e7eb;
  margin-top: auto;
}

.dark .app-footer {
  background: #1e293b;
  border-top-color: #334155;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

/* Footer Main */
.footer-main {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.footer-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dark .footer-title {
  color: #f9fafb;
}

/* Footer Info */
.footer-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
  color: #6b7280;
}

.dark .info-item {
  color: #9ca3af;
}

.info-item svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  color: #f97316;
}

/* Footer Links */
.footer-links {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.footer-link {
  font-size: 0.875rem;
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.dark .footer-link {
  color: #9ca3af;
}

.footer-link:hover {
  color: #f97316;
}

/* Footer Contact */
.footer-contact {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
  color: #6b7280;
}

.dark .contact-item {
  color: #9ca3af;
}

.contact-item svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  color: #f97316;
}

/* Footer Bottom */
.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.dark .footer-bottom {
  border-color: #334155;
}

.footer-left {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.copyright {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.dark .copyright {
  color: #9ca3af;
}

.footer-legal {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
}

.legal-link {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.dark .legal-link {
  color: #9ca3af;
}

.legal-link:hover {
  color: #f97316;
}

.separator {
  color: #d1d5db;
}

.dark .separator {
  color: #4b5563;
}

/* Social Links */
.social-links {
  display: flex;
  gap: 0.75rem;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f3f4f6;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s;
}

.dark .social-link {
  background: #374151;
  color: #9ca3af;
}

.social-link:hover {
  background: #f97316;
  color: white;
  transform: translateY(-2px);
}

.social-link svg {
  width: 18px;
  height: 18px;
}

/* Responsive */
@media (max-width: 1024px) {
  .footer-main {
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }
  
  .footer-content {
    padding: 1.75rem 1.25rem;
  }
}

@media (max-width: 768px) {
  .footer-content {
    padding: 1.5rem 1rem;
  }

  .footer-main {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .footer-legal {
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .info-item, .contact-item {
    font-size: 0.8rem;
  }
  
  .footer-link {
    font-size: 0.8rem;
  }
}

@media (max-width: 640px) {
  .footer-content {
    padding: 1.25rem 0.75rem;
  }
  
  .footer-main {
    gap: 1.25rem;
  }
  
  .footer-section {
    gap: 0.75rem;
  }
  
  .footer-title {
    font-size: 0.8rem;
  }
  
  .info-item, .contact-item {
    font-size: 0.75rem;
    gap: 6px;
  }
  
  .info-item svg, .contact-item svg {
    width: 14px;
    height: 14px;
  }
  
  .footer-link {
    font-size: 0.75rem;
  }
  
  .copyright {
    font-size: 0.75rem;
  }
  
  .footer-legal {
    font-size: 0.7rem;
  }
}

@media (max-width: 480px) {
  .footer-content {
    padding: 1rem 0.5rem;
  }

  .social-links {
    justify-content: center;
  }
  
  .social-link {
    width: 32px;
    height: 32px;
  }
  
  .social-link svg {
    width: 16px;
    height: 16px;
  }

  .footer-left {
    align-items: center;
    text-align: center;
  }
  
  .footer-bottom {
    align-items: center;
  }
}
</style>
