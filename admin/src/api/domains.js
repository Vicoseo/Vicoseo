import client from './client'

export function getBalance() {
  return client.get('/admin/domains/balance')
}

export function searchDomain(query) {
  return client.get('/admin/domains/search', { params: { q: query } })
}

export function purchaseDomain(data) {
  return client.post('/admin/domains/purchase', data)
}

export function fullSetup(data) {
  return client.post('/admin/domains/setup', data, { timeout: 120000 })
}

export function getCfZones() {
  return client.get('/admin/domains/cloudflare/zones')
}

export function getCfZoneDetail(zoneId) {
  return client.get(`/admin/domains/cloudflare/zones/${zoneId}`)
}

export function addDnsRecord(zoneId, data) {
  return client.post(`/admin/domains/cloudflare/zones/${zoneId}/dns`, data)
}

export function getDomainStatus(domain) {
  return client.get(`/admin/domains/status/${domain}`)
}
