<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="text-xl font-bold text-gray-900">
                {{ appName }}
              </router-link>
            </div>

            <!-- Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                to="/dashboard"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary text-primary"
              >
                Dashboard
              </router-link>
            </div>
          </div>

          <!-- User Menu -->
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <div class="ml-3 relative" v-if="authStore.isAuthenticated">
              <div>
                <button
                  @click="toggleUserMenu"
                  class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                >
                  <span class="sr-only">Open user menu</span>
                  <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700">
                      {{ userStore.user?.name?.charAt(0) }}
                    </span>
                  </div>
                </button>
              </div>

              <!-- Dropdown menu -->
              <div
                v-show="userMenuOpen"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <router-link
                  to="/profile"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Your Profile
                </router-link>
                <router-link
                  to="/settings"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Settings
                </router-link>
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Sign out
                </button>
              </div>
            </div>

            <div v-else class="flex space-x-4">
              <router-link
                to="/login"
                class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium"
              >
                Login
              </router-link>
              <router-link
                to="/register"
                class="bg-primary text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium"
              >
                Register
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main>
      <router-view />
    </main>

    <!-- Loading Overlay -->
    <div
      v-if="loading"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    >
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-2">Loading...</h3>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useUserStore } from './stores/user'

const authStore = useAuthStore()
const userStore = useUserStore()

const userMenuOpen = ref(false)
const loading = ref(false)

const appName = computed(() => import.meta.env.VITE_APP_NAME || 'Laravel Vue SPA')

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const logout = async () => {
  loading.value = true
  try {
    await authStore.logout()
    userMenuOpen.value = false
  } catch (error) {
    console.error('Logout failed:', error)
  } finally {
    loading.value = false
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  
  // Initialize auth state
  authStore.checkAuth()
})
</script>

<style>
/* Global styles */
.router-link-active {
  @apply border-primary text-primary;
}
</style>
