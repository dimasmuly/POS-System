import axios from 'axios'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const api = axios.create({
    baseURL: config.public.apiBase,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })

  // Request interceptor to add auth token
  api.interceptors.request.use((config) => {
    const token = authStore.getToken || (process.client ? localStorage.getItem('token') : null)
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  })

  // Response interceptor to handle auth errors
  api.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.response?.status === 401) {
        authStore.clearAuth()
        navigateTo('/login')
      }
      return Promise.reject(error)
    }
  )

  return {
    provide: {
      api
    }
  }
})