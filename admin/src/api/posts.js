import client from './client'

export function getPosts(siteId, params = {}) {
  return client.get(`/admin/sites/${siteId}/posts`, { params })
}

export function getPost(siteId, id) {
  return client.get(`/admin/sites/${siteId}/posts/${id}`)
}

export function createPost(siteId, data) {
  return client.post(`/admin/sites/${siteId}/posts`, data)
}

export function updatePost(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/posts/${id}`, data)
}

export function deletePost(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/posts/${id}`)
}

export function getPostRevisions(siteId, id) {
  return client.get(`/admin/sites/${siteId}/posts/${id}/revisions`)
}

export function revertPostRevision(siteId, id, revisionId) {
  return client.post(`/admin/sites/${siteId}/posts/${id}/revisions/${revisionId}/revert`)
}
