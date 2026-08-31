<template>
  <div class="flex min-h-screen bg-white">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-300 shadow-sm hidden md:block">
      <div class="p-6 border-b border-slate-300 shadow-sm h-30 flex justify-center items-center">
        <img
          src="/assets/logo.png"
          alt="Admin Panel"
          class="absolute h-50 object-cover pointer-events-none select-none"
        />
      </div>

      <nav class="mt-4 flex flex-col font-oswald text-lg space-y-2 px-4">
        <router-link
          to="/admin/bookings"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-address-book"></i>
          Bookings
        </router-link>

        <router-link
          to="/admin/clients"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-user"></i>
          Clients
        </router-link>

        <router-link
          to="/admin/coaches"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-user-tie"></i>
          Coaches
        </router-link>

        <router-link
          to="/admin/codes"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-key"></i>
          Codes
        </router-link>

        <router-link
          to="/admin/monitoring"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-eye"></i>
          Monitoring
        </router-link>

        <router-link
          to="/admin/schedules"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-calendar-days"></i>
          Schedule
        </router-link>

        <router-link
          to="/admin/reports"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-file-csv"></i>
          Reports
        </router-link>

        <router-link
          to="/admin/account"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-gray-700"
        >
          <i class="fa-solid fa-user-gear"></i>
          My Account
        </router-link>
        <button
          @click="confirmLogout"
          class="flex items-center gap-2 py-2 px-4 rounded hover:bg-gray-200 transition font-medium text-red-600"
        >
          <i class="fa-solid fa-right-from-bracket"></i>
          Logout
        </button>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <AdminTopbar :title="title" :user="me" @toggleSidebar="$emit('toggleSidebar')" />
      <slot />
    </main>
  </div>
</template>

<script>
import { inject } from 'vue'

export default {
  data() {
    return {
      me: null,
    }
  },

  async mounted() {
    const baseURL = import.meta.env.VITE_API_BASE_URL
    try {
      const res = await fetch(`${baseURL}/backend/api/auth/me.php`, {
        credentials: 'include',
      })
      const data = await res.json()
      if (data.success) this.me = data.user
    } catch (e) {
      console.error(e)
    }
  },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  methods: {
    confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        this.logout()
      }
    },

    async logout() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      try {
        await fetch(`${baseURL}/backend/api/auth/logout.php`, {
          method: 'POST',
          credentials: 'include',
        })
        this.$router.replace('/login')
      } catch (e) {
        console.error(e)
        this.$router.replace('/login')
      }
    },
  },
}
</script>
