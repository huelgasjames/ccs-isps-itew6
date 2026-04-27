<template>
  <div class="schedule-form-container">
    <!-- Header Section -->
    <div class="section-header">
      <div class="header-content">
        <h1 class="page-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          {{ isEdit ? 'Edit Schedule' : 'Create Schedule' }}
        </h1>
        <p class="page-subtitle">{{ isEdit ? 'Update schedule information' : 'Add a new course schedule' }}</p>
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
      <form class="schedule-form" @submit.prevent="submit">
        <div class="form-grid">
          <!-- Course Information -->
          <div class="form-section">
            <h3 class="section-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
              </svg>
              Course Information
            </h3>
            <div class="form-row">
              <div class="form-group">
                <label for="course_id">Course ID</label>
                <input 
                  id="course_id"
                  v-model.number="form.course_id" 
                  type="number" 
                  min="1" 
                  class="form-input" 
                  placeholder="Enter course ID" 
                  required 
                />
              </div>
              <div class="form-group">
                <label for="professor_id">Professor ID</label>
                <input 
                  id="professor_id"
                  v-model.number="form.professor_id" 
                  type="number" 
                  min="1" 
                  class="form-input" 
                  placeholder="Enter professor ID" 
                  required 
                />
              </div>
            </div>
            <div class="form-group">
              <label for="section">Section</label>
              <input 
                id="section"
                v-model="form.section" 
                class="form-input" 
                placeholder="e.g., BSIT-2A" 
                required 
              />
            </div>
          </div>

          <!-- Schedule Details -->
          <div class="form-section">
            <h3 class="section-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              Schedule Details
            </h3>
            <div class="form-row">
              <div class="form-group">
                <label for="room">Room</label>
                <input 
                  id="room"
                  v-model="form.room" 
                  class="form-input" 
                  placeholder="e.g., Room 301" 
                  required 
                />
              </div>
              <div class="form-group">
                <label for="room_type">Room Type</label>
                <select id="room_type" v-model="form.room_type" class="form-select" required>
                  <option value="lecture">Lecture</option>
                  <option value="lab">Laboratory</option>
                  <option value="computer_lab">Computer Lab</option>
                  <option value="multimedia">Multimedia</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="day_of_week">Day of Week</label>
              <select id="day_of_week" v-model="form.day_of_week" class="form-select" required>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
                <option>Saturday</option>
              </select>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="start_time">Start Time</label>
                <input 
                  id="start_time"
                  v-model="form.start_time" 
                  type="time" 
                  class="form-input" 
                  required 
                />
              </div>
              <div class="form-group">
                <label for="end_time">End Time</label>
                <input 
                  id="end_time"
                  v-model="form.end_time" 
                  type="time" 
                  class="form-input" 
                  required 
                />
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="form-section">
            <h3 class="section-title">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
              </svg>
              Academic Information
            </h3>
            <div class="form-row">
              <div class="form-group">
                <label for="academic_year">Academic Year</label>
                <input 
                  id="academic_year"
                  v-model="form.academic_year" 
                  class="form-input" 
                  placeholder="e.g., 2025-2026" 
                  required 
                />
              </div>
              <div class="form-group">
                <label for="semester">Semester</label>
                <select id="semester" v-model="form.semester" class="form-select" required>
                  <option>First Semester</option>
                  <option>Second Semester</option>
                  <option>Summer</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="max_capacity">Max Capacity</label>
                <input 
                  id="max_capacity"
                  v-model.number="form.max_capacity" 
                  type="number" 
                  min="1" 
                  class="form-input" 
                  placeholder="Maximum students" 
                  required 
                />
              </div>
              <div class="form-group">
                <label for="current_enrollment">Current Enrollment</label>
                <input 
                  id="current_enrollment"
                  v-model.number="form.current_enrollment" 
                  type="number" 
                  min="0" 
                  class="form-input" 
                  placeholder="Current enrolled students" 
                />
              </div>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="form.status" class="form-select" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea 
                id="notes"
                v-model="form.notes" 
                class="form-textarea" 
                placeholder="Additional notes or comments"
                rows="4"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="submit" class="submit-btn" :disabled="loading">
            <div v-if="loading" class="btn-spinner"></div>
            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
              <polyline points="17 21 17 13 7 13 7 21"/>
              <polyline points="7 3 7 8 15 8"/>
            </svg>
            {{ isEdit ? 'Update Schedule' : 'Create Schedule' }}
          </button>
          <router-link to="/schedules" class="cancel-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useScheduleStore } from '@/stores/schedule'
