<template>
  <AdminLayout>
    <div class="flex justify-between items-center mb-2">
      <!-- Left: Title -->
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold mb-2">Booking Management</h1>

      <!-- Right: Buttons grouped side by side -->
      <div class="flex space-x-2">
        <button
          @click="exportClients"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="mr-2 fas fa-regular fa-table text-md"></i>
          Generate Excel
        </button>
        <button
          @click="openEditBooking"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="fa-solid fa-pen-to-square mr-1"></i>
          Modify Booking
        </button>
      </div>
    </div>

    <!-- Search -->
    <div class="mb-5 relative w-full md:w-1/3">
      <i
        class="fas fa-search text-slate-500 text-sm absolute left-3 top-1/2 transform -translate-y-1/2"
      ></i>
      <input
        v-model="search"
        type="text"
        placeholder="Search by name"
        class="w-full py-2 pl-10 pr-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 placeholder-slate-400 border-slate-200 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200"
      />
    </div>

    <!-- Bookings Table -->
    <div class="overflow-x-auto rounded-md shadow-lg border border-slate-200">
      <table class="min-w-full text-left text-sm text-slate-700 font-medium bg-white">
        <thead class="...">
          <tr>
            <th class="p-3">Client's Name</th>
            <th class="p-3">Class Type</th>
            <th class="p-3">Date & Time</th>
            <th class="p-3">Sessions</th>
            <th class="p-3">Contact Number</th>
            <th class="p-3">Total Amount</th>
            <th class="p-3">Proof of Payment</th>
            <th class="p-3">Code Used</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="paginatedBookings.length === 0">
            <td colspan="7" class="text-center text-slate-500 py-6">
              No Pending Booking Confirmation
            </td>
          </tr>
          <tr v-for="booking in paginatedBookings" :key="booking.id">
            <td class="p-3">{{ booking.client_name || '—' }}</td>
            <td class="p-3">
              {{ booking.schedule_class }}
              <template v-if="booking.schedule_class !== 'Yoga Class'">
                <br />
                ( {{ displayReformerLabel(booking.schedule_type) }} )
              </template>
            </td>

            <td class="p-3">
              {{ formatDate(booking.schedule_date) }} ( {{ formatTime(booking.schedule_time) }} )
            </td>
            <td class="p-3">
              {{ booking.sessions_left > 100 ? 'Unlimited' : booking.session_count }}
            </td>

            <td class="p-3">
              {{ booking.client_phone }}
            </td>
            <td class="p-3">₱{{ Number(booking.total_amount).toLocaleString('en-PH') }}</td>
            <!-- <td class="p-3">{{ booking.schedule_coach }}</td> -->
            <td class="p-3">
              <a
                v-if="booking.proof_url"
                :href="`${baseURL}/php/image-preview.php?file=${booking.proof_url}`"
                target="_blank"
                class="text-blue-600 hover:underline"
              >
                View Payment
              </a>
              <span v-else class="text-slate-400">No Image</span>
            </td>
            <td class="p-3">{{ booking.code_value ?? 'None' }}</td>
            <td class="p-3 flex flex-wrap gap-2">
              <button
                @click="approveBooking(booking.id)"
                class="px-3 py-1 text-sm border rounded-full shadow text-green-500 border-slate-200 hover:bg-green-400 hover:text-slate-700 transition"
              >
                <i class="fa-solid fa-check"></i> Approve
              </button>
              <button
                @click="declineBooking(booking.id)"
                class="px-3 py-1 text-sm border rounded-full shadow text-red-500 border-slate-200 hover:bg-red-400 hover:text-slate-700 transition"
              >
                <i class="fa-solid fa-xmark"></i> Decline
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      class="mt-6 flex flex-col md:flex-row md:justify-between md:items-center gap-3 text-md text-slate-600"
    >
      <!-- Left: Showing X to Y of Z entries -->
      <div>
        Showing <span class="font-semibold">{{ startIndex + 1 }}</span> to
        <span class="font-semibold">{{ endIndex }}</span> of
        <span class="font-semibold">{{ filteredBookings.length }}</span> entries
      </div>

      <!-- Right: Pagination Controls -->
      <div class="flex items-center font-oswald gap-2">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="flex font-oswald text-md items-center gap-1 px-3 py-1.5 border rounded transition-colors duration-500 bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <i class="fa-solid fa-angle-left"></i> Prev
        </button>

        <span class="px-3 py-1.5 rounded bg-slate-200 text-slate-800 font-semibold">
          Page {{ currentPage }}
        </span>

        <button
          @click="currentPage++"
          :disabled="endIndex >= filteredBookings.length"
          class="flex text-md items-center gap-1 px-3 py-1.5 border rounded transition-colors duration-500 bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Next <i class="fa-solid fa-angle-right"></i>
        </button>
      </div>
    </div>

    <!-- Booking Edit Modal -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-lg">
        <h2 class="text-xl font-semibold text-slate-800 mb-2 font-oswald">Modify Booking</h2>
        <p class="text-sm text-slate-600 mb-4">
          Select the client and schedule, then update the booking details.
        </p>

        <!-- Client Dropdown -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-slate-700 mb-1">Client</label>
          <select
            v-model="form.client_id"
            @change="onClientChange"
            class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none border-slate-300 text-slate-800"
          >
            <option value="" disabled>Select a client</option>
            <option v-for="client in clients" :key="client.id" :value="client.id">
              {{ client.name }}
            </option>
          </select>
        </div>

        <!-- Booking Dropdown based on client -->
        <div class="mb-4" v-if="form.client_id">
          <label class="block text-sm font-medium text-slate-700 mb-1">Booking (Date & Time)</label>
          <select
            v-model="form.booking_id"
            @change="onBookingChange"
            class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none border-slate-300 text-slate-800"
          >
            <option value="" disabled>Select a booking</option>
            <option
              v-for="schedule in schedules"
              :key="schedule.booking_id"
              :value="schedule.booking_id"
            >
              Booking ID: {{ schedule.booking_id }} — {{ formatDate(schedule.date) }}
              {{ formatTime(schedule.time) }}
            </option>
          </select>
        </div>

        <!-- Editable Fields -->
        <div v-if="showEditableFields && form.client_id && form.booking_id" class="space-y-4 mb-4">
          <!-- Change Date & Time (optional) -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              Change Date & Time (optional)
            </label>
            <select
              :key="'resched-' + rescheduleSelectKey"
              v-model="form.new_schedule_id"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none border-slate-300 text-slate-800"
              :disabled="rescheduleLoading"
            >
              <option value="">
                {{
                  rescheduleLoading ? 'Loading available schedules…' : '— Keep current schedule —'
                }}
              </option>

              <option
                v-for="opt in rescheduleOptions"
                :key="opt.id"
                :value="opt.id"
                :disabled="!opt.is_current && opt.slots_available <= 0"
              >
                {{ formatDate(opt.date) }} ({{ formatTime(opt.time) }}) — Coach {{ opt.coach }}
                <span v-if="opt.is_current"> — current</span>
                <span v-else>
                  — {{ opt.slots_available }} slot{{
                    opt.slots_available === 1 ? '' : 's'
                  }}
                  left</span
                >
              </option>
            </select>

            <p
              v-if="!rescheduleLoading && !rescheduleOptions.length"
              class="text-xs text-slate-500 mt-1"
            >
              No alternative schedules with available slots for this class/type.
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1"
              >Number of Session(s)</label
            >
            <input
              v-model="form.session_count"
              type="text"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm border-slate-300 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200 text-slate-800"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Total Amount</label>
            <input
              v-model="form.total_amount"
              type="number"
              step="0.01"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm border-slate-300 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200 text-slate-800"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Attendees</label>
            <input
              v-model="form.pax"
              type="number"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm border-slate-300 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200 text-slate-800"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
            <select
              v-model="form.status"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm border-slate-300 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200 text-slate-800"
            >
              <option value="Pending Confirmation">Pending Confirmation</option>
              <option value="Approved">Approved</option>
              <option value="Declined">Declined</option>
              <option value="Cancelled">Cancelled</option>
              <option value="No Show">No Show</option>
            </select>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2">
          <button
            @click="closeModal"
            class="px-4 py-2 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition font-oswald"
          >
            Cancel
          </button>
          <button
            @click="openConfirmEditModal"
            :disabled="rescheduleLoading"
            class="px-4 py-2 rounded bg-slate-700 text-white hover:bg-slate-800 transition font-oswald disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Update
          </button>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div
      v-if="confirmActionModal.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-2 font-oswald">
          Confirm {{ confirmActionModal.action === 'approve' ? 'Approval' : 'Decline' }}
        </h2>
        <p class="text-sm text-slate-600 mb-4">
          Are you sure you want to
          <strong class="uppercase">{{ confirmActionModal.action }}</strong>
          the booking of <strong>{{ confirmActionModal.booking?.client_name }}</strong> on
          <strong>{{ formatDate(confirmActionModal.booking?.schedule_date) }}</strong> at
          <strong>{{ formatTime(confirmActionModal.booking?.schedule_time) }}</strong
          >?
        </p>

        <div class="flex justify-end gap-2">
          <button
            @click="confirmActionModal.open = false"
            class="px-4 py-2 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition font-oswald"
          >
            Cancel
          </button>
          <button
            @click="confirmApproveDecline"
            :class="`px-4 py-2 rounded text-white hover:opacity-90 transition font-oswald ${
              confirmActionModal.action === 'approve'
                ? 'bg-green-600 hover:bg-green-700'
                : 'bg-red-600 hover:bg-red-700'
            }`"
          >
            {{ confirmActionModal.action === 'approve' ? 'Approve' : 'Decline' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm Edit Modal -->
    <div
      v-if="confirmEditModal.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-2 font-oswald">Confirm Update</h2>
        <p class="text-sm text-slate-600 mb-4">
          Are you sure you want to update this booking? Please double-check all values before
          proceeding.
        </p>
        <ul class="text-sm text-slate-700 mb-4 space-y-1">
          <li><strong>Number of Session(s):</strong> {{ form.session_count }}</li>
          <li><strong>Total Amount:</strong> ₱{{ Number(form.total_amount).toLocaleString() }}</li>
          <li><strong>Attendees:</strong> {{ form.pax }}</li>
          <li><strong>Status:</strong> {{ form.status }}</li>
        </ul>

        <div class="flex justify-end gap-2">
          <button
            @click="confirmEditModal.open = false"
            class="px-4 py-2 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition font-oswald"
          >
            Cancel
          </button>
          <button
            @click="confirmUpdateBooking"
            class="px-4 py-2 rounded bg-slate-700 text-white hover:bg-slate-800 transition font-oswald"
          >
            Confirm Update
          </button>
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
    return {
      bookings: [],
      baseURL: import.meta.env.VITE_API_BASE_URL,
      isModalOpen: false,
      isEditMode: false,
      search: '',
      currentPage: 1,
      perPage: 10,
      rescheduleOptions: [], // NEW
      rescheduleLoading: false, // NEW
      rescheduleSelectKey: 0,
      form: {
        booking_id: '',
        client_id: '',
        session_count: '',
        total_amount: '',
        pax: '',
        status: '',
        new_schedule_id: '',
      },
      clients: [],
      schedules: [],
      showEditableFields: false,
      confirmActionModal: {
        open: false,
        action: null, // 'approve' or 'decline'
        booking: null,
      },
      confirmEditModal: {
        open: false,
      },
      deleteCandidate: null,
      isDeleteModalOpen: false,
      errors: {},
      showToast: null,
    }
  },
  watch: {
    search() {
      this.currentPage = 1
    },
  },
  computed: {
    filteredBookings() {
      const term = this.search.toLowerCase()
      return this.bookings.filter(
        (b) => b.client_name && b.client_name.toLowerCase().includes(term),
      )
    },
    startIndex() {
      return (this.currentPage - 1) * this.perPage
    },
    endIndex() {
      return Math.min(this.startIndex + this.perPage, this.filteredBookings.length)
    },
    paginatedBookings() {
      return this.filteredBookings.slice(this.startIndex, this.endIndex)
    },
  },
  created() {
    this.showToast = inject('showToast')
    this.fetchBookings()
  },
  methods: {
    async fetchBookings() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/fetch.php`, {
          credentials: 'include',
        })
        const data = await res.json()
        this.bookings = data
      } catch {
        this.showToast('Failed to fetch bookings.', 'error')
      }
    },
    async fetchSchedulesForClient(clientId) {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/fetch_edit_schedules.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ client_id: clientId }),
        })

        const data = await res.json()
        this.schedules = data
      } catch {
        this.showToast('Failed to load schedules for selected client.', 'error')
        this.schedules = []
      }
    },
    async fetchBookingDetailsById(bookingId) {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/fetch_booking_by_id.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ booking_id: bookingId }),
        })

        const data = await res.json()

        if (data && data.id) {
          this.form.booking_id = data.id
          this.form.session_count = data.session_count
          this.form.total_amount = data.total_amount
          this.form.pax = data.pax
          this.form.status = data.status
          this.showEditableFields = true
        } else {
          this.showToast('Booking not found for the selected ID.', 'warning')
        }
      } catch {
        this.showToast('Failed to load booking details.', 'error')
      }
    },
    async fetchClientsWithBookings() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/fetch_edit_clients.php`, {
          credentials: 'include',
        })
        this.clients = await res.json()
      } catch {
        this.showToast('Failed to load clients with bookings.', 'error')
      }
    },
    async onBookingChange() {
      if (!this.form.booking_id) {
        this.showEditableFields = false
        return
      }
      this.form.new_schedule_id = ''
      this.rescheduleOptions = []
      this.rescheduleLoading = true
      this.showEditableFields = false

      try {
        await this.fetchBookingDetailsById(this.form.booking_id)
        await this.fetchRescheduleOptions(this.form.booking_id)
      } finally {
        this.rescheduleLoading = false
        this.showEditableFields = true
        this.rescheduleSelectKey++ // force select to rebuild with fresh options
      }
    },

    async fetchRescheduleOptions(bookingId) {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/fetch_reschedule_options.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ booking_id: bookingId }),
        })
        this.rescheduleOptions = await res.json()
      } catch {
        this.rescheduleOptions = []
        this.showToast('Failed to load reschedule options.', 'error')
      }
    },

    approveBooking(id) {
      const booking = this.bookings.find((b) => b.id === id)
      if (!booking) return
      this.confirmActionModal = { open: true, action: 'approve', booking }
    },

    declineBooking(id) {
      const booking = this.bookings.find((b) => b.id === id)
      if (!booking) return
      this.confirmActionModal = { open: true, action: 'decline', booking }
    },
    async confirmApproveDecline() {
      const { booking, action } = this.confirmActionModal
      const newStatus = action === 'approve' ? 'Approved' : 'Declined'

      const payload = {
        id: booking.id, // ✅ use the booking.id from table row
        session_count: booking.session_count,
        total_amount: booking.total_amount,
        pax: booking.pax,
        status: newStatus,
      }

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchBookings()
          this.showToast(`Booking ${newStatus}.`, 'success')
        } else {
          this.showToast(data.message || 'Update failed', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      } finally {
        this.confirmActionModal = { open: false, action: null, booking: null }
      }
    },
    openCreateModal() {
      this.resetForm()
      this.isEditMode = false
      this.isModalOpen = true
    },
    async openEditBooking() {
      this.resetForm()
      this.isEditMode = true
      this.isModalOpen = true
      await this.fetchClientsWithBookings() // NEW METHOD
    },
    closeModal() {
      this.isModalOpen = false
    },
    resetForm() {
      this.form = {
        booking_id: '',
        client_id: '',
        session_count: '',
        total_amount: '',
        pax: '',
        status: '',
        new_schedule_id: '',
      }
      this.errors = {}
      this.rescheduleOptions = []
      this.rescheduleLoading = false
      this.rescheduleSelectKey++
    },
    displayReformerLabel(type) {
      if (!type) return '—'
      return type === 'Private' ? 'Solo' : type
    },

    openConfirmEditModal() {
      if (!this.validateForm()) return
      this.confirmEditModal.open = true
    },
    validateForm() {
      this.errors = {}

      if (!this.form.session_count) this.errors.session_count = 'Session count is required'
      if (!this.form.total_amount) this.errors.total_amount = 'Total amount is required'
      if (!this.form.pax) this.errors.pax = 'Pax is required'
      if (!this.form.status.trim()) this.errors.status = 'Status is required'
      if (!this.form.client_id) this.errors.client_id = 'Client is required'
      if (!this.form.booking_id) this.errors.booking_id = 'Booking is required'

      return Object.keys(this.errors).length === 0
    },
    deleteBooking(id) {
      const booking = this.bookings.find((b) => b.id === id)
      if (!booking) return
      this.deleteCandidate = booking
      this.isDeleteModalOpen = true
    },
    async onClientChange() {
      this.form.schedule_id = ''
      this.form.session_count = ''
      this.form.total_amount = ''
      this.form.pax = ''
      this.form.status = ''
      this.schedules = []
      this.showEditableFields = false

      if (this.form.client_id) {
        await this.fetchSchedulesForClient(this.form.client_id)
      }
    },
    async createBooking() {
      if (!this.validateForm()) return

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/create.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchBookings()
          this.closeModal()
          this.showToast('Booking created successfully.', 'success')
        } else if (data.errors) {
          this.errors = Object.fromEntries(
            data.errors.map((e) => [e.split(' ')[0].toLowerCase(), e]),
          )
          this.showToast('Validation error.', 'error')
        } else {
          this.showToast(data.message || 'Create failed', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      }
    },
    formatTime(timeStr) {
      if (!timeStr) return ''
      const [hour, minute] = timeStr.split(':')
      const date = new Date()
      date.setHours(+hour)
      date.setMinutes(+minute)
      return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      const options = { year: 'numeric', month: 'long', day: 'numeric' }
      return new Date(dateStr).toLocaleDateString('en-US', options)
    },
    async updateBooking() {
      if (!this.validateForm()) return

      const payload = {
        id: this.form.booking_id,
        session_count: this.form.session_count,
        total_amount: this.form.total_amount,
        pax: this.form.pax,
        status: this.form.status,
      }

      if (this.form.new_schedule_id) {
        payload.new_schedule_id = Number(this.form.new_schedule_id)
      }

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        })
        const data = await res.json()
        if (data.success) {
          this.fetchBookings()
          this.closeModal()
          if (data.generated_code) {
            this.showToast(`Booking updated. Code generated: ${data.generated_code}`, 'success')
          } else {
            this.showToast('Booking updated successfully.', 'success')
          }
        } else {
          this.showToast(data.message || 'Update failed', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      }
    },

    async confirmDelete() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/delete.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: this.deleteCandidate.id }),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchBookings()
          this.showToast('Booking deleted successfully.', 'success')
        } else {
          this.showToast(data.message || 'Delete failed', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      } finally {
        this.isDeleteModalOpen = false
        this.deleteCandidate = null
      }
    },
    async confirmUpdateBooking() {
      this.confirmEditModal.open = false
      await this.updateBooking()
    },
    async exportClients() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/export.php`, {
          method: 'GET',
          credentials: 'include',
        })

        const blob = await res.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'bookings.csv')
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch {
        this.showToast('Failed to export CSV file.', 'error')
      }
    },
  },
}
</script>
