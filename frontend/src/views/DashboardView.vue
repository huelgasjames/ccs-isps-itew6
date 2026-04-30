<template>
  <div class="dashboard-root" :class="{ 'dark': isDark }">
    <main class="main-content">

      <!-- ── Top Row: Welcome + Weather ── -->
      <div class="top-row">

        <!-- Welcome Banner -->
        <div class="welcome-banner">
          <div class="welcome-avatar">{{ user?.name?.charAt(0) ?? 'U' }}</div>
          <div class="welcome-text">
            <h1 class="welcome-name">Welcome back, <span class="accent">{{ user?.name }}</span></h1>
            <div class="welcome-chips">
              <span class="chip orange">{{ user?.role === 'admin' ? 'Administrator' : user?.role === 'professor' ? 'Professor' : 'Student' }}</span>
              <span class="chip gray">CCS Department</span>
              <span v-if="isDemoMode" class="chip yellow">Demo Mode</span>
            </div>
            <div class="welcome-time">{{ currentDate }}</div>
          </div>
          <div class="welcome-clock">{{ currentTime }}</div>
        </div>

        <!-- Weather Card -->
        <div class="weather-card">
          <div class="weather-location">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Laguna, Philippines
          </div>
          <div v-if="weatherLoading" class="weather-loading">
            <div class="mini-spinner"></div> Fetching weather…
          </div>
          <div v-else-if="weather" class="weather-body">
            <div class="weather-main">
              <span class="weather-icon">{{ weatherIcon(weather.condition) }}</span>
              <span class="weather-temp">{{ weather.temp }}°C</span>
            </div>
            <div class="weather-desc">{{ weather.condition }}</div>
            <div class="weather-details">
              <span>💧 {{ weather.humidity }}%</span>
              <span>💨 {{ weather.wind }} km/h</span>
              <span>👁 {{ weather.visibility }} km</span>
            </div>
            <div class="weather-updated">Updated {{ weather.updated }}</div>
          </div>
          <div v-else class="weather-error">Unable to load weather</div>
        </div>
      </div>

      <!-- ── Stat Cards ── -->
      <div class="stats-grid">
        <div class="stat-card blue" @click="$router.push('/students')">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ stats.totalStudents }}</div>
            <div class="stat-sub">Enrolled this semester</div>
          </div>
          <div class="stat-trend up">↑ Active</div>
        </div>

        <div class="stat-card green" @click="$router.push('/professors')">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Total Professors</div>
            <div class="stat-value">{{ stats.totalProfessors }}</div>
            <div class="stat-sub">Faculty members</div>
          </div>
          <div class="stat-trend up">↑ Active</div>
        </div>

        <div class="stat-card amber" @click="$router.push('/violations')">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">Pending Violations</div>
            <div class="stat-value">{{ stats.pendingViolations }}</div>
            <div class="stat-sub">Needs resolution</div>
          </div>
          <div class="stat-trend" :class="stats.pendingViolations > 0 ? 'warn' : 'up'">
            {{ stats.pendingViolations > 0 ? '⚠ Review' : '✓ Clear' }}
          </div>
        </div>

        <div class="stat-card red">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="stat-body">
            <div class="stat-label">At-Risk Students</div>
            <div class="stat-value">{{ stats.atRiskStudents }}</div>
            <div class="stat-sub">Requires attention</div>
          </div>
          <div class="stat-trend" :class="stats.atRiskStudents > 0 ? 'warn' : 'up'">
            {{ stats.atRiskStudents > 0 ? '⚠ Alert' : '✓ Good' }}
          </div>
        </div>
      </div>

      <!-- ── Analytics Section ── -->
      <div class="section-header">
        <h2 class="section-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Analytics
        </h2>
        <button class="refresh-btn" @click="fetchAll">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
          Refresh
        </button>
      </div>

      <div class="analytics-grid">

        <!-- Student Carousel -->
        <div class="analytics-panel">
          <div class="panel-header">
            <div class="panel-title-row">
              <span class="panel-dot blue"></span>
              <span class="panel-title">Student Profiles</span>
              <span class="panel-count">{{ students.length }} total</span>
            </div>
            <div class="carousel-controls">
              <button class="ctrl-btn" @click="prevStudent" :disabled="students.length === 0">‹</button>
              <span class="ctrl-indicator">{{ students.length ? studentIdx + 1 : 0 }} / {{ students.length }}</span>
              <button class="ctrl-btn" @click="nextStudent" :disabled="students.length === 0">›</button>
            </div>
          </div>

          <div v-if="studentsLoading" class="carousel-loading">
            <div class="mini-spinner"></div> Loading students…
          </div>
          <div v-else-if="students.length === 0" class="carousel-empty">No students found</div>
          <div v-else class="carousel-body">
            <transition :name="carouselDir" mode="out-in">
              <div :key="studentIdx" class="profile-card">
                <div class="profile-avatar blue">
                  {{ currentStudent?.personalInfo?.firstName?.charAt(0) ?? currentStudent?.first_name?.charAt(0) ?? 'S' }}
                </div>
                <div class="profile-info">
                  <div class="profile-name">
                    {{ currentStudent?.personalInfo?.firstName ?? currentStudent?.first_name }}
                    {{ currentStudent?.personalInfo?.lastName ?? currentStudent?.last_name }}
                  </div>
                  <div class="profile-id">{{ currentStudent?.personalInfo?.studentId ?? currentStudent?.student_id ?? '—' }}</div>
                  <div class="profile-tags">
                    <span class="ptag blue">{{ currentStudent?.academicStanding?.standing ?? currentStudent?.standing ?? 'N/A' }}</span>
                    <span class="ptag gray">Year {{ currentStudent?.academicStanding?.currentYear ?? currentStudent?.current_year ?? '?' }}</span>
                    <span class="ptag" :class="(currentStudent?.academicStanding?.currentGPA ?? currentStudent?.current_gpa ?? 0) >= 3 ? 'green' : 'amber'">
                      GPA {{ Number(currentStudent?.academicStanding?.currentGPA ?? currentStudent?.current_gpa ?? 0).toFixed(2) }}
                    </span>
                  </div>
                  <div class="profile-meta">
                    <span>📧 {{ currentStudent?.personalInfo?.email ?? currentStudent?.email ?? '—' }}</span>
                    <span>📱 {{ currentStudent?.personalInfo?.phone ?? currentStudent?.phone ?? '—' }}</span>
                  </div>
                </div>
                <div class="profile-stats">
                  <div class="pstat">
                    <div class="pstat-val">{{ currentStudent?.skills?.length ?? 0 }}</div>
                    <div class="pstat-label">Skills</div>
                  </div>
                  <div class="pstat">
                    <div class="pstat-val">{{ currentStudent?.activities?.length ?? 0 }}</div>
                    <div class="pstat-label">Activities</div>
                  </div>
                  <div class="pstat">
                    <div class="pstat-val">{{ currentStudent?.violations?.length ?? 0 }}</div>
                    <div class="pstat-label">Violations</div>
                  </div>
                </div>
              </div>
            </transition>
            <!-- Dot indicators -->
            <div class="dot-row">
              <span
                v-for="(_, i) in Math.min(students.length, 8)"
                :key="i"
                class="dot"
                :class="{ active: i === studentIdx % 8 }"
                @click="studentIdx = i"
              ></span>
              <span v-if="students.length > 8" class="dot-more">+{{ students.length - 8 }}</span>
            </div>
          </div>
        </div>

        <!-- Professor Carousel -->
        <div class="analytics-panel">
          <div class="panel-header">
            <div class="panel-title-row">
              <span class="panel-dot green"></span>
              <span class="panel-title">Professor Profiles</span>
              <span class="panel-count">{{ professors.length }} total</span>
            </div>
            <div class="carousel-controls">
              <button class="ctrl-btn" @click="prevProf" :disabled="professors.length === 0">‹</button>
              <span class="ctrl-indicator">{{ professors.length ? profIdx + 1 : 0 }} / {{ professors.length }}</span>
              <button class="ctrl-btn" @click="nextProf" :disabled="professors.length === 0">›</button>
            </div>
          </div>

          <div v-if="profsLoading" class="carousel-loading">
            <div class="mini-spinner"></div> Loading professors…
          </div>
          <div v-else-if="professors.length === 0" class="carousel-empty">No professors found</div>
          <div v-else class="carousel-body">
            <transition :name="carouselDir" mode="out-in">
              <div :key="profIdx" class="profile-card">
                <div class="profile-avatar green">
                  {{ currentProf?.first_name?.charAt(0) ?? 'P' }}
                </div>
                <div class="profile-info">
                  <div class="profile-name">{{ currentProf?.first_name }} {{ currentProf?.last_name }}</div>
                  <div class="profile-id">{{ currentProf?.professor_unique_id ?? '—' }}</div>
                  <div class="profile-tags">
                    <span class="ptag green">{{ currentProf?.employment_type ?? 'Full-time' }}</span>
                    <span class="ptag gray">{{ currentProf?.department ?? 'CCS' }}</span>
                  </div>
                  <div class="profile-meta">
                    <span>📧 {{ currentProf?.email ?? '—' }}</span>
                    <span>🎓 {{ currentProf?.educational_attainment ?? '—' }}</span>
                  </div>
                  <div class="prof-role-badge">{{ currentProf?.role ?? 'Instructor' }}</div>
                </div>
                <div class="profile-stats">
                  <div class="pstat">
                    <div class="pstat-val">{{ currentProf?.experience ?? '—' }}</div>
                    <div class="pstat-label">Experience</div>
                  </div>
                  <div class="pstat">
                    <div class="pstat-val">{{ String(currentProf?.courses_handled ?? '').split(',').filter(Boolean).length }}</div>
                    <div class="pstat-label">Courses</div>
                  </div>
                </div>
              </div>
            </transition>
            <div class="dot-row">
              <span
                v-for="(_, i) in Math.min(professors.length, 8)"
                :key="i"
                class="dot"
                :class="{ active: i === profIdx % 8 }"
                @click="profIdx = i"
              ></span>
              <span v-if="professors.length > 8" class="dot-more">+{{ professors.length - 8 }}</span>
            </div>
          </div>
        </div>

        <!-- Student Analytics Chart -->
        <div class="analytics-panel wide">
          <div class="panel-header">
            <div class="panel-title-row">
              <span class="panel-dot amber"></span>
              <span class="panel-title">Student Distribution</span>
            </div>
          </div>
          <div class="chart-area">
            <!-- Year Level Bar Chart -->
            <div class="chart-section">
              <div class="chart-label">By Year Level</div>
              <div class="bar-chart">
                <div v-for="(item, i) in yearLevelData" :key="i" class="bar-group">
                  <div class="bar-track">
                    <div class="bar-fill blue" :style="{ width: item.pct + '%' }"></div>
                  </div>
                  <div class="bar-meta">
                    <span class="bar-name">{{ item.label }}</span>
                    <span class="bar-val">{{ item.count }}</span>
                  </div>
                </div>
              </div>
            </div>
            <!-- Standing Donut-style -->
            <div class="chart-section">
              <div class="chart-label">By Academic Standing</div>
              <div class="standing-grid">
                <div v-for="(item, i) in standingData" :key="i" class="standing-item">
                  <div class="standing-circle" :class="item.color">{{ item.count }}</div>
                  <div class="standing-name">{{ item.label }}</div>
                </div>
              </div>
            </div>
            <!-- Program split -->
            <div class="chart-section">
              <div class="chart-label">By Program</div>
              <div class="bar-chart">
                <div v-for="(item, i) in programData" :key="i" class="bar-group">
                  <div class="bar-track">
                    <div class="bar-fill green" :style="{ width: item.pct + '%' }"></div>
                  </div>
                  <div class="bar-meta">
                    <span class="bar-name">{{ item.label }}</span>
                    <span class="bar-val">{{ item.count }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ── Quick Actions + Announcements ── -->
      <div class="bottom-grid">

        <!-- Quick Actions -->
        <div v-if="isAdmin" class="panel">
          <div class="panel-header">
            <div class="panel-title-row">
              <span class="panel-dot orange"></span>
              <span class="panel-title">Quick Actions</span>
            </div>
            <span class="admin-chip">Admin</span>
          </div>
          <div class="panel-body">
            <div class="qa-grid">
              <router-link to="/students/create" class="qa-btn blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="12" y1="14" x2="12" y2="20"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                Add Student
              </router-link>
              <router-link to="/professors/create" class="qa-btn green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Add Professor
              </router-link>
              <router-link to="/violations/create" class="qa-btn amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                Add Violation
              </router-link>
              <router-link to="/announcements/create" class="qa-btn orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6"/></svg>
                Announcement
              </router-link>
            </div>
          </div>
        </div>

        <!-- Recent Announcements -->
        <div class="panel flex-grow">
          <div class="panel-header">
            <div class="panel-title-row">
              <span class="panel-dot orange"></span>
              <span class="panel-title">Recent Announcements</span>
            </div>
            <router-link to="/announcements" class="view-all">View All →</router-link>
          </div>
          <div class="panel-body">
            <div v-if="loadingAnnouncements" class="carousel-loading">
              <div class="mini-spinner"></div> Loading…
            </div>
            <div v-else-if="announcements.length === 0" class="carousel-empty">No announcements</div>
            <div v-else class="ann-list">
              <div v-for="ann in announcements" :key="ann.id" class="ann-item" :class="{ unread: !ann.is_viewed }">
                <div class="ann-dot"></div>
                <div class="ann-body">
                  <div class="ann-title">{{ ann.title }}</div>
                  <div class="ann-excerpt">{{ ann.content?.substring(0, 90) }}…</div>
                  <div class="ann-meta">
                    <span>{{ formatDate(ann.created_at) }}</span>
                    <span class="ann-tag">{{ getTargetLabel(ann.target_type) }}</span>
                  </div>
                </div>
                <span v-if="!ann.is_viewed" class="new-badge">New</span>
              </div>
            </div>
          </div>
        </div>

      </div>

    </main>
  </div>
</template>



<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/services/api'
import { announcementService, type Announcement } from '@/services/announcements'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()
const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const isDemoMode = computed(() => authStore.isDemoMode)
const isDark = computed(() => themeStore.isDark)

// ── Clock ──────────────────────────────────────────────
const now = ref(new Date())
let clockTimer: any
const currentTime = computed(() => now.value.toLocaleTimeString('en-US', { hour12: false }))
const currentDate = computed(() => now.value.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }))

