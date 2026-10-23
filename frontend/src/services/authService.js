
import api from './api'

const TOKEN_KEY = 'craftory_token'

export async function registerUser(payload) {
  const response = await api.post('/register', payload)

  if (!response.data.token) {
    throw new Error('Registration succeeded but no token was returned.')
  }

  localStorage.setItem(TOKEN_KEY, response.data.token)

  return response.data
}

export async function loginUser(email, password) {
  const response = await api.post('/login', {
    email,
    password,
  })

  if (!response.data.token) {
    throw new Error('Login succeeded but no token was returned.')
  }

  localStorage.setItem(TOKEN_KEY, response.data.token)

  return response.data
}

export async function logoutUser() {
  try {
    await api.post('/logout')
  } finally {
    localStorage.removeItem(TOKEN_KEY)
  }
}

export function getToken() {
  return localStorage.getItem(TOKEN_KEY)
}

export async function getAuthenticatedUser() {
  const response = await api.get('/user')
  return response.data
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY)
}