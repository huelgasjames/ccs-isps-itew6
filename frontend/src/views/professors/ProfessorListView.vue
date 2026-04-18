<template>
  <div class="professor-list-view">
    <div class="page-header">
      <h1>Professors List</h1>
      <router-link to="/professors/create" class="btn btn-primary">
        Create New Professor
      </router-link>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="professor-table-container">
      <table class="professor-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Employment Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="professor in professors" :key="professor.id">
            <td>{{ professor.id }}</td>
            <td>{{ professor.first_name }} {{ professor.last_name }}</td>
            <td>{{ professor.email }}</td>
            <td>{{ professor.department }}</td>
            <td>
              <span :class="['status-badge', getEmploymentClass(professor.employment_type)]">
                {{ professor.employment_type }}
              </span>
            </td>
            <td>
              <router-link :to="`/professors/${professor.id}`" class="btn btn-sm btn-info">
                View
              </router-link>
              <router-link :to="`/professors/${professor.id}/edit`" class="btn btn-sm btn-warning">
                Edit
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Professor {
  id: number
  first_name: string
  last_name: string
  email: string
  department: string
  employment_type: string
  role: string
}

const professors = ref<Professor[]>([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/professors')
    professors.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to fetch professors'
  } finally {
    loading.value = false
  }
})

function getEmploymentClass(type: string) {
  if (type === 'Full-time') return 'full-time'
  if (type === 'Part-time') return 'part-time'
  return 'contract'
}
</script>

<style scoped>
.professor-list-view {
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.btn-info {
  background-color: #0ea5e9;
  color: white;
}

.btn-info:hover {
  background-color: #0284c7;
}

.btn-warning {
  background-color: #f59e0b;
  color: white;
}

.btn-warning:hover {
  background-color: #d97706;
}

.loading,
.error {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #dc2626;
}

.professor-table-container {
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.professor-table {
  width: 100%;
  border-collapse: collapse;
}

.professor-table th,
.professor-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.professor-table th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
}

.professor-table tbody tr:hover {
  background-color: #f9fafb;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge.full-time {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.part-time {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.contract {
  background-color: #fef3c7;
  color: #92400e;
  font-family: 'Share Tech Mono', monospace; font-size: 0.7rem;
  cursor: pointer; transition: all 0.18s; white-space: nowrap;
}
.page-btn:hover:not(:disabled) { border-color: rgba(255,140,0,0.45); color: #ff8c00; background: rgba(255,140,0,0.08); }
.page-btn.active { background: rgba(255,140,0,0.14); border-color: rgba(255,140,0,0.5); color: #ff8c00; }
.page-btn:disabled { opacity: 0.3; cursor: not-allowed; }

/* Dark Mode Styles */
.dark .page-root {
  background: #0f172a;
}

.dark .bg-grid {
  background-image: 
    linear-gradient(rgba(249, 115, 22, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(249, 115, 22, 0.03) 1px, transparent 1px);
}

.dark .topbar {
  background: #1e293b;
  border-bottom-color: #334155;
}

.dark .topbar-back {
  color: #9ca3af;
  border-color: #334155;
}

.dark .topbar-back:hover {
  background: rgba(249, 115, 22, 0.1);
  border-color: #f97316;
  color: #f97316;
}

.dark .title-name {
  color: #f9fafb;
}

.dark .action-btn {
  border-color: #334155;
  color: #9ca3af;
}

.dark .action-btn.primary {
  background: #f97316;
  border-color: #f97316;
  color: white;
}

.dark .action-btn.danger {
  background: #ef4444;
  border-color: #ef4444;
  color: white;
}

.dark .panel {
  background: #1e293b;
  border-color: #334155;
}

.dark .panel-header {
  border-bottom-color: #334155;
}

.dark .panel-title {
  color: #f9fafb;
}

.dark .field-input {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .field-input:focus {
  border-color: rgba(249, 115, 22, 0.6);
  background: rgba(55, 65, 81, 0.95);
}

.dark .field-select option {
  background: #1e293b;
}

.dark .reset-btn {
  background: rgba(249, 115, 22, 0.05);
  border-color: rgba(249, 115, 22, 0.2);
  color: rgba(249, 115, 22, 0.7);
}

.dark .reset-btn:hover {
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
  background: rgba(249, 115, 22, 0.1);
}

.dark .data-table thead tr {
  background: rgba(249, 115, 22, 0.08);
  border-bottom-color: rgba(249, 115, 22, 0.15);
}

.dark .data-row {
  border-bottom-color: rgba(249, 115, 22, 0.08);
}

.dark .data-row:hover {
  background: rgba(249, 115, 22, 0.04);
}

.dark .data-row td {
  color: #f9fafb;
}

.dark .page-btn {
  background: rgba(30, 41, 59, 0.6);
  border-color: rgba(249, 115, 22, 0.15);
  color: rgba(249, 115, 22, 0.5);
}

.dark .page-btn:hover:not(:disabled) {
  border-color: rgba(249, 115, 22, 0.45);
  color: #f97316;
  background: rgba(249, 115, 22, 0.08);
}

.dark .page-btn.active {
  background: rgba(249, 115, 22, 0.14);
  border-color: rgba(249, 115, 22, 0.5);
  color: #f97316;
}

.dark .pagination-bar {
  border-top-color: rgba(249, 115, 22, 0.07);
}

.dark .loading-state,
.dark .empty-state {
  color: rgba(249, 115, 22, 0.4);
}

/* Responsive */
@media (max-width: 900px) {
  .filter-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .filter-grid { grid-template-columns: 1fr; }
  .topbar-title { display: none; }
  .prof-actions { flex-direction: column; }
  .row-btn span { display: none; }
}
</style>