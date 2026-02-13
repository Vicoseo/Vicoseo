import client from './client'

export function getSponsors(params = {}) {
  return client.get('/admin/sponsors', { params })
}

export function createSponsor(data) {
  return client.post('/admin/sponsors', data)
}

export function updateSponsor(id, data) {
  return client.put(`/admin/sponsors/${id}`, data)
}

export function deleteSponsor(id) {
  return client.delete(`/admin/sponsors/${id}`)
}
