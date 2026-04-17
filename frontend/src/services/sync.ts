import { offlineService, type OfflineOperation } from './offline'
import api from './api'

class SyncService {
  // Sync all pending operations when back online
  async syncAll(): Promise<{ success: number; failed: number; errors: string[] }> {
    const operations = offlineService.getPendingOperations().filter(op => !op.synced)
    let success = 0
    let failed = 0
    const errors: string[] = []

    for (const operation of operations) {
      try {
        await this.syncOperation(operation)
        offlineService.markOperationAsSynced(operation.id)
        success++
      } catch (error) {
        failed++
        errors.push(`Failed to sync ${operation.type} operation: ${error}`)
      }
    }

    // Clear synced operations
    offlineService.clearSyncedOperations()

    return { success, failed, errors }
  }

  // Sync individual operation
  private async syncOperation(operation: OfflineOperation): Promise<void> {
    const { type, endpoint, data } = operation

    switch (type) {
      case 'CREATE':
        await api.post(endpoint, data)
        break
      case 'UPDATE':
        await api.put(endpoint, data)
        break
      case 'DELETE':
        await api.delete(endpoint)
        break
      default:
        throw new Error(`Unknown operation type: ${type}`)
    }
  }

  // Get sync status
  getSyncStatus() {
    const operations = offlineService.getPendingOperations()
    const pending = operations.filter(op => !op.synced)
    
    return {
      totalOperations: operations.length,
      pendingOperations: pending.length,
      syncedOperations: operations.filter(op => op.synced).length,
      lastSyncTime: operations.length > 0 
        ? Math.max(...operations.map(op => op.timestamp))
        : null
    }
  }

  // Force refresh local data from server
  async refreshFromServer(endpoint: string): Promise<void> {
    try {
      const response = await api.get(endpoint)
      
      if (endpoint.includes('/announcements')) {
        offlineService.setLocalAnnouncements(response.data)
      }
      
      if (endpoint.includes('/students')) {
        if (endpoint.includes('/statistics')) {
          // Statistics don't need to be cached
          return
        }
        offlineService.setLocalStudents(response.data.students || response.data)
      }
      // Add more endpoints as needed
      
    } catch (error) {
      console.warn('Failed to refresh from server:', error)
    }
  }
}

export const syncService = new SyncService()
export default syncService
