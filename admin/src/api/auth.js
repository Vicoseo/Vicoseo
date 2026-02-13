import client from './client'

export function login(credentials) {
  return client.post('/admin/login', credentials)
}

export function logout() {
  return client.post('/admin/logout')
}

export function getMe() {
  return client.get('/admin/me')
}
