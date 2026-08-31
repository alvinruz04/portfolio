<template>
  <div class="min-h-dvh bg-[#F3F7FA] text-[#334155]">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
      <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-[#025199]/5 blur-3xl"></div>
      <div
        class="absolute -bottom-40 -right-24 h-96 w-96 rounded-full bg-[#F27C29]/5 blur-3xl"
      ></div>
      <div class="admin-dot-pattern absolute inset-0 opacity-[0.035]"></div>
    </div>

    <transition name="overlay-fade">
      <div
        v-if="sidebarOpen"
        class="fixed inset-0 z-40 bg-[#071A2B]/45 backdrop-blur-[2px] lg:hidden"
        aria-hidden="true"
        @click="closeSidebar"
      ></div>
    </transition>

    <AdminSidebar
      :open="sidebarOpen"
      :active="active"
      :user="me"
      @close="closeSidebar"
      @navigate="handleNavigate"
    />

    <div class="relative min-h-dvh lg:pl-72">
      <AdminTopbar :title="title" :subtitle="subtitle" :user="me" @toggle-sidebar="toggleSidebar" />

      <main class="px-4 py-6 sm:px-6 sm:py-7 lg:px-8 lg:py-8">
        <div class="mx-auto w-full max-w-7xl">
          <slot :me="me" :refresh-user="loadMe" />
        </div>
      </main>

      <footer class="px-4 pb-6 sm:px-6 lg:px-8">
        <div
          class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-2 border-t border-[#D8E4ED] pt-5 text-center sm:flex-row sm:text-left"
        >
          <p class="font-nunito text-[12px] text-[#94A3B8]">
            © 2026 APESCON Associated Pest Control
          </p>
          <p
            class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.15em] text-[#94A3B8]"
          >
            Secure Service Management Portal
          </p>
        </div>
      </footer>
    </div>

    <ConfirmDialog
      :show="showLogoutConfirm"
      :loading="isLoggingOut"
      title="Log out of APESCON?"
      message="Your current session will be ended and you will be returned to the secure login page."
      confirm-text="Log Out"
      loading-text="Logging Out..."
      confirm-icon="fa-solid fa-right-from-bracket"
      variant="danger"
      @confirm="confirmLogout"
      @cancel="cancelLogout"
    />
  </div>
</template>

<script>
import { inject } from 'vue'
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import AdminTopbar from '@/components/admin/AdminTopbar.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'

export default {
  name: 'AdminLayout',

  components: {
    AdminSidebar,
    AdminTopbar,
    ConfirmDialog,
  },

  props: {
    title: { type: String, default: 'Admin Portal' },
    subtitle: { type: String, default: 'APESCON service management' },
    active: { type: String, default: 'dashboard' },
    sidebarOpen: { type: Boolean, default: false },
  },

  emits: ['update:sidebarOpen'],

  setup() {
    const showToast = inject('showToast', null)
    return { showToast }
  },

  data() {
    return {
      me: null,
      isLoggingOut: false,
      showLogoutConfirm: false,
    }
  },

  async mounted() {
    await this.loadMe()
  },

  methods: {
    getBaseURL() {
      return (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '')
    },

    toggleSidebar() {
      this.$emit('update:sidebarOpen', !this.sidebarOpen)
    },

    closeSidebar() {
      this.$emit('update:sidebarOpen', false)
    },

    async loadMe() {
      const baseURL = this.getBaseURL()

      if (!baseURL) {
        console.error('VITE_API_BASE_URL is not configured.')
        return
      }

      try {
        const response = await fetch(`${baseURL}/api/auth/check.php`, {
          method: 'GET',
          headers: { Accept: 'application/json' },
          credentials: 'include',
          cache: 'no-store',
        })

        const responseText = await response.text()
        let data = {}

        try {
          data = responseText ? JSON.parse(responseText) : {}
        } catch {
          console.error('Invalid check.php response:', responseText)
          return
        }

        if (!response.ok || !data.authenticated) {
          this.me = null
          await this.$router.replace({
            name: 'login',
            query: { redirect: this.$route.fullPath },
          })
          return
        }

        this.me = {
          id: data.user_id,
          name: data.name,
          email_address: data.email_address,
          role: data.role,
        }

        if (data.role !== 'admin') {
          if (data.role === 'user') {
            await this.$router.replace('/user/dashboard')
            return
          }

          await this.$router.replace('/')
        }
      } catch (error) {
        console.error('APESCON session check failed:', error)
        this.showToast?.('Unable to verify your APESCON session.', 'error')
      }
    },

    async handleNavigate(key) {
      this.closeSidebar()

      if (key === 'logout') {
        this.showLogoutConfirm = true
        return
      }

      if (key === 'website') {
        await this.$router.push('/')
        return
      }

      const routes = {
        dashboard: '/admin/dashboard',
        transactions: '/admin/transactions',
        settings: '/admin/settings',
      }

      const destination = routes[key]

      if (destination) {
        if (this.$route.path !== destination) {
          await this.$router.push(destination)
        }
        return
      }

      const upcomingModules = {
        customers: 'Customer Management',
        quotations: 'Quotations',
        schedules: 'Service Schedule',
        reports: 'Reports',
      }

      if (upcomingModules[key]) {
        this.showToast?.(
          `${upcomingModules[key]} is being prepared for the APESCON portal.`,
          'info',
          3500,
        )
      }
    },

    cancelLogout() {
      if (this.isLoggingOut) return
      this.showLogoutConfirm = false
    },

    async getCsrfToken() {
      const baseURL = this.getBaseURL()
      const response = await fetch(`${baseURL}/api/auth/csrf.php`, {
        method: 'GET',
        headers: { Accept: 'application/json' },
        credentials: 'include',
        cache: 'no-store',
      })

      const responseText = await response.text()
      let data = {}

      try {
        data = responseText ? JSON.parse(responseText) : {}
      } catch {
        data = {}
      }

      if (response.status === 401) return null

      if (!response.ok || !data.success || !data.csrf) {
        throw new Error(data.message || 'Unable to retrieve the security token.')
      }

      return data.csrf
    },

    async confirmLogout() {
      if (this.isLoggingOut) return
      this.isLoggingOut = true
      const baseURL = this.getBaseURL()

      try {
        const csrf = await this.getCsrfToken()

        if (!csrf) {
          this.showLogoutConfirm = false
          this.me = null
          await this.$router.replace({ name: 'login' })
          return
        }

        const response = await fetch(`${baseURL}/api/auth/logout.php`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrf,
          },
          credentials: 'include',
          cache: 'no-store',
          body: JSON.stringify({}),
        })

        const responseText = await response.text()
        let data = {}

        try {
          data = responseText ? JSON.parse(responseText) : {}
        } catch {
          data = {}
        }

        if (response.status === 401) {
          this.showLogoutConfirm = false
          this.me = null
          await this.$router.replace({ name: 'login' })
          return
        }

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Unable to log out.')
        }

        this.showLogoutConfirm = false
        this.me = null
        this.showToast?.('You have been logged out successfully.', 'success')
        await this.$router.replace({ name: 'login' })
      } catch (error) {
        console.error('APESCON logout failed:', error)
        this.showToast?.(error?.message || 'Unable to log out. Please try again.', 'error')
      } finally {
        this.isLoggingOut = false
      }
    },
  },
}
</script>

<style scoped>
.admin-dot-pattern {
  background-image: radial-gradient(circle at 1px 1px, rgba(2, 81, 153, 0.48) 1px, transparent 0);
  background-size: 24px 24px;
}

.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 220ms ease;
}

.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
  .overlay-fade-enter-active,
  .overlay-fade-leave-active {
    transition-duration: 0.01ms;
  }
}
</style>