// ── Stats ──────────────────────────────────────────────
const stats = ref({ totalStudents: 0, totalProfessors: 0, pendingViolations: 0, atRiskStudents: 0 })

// ── Students ───────────────────────────────────────────
const students = ref<any[]>([])
const studentsLoading = ref(true)
const studentIdx = ref(0)
const carouselDir = ref('slide-left')

const currentStudent = computed(() => students.value[studentIdx.value] ?? null)

const nextStudent = () => {
  if (students.value.length === 0) return
  carouselDir.value = 'slide-left'
  studentIdx.value = (studentIdx.value + 1) % students.value.length
}
const prevStudent = () => {
  if (students.value.length === 0) return
  carouselDir.value = 'slide-right'
  studentIdx.value = (studentIdx.value - 1 + students.value.length) % students.value.length
}

// ── Professors ─────────────────────────────────────────
const professors = ref<any[]>([])
const profsLoading = ref(true)
const profIdx = ref(0)

const currentProf = computed(() => professors.value[profIdx.value] ?? null)

const nextProf = () => {
  if (professors.value.length === 0) return
  carouselDir.value = 'slide-left'
  profIdx.value = (profIdx.value + 1) % professors.value.length
}
const prevProf = () => {
  if (professors.value.length === 0) return
  carouselDir.value = 'slide-right'
  profIdx.value = (profIdx.value - 1 + professors.value.length) % professors.value.length
}

