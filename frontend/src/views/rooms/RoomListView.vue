<template>
  <div class="room-list-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Rooms Management</h1>
          <p>Manage CCS rooms and facilities</p>
        </div>
        <div class="header-actions">
          <button @click="roomStore.fetchStatistics" class="btn btn-info btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M9 19v-6a2 2 0 0 0-2 2H5a2 2 0 0 0 2v6"/>
              <path d="M20.49 15a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Statistics
          </button>
          <button @click="roomStore.fetchRooms" class="btn btn-primary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M23 4v6h-6M1 20v-6h6"/>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Refresh
          </button>
          <router-link to="/rooms/create" class="btn btn-primary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            Create New Room
          </router-link>
        </div>
      </div>
    </div>

    <!-- Top Statistics -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-9 9-9-9 2 16l-7 7-7-7 16z"/>
            <path d="M9 19v6a2 2 0 0 0-2 2H5a2 2 0 0 2v6"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Rooms</h3>
          <div class="stat-number">{{ roomStore.statistics.totalRooms }}</div>
          <span class="stat-change">All facilities</span>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V6a2 2 0 0 0-2 2H12a2 2 0 0 2v10.92a2 2 0 0 0 1.11l6.26 6.26z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Available</h3>
          <div class="stat-number">{{ roomStore.statistics.availableRooms }}</div>
          <span class="stat-change">Ready for use</span>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.29 20.14c-.64-.64-1.11-.29H1.73a2 2 0 0 0-2 2h12.54a2 2 0 0 1.85l3.23 3.23z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Maintenance</h3>
          <div class="stat-number">{{ roomStore.statistics.maintenanceRooms }}</div>
          <span class="stat-change">Under repair</span>
        </div>
      </div>

      <div class="stat-card danger">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-8.73-8.73z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Occupied</h3>
          <div class="stat-number">{{ roomStore.statistics.occupiedRooms }}</div>
          <span class="stat-change">Currently in use</span>
        </div>
      </div>
    </div>

    <!-- Type Distribution -->
    <div class="analytics-section">
      <h2>Room Distribution by Type</h2>
      <div class="type-distribution">
        <div 
          v-for="type in roomStore.statistics.roomsByType" 
          :key="type.type"
          class="type-card"
        >
          <div class="type-header">
            <h3>{{ type.label }}</h3>
            <span class="type-count">{{ type.count }} rooms</span>
          </div>
          <div class="type-progress">
            <div class="progress-bar" :style="{ width: (type.count / roomStore.statistics.totalRooms * 100) + '%' }"></div>
          </div>
          <div class="type-details">
            <span class="type-percentage">{{ Math.round((type.count / roomStore.statistics.totalRooms) * 100) }}%</span>
            <span class="type-usage">Total rooms</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-container">
      <div class="search-box">
        <input
          v-model="roomStore.filter.search"
          type="text"
          placeholder="Search rooms by name, code, building..."
          class="search-input"
        />
      </div>
      <div class="filter-group">
        <select v-model="roomStore.filter.type" class="filter-select">
          <option value="">All Types</option>
          <option v-for="type in roomStore.availableTypes" :key="type.value" :value="type.value">
            {{ type.label }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="roomStore.filter.building" class="filter-select">
          <option value="">All Buildings</option>
          <option v-for="building in roomStore.availableBuildings" :key="building" :value="building">
            {{ building }}
          </option>
        </select>
      </div>
      <div class="filter-group">
        <select v-model="roomStore.filter.status" class="filter-select">
          <option value="">All Status</option>
          <option v-for="status in roomStore.availableStatuses" :key="status.value" :value="status.value">
            {{ status.label }}
          </option>
        </select>
      </div>
      <button @click="roomStore.resetFilter" class="btn btn-secondary btn-sm">
        Reset Filters
      </button>
    </div>

    <!-- Room Table -->
    <div class="room-table-container">
      <div v-if="roomStore.loading" class="loading">
        <div class="spinner"></div>
        <p>Loading rooms...</p>
      </div>
      
      <div v-else-if="roomStore.error" class="error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/>
          <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <h3>Error loading rooms</h3>
        <p>{{ roomStore.error }}</p>
      </div>

      <div v-else class="table-wrapper">
        <table class="room-table">
          <thead>
            <tr>
              <th>Room Code</th>
              <th>Name</th>
              <th>Type</th>
              <th>Building</th>
              <th>Floor</th>
              <th>Capacity</th>
              <th>Status</th>
              <th>Equipment</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="room in roomStore.filteredRooms" :key="room.id">
              <td>
                <span class="room-code">{{ room.room_code }}</span>
              </td>
              <td>
                <span class="room-name">{{ room.name }}</span>
              </td>
              <td>
                <span class="type-badge" :class="room.type">{{ room.type }}</span>
              </td>
              <td>
                <span class="building-name">{{ room.building || '—' }}</span>
              </td>
              <td>
                <span class="floor-number">{{ room.floor || '—' }}</span>
              </td>
              <td>
                <span class="capacity-number">{{ room.capacity }}</span>
              </td>
              <td>
                <span class="status-badge" :class="room.status">{{ room.status }}</span>
              </td>
              <td>
                <div class="equipment-list">
                  <span v-if="room.equipment && room.equipment.length > 0" 
                        v-for="(item, index) in room.equipment.slice(0, 3)" 
                        :key="index" 
                        class="equipment-item">
                    {{ item }}
                  </span>
                  <span v-if="room.equipment && room.equipment.length > 3" class="equipment-more">
                    +{{ room.equipment.length - 3 }} more
                  </span>
                  <span v-if="!room.equipment || room.equipment.length === 0" class="no-equipment">—</span>
                </div>
              </td>
              <td class="actions-cell">
                <router-link :to="`/rooms/${room.id}/edit`" class="action-btn edit">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 2-2 2h14a2 2 0 0 2 2v6"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </router-link>
                <button class="action-btn delete" @click="deleteRoom(room.id)">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 0-2 2h-16a2 2 0 0 2v2"/>
                  </svg>
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoomStore } from '@/stores/room'

