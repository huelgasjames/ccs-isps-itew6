<template>
  <div class="schedule-detail-container">
    <!-- Header Section -->
    <div class="section-header">
      <div class="header-content">
        <h1 class="page-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          Schedule Details
        </h1>
        <p class="page-subtitle">View detailed schedule information</p>
      </div>
      <router-link to="/schedules" class="back-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="19" y1="12" x2="5" y2="12"/>
          <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to Schedules
      </router-link>
    </div>

    <!-- Main Content -->
    <div class="content-card">
      <div class="coming-soon">
        <div class="icon-wrapper">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <h2 class="coming-soon-title">Schedule Details</h2>
        <p class="coming-soon-text">Detailed schedule information will be available soon.</p>
        <div class="feature-list">
          <div class="feature-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>Complete schedule information</span>
          </div>
          <div class="feature-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>Student enrollment details</span>
          </div>
          <div class="feature-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>Room availability status</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.schedule-detail-container {
  min-height: 100vh;
  background: #f8f9fa;
  font-family: 'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding: 40px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
  gap: 24px;
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.page-title svg {
  width: 32px;
  height: 32px;
  color: #fd7e14;
}

.page-subtitle {
  font-size: 1.1rem;
  color: #666;
  margin-left: 44px;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8f9fa;
  color: #666;
  padding: 12px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.2s;
  border: 1px solid #e9ecef;
}

.back-btn:hover {
  background: #e9ecef;
  color: #333;
  transform: translateY(-1px);
}

.back-btn svg {
  width: 18px;
  height: 18px;
}

.content-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
  overflow: hidden;
}

.coming-soon {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 40px;
  text-align: center;
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(253, 126, 20, 0.15), rgba(255, 146, 43, 0.1));
  border: 2px solid rgba(253, 126, 20, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.icon-wrapper svg {
  width: 40px;
  height: 40px;
  color: #fd7e14;
}

.coming-soon-title {
  font-size: 2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 12px;
}

.coming-soon-text {
  font-size: 1.1rem;
  color: #666;
  margin-bottom: 32px;
  max-width: 500px;
}

.feature-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 400px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 1rem;
  color: #666;
}

.feature-item svg {
  width: 20px;
  height: 20px;
  color: #16a34a;
  flex-shrink: 0;
}

/* Dark mode support */
.dark .schedule-detail-container {
  background: #0f172a;
}

.dark .page-title {
  color: #f9fafb;
}

.dark .page-subtitle {
  color: #9ca3af;
}

.dark .back-btn {
  background: #1e293b;
  color: #9ca3af;
  border-color: #334155;
}

.dark .back-btn:hover {
  background: #334155;
  color: #f9fafb;
}

.dark .content-card {
  background: #1e293b;
  border-color: #334155;
}

.dark .coming-soon-title {
  color: #f9fafb;
}

.dark .coming-soon-text {
  color: #9ca3af;
}

.dark .feature-item {
  color: #9ca3af;
}

/* Responsive design */
@media (max-width: 768px) {
  .schedule-detail-container {
    padding: 20px;
  }
  
  .section-header {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .page-subtitle {
    margin-left: 0;
  }
  
  .back-btn {
    justify-content: center;
  }
  
  .coming-soon {
    padding: 60px 20px;
  }
  
  .icon-wrapper {
    width: 60px;
    height: 60px;
  }
  
  .icon-wrapper svg {
    width: 30px;
    height: 30px;
  }
  
  .coming-soon-title {
    font-size: 1.5rem;
  }
}
</style>