// ── Analytics computed ─────────────────────────────────
const yearLevelData = computed(() => {
  const counts: Record<string, number> = { '1st': 0, '2nd': 0, '3rd': 0, '4th': 0 }
  students.value.forEach(s => {
    const yr = s.academicStanding?.currentYear ?? s.current_year ?? s.year_level
    const key = yr === 1 || yr === '1st' ? '1st'
              : yr === 2 || yr === '2nd' ? '2nd'
              : yr === 3 || yr === '3rd' ? '3rd'
              : yr === 4 || yr === '4th' ? '4th' : null
    if (key) counts[key]++
  })
  const max = Math.max(...Object.values(counts), 1)
  return Object.entries(counts).map(([label, count]) => ({ label, count, pct: Math.round((count / max) * 100) }))
})

const standingData = computed(() => {
  const map: Record<string, { label: string; color: string; count: number }> = {
    good:      { label: 'Good',      color: 'green', count: 0 },
    excellent: { label: 'Excellent', color: 'blue',  count: 0 },
    average:   { label: 'Average',   color: 'amber', count: 0 },
    probation: { label: 'Probation', color: 'red',   count: 0 },
    warning:   { label: 'Warning',   color: 'amber', count: 0 },
  }
  students.value.forEach(s => {
    const st = (s.academicStanding?.standing ?? s.standing ?? 'good').toLowerCase()
    if (map[st]) map[st].count++
    else map['good'].count++
  })
  return Object.values(map).filter(v => v.count > 0)
})

