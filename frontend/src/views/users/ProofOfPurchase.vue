<template>
  <UserLayout
    title="Proof of Purchase"
    subtitle="Upload your You Glow Babe purchase proof and track review status"
    active="proofs"
    v-model:sidebarOpen="sidebarOpen"
  >
    <template #default>
      <div class="space-y-5">
        <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <div class="rounded-3xl border border-[#f0e4e9] bg-white/90 p-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-[#b38497]">Current Points</div>
            <div class="mt-2 text-3xl font-semibold text-[#5f6670]">
              {{ userPoints }}
            </div>
            <div class="mt-1 text-sm text-[#8a8f99]">Reward points balance</div>
          </div>

          <div class="rounded-3xl border border-[#f0e4e9] bg-white/90 p-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-[#b38497]">Pending Review</div>
            <div class="mt-2 text-3xl font-semibold text-[#5f6670]">
              {{ hasPending ? 'Yes' : 'No' }}
            </div>
            <div class="mt-1 text-sm text-[#8a8f99]">
              {{ hasPending ? 'Please wait for admin review' : 'You can submit a proof' }}
            </div>
          </div>

          <div class="rounded-3xl border border-[#f0e4e9] bg-white/90 p-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-[#b38497]">File Rules</div>
            <div class="mt-2 text-lg font-semibold text-[#5f6670]">JPG / PNG / WEBP</div>
            <div class="mt-1 text-sm text-[#8a8f99]">Maximum 5MB per image</div>
          </div>
        </section>

        <section
          class="rounded-[28px] border border-[#f1dce5] bg-white/90 p-5 shadow-[0_18px_50px_rgba(0,0,0,0.06)]"
        >
          <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
              <h2 class="font-playfair text-[26px] text-[#5f6670]">Submit Proof</h2>
              <p class="mt-1 text-sm text-[#8a8f99]">
                Upload a clear photo or screenshot of your You Glow Babe product purchase.
              </p>
            </div>

            <span
              v-if="hasPending"
              class="rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-amber-700"
            >
              Pending Review
            </span>
          </div>

          <div
            v-if="hasPending"
            class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"
          >
            You already have a pending proof. You can submit another one after the admin approves or
            rejects your current submission.
          </div>

          <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
              <label
                for="proof-input"
                class="flex min-h-[190px] cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[#efc7d8] bg-[#fff7fa] px-5 py-6 text-center transition hover:border-[#e97aac] hover:bg-white"
                :class="hasPending ? 'pointer-events-none opacity-60' : ''"
              >
                <div
                  class="grid h-14 w-14 place-items-center rounded-full bg-[#e97aac] text-white shadow-[0_12px_24px_rgba(233,122,172,0.22)]"
                >
                  <i class="fas fa-upload"></i>
                </div>

                <div class="mt-3 text-sm font-semibold text-[#5f6670]">
                  {{ selectedFile ? 'Change selected proof image' : 'Tap here to upload proof' }}
                </div>

                <div class="mt-1 max-w-md text-sm text-[#8a8f99]">
                  {{ selectedFileName || 'Accepted: JPG, PNG, WEBP only. Maximum 5MB.' }}
                </div>
              </label>

              <input
                id="proof-input"
                type="file"
                class="hidden"
                accept="image/jpeg,image/png,image/webp"
                :disabled="hasPending"
                @change="onFileChange"
              />

              <textarea
                v-model="customerNote"
                maxlength="255"
                :disabled="hasPending"
                placeholder="Optional note, order reference, or purchase details..."
                class="mt-4 min-h-24 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 py-3 text-sm text-[#5f6670] outline-none focus:border-[#e97aac] disabled:opacity-60"
              ></textarea>

              <button
                @click="submitProof"
                :disabled="!canSubmit"
                class="mt-4 h-11 rounded-full bg-[#e97aac] px-6 text-sm font-semibold text-white transition hover:bg-[#d96799] disabled:cursor-not-allowed disabled:opacity-50"
              >
                {{ submitting ? 'Submitting...' : 'Submit Proof for Review' }}
              </button>
            </div>

            <div
              class="flex min-h-[260px] items-center justify-center overflow-hidden rounded-3xl border border-[#f0e4e9] bg-[#fffafb]"
            >
              <img
                v-if="previewUrl"
                :src="previewUrl"
                alt="Proof preview"
                class="max-h-[420px] w-full object-contain"
              />
              <div v-else class="px-6 text-center text-[#9aa0a9]">
                <i class="fas fa-image text-4xl text-[#d3b2c1]"></i>
                <p class="mt-3 text-sm">Selected proof preview will appear here.</p>
              </div>
            </div>
          </div>
        </section>

        <section
          class="overflow-hidden rounded-[28px] border border-[#f0e4e9] bg-white shadow-[0_18px_40px_rgba(0,0,0,0.05)]"
        >
          <div class="border-b border-[#f3e9ee] px-5 py-4">
            <h2 class="font-playfair text-[24px] text-[#5f6670]">Submission History</h2>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-[#fcf4f7]">
                <tr class="text-left">
                  <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                    Date
                  </th>
                  <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                    Status
                  </th>
                  <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                    Points
                  </th>
                  <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                    Note / Reason
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr v-if="loading">
                  <td colspan="4" class="px-5 py-12 text-center text-[#8a8f99]">
                    Loading proof history...
                  </td>
                </tr>

                <tr
                  v-for="row in rows"
                  :key="row.id"
                  class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
                >
                  <td class="px-5 py-4 text-sm text-[#7d838d]">
                    {{ formatDate(row.created_at) }}
                  </td>

                  <td class="px-5 py-4">
                    <span
                      class="rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.08em]"
                      :class="statusClass(row.status)"
                    >
                      {{ row.status }}
                    </span>
                  </td>

                  <td class="px-5 py-4 text-sm font-semibold text-[#5f6670]">
                    {{ Number(row.points_awarded || 0) }}
                  </td>

                  <td class="px-5 py-4 text-sm text-[#7d838d]">
                    <div v-if="row.status === 'rejected'" class="text-rose-600">
                      {{ row.rejection_reason || 'Rejected by admin.' }}
                    </div>
                    <div v-else>
                      {{ row.customer_note || '—' }}
                    </div>
                  </td>
                </tr>

                <tr v-if="!loading && rows.length === 0">
                  <td colspan="4" class="px-5 py-12 text-center text-[#8a8f99]">
                    No proof submissions yet.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </template>
  </UserLayout>
</template>

<script>
import { inject } from 'vue'
import UserLayout from '@/layouts/UserLayout.vue'

export default {
  name: 'ProofOfPurchase',
  components: { UserLayout },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  data() {
    return {
      sidebarOpen: false,
      loading: false,
      submitting: false,
      csrf: '',
      rows: [],
      userPoints: 0,
      hasPending: false,
      selectedFile: null,
      previewUrl: '',
      customerNote: '',
    }
  },

  computed: {
    selectedFileName() {
      return this.selectedFile?.name || ''
    },

    canSubmit() {
      return !!this.selectedFile && !this.hasPending && !this.submitting
    },
  },

  methods: {
    cleanupPreview() {
      if (this.previewUrl) {
        URL.revokeObjectURL(this.previewUrl)
        this.previewUrl = ''
      }
    },

    onFileChange(e) {
      const file = e.target.files?.[0] || null
      this.cleanupPreview()
      this.selectedFile = null

      if (!file) return

      const allowed = ['image/jpeg', 'image/png', 'image/webp']
      if (!allowed.includes(file.type)) {
        this.showToast?.('Only JPG, PNG, and WEBP images are allowed.', 'error')
        e.target.value = ''
        return
      }

      if (file.size > 5 * 1024 * 1024) {
        this.showToast?.('Proof image must not exceed 5MB.', 'error')
        e.target.value = ''
        return
      }

      this.selectedFile = file
      this.previewUrl = URL.createObjectURL(file)
    },

    async ensureCSRF() {
      if (this.csrf) return this.csrf

      const baseURL = import.meta.env.VITE_API_BASE_URL

      const res = await fetch(`${baseURL}/api/auth/check.php`, {
        credentials: 'include',
      })

      const data = await res.json()

      if (data?.authenticated && data?.csrf) {
        this.csrf = data.csrf
        return this.csrf
      }

      const res2 = await fetch(`${baseURL}/api/auth/csrf.php`, {
        credentials: 'include',
      })

      const data2 = await res2.json()
      if (!data2.success) throw new Error(data2.message || 'Failed to get CSRF token.')

      this.csrf = data2.csrf
      return this.csrf
    },

    async apiFetch(url, { method = 'GET', headers = {}, body = null } = {}) {
      const finalHeaders = { ...headers }
      const m = method.toUpperCase()

      if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(m)) {
        finalHeaders['X-CSRF-Token'] = await this.ensureCSRF()
      }

      const doFetch = () =>
        fetch(url, {
          method,
          headers: finalHeaders,
          credentials: 'include',
          body: body == null ? undefined : body,
        })

      let res = await doFetch()

      if (res.status === 419) {
        this.csrf = ''
        finalHeaders['X-CSRF-Token'] = await this.ensureCSRF()
        res = await doFetch()
      }

      return res
    },

    async fetchProofs() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true

      try {
        const res = await fetch(`${baseURL}/api/proofs/my_list.php`, {
          credentials: 'include',
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Failed to load proof history.')

        this.rows = data.rows || []
        this.userPoints = Number(data.user_points || 0)
        this.hasPending = !!data.hasPending
      } catch (e) {
        this.showToast?.(e.message || 'Failed to load proof history.', 'error')
      } finally {
        this.loading = false
      }
    },

    async submitProof() {
      if (!this.canSubmit) return

      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.submitting = true

      try {
        const fd = new FormData()
        fd.append('proof', this.selectedFile)
        fd.append('customer_note', this.customerNote.trim())

        const res = await this.apiFetch(`${baseURL}/api/proofs/submit.php`, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Upload failed.')

        this.showToast?.('Proof submitted successfully. Please wait for admin review.', 'success')

        this.selectedFile = null
        this.customerNote = ''
        this.cleanupPreview()

        const input = document.getElementById('proof-input')
        if (input) input.value = ''

        await this.fetchProofs()
      } catch (e) {
        this.showToast?.(e.message || 'Upload failed.', 'error')
      } finally {
        this.submitting = false
      }
    },

    statusClass(status) {
      if (status === 'approved') return 'border border-emerald-200 bg-emerald-50 text-emerald-700'
      if (status === 'rejected') return 'border border-rose-200 bg-rose-50 text-rose-700'
      return 'border border-amber-200 bg-amber-50 text-amber-700'
    },

    formatDate(value) {
      if (!value) return '—'
      return new Date(value.replace(' ', 'T')).toLocaleString()
    },
  },

  mounted() {
    this.fetchProofs()
    this.ensureCSRF().catch(() => {})
  },

  beforeUnmount() {
    this.cleanupPreview()
  },
}
</script>
