<template>
  <AdminLayout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold">Booking Reports</h1>

      <div class="flex items-center gap-2">
        <button
          @click="exportCSV"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="fa-regular fa-file-excel mr-2"></i>
          Export CSV (Filtered)
        </button>
      </div>
    </div>

    <!-- Filters + Search -->
    <div class="flex flex-col gap-4 mb-5 md:flex-row md:flex-wrap md:items-end">
      <!-- Search by Client Name -->
      <div class="relative w-full md:w-64">
        <label class="block text-sm font-medium text-slate-700 mb-1">Search Client</label>
        <div class="relative">
          <i
            class="fas fa-search text-slate-500 text-sm absolute left-3 top-1/2 -translate-y-1/2"
          ></i>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Type client name..."
            class="w-full py-2 pl-10 pr-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 placeholder-slate-400 border-slate-200 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200"
          />
        </div>
      </div>

      <!-- Date Range + Status -->
      <div class="flex flex-col md:flex-row md:items-end md:gap-4 w-full md:w-auto">
        <div class="flex-1 min-w-[140px]">
          <label class="block text-sm font-medium text-slate-700 mb-1">From</label>
          <input
            type="date"
            v-model="filters.from"
            class="w-full py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200"
          />
        </div>

        <div class="flex-1 min-w-[140px]">
          <label class="block text-sm font-medium text-slate-700 mb-1">To</label>
          <input
            type="date"
            v-model="filters.to"
            class="w-full py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200"
          />
        </div>

        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select
            v-model="filters.status"
            class="w-full py-2 px-5 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200"
          >
            <option value="All">All</option>
            <option value="Pending Confirmation">Pending Confirmation</option>
            <option value="Approved">Approved</option>
            <option value="Declined">Declined</option>
            <option value="Cancelled">Cancelled</option>
            <option value="No Show">No Show</option>
          </select>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex items-center gap-2">
        <button
          @click="applyFilters"
          class="px-4 py-2 rounded bg-slate-700 text-white hover:bg-slate-800 transition font-oswald"
        >
          Apply
        </button>
        <button
          @click="resetFilters"
          class="px-4 py-2 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition font-oswald"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-md shadow-lg border border-slate-200">
      <table class="min-w-full text-left text-sm text-slate-700 font-medium bg-white">
        <thead
          class="bg-slate-700 font-sans font-extrabold text-white uppercase text-xs border-b border-slate-200"
        >
          <tr>
            <th class="p-3">Client</th>
            <th class="p-3">Class / Type</th>
            <th class="p-3">Date & Time</th>
            <th class="p-3">Sessions</th>
            <th class="p-3">Total Amount</th>
            <th class="p-3">Status</th>
            <th class="p-3">Coach</th>
            <th class="p-3">Proof</th>
            <th class="p-3">Code</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="paginated.length === 0">
            <td colspan="9" class="text-center text-slate-500 py-6">
              No bookings found for the selected filters.
            </td>
          </tr>

          <tr v-for="b in paginated" :key="b.id" class="border-b border-slate-100">
            <td class="p-3">{{ b.client_name || '—' }}</td>
            <td class="p-3">
              {{ b.schedule_class || '—' }}
              <template
                v-if="b.schedule_class && b.schedule_class !== 'Yoga Class' && b.schedule_type"
              >
                <br />
                ({{ displayReformerLabel(b.schedule_type) }})
              </template>
            </td>
            <td class="p-3">
              {{ formatDate(b.schedule_date) }} ({{ formatTime(b.schedule_time) }})
            </td>
            <td class="p-3">
              {{ b.sessions_left > 100 ? 'Unlimited' : b.session_count }}
            </td>
            <td class="p-3">₱{{ Number(b.total_amount || 0).toLocaleString('en-PH') }}</td>
            <td class="p-3" :class="b.status === 'Declined' ? 'text-red-500 font-semibold' : ''">
              {{ b.status }}
            </td>
            <td class="p-3">{{ b.schedule_coach || '—' }}</td>
            <td class="p-3">
              <a
                v-if="b.proof_url"
                :href="`${baseURL}/php/image-preview.php?file=${b.proof_url}`"
                target="_blank"
                class="text-blue-600 hover:underline"
              >
                View
              </a>
              <span v-else class="text-slate-400">—</span>
            </td>
            <td class="p-3">{{ b.code_value ?? 'None' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      class="mt-6 flex flex-col md:flex-row md:justify-between md:items-center gap-3 text-md text-slate-600"
    >
      <div>
        Showing <span class="font-semibold">{{ startIndex + 1 }}</span> to
        <span class="font-semibold">{{ endIndex }}</span> of
        <span class="font-semibold">{{ bookings.length }}</span> entries
      </div>

      <div class="flex items-center font-oswald gap-2">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="flex items-center gap-1 px-3 py-1.5 border rounded transition-colors duration-500 bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <i class="fa-solid fa-angle-left"></i> Prev
        </button>

        <span class="px-3 py-1.5 rounded bg-slate-200 text-slate-800 font-semibold">
          Page {{ currentPage }}
        </span>

        <button
          @click="currentPage++"
          :disabled="endIndex >= bookings.length"
          class="flex items-center gap-1 px-3 py-1.5 border rounded transition-colors duration-500 bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Next <i class="fa-solid fa-angle-right"></i>
        </button>
      </div>
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
      baseURL: import.meta.env.VITE_API_BASE_URL,
      bookings: [],
      currentPage: 1,
      perPage: 10,
      showToast: null,
      loading: false,

      // Filters now include search
      filters: {
        from: '', // 'YYYY-MM-DD'
        to: '', // 'YYYY-MM-DD'
        status: 'All',
        search: '', // <-- NEW (client name partial match)
      },

      // snapshot for export
      lastApplied: {
        from: '',
        to: '',
        status: 'All',
        search: '', // <-- NEW
      },
    }
  },
  created() {
    this.showToast = inject('showToast')
    this.applyFilters() // initial load
  },
  watch: {
    // if user types a new search we can optionally auto-Apply,
    // but to copy your myClients.vue behavior we will just reset page
    'filters.search'() {
      this.currentPage = 1
    },
  },
  computed: {
    startIndex() {
      return (this.currentPage - 1) * this.perPage
    },
    endIndex() {
      return Math.min(this.startIndex + this.perPage, this.bookings.length)
    },
    paginated() {
      return this.bookings.slice(this.startIndex, this.endIndex)
    },
  },
  methods: {
    displayReformerLabel(type) {
      if (!type) return '—'
      return type === 'Private' ? 'Solo' : type
    },
    formatTime(timeStr) {
      if (!timeStr) return ''
      const [hour, minute] = String(timeStr).split(':')
      const d = new Date()
      d.setHours(+hour || 0)
      d.setMinutes(+minute || 0)
      return d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      const opts = { year: 'numeric', month: 'long', day: 'numeric' }
      return new Date(dateStr).toLocaleDateString('en-US', opts)
    },
    resetFilters() {
      this.filters = {
        from: '',
        to: '',
        status: 'All',
        search: '',
      }
      this.applyFilters()
    },
    async applyFilters() {
      // 1) Validate date range before hitting backend
      if (this.filters.from && this.filters.to) {
        const fromDate = new Date(this.filters.from)
        const toDate = new Date(this.filters.to)

        if (fromDate > toDate) {
          this.showToast &&
            this.showToast('Invalid range: "From" date cannot be after "To" date.', 'error')
          return // stop here, don't fetch
        }
      }

      this.loading = true
      this.currentPage = 1

      try {
        const res = await fetch(`${this.baseURL}/php/reports/fetch.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            from: this.filters.from || null,
            to: this.filters.to || null,
            status: this.filters.status || 'All',
            search: this.filters.search || '',
          }),
        })

        if (!res.ok) {
          // try to read server error (like invalid range from backend)
          const errJson = await res.json().catch(() => null)
          const msg = errJson?.error || 'Network response was not ok'
          throw new Error(msg)
        }

        const data = await res.json()
        this.bookings = Array.isArray(data) ? data : []

        // snapshot for consistent export
        this.lastApplied = { ...this.filters }
      } catch (e) {
        this.bookings = []
        this.showToast && this.showToast(e.message || 'Failed to fetch bookings.', 'error')
      } finally {
        this.loading = false
      }
    },
    exportCSV() {
      const params = new URLSearchParams()
      if (this.lastApplied.from) params.set('from', this.lastApplied.from)
      if (this.lastApplied.to) params.set('to', this.lastApplied.to)
      if (this.lastApplied.status && this.lastApplied.status !== 'All') {
        params.set('status', this.lastApplied.status)
      }
      if (this.lastApplied.search) {
        params.set('search', this.lastApplied.search) // <-- include search
      }

      const url = `${this.baseURL}/php/reports/export.php?${params.toString()}`
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'bookings.csv')
      document.body.appendChild(link)
      link.click()
      link.remove()
    },
  },
}
</script>