const roomStore = useRoomStore()

const deleteRoom = async (id: number) => {
  if (!confirm('Delete this room? This action cannot be undone.')) return
  await roomStore.deleteRoom(id)
}

onMounted(() => {
  roomStore.fetchRooms()
})
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.room-list-view {
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

.btn-primary {
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  color: white;
  box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(253, 126, 20, 0.4);
}

.btn-info {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-info:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
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

.btn-sm {
  padding: 8px 12px;
  font-size: 0.85rem;
}

.icon-small {
  width: 16px;
  height: 16px;
}

/* Statistics Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-start), var(--color-end));
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
}

.stat-card.primary {
  --color-start: #3b82f6;
  --color-end: #2563eb;
}

.stat-card.success {
  --color-start: #10b981;
  --color-end: #059669;
}

.stat-card.warning {
  --color-start: #f59e0b;
  --color-end: #d97706;
}

.stat-card.danger {
  --color-start: #ef4444;
  --color-end: #dc2626;
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  background: linear-gradient(135deg, var(--color-start), var(--color-end));
  color: white;
}

.stat-icon svg {
  width: 28px;
  height: 28px;
}

.stat-content h3 {
  font-size: 0.9rem;
  color: #6b7280;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-bottom: 4px;
  line-height: 1;
}

.stat-change {
  font-size: 0.85rem;
  color: #9ca3af;
  font-weight: 500;
}

/* Analytics Section */
.analytics-section {
  background: white;
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.analytics-section h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.type-distribution {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.type-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.type-card:hover {
  background: #f3f4f6;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.type-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.type-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
}

.type-count {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 500;
}

.type-progress {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  margin-bottom: 12px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #fd7e14, #ff922b);
  border-radius: 4px;
  transition: width 0.5s ease;
}

.type-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.type-percentage {
  font-size: 1rem;
  font-weight: 700;
  color: #fd7e14;
}

.type-usage {
  font-size: 0.8rem;
  color: #9ca3af;
}

/* Filters */
.filters-container {
  background: white;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
  display: flex;
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 300px;
}

.search-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
}

.search-input:focus {
  outline: none;
  border-color: #fd7e14;
  background: white;
  box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.1);
}

.filter-group {
  min-width: 150px;
}

.filter-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #fd7e14;
  background: white;
  box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.1);
}

/* Table */
.room-table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.loading, .error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #fd7e14;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error svg {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin-bottom: 16px;
}

.error h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
}

.table-wrapper {
  overflow-x: auto;
}

.room-table {
  width: 100%;
  border-collapse: collapse;
}

.room-table th {
  background: #f8f9fa;
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e5e7eb;
}

.room-table td {
  padding: 16px;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.95rem;
}

.room-table tbody tr {
  transition: all 0.2s ease;
}

.room-table tbody tr:hover {
  background: #f8f9fa;
}

.room-code {
  font-weight: 700;
  color: #1a1a1a;
  background: #fef3c7;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.85rem;
}

.room-name {
  font-weight: 600;
  color: #374151;
}

.type-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.type-badge.lecture {
  background: #dbeafe;
  color: #1e40af;
}

.type-badge.lab {
  background: #dcfce7;
  color: #166534;
}

.type-badge.computer_lab {
  background: #e0f2fe;
  color: #1e40af;
}

.type-badge.multimedia {
  background: #f3e8ff;
  color: #8b5cf6;
}

.type-badge.conference {
  background: #fef3c7;
  color: #f59e0b;
}

