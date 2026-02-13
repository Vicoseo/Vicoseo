import client from './client'

export function getRedirects(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/redirects`, { params })
}

export function getRedirect(siteId, id) {
  return client.get(`/admin/sites/${siteId}/redirects/${id}`)
}

export function createRedirect(siteId, data) {
  return client.post(`/admin/sites/${siteId}/redirects`, data)
}

export function updateRedirect(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/redirects/${id}`, data)
}

export function deleteRedirect(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/redirects/${id}`)
}
