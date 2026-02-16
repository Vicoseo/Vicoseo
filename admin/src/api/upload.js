import client from './client'

export function uploadImage(file, directory = 'sponsors') {
  const formData = new FormData()
  formData.append('file', file)
  formData.append('directory', directory)

  return client.post('/admin/upload', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
}