const programData = computed(() => {
  const counts: Record<string, number> = {}
  students.value.forEach(s => {
    const prog = s.academicStanding?.program ?? s.program ?? 'BSIT'
    counts[prog] = (counts[prog] ?? 0) + 1
  })
  const max = Math.max(...Object.values(counts), 1)
  return Object.entries(counts).map(([label, count]) => ({ label, count, pct: Math.round((count / max) * 100) }))
})

// ── Weather ────────────────────────────────────────────
const weather = ref<any>(null)
const weatherLoading = ref(true)

const weatherIcon = (condition: string) => {
  const c = condition.toLowerCase()
  if (c.includes('thunder')) return '⛈'
  if (c.includes('rain') || c.includes('drizzle')) return '🌧'
  if (c.includes('cloud')) return '☁️'
  if (c.includes('clear') || c.includes('sunny')) return '☀️'
  if (c.includes('fog') || c.includes('mist')) return '🌫'
  if (c.includes('snow')) return '❄️'
  return '🌤'
}

const fetchWeather = async () => {
  try {
    weatherLoading.value = true
    // Open-Meteo — free, no API key, lat/lon for Laguna (Santa Cruz)
    const res = await fetch(
      'https://api.open-meteo.com/v1/forecast?latitude=14.2793&longitude=121.4117&current=temperature_2m,relative_humidity_2m,wind_speed_10m,visibility,weather_code&timezone=Asia%2FManila'
    )
    const data = await res.json()
    const c = data.current
    const codeMap: Record<number, string> = {
      0: 'Clear Sky', 1: 'Mainly Clear', 2: 'Partly Cloudy', 3: 'Overcast',
      45: 'Foggy', 48: 'Icy Fog', 51: 'Light Drizzle', 53: 'Drizzle', 55: 'Heavy Drizzle',
      61: 'Light Rain', 63: 'Rain', 65: 'Heavy Rain', 71: 'Light Snow', 73: 'Snow', 75: 'Heavy Snow',
      80: 'Rain Showers', 81: 'Rain Showers', 82: 'Violent Showers',
      95: 'Thunderstorm', 96: 'Thunderstorm w/ Hail', 99: 'Thunderstorm w/ Heavy Hail',
    }
    weather.value = {
      temp: Math.round(c.temperature_2m),
      humidity: c.relative_humidity_2m,
      wind: Math.round(c.wind_speed_10m),
      visibility: Math.round((c.visibility ?? 10000) / 1000),
      condition: codeMap[c.weather_code] ?? 'Unknown',
      updated: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    }
  } catch {
    weather.value = null
  } finally {
    weatherLoading.value = false
  }
}

// ── Announcements ──────────────────────────────────────
const announcements = ref<Announcement[]>([])
const loadingAnnouncements = ref(true)

const fetchAnnouncements = async () => {
  try {
    loadingAnnouncements.value = true
    const all = await announcementService.getAnnouncements()
    if (!Array.isArray(all)) { announcements.value = []; return }
    announcements.value = all
      .filter(a => {
        if (a.status !== 'published') return false
        if (!authStore.user) return false
        if (a.target_type === 'all') return true
        if (a.target_type === 'students' && authStore.user.role === 'student') return true
        if (a.target_type === 'professors' && authStore.user.role === 'professor') return true
        if (a.target_type === 'specific' && a.target_users) return a.target_users.includes(authStore.user.id)
        return authStore.user.role === 'admin'
      })
      .slice(0, 5)
  } catch { announcements.value = [] }
  finally { loadingAnnouncements.value = false }
}

const formatDate = (d: string) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
const getTargetLabel = (t: string) => ({ all: 'All', students: 'Students', professors: 'Professors', specific: 'Specific' }[t] ?? t)

// ── Data fetching ──────────────────────────────────────
const generateSampleAnalytics = () => {
  stats.value = {
    totalStudents: Math.floor(Math.random() * 500) + 200,
    totalProfessors: Math.floor(Math.random() * 50) + 20,
    pendingViolations: Math.floor(Math.random() * 10),
    atRiskStudents: Math.floor(Math.random() * 30) + 5
  }
}

