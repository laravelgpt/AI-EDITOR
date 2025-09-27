<template>
  <div class="dashboard">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, {{ userStore.profile?.name || 'User' }}!</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                  <dd class="text-lg font-medium text-gray-900">1,234</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Revenue</dt>
                  <dd class="text-lg font-medium text-gray-900">$12,345</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Performance</dt>
                  <dd class="text-lg font-medium text-gray-900">98.5%</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="flow-root">
              <ul class="-mb-8">
                <li v-for="(activity, index) in activities" :key="index">
                  <div class="relative pb-8">
                    <span v-if="index !== activities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                    <div class="relative flex space-x-3">
                      <div>
                        <span :class="activity.iconBg" class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                          <component :is="activity.icon" class="h-5 w-5 text-white" />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-500">{{ activity.description }}</p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                          <time>{{ activity.time }}</time>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <button
                @click="createUser"
                class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-colors"
              >
                Create New User
              </button>
              <button
                @click="generateReport"
                class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition-colors"
              >
                Generate Report
              </button>
              <button
                @click="viewAnalytics"
                class="w-full bg-purple-500 text-white py-2 px-4 rounded-md hover:bg-purple-600 transition-colors"
              >
                View Analytics
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '../stores/user'

const userStore = useUserStore()

const activities = ref([
  {
    description: 'New user registered',
    time: '2 minutes ago',
    icon: 'UserIcon',
    iconBg: 'bg-blue-500'
  },
  {
    description: 'Payment received',
    time: '5 minutes ago',
    icon: 'CurrencyDollarIcon',
    iconBg: 'bg-green-500'
  },
  {
    description: 'System update completed',
    time: '10 minutes ago',
    icon: 'CheckIcon',
    iconBg: 'bg-yellow-500'
  }
])

const createUser = () => {
  // Implement create user logic
  console.log('Create user clicked')
}

const generateReport = () => {
  // Implement generate report logic
  console.log('Generate report clicked')
}

const viewAnalytics = () => {
  // Implement view analytics logic
  console.log('View analytics clicked')
}

onMounted(() => {
  // Fetch user profile if not already loaded
  if (!userStore.hasProfile) {
    userStore.fetchProfile()
  }
})
</script>
