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