const fetchStats = async () => {
  try {
    const [sRes, pRes] = await Promise.all([
      api.get('/students?per_page=1000'),
      api.get('/professors?per_page=100'),
    ])
    const sd = sRes.data.students ?? sRes.data.data ?? sRes.data
    const pd = pRes.data.data ?? pRes.data
    stats.value.totalStudents = Array.isArray(sd) ? sd.length : 0
    stats.value.totalProfessors = Array.isArray(pd) ? pd.length : 0
    if (Array.isArray(sd)) stats.value.atRiskStudents = sd.filter((s: any) => s.is_at_risk === true).length
  } catch {
    // Generate sample data when backend is unavailable
    generateSampleAnalytics()
  }
}

const generateSampleStudents = () => {
  const firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Chris', 'Anna', 'Mark', 'Lisa']
  const lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez']
  const standings = ['Good', 'Excellent', 'Average', 'Probation']
  const programs = ['BSIT', 'BSCS', 'BSIS', 'ACT']
  
  return Array.from({ length: 10 }, (_, i) => ({
    id: i + 1,
    personalInfo: {
      firstName: firstNames[i % firstNames.length],
      lastName: lastNames[i % lastNames.length],
      studentId: `2024-${String(i + 1).padStart(4, '0')}`,
      email: `student${i + 1}@example.com`,
      phone: `+63 9${Math.floor(Math.random() * 1000000000)}`
    },
    academicStanding: {
      standing: standings[Math.floor(Math.random() * standings.length)],
      currentYear: Math.floor(Math.random() * 4) + 1,
      currentGPA: (Math.random() * 2 + 2).toFixed(2),
      program: programs[Math.floor(Math.random() * programs.length)]
    },
    skills: Array.from({ length: Math.floor(Math.random() * 5) + 1 }, (_, j) => ({ id: j })),
    activities: Array.from({ length: Math.floor(Math.random() * 3) }, (_, j) => ({ id: j })),
    violations: Array.from({ length: Math.floor(Math.random() * 2) }, (_, j) => ({ id: j }))
  }))
}

const fetchStudents = async () => {
  try {
    studentsLoading.value = true
    const res = await api.get('/students?per_page=1000')
    const data = res.data.students ?? res.data.data ?? res.data
    students.value = Array.isArray(data) ? data : []
  } catch {
    // Generate sample students when backend is unavailable
    students.value = generateSampleStudents()
  }
  finally { studentsLoading.value = false }
}

const generateSampleProfessors = () => {
  const firstNames = ['Dr. Robert', 'Prof. Maria', 'Dr. James', 'Prof. Linda', 'Dr. William', 'Prof. Patricia']
  const lastNames = ['Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin']
  const departments = ['CCS', 'Engineering', 'Business', 'Arts']
  const roles = ['Instructor', 'Associate Professor', 'Full Professor', 'Lecturer']
  
  return Array.from({ length: 8 }, (_, i) => ({
    id: i + 1,
    first_name: firstNames[i % firstNames.length].split(' ')[1] || firstNames[i % firstNames.length],
    last_name: lastNames[i % lastNames.length],
    professor_unique_id: `PROF-${String(i + 1).padStart(4, '0')}`,
    email: `prof${i + 1}@example.com`,
    department: departments[Math.floor(Math.random() * departments.length)],
    employment_type: Math.random() > 0.3 ? 'Full-time' : 'Part-time',
    educational_attainment: Math.random() > 0.5 ? 'PhD' : 'Masters',
    role: roles[Math.floor(Math.random() * roles.length)],
    experience: `${Math.floor(Math.random() * 20) + 1} years`,
    courses_handled: ['CS101', 'CS102', 'IT201'].slice(0, Math.floor(Math.random() * 3) + 1).join(',')
  }))
}

const fetchProfessors = async () => {
  try {
    profsLoading.value = true
    const res = await api.get('/professors?per_page=100')
    const data = res.data.data ?? res.data
    professors.value = Array.isArray(data) ? data : []
  } catch {
    // Generate sample professors when backend is unavailable
    professors.value = generateSampleProfessors()
  }
  finally { profsLoading.value = false }
}

const fetchAll = () => {
  fetchStats()
  fetchStudents()
  fetchProfessors()
  fetchAnnouncements()
  fetchWeather()
}

// ── Auto-advance carousels ─────────────────────────────
let studentTimer: any
let profTimer: any

const startCarousels = () => {
  studentTimer = setInterval(() => {
    if (students.value.length > 1) nextStudent()
  }, 5000)
  profTimer = setInterval(() => {
    if (professors.value.length > 1) nextProf()
  }, 6000)
}

onMounted(() => {
  fetchAll()
  clockTimer = setInterval(() => { now.value = new Date() }, 1000)
  startCarousels()
})

