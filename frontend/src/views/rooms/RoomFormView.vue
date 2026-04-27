<template>
  <div class="room-form-view">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>{{ isEditing ? 'Edit Room' : 'Create New Room' }}</h1>
          <p>{{ isEditing ? 'Update room information and settings' : 'Add a new room to the facility inventory' }}</p>
        </div>
        <div class="header-actions">
          <router-link to="/rooms" class="btn btn-secondary btn-small">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M19 12H5M12 19l7-7 7-7"/>
            </svg>
            Back to Rooms
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>{{ isEditing ? 'Updating room...' : 'Creating room...' }}</p>
      </div>

      <div v-else-if="error" class="error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/>
          <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <h3>{{ isEditing ? 'Update Failed' : 'Create Failed' }}</h3>
        <p>{{ error }}</p>
      </div>

      <form v-else @submit.prevent="handleSubmit" class="room-form">
        <!-- Basic Information -->
        <div class="form-section">
          <h2>Basic Information</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="name">Room Name *</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                placeholder="e.g., Main Lecture Hall"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="room_code">Room Code *</label>
              <input
                id="room_code"
                v-model="form.room_code"
                type="text"
                required
                placeholder="e.g., LH-001"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="type">Room Type *</label>
              <select id="type" v-model="form.type" required class="form-select">
                <option value="">Select room type</option>
                <option v-for="type in availableTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="capacity">Capacity *</label>
              <input
                id="capacity"
                v-model.number="form.capacity"
                type="number"
                required
                min="1"
                max="500"
                placeholder="e.g., 50"
                class="form-input"
              />
            </div>
          </div>
        </div>

        <!-- Location Information -->
        <div class="form-section">
          <h2>Location Information</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="building">Building</label>
              <input
                id="building"
                v-model="form.building"
                type="text"
                placeholder="e.g., Main Building"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="floor">Floor</label>
              <input
                id="floor"
                v-model.number="form.floor"
                type="number"
                min="1"
                max="20"
                placeholder="e.g., 1"
                class="form-input"
              />
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
          <h2>Additional Information</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="status">Status *</label>
              <select id="status" v-model="form.status" required class="form-select">
                <option value="">Select status</option>
                <option v-for="status in availableStatuses" :key="status.value" :value="status.value">
                  {{ status.label }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="hourly_rate">Hourly Rate ($)</label>
              <input
                id="hourly_rate"
                v-model.number="form.hourly_rate"
                type="number"
                min="0"
                max="10000"
                step="0.01"
                placeholder="e.g., 150.00"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-group full-width">
            <label for="description">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              placeholder="Enter room description, special features, or additional notes..."
              class="form-textarea"
            ></textarea>
          </div>
        </div>

        <!-- Equipment -->
        <div class="form-section">
          <h2>Equipment & Features</h2>
          <div class="equipment-section">
            <div class="form-group full-width">
              <label>Available Equipment</label>
              <div class="equipment-grid">
                <div v-for="equipment in commonEquipment" :key="equipment" class="equipment-checkbox">
                  <label class="checkbox-label">
                    <input
                      type="checkbox"
                      :value="equipment"
                      v-model="form.equipment"
                      class="checkbox-input"
                    />
                    <span>{{ equipment }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="button" @click="$router.push('/rooms')" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M19 12H5M12 19l7-7 7-7"/>
            </svg>
            Cancel
          </button>
          <button type="submit" :disabled="loading" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-small">
              <path d="M5 13l4 4L7 8l-4 1 1-4 9.5-9.5z"/>
              <path d="M12 2l3.09 6.26L22 9.27l-8.73-8.73z"/>
            </svg>
            {{ isEditing ? 'Update Room' : 'Create Room' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRoomStore } from '@/stores/room'

const route = useRoute()
const router = useRouter()
const roomStore = useRoomStore()

const isEditing = computed(() => !!route.params.id)

const form = ref({
  name: '',
  room_code: '',
  type: '',
  capacity: 0,
  building: '',
  floor: null,
  status: 'available',
  hourly_rate: null,
  description: '',
  equipment: [] as string[]
})

const loading = ref(false)
const error = ref('')

const availableTypes = [
  { value: 'lecture', label: 'Lecture Hall' },
  { value: 'lab', label: 'Laboratory' },
  { value: 'computer_lab', label: 'Computer Lab' },
  { value: 'multimedia', label: 'Multimedia Room' },
  { value: 'conference', label: 'Conference Room' }
]

const availableStatuses = [
  { value: 'available', label: 'Available' },
  { value: 'maintenance', label: 'Under Maintenance' },
  { value: 'occupied', label: 'Occupied' },
  { value: 'unavailable', label: 'Unavailable' }
]

const commonEquipment = [
  'Projector',
  'Whiteboard',
  'Computer',
  'Sound System',
  'Air Conditioning',
  'WiFi',
  'Smart Board',
  'Video Conference Equipment',
  'Lab Equipment',
  'Safety Equipment',
  'Ventilation'
]

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  
  try {
    if (isEditing.value) {
      await roomStore.updateRoom(Number(route.params.id), form.value)
    } else {
      await roomStore.createRoom(form.value)
    }
    
    router.push('/rooms')
  } catch (err: any) {
    error.value = err.response?.data?.message || `${isEditing.value ? 'Update' : 'Create'} failed`
  } finally {
    loading.value = false
  }
}

const loadRoom = async () => {
  if (isEditing.value) {
    try {
      const room = await roomStore.fetchRoom(Number(route.params.id))
      if (room) {
        form.value = {
          name: room.name || '',
          room_code: room.room_code || '',
          type: room.type || '',
          capacity: room.capacity || 0,
          building: room.building || '',
          floor: room.floor || null,
          status: room.status || 'available',
          hourly_rate: room.hourly_rate || null,
          description: room.description || '',
          equipment: room.equipment || []
        }
      }
    } catch (err: any) {
      error.value = 'Failed to load room data'
    }
  }
}

onMounted(() => {
  loadRoom()
})
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.room-form-view {
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

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
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

/* Form Container */
.form-container {
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

.room-form {
  padding: 32px;
}

.form-section {
  margin-bottom: 32px;
}

.form-section h2 {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #e5e7eb;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
  font-size: 0.95rem;
}

.form-input,
.form-select,
.form-textarea {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
  color: #1a1a1a;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #fd7e14;
  background: white;
  box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.form-actions {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  padding-top: 32px;
  border-top: 2px solid #e5e7eb;
  margin: 0 -32px;
  padding: 32px 32px 0 32px;
}

/* Equipment Section */
.equipment-section {
  grid-column: 1 / -1;
}

.equipment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
}

.equipment-checkbox {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.equipment-checkbox:hover {
  border-color: #fd7e14;
  background: #fef3c7;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #374151;
}

.checkbox-input {
  width: 18px;
  height: 18px;
  accent-color: #fd7e14;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Dark mode */
.dark .room-form-view {
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

.dark .form-container {
  background: #1e293b;
  border-color: #334155;
}

.dark .form-section h2 {
  color: #f9fafb;
  border-color: #4b5563;
}

.dark .form-input,
.dark .form-select,
.dark .form-textarea {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .form-input:focus,
.dark .form-select:focus,
.dark .form-textarea:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.dark .form-group label {
  color: #e5e7eb;
}

.dark .equipment-checkbox {
  border-color: #4b5563;
}

.dark .equipment-checkbox:hover {
  border-color: #f97316;
  background: rgba(249, 115, 22, 0.1);
}

.dark .checkbox-label {
  color: #e5e7eb;
}

.dark .form-actions {
  border-color: #4b5563;
}

/* Responsive */
@media (max-width: 768px) {
  .room-form-view {
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
  
  .form-container {
    padding: 24px;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .equipment-grid {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .form-actions .btn {
    justify-content: center;
  }
}
</style>
