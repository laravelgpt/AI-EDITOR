import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && state.isAuthenticated,
    userRole: (state) => state.user?.role || 'user'
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/login', credentials)
        const { user, token } = response.data

        this.user = user
        this.token = token
        this.isAuthenticated = true

        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/register', userData)
        const { user, token } = response.data

        this.user = user
        this.token = token
        this.isAuthenticated = true

        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        this.isAuthenticated = false
        this.error = null

        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async checkAuth() {
      if (!this.token) return

      try {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        const response = await axios.get('/api/user')
        
        this.user = response.data
        this.isAuthenticated = true
      } catch (error) {
        this.logout()
      }
    },

    async refreshToken() {
      try {
        const response = await axios.post('/api/refresh')
        const { token } = response.data

        this.token = token
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return true
      } catch (error) {
        this.logout()
        return false
      }
    },

    clearError() {
      this.error = null
    }
  }
})
