<template>
  <div class="student-profiling-view">
    <div class="page-header">
      <h1>Student Profiling</h1>
      <p>Manage and monitor student information</p>
    </div>

    <div class="actions">
      <router-link to="/students/list" class="btn btn-primary">
        View All Students
      </router-link>
      <router-link to="/students/create" class="btn btn-success">
        Create New Student
      </router-link>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Students</h3>
        <p class="stat-number">{{ stats.total }}</p>
      </div>
      <div class="stat-card">
        <h3>Good Standing</h3>
        <p class="stat-number">{{ stats.goodStanding }}</p>
      </div>
      <div class="stat-card warning">
        <h3>At Risk</h3>
        <p class="stat-number">{{ stats.atRisk }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
  total: 0,
  goodStanding: 0,
  atRisk: 0
})

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/students')
    const students = response.data
    
    stats.value.total = students.length
    stats.value.goodStanding = students.filter((s: any) => s.academic_standing === 'good').length
    stats.value.atRisk = students.filter((s: any) => s.academic_standing === 'at_risk').length
  } catch (error) {
    console.error('Error fetching students:', error)
  }
})
</script>

<style scoped>
.student-profiling-view {
  padding: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: #666;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
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

.btn-success {
  background-color: #10b981;
  color: white;
}

.btn-success:hover {
  background-color: #059669;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1f2937;
}

.stat-card.warning .stat-number {
  color: #f59e0b;
}
</style>
