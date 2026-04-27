<template>
  <div class="reports-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Reports</h1>
          <p>Generate and view system reports and analytics</p>
        </div>
        <div class="header-actions">
          <router-link to="/dashboard" class="btn btn-secondary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M3 9l9-9 9-9-9 2 16l-7 7-7-7 16z"/>
            </svg>
            Back to Dashboard
          </router-link>
        </div>
      </div>
    </div>

    <!-- Coming Soon Content -->
    <div class="coming-soon-container">
      <div class="coming-soon-card">
        <div class="icon-wrapper">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="coming-soon-icon">
            <path d="M9 11H12M17 13l-5 5-5 5-5"/>
            <path d="M12 2v10M8 6h8M8 10h8"/>
            <path d="M3 13h18"/>
          </svg>
        </div>
        <h2>Reports Coming Soon</h2>
        <p class="coming-soon-description">
          The comprehensive reporting system is under development. This feature will provide:
        </p>
        
        <div class="features-grid">
          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 19v-6a2 2 0 0 0-2 2H5a2 2 0 0 2v6"/>
                <path d="M20.49 15a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
            </div>
            <h3>Room Utilization Reports</h3>
            <p>Analytics on room usage and capacity</p>
          </div>
          
          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5v11c0 1.1.1.1h8v2c0 1.1-.9.9-.9H4a2 2 0 0 0-2v-2h16v12z"/>
                <path d="M12 6v2a2 2 0 0 0-2h-2v8a2 2 0 0 0 2h8a2 2 0 0 0 2v2z"/>
              </svg>
            </div>
            <h3>Schedule Analytics</h3>
            <p>Comprehensive scheduling and conflict reports</p>
          </div>
          
          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 8H8a2 2 0 0 0-2h8v2a2 2 0 0 0 2h8a2 2 0 0 0 2v2h8a2 2 0 0 0 2v2z"/>
                <path d="M12 2v10M12 18v2"/>
              </svg>
            </div>
            <h3>Student Enrollment Reports</h3>
            <p>Detailed enrollment statistics and trends</p>
          </div>
          
          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 19v-6a2 2 0 0 0-2 2H5a2 2 0 0 2v6"/>
                <path d="M20.49 15a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
            </div>
            <h3>Financial Reports</h3>
            <p>Revenue and expense tracking</p>
          </div>
        </div>
        
        <div class="coming-soon-footer">
          <p class="release-info">
            <strong>Expected Release:</strong> Next Major Update<br>
            <strong>Status:</strong> In Development
          </p>
          <div class="progress-bar">
            <div class="progress-fill" style="width: 30%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'

onMounted(() => {
  console.log('Reports page loaded')
})
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.reports-view {
  min-height: 100vh;
  background: #f8f9fa;
  font-family: 'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding: 40px;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, #fff, #f8f9fa);
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(253, 126, 20, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.header-left h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-bottom: 8px;
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.header-left p {
  color: #6b7280;
  font-size: 1.1rem;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
}

.btn-secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
}

.btn-small {
  padding: 10px 16px;
  font-size: 0.9rem;
}

.icon-small {
  width: 16px;
  height: 16px;
}

/* Coming Soon Container */
.coming-soon-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 200px);
}

.coming-soon-card {
  background: white;
  border-radius: 20px;
  padding: 48px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
  border: 2px solid rgba(253, 126, 20, 0.2);
  text-align: center;
  max-width: 600px;
  transition: all 0.3s ease;
}

.coming-soon-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.icon-wrapper {
  margin-bottom: 24px;
}

.coming-soon-icon {
  width: 80px;
  height: 80px;
  color: #fd7e14;
  opacity: 0.8;
}

.coming-soon h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 16px;
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.coming-soon-description {
  color: #6b7280;
  font-size: 1.1rem;
  margin-bottom: 32px;
  line-height: 1.6;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.feature-item {
  text-align: center;
  padding: 24px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.feature-item:hover {
  border-color: #fd7e14;
  background: #fef3c7;
  transform: translateY(-2px);
}

.feature-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 16px;
  padding: 12px;
  border-radius: 12px;
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  color: white;
}

.feature-icon svg {
  width: 24px;
  height: 24px;
}

.feature-item h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 8px;
}

.feature-item p {
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.4;
}

.coming-soon-footer {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e5e7eb;
}

.release-info {
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 16px;
}

.release-info strong {
  color: #fd7e14;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #fd7e14, #ff922b);
  border-radius: 4px;
  transition: width 0.5s ease;
}

/* Dark mode */
.dark .reports-view {
  background: #0f172a;
}

.dark .page-header {
  background: linear-gradient(135deg, #1e293b, #334155);
  border-color: rgba(253, 126, 20, 0.2);
}

.dark .header-left h1 {
  background: linear-gradient(135deg, #f97316, #fb923c);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.dark .header-left p {
  color: #9ca3af;
}

.dark .coming-soon-card {
  background: #1e293b;
  border-color: rgba(253, 126, 20, 0.2);
}

.dark .coming-soon-card:hover {
  border-color: #f97316;
  background: rgba(249, 115, 22, 0.1);
}

.dark .coming-soon h2 {
  background: linear-gradient(135deg, #f97316, #fb923c);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.dark .coming-soon-description {
  color: #9ca3af;
}

.dark .feature-item {
  border-color: #4b5563;
}

.dark .feature-item:hover {
  border-color: #f97316;
  background: rgba(249, 115, 22, 0.1);
}

.dark .feature-item h3 {
  color: #f9fafb;
}

.dark .feature-item p {
  color: #9ca3af;
}

.dark .feature-icon {
  background: linear-gradient(135deg, #f97316, #fb923c);
}

.dark .coming-soon-footer {
  border-color: #4b5563;
}

.dark .release-info {
  color: #9ca3af;
}

.dark .release-info strong {
  color: #fbbf24;
}

.dark .progress-bar {
  background: #4b5563;
}

/* Responsive */
@media (max-width: 768px) {
  .reports-view {
    padding: 20px;
  }
  
  .page-header {
    padding: 24px;
    flex-direction: column;
    align-items: stretch;
    gap: 20px;
  }
  
  .header-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .header-left h1 {
    font-size: 2rem;
  }
  
  .header-actions {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .coming-soon-card {
    padding: 32px;
    margin: 0 16px;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .feature-item {
    padding: 20px;
  }
}
</style>
