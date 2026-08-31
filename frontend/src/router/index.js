import { createRouter, createWebHistory } from 'vue-router'

import Home from '../views/LandingPage.vue'
import LoginView from '../views/LoginView.vue'
import AdminDashboard from '@/views/admin/admin_dashboard.vue'
import SettingsView from '@/views/SettingsView.vue'
import UserDashboard from '@/views/users/user_dashboard.vue'

import TransactionsView from '@/views/admin/transactions/TransactionsView.vue'
import TransactionCreateView from '@/views/admin/transactions/TransactionCreateView.vue'
import TransactionDetailsView from '@/views/admin/transactions/TransactionDetailsView.vue'
import TransactionEditView from '@/views/admin/transactions/TransactionEditView.vue'

const roleHome = {
  admin: '/admin/dashboard',
  user: '/user/dashboard',
}

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true },
  },

  // Admin
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/transactions',
    name: 'admin-transactions',
    component: TransactionsView,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/transactions/new',
    name: 'admin-transaction-create',
    component: TransactionCreateView,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/transactions/:id(\\d+)',
    name: 'admin-transaction-details',
    component: TransactionDetailsView,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/transactions/:id(\\d+)/edit',
    name: 'admin-transaction-edit',
    component: TransactionEditView,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: SettingsView,
    meta: { requiresAuth: true, role: 'admin' },
  },

  // User
  {
    path: '/user/dashboard',
    name: 'user-dashboard',
    component: UserDashboard,
    meta: { requiresAuth: true, role: 'user' },
  },

  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,

  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition

    if (to.hash) {
      return new Promise((resolve) => {
        window.setTimeout(() => {
          resolve({
            el: to.hash,
            top: 90,
            behavior: 'smooth',
          })
        }, 300)
      })
    }

    return {
      top: 0,
      left: 0,
      behavior: 'smooth',
    }
  },
})

const fetchSession = async () => {
  const baseURL = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '')

  try {
    const response = await fetch(`${baseURL}/api/auth/check.php`, {
      method: 'GET',
      headers: {
        Accept: 'application/json',
      },
      credentials: 'include',
      cache: 'no-store',
    })

    if (!response.ok) {
      return {
        authenticated: false,
        role: null,
      }
    }

    const data = await response.json()

    return {
      authenticated: Boolean(data.authenticated),
      role: typeof data.role === 'string' ? data.role : null,
    }
  } catch (error) {
    console.error('Authentication check failed:', error)

    return {
      authenticated: false,
      role: null,
    }
  }
}

router.beforeEach(async (to) => {
  const needsAuthentication = Boolean(to.meta.requiresAuth)
  const guestOnly = Boolean(to.meta.guestOnly)

  if (!needsAuthentication && !guestOnly) {
    return true
  }

  const session = await fetchSession()

  if (guestOnly && session.authenticated) {
    return roleHome[session.role] || '/'
  }

  if (needsAuthentication && !session.authenticated) {
    return {
      name: 'login',
      query: {
        redirect: to.fullPath,
      },
    }
  }

  const requiredRoles = Array.isArray(to.meta.role)
    ? to.meta.role
    : to.meta.role
      ? [to.meta.role]
      : []

  if (requiredRoles.length > 0 && !requiredRoles.includes(session.role)) {
    return roleHome[session.role] || '/'
  }

  return true
})

export default router
