import client from './client'

export function getGscPerformance(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/gsc/performance`, { params })
}

export function getGscSitemaps(siteId) {
  return client.get(`/admin/sites/${siteId}/gsc/sitemaps`)
}

export function submitGscSitemap(siteId, data = {}) {
  return client.post(`/admin/sites/${siteId}/gsc/sitemaps/submit`, data)
}

export function getGscPages(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/gsc/pages`, { params })
}
