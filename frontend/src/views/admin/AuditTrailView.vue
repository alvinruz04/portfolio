<template>
  <AdminLayout
    title="Audit Trail"
    subtitle="Review branch activities for investigation and security"
    active="audit_trail"
    v-model:sidebarOpen="sidebarOpen"
  >
    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div class="flex flex-col lg:flex-row lg:items-center gap-2 w-full">
        <!-- Search -->
        <div class="relative w-full lg:w-80">
          <i
            class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-xs"
          ></i>
          <input
            v-model="filters.q"
            class="pl-9 pr-3 h-10 w-full rounded-xl bg-neutral-900/60 border border-white/10 text-sm outline-none focus:border-white/20"
            placeholder="Search full name / username / action / IP / meta..."
          />
        </div>

        <!-- Username Filter -->
        <select
          v-model="filters.username"
          @change="fetchAudit(1)"
          class="h-10 rounded-xl bg-neutral-900/60 border border-white/10 text-sm px-3 outline-none focus:border-white/20 text-white/80 min-w-45"
        >
          <option value="">All Users</option>
          <option v-for="u in usernames" :key="u" :value="u">
            {{ u }}
          </option>
        </select>

        <!-- Action Filter -->
        <select
          v-model="filters.action"
          @change="fetchAudit(1)"
          class="h-10 rounded-xl bg-neutral-900/60 border border-white/10 text-sm px-3 outline-none focus:border-white/20 text-white/80 min-w-50"
        >
          <option value="">All Actions</option>
          <option v-for="a in actions" :key="a" :value="a">
            {{ a }}
          </option>
        </select>

        <!-- Date range -->
        <div class="flex flex-wrap items-center gap-2">
          <div class="flex items-center gap-2">
            <span class="text-xs text-white/40">From</span>
            <input
              v-model="filters.from"
              type="date"
              class="h-10 rounded-xl bg-neutral-900/60 border border-white/10 text-sm px-3 outline-none focus:border-white/20 text-white/70"
            />
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs text-white/40">To</span>
            <input
              v-model="filters.to"
              type="date"
              class="h-10 rounded-xl bg-neutral-900/60 border border-white/10 text-sm px-3 outline-none focus:border-white/20 text-white/70"
            />
          </div>

          <button
            @click="applyFilters"
            class="h-10 px-4 rounded-xl border border-white/10 bg-neutral-900/40 hover:bg-white/5 transition text-sm text-white/70"
          >
            Apply
          </button>

          <button
            @click="resetFilters"
            class="h-10 px-4 rounded-xl border border-white/10 hover:bg-white/5 transition text-sm text-white/70"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Right actions -->
      <div class="flex items-center gap-2 shrink-0">
        <select
          v-model="exportFormat"
          class="h-10 rounded-xl bg-neutral-900/60 border border-white/10 text-sm px-3 outline-none focus:border-white/20 text-white/80"
        >
          <option value="csv">CSV</option>
          <option value="xlsx">Excel (.xlsx)</option>
        </select>

        <button
          @click="exportAudit"
          class="h-10 px-4 rounded-xl border border-white/10 bg-neutral-900/40 hover:bg-white/5 transition text-sm text-white/70"
        >
          <i class="fas fa-download mr-2"></i>Export
        </button>
      </div>
    </div>

    <!-- Summary -->
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
      <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-4">
        <div class="text-[11px] uppercase tracking-widest text-white/40">Filtered Rows</div>
        <div class="mt-2 text-2xl font-semibold text-white">{{ total }}</div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-4">
        <div class="text-[11px] uppercase tracking-widest text-white/40">Current Page</div>
        <div class="mt-2 text-2xl font-semibold text-white">{{ page }}</div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-4">
        <div class="text-[11px] uppercase tracking-widest text-white/40">Rows Per Page</div>
        <div class="mt-2 text-2xl font-semibold text-white">{{ limit }}</div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-4">
        <div class="text-[11px] uppercase tracking-widest text-white/40">Pages</div>
        <div class="mt-2 text-2xl font-semibold text-white">{{ totalPages }}</div>
      </div>
    </div>

    <!-- Table -->
    <div class="mt-4 rounded-2xl border border-white/10 bg-neutral-900/60 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr
              class="text-left text-[11px] uppercase tracking-widest text-white/40 border-b border-white/10"
            >
              <th class="py-3 px-4">Date</th>
              <th class="py-3 px-4">User</th>
              <th class="py-3 px-4">Action</th>
              <th class="py-3 px-4">Entity</th>
              <th class="py-3 px-4">Summary</th>
              <th class="py-3 px-4">IP</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="row in rows" :key="row.id" class="border-t border-white/10 text-white/80">
              <td class="py-3 px-4 text-white/70">{{ formatDateTime(row.created_at) }}</td>

              <td class="py-3 px-4">
                <div class="font-semibold text-white">{{ row.full_name || '—' }}</div>
                <div class="text-xs text-white/50">@{{ row.username || 'unknown' }}</div>
              </td>

              <td class="py-3 px-4">
                <span
                  class="px-2 py-1 rounded-full text-[11px] border uppercase"
                  :class="actionPill(row.action)"
                >
                  {{ row.action || '—' }}
                </span>
              </td>

              <td class="py-3 px-4 text-white/70">
                <div>{{ row.entity_type || '—' }}</div>
                <div class="text-xs text-white/50" v-if="row.entity_id">#{{ row.entity_id }}</div>
              </td>

              <td class="py-3 px-4 text-white/70 max-w-105">
                <div class="truncate">{{ row.summary || '—' }}</div>
              </td>

              <td class="py-3 px-4 text-white/70">
                {{ row.ip_address || parsedMeta(row).ip_address || '—' }}
              </td>

              <td class="py-3 px-4">
                <div class="flex justify-end">
                  <button
                    class="h-9 px-3 rounded-xl border border-white/10 hover:bg-white/5 transition text-xs text-white/70"
                    @click="openDetails(row)"
                  >
                    <i class="fas fa-eye mr-2"></i>View
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="loading">
              <td colspan="7" class="py-10 text-center text-white/40">Loading...</td>
            </tr>

            <tr v-if="!loading && rows.length === 0">
              <td colspan="7" class="py-10 text-center text-white/40">No audit logs found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-white/10 text-sm">
        <div class="flex items-center gap-3 text-white/50">
          <div>
            Showing <span class="text-white/80">{{ startRow }}</span> to
            <span class="text-white/80">{{ endRow }}</span> of
            <span class="text-white/80">{{ total }}</span>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs text-white/40">Rows:</span>
            <select
              v-model.number="limit"
              @change="onLimitChange"
              class="h-9 rounded-xl bg-neutral-900/60 border border-white/10 text-xs px-3 outline-none focus:border-white/20 text-white/70"
            >
              <option :value="10">10</option>
              <option :value="15">15</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            class="h-9 px-3 rounded-xl border border-white/10 hover:bg-white/5 transition text-xs text-white/70"
            :disabled="page <= 1"
            :class="page <= 1 ? 'opacity-40 cursor-not-allowed' : ''"
            @click="fetchAudit(page - 1)"
          >
            Prev
          </button>

          <div class="text-xs text-white/60 px-2">
            Page <span class="text-white/80">{{ page }}</span> /
            <span class="text-white/80">{{ totalPages }}</span>
          </div>

          <button
            class="h-9 px-3 rounded-xl border border-white/10 hover:bg-white/5 transition text-xs text-white/70"
            :disabled="page >= totalPages"
            :class="page >= totalPages ? 'opacity-40 cursor-not-allowed' : ''"
            @click="fetchAudit(page + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <div v-if="detailsOpen" class="fixed inset-0 z-50 grid place-items-center bg-black/60 px-4">
      <div class="w-full max-w-5xl rounded-2xl border border-white/10 bg-neutral-950 p-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold">Audit Log Details</div>
            <div class="text-[11px] text-white/40">Investigation-ready activity details</div>
          </div>
          <button class="text-white/60 hover:text-white" @click="closeDetails">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div
          v-if="selected"
          class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm"
        >
          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Audit ID</div>
            <div class="mt-1 font-semibold text-white">#{{ selected.id }}</div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Date</div>
            <div class="mt-1 font-semibold text-white">
              {{ formatDateTime(selected.created_at) }}
            </div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Action</div>
            <div class="mt-1">
              <span
                class="px-2 py-1 rounded-full text-[11px] border uppercase"
                :class="actionPill(selected.action)"
              >
                {{ selected.action || '—' }}
              </span>
            </div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Full Name</div>
            <div class="mt-1 font-semibold text-white">{{ selected.full_name || '—' }}</div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Username</div>
            <div class="mt-1 font-semibold text-white">{{ selected.username || '—' }}</div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Role</div>
            <div class="mt-1 font-semibold text-white">{{ selected.role || '—' }}</div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Entity Type</div>
            <div class="mt-1 font-semibold text-white">{{ selected.entity_type || '—' }}</div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Entity ID</div>
            <div class="mt-1 font-semibold text-white">{{ selected.entity_id || '—' }}</div>
          </div>

          <div class="rounded-xl border border-white/10 bg-neutral-900/50 p-3">
            <div class="text-[11px] text-white/40 uppercase tracking-widest">IP Address</div>
            <div class="mt-1 font-semibold text-white break-all">
              {{ selected.ip_address || selected.meta.ip_address || '—' }}
            </div>
          </div>

          <div
            class="rounded-xl border border-white/10 bg-neutral-900/50 p-3 md:col-span-2 xl:col-span-3"
          >
            <div class="text-[11px] text-white/40 uppercase tracking-widest">
              User Agent / Device
            </div>
            <div class="mt-1 font-semibold text-white wrap-break-word">
              {{ selected.user_agent || selected.meta.user_agent || '—' }}
            </div>
          </div>

          <div
            class="rounded-xl border border-white/10 bg-neutral-900/50 p-3 md:col-span-2 xl:col-span-3"
          >
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Summary</div>
            <div class="mt-1 font-semibold text-white">{{ selected.summary || '—' }}</div>
          </div>

          <div
            class="rounded-xl border border-white/10 bg-neutral-900/50 p-3 md:col-span-2 xl:col-span-3"
          >
            <div class="text-[11px] text-white/40 uppercase tracking-widest">Meta JSON</div>
            <pre class="mt-2 whitespace-pre-wrap wrap-break-word text-xs text-white/75">{{
              prettyMeta(selected.meta)
            }}</pre>
          </div>
        </div>

        <div class="mt-5 flex justify-end">
          <button
            class="h-10 px-4 rounded-xl bg-white text-neutral-950 font-semibold text-sm hover:opacity-90 transition"
            @click="closeDetails"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import { inject } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