onUnmounted(() => {
  clearInterval(clockTimer)
  clearInterval(studentTimer)
  clearInterval(profTimer)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.dashboard-root {
  min-height: 100vh;
  background: #f8fafc;
  font-family: 'Inter', sans-serif;
}

.main-content {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* ── Top Row ── */
.top-row {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

/* Welcome Banner */
.welcome-banner {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border-radius: 12px;
  padding: 1.5rem 2rem;
  color: white;
  position: relative;
  overflow: hidden;
}
.welcome-banner::before {
  content: '';
  position: absolute;
  top: -40px; right: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(249, 115, 22, 0.12);
}
.welcome-avatar {
  width: 56px; height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f97316, #fb923c);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; font-weight: 700; color: white;
  flex-shrink: 0;
  box-shadow: 0 0 0 3px rgba(249,115,22,0.3);
}
.welcome-text { flex: 1; }
.welcome-name { font-size: 1.35rem; font-weight: 700; margin-bottom: 0.4rem; }
.welcome-name .accent { color: #f97316; }
.welcome-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.4rem; }
.chip { font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
.chip.orange { background: rgba(249,115,22,0.2); color: #fb923c; border: 1px solid rgba(249,115,22,0.3); }
.chip.gray   { background: rgba(255,255,255,0.1); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); }
.chip.yellow { background: rgba(234,179,8,0.2); color: #fbbf24; border: 1px solid rgba(234,179,8,0.3); }
.welcome-time { font-size: 0.75rem; color: #64748b; }
.welcome-clock {
  font-size: 2rem; font-weight: 800;
  color: white; letter-spacing: 0.05em;
  font-variant-numeric: tabular-nums;
  flex-shrink: 0;
}

/* Weather Card */
.weather-card {
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  border-radius: 12px;
  padding: 1.25rem;
  color: white;
}
.weather-location {
  display: flex; align-items: center; gap: 6px;
  font-size: 0.8rem; font-weight: 600; opacity: 0.9;
  margin-bottom: 0.75rem;
}
.weather-location svg { width: 14px; height: 14px; }
.weather-loading, .weather-error {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.85rem; opacity: 0.8;
}
.weather-main {
  display: flex; align-items: center; gap: 0.5rem;
  margin-bottom: 0.25rem;
}
.weather-icon { font-size: 2.5rem; line-height: 1; }
.weather-temp { font-size: 2.5rem; font-weight: 800; }
.weather-desc { font-size: 0.9rem; font-weight: 500; opacity: 0.9; margin-bottom: 0.75rem; }
.weather-details {
  display: flex; gap: 0.75rem;
  font-size: 0.75rem; opacity: 0.85;
  margin-bottom: 0.5rem;
}
.weather-updated { font-size: 0.65rem; opacity: 0.6; }

/* ── Stat Cards ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.25rem;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  overflow: hidden;
}
.stat-card::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
}
.stat-card.blue::after  { background: #3b82f6; }
.stat-card.green::after { background: #10b981; }
.stat-card.amber::after { background: #f59e0b; }
.stat-card.red::after   { background: #ef4444; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }

.stat-icon-wrap {
  width: 46px; height: 46px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.stat-icon-wrap svg { width: 22px; height: 22px; }
.stat-card.blue  .stat-icon-wrap { background: #eff6ff; color: #3b82f6; }
.stat-card.green .stat-icon-wrap { background: #ecfdf5; color: #10b981; }
.stat-card.amber .stat-icon-wrap { background: #fffbeb; color: #f59e0b; }
.stat-card.red   .stat-icon-wrap { background: #fef2f2; color: #ef4444; }

.stat-body { flex: 1; }
.stat-label { font-size: 0.75rem; color: #64748b; font-weight: 500; margin-bottom: 4px; }
.stat-value { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 4px; }
.stat-sub { font-size: 0.7rem; color: #94a3b8; }
.stat-trend {
  font-size: 0.7rem; font-weight: 600;
  padding: 3px 8px; border-radius: 20px;
  align-self: flex-start; flex-shrink: 0;
}
.stat-trend.up   { background: #dcfce7; color: #16a34a; }
.stat-trend.warn { background: #fef3c7; color: #d97706; }

/* ── Section Header ── */
.section-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 1rem;
}
.section-title {
  display: flex; align-items: center; gap: 8px;
  font-size: 1.1rem; font-weight: 700; color: #0f172a;
}
.section-title svg { width: 18px; height: 18px; color: #f97316; }
.refresh-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 8px;
  background: white; border: 1px solid #e2e8f0;
  color: #475569; font-size: 0.8rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
}
.refresh-btn svg { width: 14px; height: 14px; }
.refresh-btn:hover { border-color: #f97316; color: #f97316; }

/* ── Analytics Grid ── */
.analytics-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.analytics-panel {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}
.analytics-panel.wide {
  grid-column: 1 / -1;
}

/* Panel Header */
.panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #f1f5f9;
}
.panel-title-row {
  display: flex; align-items: center; gap: 8px;
}
.panel-dot {
  width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.panel-dot.blue   { background: #3b82f6; }
.panel-dot.green  { background: #10b981; }
.panel-dot.amber  { background: #f59e0b; }
.panel-dot.orange { background: #f97316; }
.panel-title { font-size: 0.9rem; font-weight: 600; color: #0f172a; }
.panel-count {
  font-size: 0.7rem; padding: 2px 8px; border-radius: 20px;
  background: #f1f5f9; color: #64748b; font-weight: 500;
}

/* Carousel Controls */
.carousel-controls {
  display: flex; align-items: center; gap: 8px;
}
.ctrl-btn {
  width: 28px; height: 28px;
  border-radius: 6px; border: 1px solid #e2e8f0;
  background: white; color: #475569;
  font-size: 1rem; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.ctrl-btn:hover:not(:disabled) { border-color: #f97316; color: #f97316; }
.ctrl-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.ctrl-indicator { font-size: 0.75rem; color: #94a3b8; min-width: 40px; text-align: center; }

/* Carousel Body */
.carousel-loading, .carousel-empty {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 2.5rem; color: #94a3b8; font-size: 0.875rem;
}
.carousel-body { padding: 1.25rem; }

/* Profile Card */
.profile-card {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}
.profile-avatar {
  width: 52px; height: 52px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; font-weight: 700; color: white;
  flex-shrink: 0;
}
.profile-avatar.blue  { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.profile-avatar.green { background: linear-gradient(135deg, #10b981, #059669); }

.profile-info { flex: 1; min-width: 0; }
.profile-name { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.profile-id { font-size: 0.75rem; color: #94a3b8; margin-bottom: 8px; }
.profile-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px; }
.ptag {
  font-size: 0.65rem; padding: 2px 8px; border-radius: 20px; font-weight: 600;
}
.ptag.blue   { background: #eff6ff; color: #2563eb; }
.ptag.green  { background: #ecfdf5; color: #059669; }
.ptag.amber  { background: #fffbeb; color: #d97706; }
.ptag.gray   { background: #f1f5f9; color: #475569; }
.ptag.red    { background: #fef2f2; color: #dc2626; }
.profile-meta { display: flex; flex-direction: column; gap: 2px; }
.profile-meta span { font-size: 0.72rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prof-role-badge {
  display: inline-block; margin-top: 6px;
  font-size: 0.7rem; padding: 3px 10px; border-radius: 20px;
  background: #ecfdf5; color: #059669; font-weight: 600;
}

.profile-stats {
  display: flex; flex-direction: column; gap: 8px;
  flex-shrink: 0;
}
.pstat { text-align: center; }
.pstat-val { font-size: 1.25rem; font-weight: 800; color: #0f172a; }
.pstat-label { font-size: 0.65rem; color: #94a3b8; font-weight: 500; }

/* Dot indicators */
.dot-row {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  margin-top: 1rem;
}
.dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #e2e8f0; cursor: pointer; transition: all 0.2s;
}
.dot.active { background: #f97316; width: 18px; border-radius: 3px; }
.dot-more { font-size: 0.65rem; color: #94a3b8; }

/* Carousel transitions */
.slide-left-enter-active, .slide-left-leave-active,
.slide-right-enter-active, .slide-right-leave-active {
  transition: all 0.3s ease;
}
.slide-left-enter-from  { opacity: 0; transform: translateX(30px); }
.slide-left-leave-to    { opacity: 0; transform: translateX(-30px); }
.slide-right-enter-from { opacity: 0; transform: translateX(-30px); }
.slide-right-leave-to   { opacity: 0; transform: translateX(30px); }

/* ── Chart Area ── */
.chart-area {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  padding: 1.25rem;
}
.chart-section { }
.chart-label { font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }

.bar-chart { display: flex; flex-direction: column; gap: 10px; }
.bar-group { }
.bar-track {
  height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; margin-bottom: 4px;
}
.bar-fill {
  height: 100%; border-radius: 4px; transition: width 0.8s ease;
}
.bar-fill.blue  { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.bar-fill.green { background: linear-gradient(90deg, #10b981, #34d399); }
.bar-meta { display: flex; justify-content: space-between; }
.bar-name { font-size: 0.75rem; color: #475569; }
.bar-val  { font-size: 0.75rem; font-weight: 600; color: #0f172a; }

.standing-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
.standing-item { text-align: center; }
.standing-circle {
  width: 48px; height: 48px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; font-weight: 800; margin: 0 auto 4px;
}
.standing-circle.green { background: #dcfce7; color: #16a34a; }
.standing-circle.blue  { background: #dbeafe; color: #2563eb; }
.standing-circle.amber { background: #fef3c7; color: #d97706; }
.standing-circle.red   { background: #fee2e2; color: #dc2626; }
.standing-name { font-size: 0.7rem; color: #64748b; font-weight: 500; }

/* ── Bottom Grid ── */
.bottom-grid {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 1rem;
}
.panel {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}
.panel.flex-grow { }
.panel-body { padding: 1rem 1.25rem; }

/* Quick Actions */
.qa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.qa-btn {
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  padding: 1rem 0.5rem; border-radius: 10px; text-decoration: none;
  font-size: 0.75rem; font-weight: 600; transition: all 0.2s;
  border: 1px solid;
}
.qa-btn svg { width: 20px; height: 20px; }
.qa-btn.blue   { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.qa-btn.green  { background: #ecfdf5; border-color: #a7f3d0; color: #059669; }
.qa-btn.amber  { background: #fffbeb; border-color: #fde68a; color: #d97706; }
.qa-btn.orange { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
.qa-btn:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

.admin-chip {
  font-size: 0.65rem; padding: 2px 8px; border-radius: 20px;
  background: #fef3c7; color: #d97706; font-weight: 600;
}
.view-all {
  font-size: 0.8rem; color: #f97316; text-decoration: none; font-weight: 500;
}
.view-all:hover { color: #ea580c; }

/* Announcements */
.ann-list { display: flex; flex-direction: column; gap: 0.75rem; }
.ann-item {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 0.75rem; border-radius: 8px;
  background: #f8fafc; border: 1px solid #f1f5f9;
  transition: all 0.2s; position: relative;
}
.ann-item.unread { border-left: 3px solid #f97316; background: #fffbeb; }
.ann-item:hover { border-color: #f97316; }
.ann-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #f97316; flex-shrink: 0; margin-top: 5px;
}
.ann-body { flex: 1; min-width: 0; }
.ann-title { font-size: 0.875rem; font-weight: 600; color: #0f172a; margin-bottom: 3px; }
.ann-excerpt { font-size: 0.75rem; color: #64748b; line-height: 1.4; margin-bottom: 6px; }
.ann-meta { display: flex; gap: 8px; font-size: 0.7rem; color: #94a3b8; }
.ann-tag {
  padding: 1px 6px; border-radius: 4px;
  background: #f1f5f9; color: #475569; font-weight: 500;
}
.new-badge {
  font-size: 0.65rem; padding: 2px 6px; border-radius: 4px;
  background: #f97316; color: white; font-weight: 600;
  flex-shrink: 0; align-self: flex-start;
}

/* Spinner */
.mini-spinner {
  width: 16px; height: 16px;
  border: 2px solid #e2e8f0;
  border-top-color: #f97316;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Dark Mode ── */
.dark .dashboard-root {
  background: #0f172a;
}

.dark .welcome-banner {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border: 1px solid #334155;
}

.dark .weather-card {
  background: linear-gradient(135deg, #0c4a6e 0%, #075985 100%);
}

.dark .stat-card {
  background: #1e293b;
  border-color: #334155;
}

.dark .stat-card:hover {
  background: #334155;
  box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}

.dark .stat-label {
  color: #94a3b8;
}

.dark .stat-value {
  color: #f1f5f9;
}

.dark .stat-sub {
  color: #64748b;
}

.dark .stat-card.blue .stat-icon-wrap {
  background: rgba(59, 130, 246, 0.15);
}

.dark .stat-card.green .stat-icon-wrap {
  background: rgba(16, 185, 129, 0.15);
}

.dark .stat-card.amber .stat-icon-wrap {
  background: rgba(245, 158, 11, 0.15);
}

.dark .stat-card.red .stat-icon-wrap {
  background: rgba(239, 68, 68, 0.15);
}

.dark .section-title {
  color: #f1f5f9;
}

.dark .refresh-btn {
  background: #1e293b;
  border-color: #334155;
  color: #94a3b8;
}

.dark .refresh-btn:hover {
  border-color: #f97316;
  color: #f97316;
  background: #334155;
}

.dark .analytics-panel {
  background: #1e293b;
  border-color: #334155;
}

.dark .panel-header {
  border-bottom-color: #334155;
}

.dark .panel-title {
  color: #f1f5f9;
}

.dark .panel-count {
  background: #334155;
  color: #94a3b8;
}

.dark .ctrl-btn {
  background: #334155;
  border-color: #475569;
  color: #94a3b8;
}

.dark .ctrl-btn:hover:not(:disabled) {
  border-color: #f97316;
  color: #f97316;
  background: #475569;
}

.dark .ctrl-indicator {
  color: #64748b;
}

.dark .carousel-loading,
.dark .carousel-empty {
  color: #64748b;
}

.dark .profile-name {
  color: #f1f5f9;
}

.dark .profile-id {
  color: #64748b;
}

.dark .profile-meta span {
  color: #94a3b8;
}

.dark .pstat-val {
  color: #f1f5f9;
}

.dark .pstat-label {
  color: #64748b;
}

.dark .dot {
  background: #475569;
}

.dark .dot.active {
  background: #f97316;
}

.dark .chart-label {
  color: #94a3b8;
}

.dark .bar-track {
  background: #334155;
}

.dark .bar-name {
  color: #94a3b8;
}

.dark .bar-val {
  color: #f1f5f9;
}

.dark .standing-circle.green {
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
}

.dark .standing-circle.blue {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
}

.dark .standing-circle.amber {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
}

.dark .standing-circle.red {
  background: rgba(239, 68, 68, 0.2);
  color: #f87171;
}

.dark .standing-name {
  color: #94a3b8;
}

.dark .panel {
  background: #1e293b;
  border-color: #334155;
}

.dark .qa-btn.blue {
  background: rgba(59, 130, 246, 0.15);
  border-color: rgba(59, 130, 246, 0.3);
  color: #60a5fa;
}

.dark .qa-btn.green {
  background: rgba(16, 185, 129, 0.15);
  border-color: rgba(16, 185, 129, 0.3);
  color: #34d399;
}

.dark .qa-btn.amber {
  background: rgba(245, 158, 11, 0.15);
  border-color: rgba(245, 158, 11, 0.3);
  color: #fbbf24;
}

.dark .qa-btn.orange {
  background: rgba(249, 115, 22, 0.15);
  border-color: rgba(249, 115, 22, 0.3);
  color: #fb923c;
}

.dark .qa-btn:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.dark .admin-chip {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
}

.dark .view-all {
  color: #fb923c;
}

.dark .view-all:hover {
  color: #f97316;
}

.dark .ann-item {
  background: #334155;
  border-color: #475569;
}

.dark .ann-item.unread {
  background: rgba(249, 115, 22, 0.1);
  border-left-color: #f97316;
}

.dark .ann-item:hover {
  border-color: #f97316;
  background: #475569;
}

.dark .ann-title {
  color: #f1f5f9;
}

.dark .ann-excerpt {
  color: #94a3b8;
}

.dark .ann-meta {
  color: #64748b;
}

.dark .ann-tag {
  background: #475569;
  color: #94a3b8;
}

.dark .mini-spinner {
  border-color: #475569;
  border-top-color: #f97316;
}

/* ── Responsive ── */
@media (max-width: 1024px) {
  .top-row { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .analytics-grid { grid-template-columns: 1fr; }
  .analytics-panel.wide { grid-column: 1; }
  .chart-area { grid-template-columns: 1fr; }
  .bottom-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
  .main-content { padding: 1rem; }
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .welcome-clock { display: none; }
}
</style>
