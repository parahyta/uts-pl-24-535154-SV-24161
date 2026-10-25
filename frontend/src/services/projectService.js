
import api from './api'

export async function getProjects() {
  const response = await api.get('/projects')

  // Mendukung respons { data: [...] }
  // serta respons paginasi { data: { data: [...] } }
  const payload = response.data?.data

  if (Array.isArray(payload)) {
    return payload
  }

  if (Array.isArray(payload?.data)) {
    return payload.data
  }

  throw new Error('Unexpected projects API response format.')
}

export async function getProject(id) {
  const response = await api.get(`/projects/${id}`)
  return response.data.data
}

export async function createProject(payload) {
  const response = await api.post('/projects', payload)
  return response.data.data
}

export async function updateProject(id, payload) {
  const response = await api.put(`/projects/${id}`, payload)
  return response.data.data
}

export async function deleteProject(id) {
  const response = await api.delete(`/projects/${id}`)
  return response.data
}
