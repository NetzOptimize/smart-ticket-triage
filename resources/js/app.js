import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './bootstrap'

// Import components
import Login from './components/auth/Login.vue'
import Register from './components/auth/Register.vue'
import TicketList from './components/tickets/TicketList.vue'
import TicketForm from './components/tickets/TicketForm.vue'

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: { requiresGuest: true }
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
            meta: { requiresGuest: true }
        },
        {
            path: '/tickets',
            name: 'tickets',
            component: TicketList,
            meta: { requiresAuth: true }
        },
        {
            path: '/tickets/create',
            name: 'tickets.create',
            component: TicketForm,
            meta: { requiresAuth: true }
        },
        {
            path: '/',
            redirect: '/tickets'
        }
    ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('token')
    
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login')
    } else if (to.meta.requiresGuest && isAuthenticated) {
        next('/tickets')
    } else {
        next()
    }
})

// Create and mount the app
const app = createApp(App)
app.use(router)
app.mount('#app')
