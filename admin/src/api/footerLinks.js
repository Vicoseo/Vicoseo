import client from './client'

export function getFooterLinks(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/footer-links`, { params })
}

export function createFooterLink(siteId, data) {
  return client.post(`/admin/sites/${siteId}/footer-links`, data)
}

export function updateFooterLink(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/footer-links/${id}`, data)
}

export function deleteFooterLink(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/footer-links/${id}`)
}