import type { ScheduleFormData } from '@/types/schedule'

const route = useRoute()
const router = useRouter()
const scheduleStore = useScheduleStore()
const isEdit = computed(() => !!route.params.id)
const loading = ref(false)

const form = ref<ScheduleFormData>({
  course_id: 1,
  professor_id: 1,
  section: '',
  room: '',
  room_type: 'lecture',
  day_of_week: 'Monday',
  start_time: '08:00',
  end_time: '09:00',
  academic_year: '2025-2026',
  semester: 'First Semester',
  max_capacity: 40,
  current_enrollment: 0,
  status: 'active',
  notes: ''
})

const submit = async () => {
  loading.value = true
  try {
    const ok = isEdit.value
      ? await scheduleStore.updateSchedule(Number(route.params.id), form.value)
      : await scheduleStore.createSchedule(form.value)
    if (ok) router.push('/schedules')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (!isEdit.value) return
  const data = await scheduleStore.fetchSchedule(Number(route.params.id))
  if (data) form.value = { ...data }
})
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.schedule-form-container {
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

.schedule-form {
  padding: 32px;
}

.form-grid {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.form-section {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 24px;
  background: #fafafa;
}

.section-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #333;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #fd7e14;
}

.section-title svg {
  width: 20px;
  height: 20px;
  color: #fd7e14;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #333;
}

.form-input,
.form-select,
.form-textarea {
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #fff;
  color: #333;
  font-size: 1rem;
  font-family: inherit;
  transition: all 0.2s;
  outline: none;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  border-color: #fd7e14;
  box-shadow: 0 0 0 2px rgba(253, 126, 20, 0.15);
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: #999;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.form-actions {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  padding: 24px 32px;
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
}

.submit-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #fd7e14, #ff922b);
  color: #fff;
  padding: 12px 24px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #e56a0a, #fd7e14);
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(253, 126, 20, 0.4);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.submit-btn svg {
  width: 18px;
  height: 18px;
}

.cancel-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8f9fa;
  color: #666;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.2s;
  border: 1px solid #e9ecef;
}

.cancel-btn:hover {
  background: #e9ecef;
  color: #333;
  transform: translateY(-1px);
}

.cancel-btn svg {
  width: 18px;
  height: 18px;
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

/* Dark mode support */
.dark .schedule-form-container {
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

.dark .form-section {
  background: #374151;
  border-color: #4b5563;
}

.dark .section-title {
  color: #f9fafb;
  border-color: #f97316;
}

.dark .form-group label {
  color: #f9fafb;
}

.dark .form-input,
.dark .form-select,
.dark .form-textarea {
  background: #1e293b;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .form-input:focus,
.dark .form-select:focus,
.dark .form-textarea:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.dark .form-actions {
  background: #374151;
  border-color: #4b5563;
}

.dark .cancel-btn {
  background: #1e293b;
  color: #9ca3af;
  border-color: #4b5563;
}

.dark .cancel-btn:hover {
  background: #374151;
  color: #f9fafb;
}

/* Responsive design */
@media (max-width: 768px) {
  .schedule-form-container {
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
  
  .schedule-form {
    padding: 20px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .form-section {
    padding: 16px;
  }
  
  .form-actions {
    flex-direction: column;
    padding: 20px;
  }
  
  .submit-btn,
  .cancel-btn {
    justify-content: center;
  }
}
</style>
