import client from './client'

export function getContentSchedules(siteId) {
  return client.get(`/admin/sites/${siteId}/content-schedules`)
}

export function createContentSchedule(siteId, data) {
  return client.post(`/admin/sites/${siteId}/content-schedules`, data)
}

export function updateContentSchedule(siteId, id, data) {
  return client.put(`/admin/sites/${siteId}/content-schedules/${id}`, data)
}

export function deleteContentSchedule(siteId, id) {
  return client.delete(`/admin/sites/${siteId}/content-schedules/${id}`)
}
