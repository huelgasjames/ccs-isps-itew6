import { defineStore } from 'pinia'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { offlineService } from '@/services/offline'
import { syncService } from '@/services/sync'

export const useOfflineStore = defineStore('offline', () => {
  const isOnline = ref(navigator.onLine)
  const isOfflineMode = computed(() => !isOnline.value)
  const isSyncing = ref(false)
  const lastSyncTime = ref<Date | null>(null)
  const syncError = ref<string | null>(null)

  const syncStatus = computed(() => {
    return syncService.getSyncStatus()
  })

  const pendingOperationsCount = computed(() => {
    return syncStatus.value.pendingOperations
  })

  // Network event listeners
  const handleOnline = () => {
    isOnline.value = true
    syncWhenOnline()
  }

  const handleOffline = () => {
    isOnline.value = false
  }

  // Auto-sync when coming back online
  const syncWhenOnline = async () => {
    if (pendingOperationsCount.value > 0) {
      try {
        isSyncing.value = true
        syncError.value = null
        
        const result = await syncService.syncAll()
        
        if (result.failed > 0) {
          syncError.value = `${result.failed} operations failed to sync`
        }
        
        lastSyncTime.value = new Date()
        
        // Refresh data from server
        await syncService.refreshFromServer('/announcements')
        
      } catch (error) {
        syncError.value = `Sync failed: ${error}`
      } finally {
        isSyncing.value = false
      }
    }
  }

  // Manual sync trigger
  const forceSync = async () => {
    if (!isOnline.value) {
      syncError.value = 'Cannot sync while offline'
      return
    }

    await syncWhenOnline()
  }

  // Initialize offline data
  const initializeOffline = () => {
    if (isOfflineMode.value) {
      offlineService.initializeDummyData()
    }
  }

  // Get offline status
  const getOfflineStatus = computed(() => {
    return offlineService.getStatus()
  })

  onMounted(() => {
    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)
    initializeOffline()
  })

  onUnmounted(() => {
    window.removeEventListener('online', handleOnline)
    window.removeEventListener('offline', handleOffline)
  })

  return {
    // State
    isOnline,
    isOfflineMode,
    isSyncing,
    lastSyncTime,
    syncError,
    
    // Computed
    syncStatus,
    pendingOperationsCount,
    getOfflineStatus,
    
    // Actions
    forceSync,
    initializeOffline
  }
})
