import { defineStore } from 'pinia'

interface User {
  id: number
  name: string
  email: string
}

interface AuthState {
  user: User | null
  token: string | null
  isLoggedIn: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isLoggedIn: false
  }),

  getters: {
    getUser: (state) => state.user,
    getToken: (state) => state.token,
    isAuthenticated: (state) => state.isLoggedIn && !!state.token
  },

  actions: {
    async login(credentials: { email: string; password: string }) {
      try {
        const { $api } = useNuxtApp()
        const response = await $api.post('/auth/login', credentials)
        
        const { user, token } = response.data
        
        this.setAuth(user, token)
        
        return { success: true, user }
      } catch (error: any) {
        throw new Error(error.response?.data?.message || 'Login failed')
      }
    },

    async register(userData: { name: string; email: string; password: string; password_confirmation: string }) {
      try {
        const { $api } = useNuxtApp()
        const response = await $api.post('/auth/register', userData)
        
        const { user, token } = response.data
        
        this.setAuth(user, token)
        
        return { success: true, user }
      } catch (error: any) {
        throw new Error(error.response?.data?.message || 'Registration failed')
      }
    },

    async logout() {
      try {
        const { $api } = useNuxtApp()
        await $api.post('/auth/logout')
      } catch (error) {
        // Ignore logout errors
      } finally {
        this.clearAuth()
        await navigateTo('/login')
      }
    },

    async fetchUser() {
      try {
        const { $api } = useNuxtApp()
        const response = await $api.get('/auth/user')
        
        this.user = response.data.user
        this.isLoggedIn = true
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    setAuth(user: User, token: string) {
      this.user = user
      this.token = token
      this.isLoggedIn = true
      
      // Store token in localStorage
      if (process.client) {
        localStorage.setItem('token', token)
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isLoggedIn = false
      
      // Remove token from localStorage
      if (process.client) {
        localStorage.removeItem('token')
      }
    },

    initializeAuth() {
      if (process.client) {
        const token = localStorage.getItem('token')
        if (token) {
          this.token = token
          this.fetchUser().catch(() => {
            this.clearAuth()
          })
        }
      }
    }
  }
})