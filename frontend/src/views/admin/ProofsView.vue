<template>
  <UserLayout
    title="Proof Reviews"
    subtitle="Review proof of purchase submissions and assign points"
    active="proofs"
    v-model:sidebarOpen="sidebarOpen"
  >
    <template #default>
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
          <div
            class="relative rounded-2xl border border-[#eadce2] bg-white shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <i
              class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#b38497] text-xs"
            ></i>
            <input
              v-model="filters.q"
              class="h-11 w-full rounded-2xl bg-transparent pl-9 pr-3 text-sm text-[#5f6670] outline-none md:w-80"
              placeholder="Search name, email, proof ID..."
            />
          </div>

          <select
            v-model="filters.status"
            @change="fetchProofs(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="">All Status</option>
          </select>

          <button
            @click="resetFilters"
            class="h-11 rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm font-semibold text-[#9a6c80] transition hover:border-[#e97aac] hover:bg-white hover:text-[#e97aac]"
          >
            <i class="fas fa-rotate-left mr-2"></i>Reset
          </button>
        </div>

        <button
          @click="fetchProofs(page)"
          class="h-11 rounded-2xl bg-[#e97aac] px-5 text-sm font-semibold text-white transition hover:bg-[#d96799] shadow-[0_12px_24px_rgba(233,122,172,0.20)]"
        >
          <i class="fas fa-refresh mr-2"></i>Refresh
        </button>
      </div>

      <div
        class="mt-5 overflow-hidden rounded-[28px] border border-[#f0e4e9] bg-white shadow-[0_18px_40px_rgba(0,0,0,0.05)]"
      >
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-[#fcf4f7]">
              <tr class="text-left">
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Proof
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  User
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Status
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Points
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Submitted
                </th>
                <th
                  class="px-5 py-4 text-right text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                >
                  Actions
                </th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="px-5 py-14 text-center text-[#8a8f99]">
                  Loading proof submissions...
                </td>
              </tr>

              <tr
                v-for="row in rows"
                :key="row.id"
                class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
              >
                <td class="px-5 py-4">
                  <div class="font-semibold text-[#5f6670]">#{{ row.id }}</div>
                  <div class="mt-1 text-xs text-[#9aa0a9]">
                    {{ row.original_name || 'Proof image' }}
                  </div>
                  <div class="mt-1 text-xs text-[#9aa0a9]">
                    {{ formatBytes(row.file_size) }}
                  </div>
                </td>

                <td class="px-5 py-4">
                  <div class="font-semibold text-[#5f6670]">
                    {{ row.user_name || 'Member' }}
                  </div>
                  <div class="mt-1 text-xs text-[#9aa0a9]">
                    {{ row.email_address }}
                  </div>
                  <div class="mt-1 text-xs text-[#b38497]">
                    Current Points: {{ row.user_points }}
                  </div>
                </td>

                <td class="px-5 py-4">
                  <span
                    class="rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.08em]"
                    :class="statusClass(row.status)"
                  >
                    {{ row.status }}
                  </span>

                  <div v-if="row.status === 'rejected'" class="mt-2 max-w-xs text-xs text-rose-600">
                    {{ row.rejection_reason }}
                  </div>
                </td>

                <td class="px-5 py-4 text-sm font-semibold text-[#5f6670]">
                  {{ Number(row.points_awarded || 0) }}
                </td>

                <td class="px-5 py-4 text-sm text-[#7d838d]">
                  {{ formatDate(row.created_at) }}
                </td>

                <td class="px-5 py-4">
                  <div class="flex flex-wrap justify-end gap-2">
                    <button
                      v-if="row.proof_available"
                      @click="openImage(row)"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-[#eadce2] bg-white px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
                    >
                      View
                    </button>

                    <button
                      v-if="row.status === 'pending'"
                      @click="openReview(row, 'approve')"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-emerald-700 transition hover:bg-emerald-100"
                    >
                      Approve
                    </button>

                    <button
                      v-if="row.status === 'pending'"
                      @click="openReview(row, 'reject')"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-rose-600 transition hover:bg-rose-100"
                    >
                      Reject
                    </button>

                    <span
                      v-if="row.status !== 'pending'"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-gray-200 bg-gray-50 px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-500"
                    >
                      Reviewed
                    </span>
                  </div>
                </td>
              </tr>

              <tr v-if="!loading && rows.length === 0">
                <td colspan="6" class="px-5 py-14 text-center text-[#8a8f99]">
                  No proof submissions found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div
          class="flex flex-col gap-3 border-t border-[#f3e9ee] px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div class="text-sm text-[#8a8f99]">
            Showing
            <span class="font-semibold text-[#5f6670]">{{ startRow }}</span>
            to
            <span class="font-semibold text-[#5f6670]">{{ endRow }}</span>
            of
            <span class="font-semibold text-[#5f6670]">{{ total }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="fetchProofs(page - 1)"
              :disabled="page <= 1"
              class="h-10 rounded-full border border-[#e8dbe1] bg-white px-4 text-xs font-semibold uppercase tracking-[0.12em] text-[#6b7280] disabled:opacity-40"
            >
              Prev
            </button>

            <div class="px-2 text-xs text-[#8a8f99]">
              Page <span class="font-semibold text-[#5f6670]">{{ page }}</span> /
              <span class="font-semibold text-[#5f6670]">{{ totalPages }}</span>
            </div>

            <button
              @click="fetchProofs(page + 1)"
              :disabled="page >= totalPages"
              class="h-10 rounded-full bg-[#e97aac] px-4 text-xs font-semibold uppercase tracking-[0.12em] text-white disabled:opacity-40"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Image modal -->
      <div v-if="imageOpen" class="fixed inset-0 z-50 grid place-items-center bg-black/50 px-4">
        <div
          class="w-full max-w-4xl rounded-[28px] bg-white p-4 shadow-[0_20px_60px_rgba(0,0,0,0.18)]"
        >
          <div class="mb-4 flex items-center justify-between">
            <div>
              <h3 class="font-playfair text-[24px] text-[#5f6670]">Proof Image</h3>
              <p class="text-sm text-[#8a8f99]">Proof #{{ selectedRow?.id }}</p>
            </div>

            <button
              @click="closeImage"
              class="grid h-10 w-10 place-items-center rounded-full border border-[#eadce2] text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="max-h-[75vh] overflow-auto rounded-2xl border border-[#f0e4e9] bg-[#fffafb]">
            <img
              v-if="imageUrl"
              :src="imageUrl"
              alt="Proof image"
              class="mx-auto max-h-[75vh] object-contain"
            />
            <div v-else class="px-5 py-12 text-center text-[#8a8f99]">Loading image...</div>
          </div>
        </div>
      </div>

      <!-- Review modal -->
      <div v-if="reviewOpen" class="fixed inset-0 z-50 grid place-items-center bg-black/50 px-4">
        <div
          class="w-full max-w-lg rounded-[28px] bg-white p-5 shadow-[0_20px_60px_rgba(0,0,0,0.18)]"
        >
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="font-playfair text-[26px] text-[#5f6670]">
                {{ reviewAction === 'approve' ? 'Approve Proof' : 'Reject Proof' }}
              </h3>
              <p class="mt-1 text-sm text-[#8a8f99]">
                Proof #{{ selectedRow?.id }} from {{ selectedRow?.user_name }}
              </p>
            </div>

            <button
              @click="closeReview"
              class="grid h-10 w-10 place-items-center rounded-full border border-[#eadce2] text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div v-if="reviewAction === 'approve'" class="mt-5">
            <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
              Points to Credit <span class="text-rose-500">*</span>
            </label>
            <input
              v-model.number="reviewForm.points"
              type="number"
              min="1"
              max="999999"
              class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              placeholder="Example: 50"
            />
            <p class="mt-2 text-xs text-[#8a8f99]">
              The proof image will be deleted after approval.
            </p>
          </div>

          <div v-else class="mt-5">
            <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
              Rejection Reason <span class="text-rose-500">*</span>
            </label>
            <textarea
              v-model="reviewForm.reason"
              maxlength="500"
              class="mt-2 min-h-32 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 py-3 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              placeholder="Example: Image is unclear / invalid receipt / duplicate proof..."
            ></textarea>
            <p class="mt-2 text-xs text-[#8a8f99]">
              The user will see this reason. The proof image will be deleted after rejection.
            </p>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button
              @click="closeReview"
              class="h-11 rounded-full border border-[#e8dbe1] bg-white px-5 text-sm font-semibold text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              Cancel
            </button>

            <button
              @click="submitReview"
              :disabled="reviewing"
              class="h-11 rounded-full px-6 text-sm font-semibold text-white disabled:opacity-60"
              :class="
                reviewAction === 'approve'
                  ? 'bg-emerald-600 hover:bg-emerald-700'
                  : 'bg-rose-600 hover:bg-rose-700'
              "
            >
              {{
                reviewing
                  ? 'Processing...'
                  : reviewAction === 'approve'
                    ? 'Approve & Credit'
                    : 'Reject Proof'
              }}
            </button>
          </div>
        </div>
      </div>
    </template>
  </UserLayout>
</template>

<script>
import { inject } from 'vue'
import UserLayout from '@/layouts/UserLayout.vue'

export default {
  name: 'AdminProofsView',
  components: { UserLayout },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  data() {
    return {
      sidebarOpen: false,
      loading: false,
      reviewing: false,
      csrf: '',
      rows: [],
      page: 1,
      limit: 10,
      total: 0,
      totalPages: 1,
      searchDebounce: null,

      filters: {
        q: '',
        status: 'pending',
      },

      imageOpen: false,
      imageUrl: '',
      selectedRow: null,

      reviewOpen: false,
      reviewAction: 'approve',
      reviewForm: {
        points: 0,
        reason: '',
      },
    }
  },

  watch: {
    'filters.q'(val) {
      clearTimeout(this.searchDebounce)
      this.searchDebounce = setTimeout(() => {
        const q = (val || '').trim()
        if (q.length === 0 || q.length >= 2) this.fetchProofs(1)
      }, 350)
    },
  },

  computed: {
    startRow() {
      if (!this.total) return 0
      return (this.page - 1) * this.limit + 1
    },

    endRow() {
      return Math.min(this.page * this.limit, this.total)
    },
  },

  methods: {
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

    async fetchProofs(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          status: this.filters.status,
          q: this.filters.q || '',
        })

        const res = await fetch(`${baseURL}/api/proofs/admin_list.php?${params.toString()}`, {
          credentials: 'include',
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Failed to load proofs.')

        this.rows = data.rows || []
        this.page = Number(data.page || 1)
        this.limit = Number(data.limit || 10)
        this.total = Number(data.total || 0)
        this.totalPages = Number(data.totalPages || 1)
      } catch (e) {
        this.showToast?.(e.message || 'Failed to load proofs.', 'error')
      } finally {
        this.loading = false
      }
    },

    resetFilters() {
      this.filters = {
        q: '',
        status: 'pending',
      }
      this.fetchProofs(1)
      this.showToast?.('Filters cleared.', 'info')
    },

    async openImage(row) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.closeImage()

      this.selectedRow = row
      this.imageOpen = true

      try {
        const res = await fetch(`${baseURL}/api/proofs/view.php?id=${row.id}`, {
          credentials: 'include',
        })

        if (!res.ok) throw new Error('Failed to load proof image.')

        const blob = await res.blob()
        this.imageUrl = URL.createObjectURL(blob)
      } catch (e) {
        this.showToast?.(e.message || 'Failed to load image.', 'error')
        this.closeImage()
      }
    },

    closeImage() {
      if (this.imageUrl) {
        URL.revokeObjectURL(this.imageUrl)
      }

      this.imageUrl = ''
      this.imageOpen = false
      this.selectedRow = null
    },

    openReview(row, action) {
      this.selectedRow = row
      this.reviewAction = action
      this.reviewForm = {
        points: action === 'approve' ? 0 : 0,
        reason: '',
      }
      this.reviewOpen = true
    },

    closeReview() {
      this.reviewOpen = false
      this.selectedRow = null
      this.reviewAction = 'approve'
      this.reviewForm = {
        points: 0,
        reason: '',
      }
    },

    async submitReview() {
      if (!this.selectedRow) return

      if (this.reviewAction === 'approve' && Number(this.reviewForm.points) <= 0) {
        this.showToast?.('Please enter points greater than 0.', 'error')
        return
      }

      if (this.reviewAction === 'reject' && !this.reviewForm.reason.trim()) {
        this.showToast?.('Please enter a rejection reason.', 'error')
        return
      }

      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.reviewing = true

      try {
        const fd = new FormData()
        fd.append('id', String(this.selectedRow.id))
        fd.append('action', this.reviewAction)

        if (this.reviewAction === 'approve') {
          fd.append('points', String(this.reviewForm.points))
        } else {
          fd.append('reason', this.reviewForm.reason.trim())
        }

        const res = await this.apiFetch(`${baseURL}/api/proofs/review.php`, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Review failed.')

        this.showToast?.(data.message || 'Proof reviewed successfully.', 'success')
        this.closeReview()
        await this.fetchProofs(this.page)
      } catch (e) {
        this.showToast?.(e.message || 'Review failed.', 'error')
      } finally {
        this.reviewing = false
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

    formatBytes(bytes) {
      const n = Number(bytes || 0)
      if (n <= 0) return '—'
      if (n < 1024) return `${n} B`
      if (n < 1024 * 1024) return `${(n / 1024).toFixed(1)} KB`
      return `${(n / (1024 * 1024)).toFixed(2)} MB`
    },
  },

  mounted() {
    this.fetchProofs(1)
    this.ensureCSRF().catch(() => {})
  },

  beforeUnmount() {
    clearTimeout(this.searchDebounce)
    this.closeImage()
  },
}
</script>
