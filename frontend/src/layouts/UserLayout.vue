<template>
  <div class="min-h-screen bg-linear-to-br from-[#fff8fb] via-[#fffdfd] to-[#fff3f7]">
    <div
      class="absolute inset-0 pointer-events-none opacity-[0.08]"
      style="
        background-image: radial-gradient(
          circle at 1px 1px,
          rgba(120, 120, 120, 0.14) 1px,
          transparent 0
        );
        background-size: 24px 24px;
      "
    ></div>

    <div
      v-show="sidebarOpen"
      class="fixed inset-0 z-30 bg-[#2b1f25]/25 backdrop-blur-[2px] lg:hidden"
      @click="$emit('update:sidebarOpen', false)"
    />

    <UserSidebar
      :open="sidebarOpen"
      :active="active"
      :user="me"
      @close="$emit('update:sidebarOpen', false)"
      @navigate="handleNavigate"
    />

    <div class="relative z-10 lg:ml-80">
      <UserTopbar
        :title="title"
        :subtitle="subtitle"
        :user="me"
        @toggleSidebar="$emit('update:sidebarOpen', !sidebarOpen)"
      />

      <main class="px-4 py-6 sm:px-6 lg:px-8">
        <slot :me="me" />
      </main>
    </div>

    <ConfirmDialog
      :show="showLogoutConfirm"
      title="Log out?"
      message="You will be returned to the home page."
      confirmText="Log out"
      confirmIcon="fas fa-sign-out-alt"
      variant="danger"
      @confirm="confirmLogout"
      @cancel="cancelLogout"
    />
  </div>
</template>

<script>
import { inject } from 'vue'
import UserSidebar from '@/components/user/UserSidebar.vue'
import UserTopbar from '@/components/user/UserTopbar.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'

export default {
  name: 'UserLayout',
  components: { UserSidebar, UserTopbar, ConfirmDialog },

  props: {
    title: { type: String, default: 'Dashboard' },
    subtitle: { type: String, default: 'Overview & quick controls' },
    active: { type: String, default: 'dashboard' },
    sidebarOpen: { type: Boolean, default: false },
  },

  emits: ['update:sidebarOpen'],

  data() {
    return {
      me: null,
      showLogoutConfirm: false,
    }
  },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  methods: {
    handleNavigate(key) {
      this.$emit('update:sidebarOpen', false)

      if (key === 'logout') {
        this.showLogoutConfirm = true
        return
      }

      const isAdmin = this.me?.role === 'admin'

      const map = {
        dashboard: isAdmin ? '/admin/dashboard' : '/user/dashboard',
        proofs: isAdmin ? '/admin/proofs' : '/user/proof-of-purchase',
        sellers: '/admin/sellers',
        'fake-sellers': '/admin/fake-sellers',
        announcements: '/admin/announcements',
        settings: isAdmin ? '/admin/settings' : '/user/settings',
      }

      if (map[key]) this.$router.push(map[key])
    },

    cancelLogout() {
      this.showLogoutConfirm = false
    },

    async confirmLogout() {
      this.showLogoutConfirm = false
      const baseURL = import.meta.env.VITE_API_BASE_URL

      try {
        await fetch(`${baseURL}/api/auth/logout.php`, {
          method: 'POST',
          credentials: 'include',
        })

        this.showToast?.('Logged out successfully.', 'info')
      } catch (e) {
        console.error('Logout failed:', e)
        this.showToast?.('Logout failed. Please try again.', 'error')
      } finally {
        this.$router.replace('/')
      }
    },

    async fetchMe() {
      const baseURL = import.meta.env.VITE_API_BASE_URL

      try {
        const res = await fetch(`${baseURL}/api/auth/check.php`, {
          credentials: 'include',
        })
        const data = await res.json()

        if (data.authenticated) {
          this.me = {
            id: data.user_id,
            role: data.role,
            name: data.name,
            email_address: data.email_address,
            user_points: data.user_points,
          }
        }
      } catch (e) {
        console.error('CHECK ERROR:', e)
      }
    },
  },

  mounted() {
    this.fetchMe()
  },
}
</script>