export default {
  name: 'AuditTrailView',
  components: { AdminLayout },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  data() {
    return {
      sidebarOpen: false,
      loading: false,
      error: '',
      searchDebounce: null,

      page: 1,
      limit: 10,
      total: 0,
      totalPages: 1,
      rows: [],

      usernames: [],
      actions: [],

      exportFormat: 'csv',

      filters: {
        q: '',
        from: '',
        to: '',
        username: '',
        action: '',
      },

      detailsOpen: false,
      selected: null,
    }
  },

  watch: {
    'filters.q'(val) {
      clearTimeout(this.searchDebounce)
      this.searchDebounce = setTimeout(() => {
        const q = (val || '').trim()
        if (q.length === 0 || q.length >= 2) this.fetchAudit(1)
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
    onLimitChange() {
      this.fetchAudit(1)
    },

    applyFilters() {
      this.fetchAudit(1)
    },

    resetFilters() {
      this.filters = {
        q: '',
        from: '',
        to: '',
        username: '',
        action: '',
      }
      this.fetchAudit(1)
    },

    parseMeta(metaJson) {
      try {
        const parsed = JSON.parse(metaJson || '{}')
        return parsed && typeof parsed === 'object' ? parsed : {}
      } catch {
        return {}
      }
    },

    parsedMeta(row) {
      return this.parseMeta(row?.meta_json)
    },

    buildSummary(row) {
      const meta = this.parseMeta(row.meta_json)

      if (meta.message) return meta.message

      const parts = []

      if (meta.plate_no) parts.push(`Plate: ${meta.plate_no}`)
      if (meta.queue_no) parts.push(`Queue: ${meta.queue_no}`)
      if (meta.status) parts.push(`Status: ${meta.status}`)
      if (meta.payment_method) parts.push(`Payment: ${meta.payment_method}`)
      if (meta.target_username) parts.push(`Target: ${meta.target_username}`)
      if (meta.reason) parts.push(`Reason: ${meta.reason}`)

      if (parts.length) return parts.join(' | ')

      return 'No summary available'
    },

    async fetchAudit(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true
      this.error = ''

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
          from: this.filters.from || '',
          to: this.filters.to || '',
          username: this.filters.username || '',
          action: this.filters.action || '',
        })

        const res = await fetch(`${baseURL}/backend/api/audit/list.php?${params.toString()}`, {
          credentials: 'include',
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Failed to load audit trail.')

        const rawRows = data.rows || []
        this.rows = rawRows.map((row) => ({
          ...row,
          summary: this.buildSummary(row),
        }))

        this.page = data.page
        this.limit = data.limit
        this.total = data.total
        this.totalPages = data.totalPages || 1

        // Prefer backend-provided dropdown options
        let usernames = Array.isArray(data.filters?.usernames) ? data.filters.usernames : []
        let actions = Array.isArray(data.filters?.actions) ? data.filters.actions : []

        // Fallback: derive from loaded rows if backend returns empty arrays
        if (!usernames.length) {
          usernames = [
            ...new Set(rawRows.map((r) => String(r.username || '').trim()).filter((v) => v !== '')),
          ].sort((a, b) => a.localeCompare(b))
        }

        if (!actions.length) {
          actions = [
            ...new Set(rawRows.map((r) => String(r.action || '').trim()).filter((v) => v !== '')),
          ].sort((a, b) => a.localeCompare(b))
        }

        this.usernames = usernames
        this.actions = actions
      } catch (e) {
        this.error = e.message || 'Network/server error.'
        this.showToast?.(this.error, 'error')
      } finally {
        this.loading = false
      }
    },

    exportAudit() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      const params = new URLSearchParams({
        q: this.filters.q || '',
        from: this.filters.from || '',
        to: this.filters.to || '',
        username: this.filters.username || '',
        action: this.filters.action || '',
        format: this.exportFormat || 'csv',
      })

      window.open(`${baseURL}/backend/api/audit/export.php?${params.toString()}`, '_blank')
      this.showToast?.('Audit export started...', 'info')
    },

    openDetails(row) {
      this.selected = {
        ...row,
        meta: this.parseMeta(row.meta_json),
        summary: this.buildSummary(row),
      }
      this.detailsOpen = true
    },

    closeDetails() {
      this.detailsOpen = false
      this.selected = null
    },

    formatDateTime(s) {
      if (!s) return '—'
      const d = new Date(s)
      if (isNaN(d.getTime())) return s
      return d.toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      })
    },

    prettyMeta(meta) {
      try {
        return JSON.stringify(meta || {}, null, 2)
      } catch {
        return '{}'
      }
    },

    actionPill(action) {
      const s = String(action || '').toUpperCase()

      if (s.includes('LOGIN') || s.includes('LOGOUT')) {
        return 'border-sky-400/30 bg-sky-400/10 text-sky-200'
      }

      if (s.includes('EXPORT')) {
        return 'border-amber-400/30 bg-amber-400/10 text-amber-200'
      }

      if (s.includes('CANCEL') || s.includes('DELETE') || s.includes('VOID')) {
        return 'border-rose-400/30 bg-rose-400/10 text-rose-200'
      }

      if (
        s.includes('CREATE') ||
        s.includes('DONE') ||
        s.includes('APPROVE') ||
        s.includes('UPDATE')
      ) {
        return 'border-emerald-400/30 bg-emerald-400/10 text-emerald-200'
      }

      return 'border-white/10 bg-white/5 text-white/70'
    },
  },

  mounted() {
    this.fetchAudit(1)
  },
}
</script>
