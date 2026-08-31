<template>
  <div
    :class="[
      'fixed inset-0 z-50 flex items-center justify-center bg-cover bg-center backdrop-blur-sm p-4',
      isClosing ? 'animate-modalOut' : 'animate-modalIn',
    ]"
  >
    <div class="bg-white w-full max-w-lg rounded-xl p-8 shadow-lg relative">
      <h2 class="text-2xl font-garamond font-normal tracking-wide text-center text-[#1A1A1A] mb-6">
        Change Password
      </h2>

      <form @submit.prevent="handleSubmit">
        <!-- Old Password -->
        <div class="mb-4 relative">
          <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2"
            >Old Password:</label
          >
          <div class="relative">
            <input
              :type="showOldPassword ? 'text' : 'password'"
              v-model="form.oldPassword"
              class="w-full border border-slate-300 rounded-md px-4 py-2 pr-10 font-quicksand focus:outline-none focus:border-slate-400"
            />
            <span
              class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500 hover:text-gray-700"
              @click="showOldPassword = !showOldPassword"
            >
              <i :class="showOldPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
            </span>
          </div>
        </div>

        <!-- New Password -->
        <div class="mb-4 relative">
          <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2"
            >New Password:</label
          >
          <div class="relative">
            <input
              :type="showNewPassword ? 'text' : 'password'"
              v-model="form.newPassword"
              class="w-full border border-slate-300 rounded-md px-4 py-2 pr-10 font-quicksand focus:outline-none focus:border-slate-400"
            />
            <span
              class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500 hover:text-gray-700"
              @click="showNewPassword = !showNewPassword"
            >
              <i :class="showNewPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
            </span>
          </div>
        </div>

        <!-- Confirm New Password -->
        <div class="mb-6 relative">
          <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2"
            >Confirm New Password:</label
          >
          <div class="relative">
            <input
              :type="showConfirmPassword ? 'text' : 'password'"
              v-model="form.confirmPassword"
              class="w-full border border-slate-300 rounded-md px-4 py-2 pr-10 font-quicksand focus:outline-none focus:border-slate-400"
            />
            <span
              class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500 hover:text-gray-700"
              @click="showConfirmPassword = !showConfirmPassword"
            >
              <i :class="showConfirmPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
            </span>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-between">
          <button
            type="button"
            @click="returnToBooking"
            class="text-gray-600 hover:text-gray-900 font-oswald py-2 rounded transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="bg-[#E8CEB0] hover:bg-[#d1b38e] text-gray-900 font-oswald py-2 px-6 rounded transition disabled:opacity-50"
          >
            {{ loading ? 'Updating...' : 'Update Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue'

export default {
  name: 'ChangePasswordModal',
  emits: ['close', 'cancel-and-show-booking'],
  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },
  data() {
    return {
      form: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: '',
      },
      loading: false,
      showOldPassword: false,
      showNewPassword: false,
      showConfirmPassword: false,
      isClosing: false,
    }
  },
  methods: {
    async handleClose() {
      this.isClosing = true
      await new Promise((resolve) => setTimeout(resolve, 300))
      this.$emit('close')
      this.isClosing = false
    },
    async returnToBooking() {
      this.isClosing = true
      await new Promise((resolve) => setTimeout(resolve, 300))
      this.$emit('cancel-and-show-booking')
      this.isClosing = false
    },
    async handleSubmit() {
      const { oldPassword, newPassword, confirmPassword } = this.form

      if (!oldPassword || !newPassword || !confirmPassword) {
        this.showToast?.('All fields are required.', 'error')
        return
      }

      if (newPassword.length < 6 || newPassword.length > 255) {
        this.showToast?.('New password must be 6–255 characters.', 'error')
        return
      }

      if (newPassword !== confirmPassword) {
        this.showToast?.('New passwords do not match.', 'error')
        return
      }

      this.loading = true

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/client/change_password.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            oldPassword,
            newPassword,
          }),
        })

        const result = await res.json()
        if (result.success) {
          this.showToast?.('Password changed successfully.', 'success')
          this.handleClose()
        } else {
          this.showToast?.(result.message || 'Password change failed.', 'error')
        }
      } catch (err) {
        console.error(err)
        this.showToast?.('An error occurred. Please try again.', 'error')
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
@keyframes modalIn {
  0% {
    opacity: 0;
    transform: scale(0.95);
  }

  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes modalOut {
  0% {
    opacity: 1;
    transform: scale(1);
  }

  100% {
    opacity: 0;
    transform: scale(0.95);
  }
}

.animate-modalIn {
  animation: modalIn 0.3s ease-out forwards;
}

.animate-modalOut {
  animation: modalOut 0.3s ease-in forwards;
}
</style>
