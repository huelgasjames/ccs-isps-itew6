<template>
  <div class="events-view">
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Events Management</h1>
          <p>Manage and organize department events</p>
        </div>
        <div class="header-actions">
          <button @click="generateSampleData" class="btn btn-secondary">Generate Sample Data</button>
          <button @click="openCreateForm" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Event
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="loading-state"><div class="spinner"></div><p>Loading events...</p></div>
    <div v-else-if="error" class="error-state"><h3>Error</h3><p>{{ error }}</p><button @click="fetchEvents" class="btn btn-primary">Retry</button></div>

    <div v-else>
      <!-- Stats -->
      <div class="stats-section">
        <div class="stat-card"><div class="stat-icon upcoming"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="stat-content"><h4>{{ upcomingCount }}</h4><p>Upcoming</p></div></div>
        <div class="stat-card"><div class="stat-icon ongoing"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div><div class="stat-content"><h4>{{ ongoingCount }}</h4><p>Ongoing</p></div></div>
        <div class="stat-card"><div class="stat-icon completed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div><div class="stat-content"><h4>{{ completedCount }}</h4><p>Completed</p></div></div>
        <div class="stat-card"><div class="stat-icon total"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div class="stat-content"><h4>{{ events.length }}</h4><p>Total</p></div></div>
      </div>

      <!-- Filters -->
      <div class="filters-section">
        <input v-model="searchQuery" type="text" class="form-input" placeholder="Search events..." />
        <select v-model="filterType" class="form-input">
          <option value="">All Types</option>
          <option value="curricular">Curricular</option>
          <option value="extra_curricular">Extra Curricular</option>
          <option value="academic">Academic</option>
          <option value="sports">Sports</option>
          <option value="cultural">Cultural</option>
          <option value="seminar">Seminar</option>
          <option value="workshop">Workshop</option>
        </select>
        <select v-model="filterStatus" class="form-input">
          <option value="">All Status</option>
          <option value="upcoming">Upcoming</option>
          <option value="ongoing">Ongoing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      <!-- Events Grid -->
      <div v-if="filteredEvents.length === 0" class="empty-state">
        <h3>No Events Found</h3>
        <p>Create your first event or generate sample data.</p>
      </div>
      <div v-else class="events-grid">
        <div v-for="event in filteredEvents" :key="event.id" class="event-card">
          <div class="event-header">
            <span :class="['type-badge', event.type]">{{ formatType(event.type) }}</span>
            <span :class="['status-badge', event.status]">{{ event.status }}</span>
          </div>
          <h3 class="event-title">{{ event.title }}</h3>
          <p class="event-desc">{{ event.description?.substring(0, 120) }}...</p>
          <div class="event-meta">
            <span>{{ formatDate(event.start_datetime) }}</span>
            <span>{{ event.venue || event.location }}</span>
            <span>{{ event.current_participants || 0 }}/{{ event.max_participants || '∞' }}</span>
            <span>₱{{ Number(event.registration_fee || 0).toFixed(2) }}</span>
          </div>
          <div class="event-actions">
            <button @click="editEvent(event)" class="btn btn-small btn-primary">Edit</button>
            <button @click="deleteEvent(event)" class="btn btn-small btn-danger">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showForm" class="modal-overlay" @click="showForm = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header"><h3>{{ editingId ? 'Edit' : 'Create' }} Event</h3><button @click="showForm = false" class="close-btn">&times;</button></div>
        <form @submit.prevent="saveEvent" class="modal-body">
          <div class="form-group"><label class="form-label">Title *</label><input v-model="form.title" type="text" class="form-input" required /></div>
          <div class="form-group"><label class="form-label">Description *</label><textarea v-model="form.description" class="form-input" rows="3" required></textarea></div>
          <div class="form-grid">
            <div class="form-group"><label class="form-label">Type *</label><select v-model="form.type" class="form-input" required><option value="">Select</option><option value="curricular">Curricular</option><option value="extra_curricular">Extra Curricular</option><option value="academic">Academic</option><option value="sports">Sports</option><option value="cultural">Cultural</option><option value="seminar">Seminar</option><option value="workshop">Workshop</option></select></div>
            <div class="form-group"><label class="form-label">Status</label><select v-model="form.status" class="form-input"><option value="upcoming">Upcoming</option><option value="ongoing">Ongoing</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
          </div>
          <div class="form-grid">
            <div class="form-group"><label class="form-label">Start *</label><input v-model="form.start_datetime" type="datetime-local" class="form-input" required /></div>
            <div class="form-group"><label class="form-label">End *</label><input v-model="form.end_datetime" type="datetime-local" class="form-input" required /></div>
          </div>
          <div class="form-grid">
            <div class="form-group"><label class="form-label">Venue *</label><input v-model="form.venue" type="text" class="form-input" required /></div>
            <div class="form-group"><label class="form-label">Organizer *</label><input v-model="form.organizer" type="text" class="form-input" required /></div>
          </div>
          <div class="form-grid">
            <div class="form-group"><label class="form-label">Target Audience *</label><select v-model="form.target_audience" class="form-input" required><option value="">Select</option><option value="all_students">All Students</option><option value="specific_year">Specific Year</option><option value="specific_course">Specific Course</option><option value="faculty">Faculty</option><option value="all">All</option></select></div>
            <div class="form-group"><label class="form-label">Max Participants</label><input v-model.number="form.max_participants" type="number" min="1" class="form-input" /></div>
          </div>
          <div class="form-grid">
            <div class="form-group"><label class="form-label">Fee *</label><input v-model.number="form.registration_fee" type="number" step="0.01" min="0" class="form-input" required /></div>
            <div class="form-group"><label class="form-label">Requirements</label><input v-model="form.requirements" type="text" class="form-input" /></div>
          </div>
          <div class="modal-footer"><button type="button" @click="showForm = false" class="btn btn-outline">Cancel</button><button type="submit" class="btn btn-primary">{{ editingId ? 'Update' : 'Create' }}</button></div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const events = ref<any[]>([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')
const filterType = ref('')
const filterStatus = ref('')
const showForm = ref(false)
const editingId = ref<number | null>(null)

const form = ref({ title: '', description: '', type: '', status: 'upcoming', start_datetime: '', end_datetime: '', venue: '', organizer: '', target_audience: '', max_participants: null as number | null, registration_fee: 0, requirements: '' })

const upcomingCount = computed(() => events.value.filter(e => e.status === 'upcoming').length)
const ongoingCount = computed(() => events.value.filter(e => e.status === 'ongoing').length)
const completedCount = computed(() => events.value.filter(e => e.status === 'completed').length)

const filteredEvents = computed(() => {
  let f = events.value
  if (searchQuery.value) { const q = searchQuery.value.toLowerCase(); f = f.filter(e => e.title?.toLowerCase().includes(q) || e.description?.toLowerCase().includes(q)) }
  if (filterType.value) f = f.filter(e => e.type === filterType.value)
  if (filterStatus.value) f = f.filter(e => e.status === filterStatus.value)
  return f
})

const fetchEvents = async () => { loading.value = true; error.value = ''; try { const r = await api.get('/events'); events.value = r.data } catch (err: any) { error.value = err.response?.data?.message || 'Failed to fetch events'; if (!events.value.length) generateSampleData() } finally { loading.value = false } }
const generateSampleData = async () => { try { await api.post('/events/generate-sample'); await fetchEvents() } catch { /* ignore */ } }
const openCreateForm = () => { editingId.value = null; form.value = { title: '', description: '', type: '', status: 'upcoming', start_datetime: '', end_datetime: '', venue: '', organizer: '', target_audience: '', max_participants: null, registration_fee: 0, requirements: '' }; showForm.value = true }
const editEvent = (e: any) => { editingId.value = e.id; form.value = { title: e.title, description: e.description, type: e.type, status: e.status, start_datetime: e.start_datetime?.substring(0,16), end_datetime: e.end_datetime?.substring(0,16), venue: e.venue||e.location||'', organizer: e.organizer, target_audience: e.target_audience, max_participants: e.max_participants, registration_fee: Number(e.registration_fee), requirements: e.requirements||'' }; showForm.value = true }
const saveEvent = async () => { try { if (editingId.value) await api.put(`/events/${editingId.value}`, form.value); else await api.post('/events', form.value); showForm.value = false; await fetchEvents() } catch (err: any) { alert(err.response?.data?.message || 'Failed to save event') } }
const deleteEvent = async (e: any) => { if (!confirm(`Delete "${e.title}"?`)) return; try { await api.delete(`/events/${e.id}`); events.value = events.value.filter(x => x.id !== e.id) } catch { alert('Failed to delete') } }
const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD'
const formatType = (t: string) => ({ extra_curricular: 'Extra Curricular', curricular: 'Curricular', academic: 'Academic', sports: 'Sports', cultural: 'Cultural', seminar: 'Seminar', workshop: 'Workshop' }[t] || t)

onMounted(() => fetchEvents())
</script>

<style scoped>
.events-view { padding: 2rem; max-width: 1400px; margin: 0 auto; }
.page-header { margin-bottom: 2rem; }
.header-content { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
.header-left h1 { font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0 0 0.25rem; }
.header-left p { color: #6b7280; margin: 0; }
.header-actions { display: flex; gap: 0.75rem; }

.stats-section { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); display: flex; align-items: center; gap: 1rem; }
.stat-icon { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: white; }
.stat-icon svg { width: 1.25rem; height: 1.25rem; }
.stat-icon.upcoming { background: #3b82f6; }
.stat-icon.ongoing { background: #f59e0b; }
.stat-icon.completed { background: #10b981; }
.stat-icon.total { background: #6366f1; }
.stat-content h4 { font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0; }
.stat-content p { color: #6b7280; font-size: 0.8rem; margin: 0; }

.filters-section { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
.form-input { padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; }
.form-input:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.1); }

.events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; }
.event-card { background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; transition: all .2s; }
.event-card:hover { box-shadow: 0 4px 6px rgba(0,0,0,.1); }
.event-header { display: flex; justify-content: space-between; margin-bottom: 0.75rem; }
.type-badge { padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; background: #dbeafe; color: #1e40af; }
.type-badge.sports { background: #fef3c7; color: #92400e; }
.type-badge.cultural { background: #fce7f3; color: #9d174d; }
.type-badge.seminar { background: #e0e7ff; color: #3730a3; }
.type-badge.workshop { background: #d1fae5; color: #065f46; }
.type-badge.academic { background: #fef9c3; color: #854d0e; }
.type-badge.extra_curricular { background: #f3e8ff; color: #6b21a8; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; }
.status-badge.upcoming { background: #dbeafe; color: #1e40af; }
.status-badge.ongoing { background: #fef3c7; color: #92400e; }
.status-badge.completed { background: #d1fae5; color: #065f46; }
.status-badge.cancelled { background: #fee2e2; color: #991b1b; }
.event-title { font-size: 1.1rem; font-weight: 600; color: #1f2937; margin: 0 0 0.5rem; }
.event-desc { color: #6b7280; font-size: 0.85rem; margin: 0 0 1rem; line-height: 1.5; }
.event-meta { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; color: #6b7280; font-size: 0.8rem; }
.event-actions { display: flex; gap: 0.5rem; }

.btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; transition: all .2s; }
.btn-primary { background: #f97316; color: white; }
.btn-primary:hover { background: #ea580c; }
.btn-secondary { background: #6b7280; color: white; }
.btn-secondary:hover { background: #4b5563; }
.btn-danger { background: #ef4444; color: white; }
.btn-danger:hover { background: #dc2626; }
.btn-outline { background: transparent; color: #6b7280; border: 1px solid #d1d5db; }
.btn-small { padding: 0.25rem 0.75rem; font-size: 0.75rem; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content { background: white; border-radius: 1rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 600; }
.close-btn { background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; }
.modal-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.5rem; }
.form-group { display: flex; flex-direction: column; gap: 0.25rem; }
.form-label { font-size: 0.8rem; font-weight: 500; color: #374151; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.loading-state, .error-state, .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4rem 2rem; text-align: center; }
.spinner { width: 3rem; height: 3rem; border: 3px solid #e5e7eb; border-top: 3px solid #f97316; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.error-state h3, .empty-state h3 { font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0 0 0.5rem; }
.error-state p, .empty-state p { color: #6b7280; margin: 0 0 1.5rem; }

@media (max-width: 768px) {
  .events-view { padding: 1rem; }
  .header-content { flex-direction: column; }
  .form-grid { grid-template-columns: 1fr; }
}
</style>
