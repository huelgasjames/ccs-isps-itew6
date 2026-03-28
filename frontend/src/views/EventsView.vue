<template>
  <div class="events-view">
    <div class="header">
      <div class="breadcrumb">
        <router-link to="/dashboard" class="breadcrumb-link">Dashboard</router-link>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Events</span>
      </div>
      <div class="header-content">
        <div>
          <h1 class="page-title">Events</h1>
          <p class="page-subtitle">Manage and organize department events</p>
        </div>
        <button class="create-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          Create Event
        </button>
      </div>
    </div>
    
    <div class="content-section">
      <div class="empty-state">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <h3>Events Management</h3>
        <p>Events management page - Coming soon!</p>
        <button class="create-btn">Get Started</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Events view component
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.events-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  font-family: 'Inter', sans-serif;
}

/* Header */
.header {
  margin-bottom: 2rem;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.breadcrumb-link {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #f97316;
}

.breadcrumb-separator {
  color: #d1d5db;
}

.breadcrumb-current {
  color: #374151;
  font-weight: 500;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
  line-height: 1.2;
}

.page-subtitle {
  color: #6b7280;
  margin: 0.5rem 0 0 0;
  font-size: 1rem;
}

.create-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #f97316;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.create-btn:hover {
  background: #ea580c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.create-btn svg {
  width: 16px;
  height: 16px;
}

/* Content */
.content-section {
  min-height: 400px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #9ca3af;
}

.empty-icon svg {
  width: 100%;
  height: 100%;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  color: #6b7280;
  margin: 0 0 1.5rem 0;
}

/* Responsive */
@media (max-width: 768px) {
  .events-view {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