.building-name {
  font-weight: 600;
  color: #374151;
}

.floor-number {
  font-weight: 600;
  color: #374151;
}

.capacity-number {
  font-weight: 600;
  color: #374151;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-badge.available {
  background: #dcfce7;
  color: #166534;
}

.status-badge.maintenance {
  background: #fef3c7;
  color: #f59e0b;
}

.status-badge.occupied {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.unavailable {
  background: #f3f4f6;
  color: #6b7280;
}

.equipment-list {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.equipment-item {
  background: #f1f5f9;
  color: #374151;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.equipment-more {
  font-size: 0.7rem;
  color: #6b7280;
  font-style: italic;
}

.no-equipment {
  color: #9ca3af;
  font-style: italic;
}

.actions-cell {
  display: flex;
  gap: 8px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 500;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn svg {
  width: 16px;
  height: 16px;
}

.action-btn.edit {
  background: #eff6ff;
  color: #2563eb;
}

.action-btn.edit:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}

.action-btn.delete {
  background: #fef2f2;
  color: #dc2626;
}

.action-btn.delete:hover {
  background: #fee2e2;
  transform: translateY(-1px);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Dark mode */
.dark .room-list-view {
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

.dark .stat-card {
  background: #1e293b;
  border-color: #334155;
}

.dark .stat-content h3 {
  color: #9ca3af;
}

.dark .stat-number {
  color: #f9fafb;
}

.dark .stat-change {
  color: #6b7280;
}

.dark .analytics-section {
  background: #1e293b;
  border-color: #334155;
}

.dark .analytics-section h2 {
  color: #f9fafb;
}

.dark .type-card {
  background: #374151;
  border-color: #4b5563;
}

.dark .type-card:hover {
  background: #4b5563;
}

.dark .type-header h3 {
  color: #f9fafb;
}

.dark .type-count {
  color: #9ca3af;
}

.dark .type-progress {
  background: #4b5563;
}

.dark .filters-container {
  background: #1e293b;
  border-color: #334155;
}

.dark .search-input,
.dark .filter-select {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .search-input:focus,
.dark .filter-select:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.dark .room-table-container {
  background: #1e293b;
  border-color: #334155;
}

.dark .room-table th {
  background: #374151;
  color: #f9fafb;
  border-color: #4b5563;
}

.dark .room-table td {
  border-color: #4b5563;
  color: #e5e7eb;
}

.dark .room-table tbody tr:hover {
  background: #374151;
}

.dark .room-code {
  background: rgba(249, 115, 22, 0.2);
  color: #fbbf24;
}

.dark .room-name,
.dark .building-name,
.dark .floor-number,
.dark .capacity-number {
  color: #e5e7eb;
}

.dark .type-badge.lecture {
  background: rgba(59, 130, 246, 0.1);
  color: #60a5fa;
}

.dark .type-badge.lab {
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
}

.dark .type-badge.computer_lab {
  background: rgba(30, 64, 175, 0.2);
  color: #60a5fa;
}

.dark .type-badge.multimedia {
  background: rgba(139, 92, 246, 0.2);
  color: #60a5fa;
}

.dark .type-badge.conference {
  background: rgba(249, 115, 22, 0.2);
  color: #fbbf24;
}

.dark .status-badge.available {
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
}

.dark .status-badge.maintenance {
  background: rgba(249, 115, 22, 0.2);
  color: #fbbf24;
}

.dark .status-badge.occupied {
  background: rgba(239, 68, 68, 0.1);
  color: #f87171;
}

.dark .status-badge.unavailable {
  background: rgba(107, 114, 128, 0.2);
  color: #9ca3af;
}

.dark .equipment-item {
  background: #374151;
  color: #e5e7eb;
}

.dark .equipment-more {
  color: #9ca3af;
}

.dark .no-equipment {
  color: #6b7280;
}

.dark .action-btn.edit {
  background: rgba(59, 130, 246, 0.1);
  color: #60a5fa;
}

.dark .action-btn.edit:hover {
  background: rgba(59, 130, 246, 0.2);
}

.dark .action-btn.delete {
  background: rgba(239, 68, 68, 0.1);
  color: #f87171;
}

.dark .action-btn.delete:hover {
  background: rgba(239, 68, 68, 0.2);
}

/* Responsive */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .type-distribution {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .room-list-view {
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
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .type-distribution {
    grid-template-columns: 1fr;
  }
  
  .filters-container {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-box {
    min-width: auto;
  }
  
  .filter-group {
    min-width: auto;
  }
  
  .room-table th,
  .room-table td {
    padding: 12px 8px;
    font-size: 0.85rem;
  }
  
  .actions-cell {
    flex-direction: column;
    gap: 4px;
  }
}
</style>
