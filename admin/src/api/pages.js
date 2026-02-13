import client from './client'

export function getPages(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/pages`, { params })
}

export function getPage(siteId, id) {
  return client.get(`/admin/sites/${siteId}/pages/${id}`)
}

export function createPage(siteId, data) {
  return client.post(`/admin/sites/${siteId}/pages`, data)
}

export function updatePage(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/pages/${id}`, data)
}

export function deletePage(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/pages/${id}`)
}
