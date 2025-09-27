import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import App from './App.vue'
import './bootstrap'

// Import components
import Dashboard from './components/Dashboard.vue'
import UserProfile from './components/UserProfile.vue'
import Settings from './components/Settings.vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'

// Import stores
import { useAuthStore } from './stores/auth'
import { useUserStore } from './stores/user'

// Router configuration
const routes = [
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/profile', component: UserProfile, meta: { requiresAuth: true } },
    { path: '/settings', component: Settings, meta: { requiresAuth: true } },
    { path: '/login', component: Login, meta: { guest: true } },
    { path: '/register', component: Register, meta: { guest: true } },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login')
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next('/dashboard')
    } else {
        next()
    }
})

// Create app
const app = createApp(App)

// Use plugins
app.use(router)
app.use(createPinia())

// Global components
app.component('Dashboard', Dashboard)
app.component('UserProfile', UserProfile)
app.component('Settings', Settings)
app.component('Login', Login)
app.component('Register', Register)

// Global properties
app.config.globalProperties.$http = window.axios

// Mount app
app.mount('#app')
