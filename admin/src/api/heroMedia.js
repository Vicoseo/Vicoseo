import client from './client'

// Background Packages
export function getBackgroundPackages(params = {}) {
  return client.get('/admin/background-packages', { params })
}

export function createBackgroundPackage(data) {
  return client.post('/admin/background-packages', data)
}

export function updateBackgroundPackage(id, data) {
  return client.put(`/admin/background-packages/${id}`, data)
}

export function deleteBackgroundPackage(id) {
  return client.delete(`/admin/background-packages/${id}`)
}

// Site Hero Settings
export function getSiteHeroSettings(siteId) {
  return client.get(`/admin/sites/${siteId}/hero-settings`)
}

export function updateSiteHeroSettings(siteId, data) {
  return client.put(`/admin/sites/${siteId}/hero-settings`, data)
}

// Post Hero Settings
export function getPostHeroSettings(siteId, postId) {
  return client.get(`/admin/sites/${siteId}/posts/${postId}/hero`)
}

export function updatePostHeroSettings(siteId, postId, data) {
  return client.put(`/admin/sites/${siteId}/posts/${postId}/hero`, data)
}

export function previewPostHero(siteId, postId) {
  return client.get(`/admin/sites/${siteId}/posts/${postId}/hero/preview`)
}
