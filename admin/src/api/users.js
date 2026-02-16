import client from './client'

export function getUsers(params = {}) {
  return client.get('/admin/users', { params })
}

export function getUser(id) {
  return client.get(`/admin/users/${id}`)
}

export function createUser(data) {
  return client.post('/admin/users', data)
}

export function updateUser(id, data) {
  return client.put(`/admin/users/${id}`, data)
}

export function deleteUser(id) {
  return client.delete(`/admin/users/${id}`)
}

export function resetUserPassword(id, data) {
  return client.post(`/admin/users/${id}/reset-password`, data)
}

export function resetUser2FA(id) {
  return client.post(`/admin/users/${id}/reset-2fa`)
}

export function enable2FA() {
  return client.post('/admin/2fa/enable')
}

export function verify2FA(code) {
  return client.post('/admin/2fa/verify', { code })
}

export function disable2FA(code) {
  return client.post('/admin/2fa/disable', { code })
}
