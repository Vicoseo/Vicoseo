import client from './client'

export function getEarnings(siteId) {
  return client.get(`/admin/sites/${siteId}/earnings`)
}

export function createEarning(siteId, data) {
  return client.post(`/admin/sites/${siteId}/earnings`, data)
}

export function updateEarning(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/earnings/${id}`, data)
}

export function deleteEarning(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/earnings/${id}`)
}
