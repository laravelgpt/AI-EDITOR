import { defineStore } from 'pinia'
import axios from 'axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    profile: null,
    preferences: {},
    loading: false,
    error: null
  }),

  getters: {
    hasProfile: (state) => !!state.profile,
    userPreferences: (state) => state.preferences
  },

  actions: {
    async fetchProfile() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/user/profile')
        this.profile = response.data
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch profile'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateProfile(profileData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.put('/api/user/profile', profileData)
        this.profile = response.data
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update profile'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async updatePassword(passwordData) {
      this.loading = true
      this.error = null

      try {
        await axios.put('/api/user/password', passwordData)
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update password'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchPreferences() {
      try {
        const response = await axios.get('/api/user/preferences')
        this.preferences = response.data
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch preferences'
        return { success: false, error: this.error }
      }
    },

    async updatePreferences(preferences) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.put('/api/user/preferences', preferences)
        this.preferences = response.data
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update preferences'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async uploadAvatar(file) {
      this.loading = true
      this.error = null

      try {
        const formData = new FormData()
        formData.append('avatar', file)

        const response = await axios.post('/api/user/avatar', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        this.profile = response.data
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to upload avatar'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    }
  }
})
