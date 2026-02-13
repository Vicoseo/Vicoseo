import client from './client'

// Global top offers
export function getGlobalOffers(params = {}) {
  return client.get('/admin/global-top-offers', { params })
}

export function createGlobalOffer(data) {
  return client.post('/admin/global-top-offers', data)
}

export function updateGlobalOffer(id, data) {
  return client.put(`/admin/global-top-offers/${id}`, data)
}

export function deleteGlobalOffer(id) {
  return client.delete(`/admin/global-top-offers/${id}`)
}

// Site-specific top offers
export function getSiteOffers(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/top-offers`, { params })
}

export function getSiteOffer(siteId, id) {
  return client.get(`/admin/sites/${siteId}/top-offers/${id}`)
}

export function createSiteOffer(siteId, data) {
  return client.post(`/admin/sites/${siteId}/top-offers`, data)
}

export function updateSiteOffer(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/top-offers/${id}`, data)
}

export function deleteSiteOffer(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/top-offers/${id}`)
}
