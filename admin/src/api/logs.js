import client from './client'

export function getAdminLogs(params = {}) {
  return client.get('/admin/logs', { params })
}
