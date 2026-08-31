<template>
  <div
    :class="[
      'fixed inset-0 z-50 flex items-center justify-center bg-cover bg-center backdrop-blur-sm p-4',
      isClosing ? 'animate-modalOut' : 'animate-modalIn',
    ]"
  >
    <div class="bg-white max-w-2xl w-full rounded-xl p-6 shadow-lg relative">
      <h2
        class="text-2xl font-oswald font-normal tracking-wide text-gray-800 mb-4 flex items-center gap-3"
      >
        <i class="fas fa-key"></i>
        My Codes
      </h2>

      <div v-if="loading" class="text-center text-gray-500">Loading your codes...</div>
      <div v-else-if="codes.length === 0" class="text-center text-gray-500">No codes found.</div>

      <div v-else class="space-y-4 max-h-[400px] overflow-y-auto">
        <div
          v-for="code in codes"
          :key="code.id"
          class="border border-gray-200 rounded-lg tracking-wider p-4 shadow-sm bg-gray-50"
        >
          <p class="text-md font-quicksand flex items-center gap-2">
            <strong>Code:</strong> {{ code.code }}
            <button
              v-if="code.status === 'On-going'"
              @click="copyToClipboard(code.code)"
              class="text-gray-500 hover:text-black"
              title="Copy to clipboard"
            >
              <i class="fa-regular fa-copy"></i>
            </button>
          </p>
          <p class="text-md font-quicksand">
            <strong>Sessions Left:</strong>
            {{ code.sessions_left > 100 ? 'Unlimited' : code.sessions_left }}
          </p>
          <p class="text-md font-quicksand">
            <strong>Status: </strong>
            <span :class="statusColorClass(code.status)">{{ code.status }}</span>
          </p>
          <p class="text-md font-quicksand">
            <strong>Valid Until:</strong> {{ formatDate(code.valid_until) }}
          </p>
        </div>
      </div>

      <div class="text-right mt-6">
        <button
          @click="handleClose"
          class="bg-[#E8CEB0] hover:bg-[#d1b38e] text-black px-6 py-2 rounded"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue'

export default {
  name: 'CodeModal',
  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },
  data() {
    return {
      codes: [],
      loading: true,
      isClosing: false,
    }
  },
  mounted() {
    this.fetchCodes()
  },
  methods: {
    async fetchCodes() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/codes/fetch_user_codes.php`, {
          credentials: 'include',
        })
        const data = await res.json()
        this.codes = data || []
      } catch (err) {
        console.error('Failed to fetch user codes:', err)
      } finally {
        this.loading = false
      }
    },
    async handleClose() {
      this.isClosing = true
      await new Promise((resolve) => setTimeout(resolve, 300))
      this.$emit('cancel-and-show-booking')
      this.isClosing = false
    },
    formatDate(dateStr) {
      const date = new Date(dateStr)
      return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    },
    copyToClipboard(text) {
      navigator.clipboard
        .writeText(text)
        .then(() => {
          this.showToast?.('Code copied to clipboard!', 'success')
        })
        .catch((err) => {
          console.error('Failed to copy code:', err)
          this.showToast?.('Failed to copy code.', 'error')
        })
    },
    statusColorClass(status) {
      switch (status.toLowerCase()) {
        case 'expired':
          return 'text-red-500 font-semibold'
        case 'on-going':
          return 'text-green-600 font-semibold'
        default:
          return 'text-yellow-500 font-semibold'
      }
    },
  },
}
</script>
