import client from './client'

export function getSites(params = {}) {
  return client.get('/admin/sites', { params })
}

export function getSite(id) {
  return client.get(`/admin/sites/${id}`)
}

export function createSite(data) {
  return client.post('/admin/sites', data)
}

export function updateSite(id, data) {
  return client.put(`/admin/sites/${id}`, data)
}

export function deleteSite(id) {
  return client.delete(`/admin/sites/${id}`)
}

export function aiGenerateContent(siteId, data) {
  return client.post(`/admin/sites/${siteId}/ai-generate`, data, { timeout: 600000 })
}

export function startBulkContent(data) {
  return client.post('/admin/bulk-content/start', data, { timeout: 30000 })
}

export function getBulkContentProgress(batchId) {
  return client.get(`/admin/bulk-content/progress/${batchId}`)
}

export function provisionSite(siteId) {
  return client.post(`/admin/sites/${siteId}/provision`, {}, { timeout: 120000 })
}

export function getProvisionStatus(siteId) {
  return client.get(`/admin/sites/${siteId}/provision-status`)
}
