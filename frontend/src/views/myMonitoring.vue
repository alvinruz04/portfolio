<template>
  <AdminLayout>
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold">Booking Calendar</h1>

      <!-- Month Navigator -->
      <div class="flex items-center gap-2">
        <button
          @click="goToPrevMonth"
          class="px-3 py-1.5 border rounded bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition"
          aria-label="Previous Month"
        >
          <i class="fa-solid fa-angle-left"></i>
        </button>

        <div class="px-3 py-1.5 rounded bg-slate-200 text-slate-800 font-semibold font-oswald">
          {{ monthLabel }}
        </div>

        <button
          @click="goToNextMonth"
          class="px-3 py-1.5 border rounded bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition"
          aria-label="Next Month"
        >
          <i class="fa-solid fa-angle-right"></i>
        </button>

        <button
          @click="goToToday"
          class="ml-2 px-3 py-1.5 border rounded bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition"
        >
          Today
        </button>
      </div>
    </div>

    <!-- Filters toolbar -->
    <div
      class="mb-4 grid grid-cols-1 md:grid-cols-[minmax(12rem,18rem)_minmax(12rem,18rem)_minmax(12rem,18rem)_auto] items-end gap-3"
    >
      <!-- Coach -->
      <!-- <div class="relative w-full">
        <label class="block text-[11px] text-slate-500 mb-1">Coach</label>
        <select
          v-model="pendingFilters.coach"
          class="h-10 w-full max-w-xs py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-200"
        >
          <option value="">All coaches</option>
          <option v-for="c in coachOptions" :key="c" :value="c">{{ c }}</option>
        </select>
      </div> -->

      <!-- Class Type -->
      <div class="relative w-full">
        <label class="block text-[11px] text-slate-500 mb-1">Class Type : </label>
        <select
          v-model="pendingFilters.class_type"
          class="h-10 w-full max-w-xs py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-200"
        >
          <option value="All">All</option>
          <option value="Reformer Class">Reformer Class</option>
          <option value="Yoga Class">Yoga Class</option>
        </select>
      </div>

      <!-- Reformer Type (only when Reformer Class) -->
      <div class="relative w-full" v-if="pendingFilters.class_type === 'Reformer Class'">
        <label class="block text-[11px] text-slate-500 mb-1">Reformer Type</label>
        <select
          v-model="pendingFilters.reformer_type"
          class="h-10 w-full max-w-xs py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-200"
        >
          <option value="All">All</option>
          <option value="Private">Solo</option>
          <option value="Duo">Duo</option>
          <option value="Trio">Trio</option>
          <option value="Group">Group</option>
        </select>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 md:self-end">
        <button
          @click="applyFilters"
          class="h-10 inline-flex items-center justify-center px-4 text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white"
        >
          Filter
        </button>
        <button
          v-if="filters.coach || filters.class_type !== 'All' || filters.reformer_type !== 'All'"
          @click="clearFilters"
          class="h-10 inline-flex items-center justify-center px-4 text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white"
        >
          Clear
        </button>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-md shadow-lg border border-slate-200 p-3">
      <!-- Weekday headers -->
      <div class="grid grid-cols-7 text-center text-xs md:text-sm font-semibold text-slate-600">
        <div v-for="d in weekdayLabels" :key="d" class="py-2">{{ d }}</div>
      </div>

      <!-- Days -->
      <div class="grid grid-cols-7 gap-2">
        <!-- Leading blanks -->
        <div v-for="n in firstWeekdayIndex" :key="'blank-' + n" class="h-24 md:h-28"></div>

        <!-- Month days -->
        <div
          v-for="day in daysInMonth"
          :key="day"
          class="h-24 md:h-28 border rounded-md p-2 flex flex-col overflow-hidden cursor-pointer hover:shadow transition"
          :class="['border-slate-200', isToday(day) ? 'ring-2 ring-sky-500' : '']"
          @click="openDayModal(day)"
        >
          <!-- Date number -->
          <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500">{{ formatDayLabel(day) }}</span>
            <span
              v-if="
                (filteredBookingsByDate[isoOf(day)] && filteredBookingsByDate[isoOf(day)].length) >
                0
              "
              class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-white"
            >
              {{ filteredBookingsByDate[isoOf(day)].length }}
            </span>
          </div>

          <div class="mt-1 space-y-1 overflow-y-auto">
            <div
              v-for="(b, idx) in (filteredBookingsByDate[isoOf(day)] || []).slice(0, 3)"
              :key="b.id + '-' + idx"
              class="text-[11px] truncate rounded px-2 py-0.5"
              :class="chipClassBooking(b)"
              :title="chipTitleBooking(b)"
            >
              {{ b.client_name }} • {{ formatTime(b.schedule_time) }}
              <!-- If you also want to show coach compactly, uncomment: -->
              <!-- <span class="opacity-80">• {{ b.schedule_coach || '—' }}</span> -->
            </div>

            <div
              v-if="
                (filteredBookingsByDate[isoOf(day)] && filteredBookingsByDate[isoOf(day)].length) >
                3
              "
              class="text-[11px] italic text-slate-500"
            >
              +{{ filteredBookingsByDate[isoOf(day)].length - 3 }} more…
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Day Modal -->
    <div
      v-if="dayModal.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-2xl">
        <div class="flex items-start justify-between gap-3">
          <h2 class="text-xl font-semibold text-slate-800 mb-2 font-oswald">
            Bookings on {{ formatDate(dayModal.dateISO) }}
          </h2>

          <div class="flex items-center gap-2">
            <label class="text-[11px] text-slate-500">Coach : </label>
            <select
              v-model="dayModal.coach"
              class="h-9 px-2 py-1 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-300 focus:outline-none focus:ring-1 focus:ring-slate-200"
              aria-label="Filter by coach for this day"
            >
              <option value="">All</option>
              <option v-for="c in modalCoachOptions" :key="c" :value="c">{{ c }}</option>
            </select>

            <button
              v-if="dayModal.coach"
              @click="dayModal.coach = ''"
              class="text-xs px-2 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-700 hover:text-white transition"
              aria-label="Clear coach filter"
            >
              Clear
            </button>

            <button
              @click="dayModal.open = false"
              class="px-3 py-1.5 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition"
            >
              Close
            </button>
          </div>
        </div>

        <!-- Summary -->
        <p class="text-sm text-slate-600 mb-4">
          Total: <strong>{{ filteredDayBookings.length }}</strong>
        </p>

        <!-- List -->
        <div class="max-h-[60vh] overflow-y-auto divide-y divide-slate-100">
          <div
            v-for="b in filteredDayBookings"
            :key="b.id"
            class="py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2"
          >
            <div class="text-sm">
              <div class="font-medium text-slate-800">
                {{ b.client_name }} with {{ b.schedule_coach || '—' }}
              </div>
              <div class="text-slate-600">
                {{ b.class_type }}
                <span v-if="b.class_type !== 'Yoga Class'">
                  ( {{ b.reformer_type === 'Private' ? 'Solo' : (b.reformer_type ?? 'N/A') }} )
                </span>
                •
                <span class="font-medium">{{ formatTime(b.schedule_time) }}</span>

                <span
                  v-if="
                    b.class_type === 'Reformer Class' &&
                    (b.reformer_type === 'Duo' || b.reformer_type === 'Trio')
                  "
                >
                  <span class="mx-2">•</span>
                  Companions: <span class="font-medium">{{ b.companions || 'none' }}</span>
                </span>
                • Code:
                <span class="font-medium">{{ b.code_value || 'none' }}</span>
              </div>
              <!-- <div class="text-xs text-slate-500">
                Status: {{ b.status }}
              </div> -->
            </div>

            <!-- <div class="flex items-center gap-2">
              <a v-if="b.proof_url" :href="`${baseURL}/php/image-preview.php?file=${b.proof_url}`" target="_blank"
                class="text-blue-600 hover:underline text-sm">
                View Payment
              </a>
              <span v-else class="text-slate-400 text-sm">No Image</span>
            </div> -->
          </div>
        </div>

        <div class="mt-4 text-xs text-slate-500">
          Tip: Use the Coach filter above to quickly narrow results for this day.
        </div>
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
    const today = new Date()
    return {
      baseURL: import.meta.env.VITE_API_BASE_URL,
      // calendar state
      viewYear: today.getFullYear(),
      viewMonth: today.getMonth(), // 0-11
      // data
      bookings: [],
      // ui
      search: '',
      filters: { coach: '', class_type: 'All', reformer_type: 'All' },
      pendingFilters: { coach: '', class_type: 'All', reformer_type: 'All' },
      dayModal: {
        open: false,
        dateISO: null, // 'YYYY-MM-DD'
        coach: '',
      },
      showToast: null,
    }
  },
  created() {
    this.showToast = inject('showToast')
    this.loadMonth()
  },
  computed: {
    monthLabel() {
      const d = new Date(this.viewYear, this.viewMonth, 1)
      return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
    },
    weekdayLabels() {
      // Start week on Sunday; change if you want Monday-start
      return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    },
    firstWeekdayIndex() {
      const d = new Date(this.viewYear, this.viewMonth, 1)
      return d.getDay() // 0 (Sun) - 6 (Sat)
    },
    daysInMonth() {
      return new Date(this.viewYear, this.viewMonth + 1, 0).getDate()
    },
    // All unique coaches from current month data
    coachOptions() {
      const set = new Set(
        (this.bookings || []).map((b) => (b.schedule_coach || '').trim()).filter(Boolean),
      )
      return Array.from(set).sort((a, b) => a.localeCompare(b))
    },
    // Apply filters globally
    filteredBookings() {
      return (this.bookings || []).filter((b) => {
        const coachOk = this.filters.coach ? b.schedule_coach === this.filters.coach : true
        const classOk =
          this.filters.class_type === 'All' ? true : b.class_type === this.filters.class_type
        const reformerOk =
          this.filters.class_type === 'Reformer Class'
            ? this.filters.reformer_type === 'All'
              ? true
              : b.reformer_type === this.filters.reformer_type
            : true
        return coachOk && classOk && reformerOk
      })
    },
    // Group AFTER filters (so chips/counts match)
    filteredBookingsByDate() {
      const map = {}
      for (const b of this.filteredBookings) {
        const key = b.schedule_date
        if (!key) continue
        ;(map[key] ||= []).push(b)
      }
      for (const k in map) {
        map[k].sort((a, b) => (a.schedule_time || '').localeCompare(b.schedule_time || ''))
      }
      return map
    },
    // Modal list = filtered + optional name search
    filteredDayBookings() {
      if (!this.dayModal.open || !this.dayModal.dateISO) return []
      const all = this.filteredBookingsByDate[this.dayModal.dateISO] || []

      const term = this.search.trim().toLowerCase()

      // NEW: filter by coach selected in the modal
      let list = this.dayModal.coach
        ? all.filter((b) => (b.schedule_coach || '') === this.dayModal.coach)
        : all

      // optional name search (kept from your code)
      if (!term) return list
      return list.filter((b) => (b.client_name || '').toLowerCase().includes(term))
    },
    modalCoachOptions() {
      if (!this.dayModal.open || !this.dayModal.dateISO) return []
      const all = this.filteredBookingsByDate[this.dayModal.dateISO] || []
      const set = new Set(all.map((b) => (b.schedule_coach || '').trim()).filter(Boolean))
      return Array.from(set).sort((a, b) => a.localeCompare(b))
    },
  },
  methods: {
    async loadMonth(/* useFilters = false */) {
      const { startISO, endISO } = this.currentMonthRangeISO()
      try {
        const body = {
          start_date: startISO,
          end_date: endISO,
          // OPTIONAL server-side filtering:
          // ...(useFilters ? {
          //   coach: this.filters.coach,
          //   class_type: this.filters.class_type,
          //   reformer_type: this.filters.reformer_type
          // } : {})
        }
        const res = await fetch(`${this.baseURL}/php/bookings/fetch_monitoring.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        })
        const data = await res.json()
        this.bookings = Array.isArray(data) ? data : []
      } catch {
        this.bookings = []
        this.showToast?.('Failed to fetch bookings for month.', 'error')
      }
    },
    goToPrevMonth() {
      if (this.viewMonth === 0) {
        this.viewMonth = 11
        this.viewYear -= 1
      } else {
        this.viewMonth -= 1
      }
      this.loadMonth()
    },
    goToNextMonth() {
      if (this.viewMonth === 11) {
        this.viewMonth = 0
        this.viewYear += 1
      } else {
        this.viewMonth += 1
      }
      this.loadMonth()
    },
    goToToday() {
      const t = new Date()
      this.viewYear = t.getFullYear()
      this.viewMonth = t.getMonth()
      this.loadMonth()
    },
    isoOf(day) {
      const d = new Date(this.viewYear, this.viewMonth, day)
      const y = d.getFullYear()
      const m = String(d.getMonth() + 1).padStart(2, '0')
      const dd = String(d.getDate()).padStart(2, '0')
      return `${y}-${m}-${dd}`
    },
    formatDayLabel(day) {
      return day
    },
    isToday(day) {
      const d = new Date(this.viewYear, this.viewMonth, day)
      const now = new Date()
      return (
        d.getFullYear() === now.getFullYear() &&
        d.getMonth() === now.getMonth() &&
        d.getDate() === now.getDate()
      )
    },
    openDayModal(day) {
      this.dayModal.dateISO = this.isoOf(day)
      this.dayModal.open = true
    },
    chipTitle(b) {
      const parts = [
        this.formatTime(b.schedule_time),
        b.client_name || '—',
        b.schedule_coach ? `Coach ${b.schedule_coach}` : null,
        b.code_value ? `Code ${b.code_value}` : null,
      ].filter(Boolean)
      return parts.join(' • ')
    },
    applyFilters() {
      // apply client-side immediately
      this.filters = {
        coach: this.pendingFilters.coach || '',
        class_type: this.pendingFilters.class_type || 'All',
        reformer_type:
          this.pendingFilters.class_type === 'Reformer Class'
            ? this.pendingFilters.reformer_type || 'All'
            : 'All',
      }
      // OPTIONAL: uncomment to fetch server-side filtered data
      // this.loadMonth(true)
    },

    clearFilters() {
      this.pendingFilters = { coach: '', class_type: 'All', reformer_type: 'All' }
      this.filters = { coach: '', class_type: 'All', reformer_type: 'All' }
      // OPTIONAL: server-side clear
      // this.loadMonth(true)
    },
    chipClassBooking(b) {
      // Example: tint differently when the booking is Reformer and Cancelled (if ever present),
      // fallback to your default schedules chip style.
      if (b?.status === 'Cancelled')
        return 'text-[11px] truncate rounded px-2 py-0.5 bg-red-50 text-red-700 border border-red-200'
      return 'text-[11px] truncate rounded px-2 py-0.5 bg-slate-100 text-slate-700'
    },
    chipTitleBooking(b) {
      const parts = [
        b.client_name || '—',
        this.formatTime(b.schedule_time),
        b.schedule_coach ? `Coach ${b.schedule_coach}` : null,
        b.class_type === 'Reformer Class'
          ? b.reformer_type === 'Private'
            ? 'Reformer Solo'
            : `Reformer ${b.reformer_type || '—'}`
          : b.class_type || null,
        b.class_type === 'Reformer Class' &&
        (b.reformer_type === 'Duo' || b.reformer_type === 'Trio') &&
        b.companions != null
          ? `Companions ${b.companions}`
          : null,
        b.code_value ? `Code ${b.code_value}` : null,
      ].filter(Boolean)
      return parts.join(' • ')
    },

    formatDate(dateISO) {
      if (!dateISO) return ''
      const [y, m, d] = dateISO.split('-').map(Number)
      const dt = new Date(y, m - 1, d)
      return dt.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
    },
    formatTime(timeStr) {
      if (!timeStr) return ''
      const [hh, mm] = timeStr.split(':').map(Number)
      const dt = new Date()
      dt.setHours(hh ?? 0)
      dt.setMinutes(mm ?? 0)
      return dt.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })
    },
    currentMonthRangeISO() {
      // start = first day 00:00, end = last day 23:59:59 (in SQL we'll use DATE between inclusive)
      const start = new Date(this.viewYear, this.viewMonth, 1)
      const end = new Date(this.viewYear, this.viewMonth + 1, 0)
      const toISO = (d) => {
        const y = d.getFullYear()
        const m = String(d.getMonth() + 1).padStart(2, '0')
        const day = String(d.getDate()).padStart(2, '0')
        return `${y}-${m}-${day}`
      }
      return { startISO: toISO(start), endISO: toISO(end) }
    },
  },
  watch: {
    'pendingFilters.class_type'(val) {
      if (val !== 'Reformer Class') this.pendingFilters.reformer_type = 'All'
    },
  },
}
</script>
