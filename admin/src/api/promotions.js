import client from './client'

export function getPromotions(siteId) {
  return client.get(`/admin/sites/${siteId}/promotions`)
}

export function createPromotion(siteId, data) {
  return client.post(`/admin/sites/${siteId}/promotions`, data)
}

export function updatePromotion(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/promotions/${id}`, data)
}

export function deletePromotion(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/promotions/${id}`)
}
