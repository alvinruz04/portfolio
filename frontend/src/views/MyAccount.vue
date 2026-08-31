<template>
  <AdminLayout>
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-md shadow-md border border-slate-200">
      <h1 class="text-2xl font-semibold text-slate-700 font-oswald mb-4">Change Password</h1>
      <form @submit.prevent="updatePassword" class="space-y-4">
        <!-- Old Password -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Old Password</label>
          <input
            v-model="form.oldPassword"
            type="password"
            class="w-full border px-3 py-2 rounded-md shadow-sm text-sm border-slate-300 focus:outline-none"
          />
          <p v-if="errors.oldPassword" class="text-red-500 text-xs mt-1">
            {{ errors.oldPassword }}
          </p>
        </div>

        <!-- New Password -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
          <input
            v-model="form.newPassword"
            type="password"
            class="w-full border px-3 py-2 rounded-md shadow-sm text-sm border-slate-300 focus:outline-none"
          />
          <p v-if="errors.newPassword" class="text-red-500 text-xs mt-1">
            {{ errors.newPassword }}
          </p>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
          <input
            v-model="form.confirmPassword"
            type="password"
            class="w-full border px-3 py-2 rounded-md shadow-sm text-sm border-slate-300 focus:outline-none"
          />
          <p v-if="errors.confirmPassword" class="text-red-500 text-xs mt-1">
            {{ errors.confirmPassword }}
          </p>
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            class="px-4 py-2 font-oswald bg-white border border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white transition rounded"
          >
            Update Password
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/components/AdminLayout.vue'
import { inject } from 'vue'

export default {
  components: { AdminLayout },
  data() {
    return {
      form: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: '',
      },
      errors: {},
      showToast: null,
    }
  },
  created() {
    this.showToast = inject('showToast')
  },
  methods: {
    async updatePassword() {
      this.errors = {}

      // Frontend validation
      if (!this.form.oldPassword) this.errors.oldPassword = 'Old password is required.'
      if (!this.form.newPassword || this.form.newPassword.length < 6)
        this.errors.newPassword = 'New password must be at least 6 characters.'
      if (this.form.newPassword !== this.form.confirmPassword)
        this.errors.confirmPassword = 'Passwords do not match.'

      if (Object.keys(this.errors).length > 0) return

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/admin/change_password.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify(this.form),
        })

        const data = await res.json()

        if (data.success) {
          this.form.oldPassword = ''
          this.form.newPassword = ''
          this.form.confirmPassword = ''
          this.showToast('Password updated successfully.', 'success')
        } else {
          this.errors = data.errors || {}
          this.showToast(data.message || 'Failed to update password.', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      }
    },
  },
}
</script>
