import client from './client'

export function getSiteAnalytics(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/analytics`, { params })
}

export function getAnalyticsSummary(params = {}) {
  return client.get('/admin/analytics/summary', { params })
}
