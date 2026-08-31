<template>
  <AdminLayout>
    <!-- Header + actions -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold">Schedule Management</h1>

      <div class="flex items-center gap-2">
        <!-- Month Navigator -->
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
          class="ml-1 px-3 py-1.5 border rounded bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition"
        >
          Today
        </button>

        <!-- Actions -->
        <button
          @click="exportSchedules"
          class="ml-3 text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white px-4 py-2 font-oswald"
        >
          <i class="fas fa-table mr-2"></i>Generate Schedule
        </button>
        <button
          @click="openCreateModal"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white px-4 py-2 font-oswald"
        >
          <i class="fa-solid fa-plus mr-1"></i>Add Schedule
        </button>
      </div>
    </div>

    <!-- Filters toolbar -->
    <div
      class="mb-5 grid grid-cols-1 md:grid-cols-[minmax(12rem,18rem)_minmax(12rem,18rem)_minmax(12rem,18rem)_auto] items-end gap-3"
    >
      <!-- Coach dropdown -->
      <div class="relative w-full">
        <label class="block text-[11px] text-slate-500 mb-1">Coach</label>
        <select
          v-model="pendingFilters.coach"
          class="h-10 w-full max-w-xs py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-200"
        >
          <option value="">All coaches</option>
          <option v-for="c in coachOptions" :key="c" :value="c">{{ c }}</option>
        </select>
      </div>

      <!-- Class Type dropdown -->
      <div class="relative w-full">
        <label class="block text-[11px] text-slate-500 mb-1">Class Type</label>
        <select
          v-model="pendingFilters.class_type"
          class="h-10 w-full max-w-xs py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-200"
        >
          <option value="All">All</option>
          <option value="Mat Pilates">Mat Pilates</option>
          <option value="Reformer Class">Reformer Class</option>
          <option value="Yoga Class">Yoga Class</option>
        </select>
      </div>

      <!-- Reformer Type dropdown (only relevant when Reformer Class is selected) -->
      <div class="relative w-full" v-if="pendingFilters.class_type === 'Reformer Class'">
        <label class="block text-[11px] text-slate-500 mb-1">Reformer Type</label>
        <select
          v-model="pendingFilters.reformer_type"
          class="h-10 w-full max-w-xs py-2 px-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-200"
        >
          <option value="All">All</option>
          <!-- value is 'Private' but label is Solo, matching your forms -->
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
        <div
          v-for="day in daysInMonth"
          :key="isoOf(day)"
          class="h-24 md:h-28 border rounded-md p-2 flex flex-col overflow-hidden cursor-pointer hover:shadow transition"
          :class="['border-slate-200', isToday(day) ? 'ring-2 ring-sky-500' : '']"
          @click="openDayModal(day)"
        >
          <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500">{{ day }}</span>
            <span
              v-if="
                (filteredSchedulesByDate[isoOf(day)] &&
                  filteredSchedulesByDate[isoOf(day)].length) > 0
              "
              class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-white"
            >
              {{ filteredSchedulesByDate[isoOf(day)].length }}
            </span>
          </div>

          <div class="mt-1 space-y-1 overflow-y-auto">
            <div
              v-for="(s, idx) in (filteredSchedulesByDate[isoOf(day)] || []).slice(0, 3)"
              :key="s.id + '-' + idx"
              class="text-[11px] truncate rounded px-2 py-0.5"
              :class="chipClass(s)"
              :title="chipTitle(s)"
            >
              {{ formatTime(s.time) }} • {{ s.coach }}
            </div>

            <div
              v-if="
                (filteredSchedulesByDate[isoOf(day)] &&
                  filteredSchedulesByDate[isoOf(day)].length) > 3
              "
              class="text-[11px] italic text-slate-500"
            >
              +{{ filteredSchedulesByDate[isoOf(day)].length - 3 }} more…
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- === Day Modal (Manage Schedules for the chosen date) === -->
    <div
      v-if="dayModal.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-2xl">
        <div class="flex items-start justify-between">
          <h2 class="text-xl font-semibold text-slate-800 mb-2 font-oswald">
            Schedules on {{ formatDate(dayModal.dateISO) }}
          </h2>
          <button
            @click="dayModal.open = false"
            class="px-3 py-1.5 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition"
          >
            Close
          </button>
        </div>

        <p class="text-sm text-slate-600 mb-4">
          Total: <strong>{{ filteredDaySchedules.length }}</strong>
          <span
            v-if="filters.coach || filters.class_type !== 'All'"
            class="ml-2 text-xs text-slate-500"
          >
            (filters:
            <template v-if="filters.coach">coach: “{{ filters.coach }}”</template>
            <template
              v-if="
                filters.coach && (filters.class_type !== 'All' || filters.reformer_type !== 'All')
              "
              >,
            </template>
            <template v-if="filters.class_type !== 'All'"
              >class: “{{ filters.class_type }}”</template
            >
            <template
              v-if="filters.class_type === 'Reformer Class' && filters.reformer_type !== 'All'"
              >, reformer: “{{
                filters.reformer_type === 'Private' ? 'Solo' : filters.reformer_type
              }}”</template
            >
            )
          </span>
        </p>

        <!-- List of schedules -->
        <div class="max-h-[60vh] overflow-y-auto divide-y divide-slate-100">
          <div
            v-for="s in filteredDaySchedules"
            :key="s.id"
            class="py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2"
          >
            <div class="text-sm">
              <div class="font-medium text-slate-800">
                {{ s.coach }} • {{ formatTime(s.time) }}
                <span class="ml-2 text-xs px-2 py-0.5 rounded" :class="statusBadgeClass(s.status)">
                  {{ s.status }}
                </span>
              </div>
              <div class="text-slate-600">
                {{ s.class_type }}
                <span v-if="s.class_type === 'Reformer Class'">
                  ( {{ s.reformer_type === 'Private' ? 'Solo' : s.reformer_type || 'N/A' }} )
                </span>
                • Slots: <span class="font-medium">{{ s.slots_available }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click.stop="openEditModal(s)"
                class="px-3 py-1 text-sm border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition"
              >
                <i class="fa-solid fa-pen"></i> Edit
              </button>
              <button
                @click.stop="deleteSchedule(s.id)"
                class="px-3 py-1 text-sm border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition"
              >
                <i class="fa-solid fa-trash-can"></i> Delete
              </button>
            </div>
          </div>
        </div>

        <div class="mt-4 text-xs text-slate-500">
          Tip: Click “Add Schedule” above to create new time slots for
          {{ formatDate(dayModal.dateISO) }}.
        </div>
      </div>
    </div>

    <!-- === Your existing modals unchanged (Create, Edit, Delete) === -->
    <!-- Create Modal -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-md">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">Schedule Form</h2>
        <form @submit.prevent="isEditMode ? updateSchedule() : createSchedule()" class="space-y-3">
          <!-- Class Type -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Class Type</label>
            <select
              v-model="form.class_type"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-slate-400 appearance-none bg-white"
            >
              <option disabled value="">Select a class type</option>
              <option>Mat Pilates</option>
              <option>Reformer Class</option>
              <option>Yoga Class</option>
            </select>
            <p v-if="errors.class_type" class="text-red-500 text-xs mt-1">
              {{ errors.class_type }}
            </p>
          </div>

          <!-- Reformer -->
          <div v-if="form.class_type === 'Reformer Class'">
            <label class="block text-sm font-medium text-slate-700 mb-1">Reformer Type</label>
            <select
              v-model="form.reformer_type"
              :disabled="form.class_type !== 'Reformer Class'"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-slate-400 appearance-none bg-white"
            >
              <option disabled value="">Select reformer type</option>
              <option value="Private">Solo</option>
              <option value="Duo">Duo</option>
              <option value="Trio">Trio</option>
              <option value="Group">Group</option>
            </select>
            <p v-if="errors.reformer_type" class="text-red-500 text-xs mt-1">
              {{ errors.reformer_type }}
            </p>
          </div>

          <!-- Coach -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Coach</label>
            <select
              v-model="form.coach"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-slate-400 appearance-none bg-white"
            >
              <option disabled value="">Select a coach</option>
              <option v-for="coach in coaches" :key="coach.id" :value="coach.name">
                {{ coach.name }}
              </option>
            </select>
            <p v-if="errors.coach" class="text-red-500 text-xs mt-1">{{ errors.coach }}</p>
          </div>

          <!-- Date -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Date</label>
            <input
              v-model="form.date"
              type="date"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="errors.date" class="text-red-500 text-xs mt-1">{{ errors.date }}</p>
          </div>

          <!-- Time slots -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Time Slots</label>
            <div
              v-for="(time, index) in form.time_slots"
              :key="index"
              class="flex items-center gap-2 mb-2"
            >
              <input
                v-model="form.time_slots[index]"
                type="time"
                class="flex-1 border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
              />
              <button
                type="button"
                @click="removeTimeSlot(index)"
                class="text-red-500 hover:text-red-700 text-sm font-bold"
              >
                <i class="fa-solid fa-times"></i>
              </button>
            </div>
            <button
              type="button"
              @click="addTimeSlot"
              class="text-sm text-blue-600 hover:underline mt-1"
            >
              + Add another time
            </button>
            <p v-if="errors.time_slots" class="text-red-500 text-xs mt-1">
              {{ errors.time_slots }}
            </p>
          </div>

          <!-- Slots available -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Slots Available</label>
            <input
              v-model.number="form.slots_available"
              type="number"
              min="0"
              step="1"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="errors.slots_available" class="text-red-500 text-xs mt-1">
              {{ errors.slots_available }}
            </p>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 rounded bg-white border border-slate-300 hover:bg-slate-700 hover:text-white transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded bg-white border border-slate-300 hover:bg-slate-700 hover:text-white transition"
            >
              {{ isEditMode ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Modal (unchanged) -->
    <div
      v-if="isEditModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-md">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">Edit Schedule</h2>
        <form @submit.prevent="submitEdit" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Class Type</label>
            <select
              v-model="form.class_type"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white"
            >
              <option disabled value="">Select a class type</option>
              <option>Mat Pilates</option>
              <option>Reformer Class</option>
              <option>Yoga Class</option>
            </select>
            <p v-if="errors.class_type" class="text-red-500 text-xs mt-1">
              {{ errors.class_type }}
            </p>
          </div>

          <div v-if="form.class_type === 'Reformer Class'">
            <label class="block text-sm font-medium text-slate-700 mb-1">Reformer Type</label>
            <select
              v-model="form.reformer_type"
              :disabled="form.class_type !== 'Reformer Class'"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white"
            >
              <option disabled value="">Select reformer type</option>
              <option value="Private">Solo</option>
              <option value="Duo">Duo</option>
              <option value="Trio">Trio</option>
              <option value="Group">Group</option>
            </select>
            <p v-if="errors.reformer_type" class="text-red-500 text-xs mt-1">
              {{ errors.reformer_type }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Coach</label>
            <select
              v-model="form.coach"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white"
            >
              <option disabled value="">Select a coach</option>
              <option v-for="coach in coaches" :key="coach.id" :value="coach.name">
                {{ coach.name }}
              </option>
            </select>
            <p v-if="errors.coach" class="text-red-500 text-xs mt-1">{{ errors.coach }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Date</label>
            <input
              v-model="form.date"
              type="date"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="errors.date" class="text-red-500 text-xs mt-1">{{ errors.date }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Time Slot</label>
            <input
              v-model="singleEditForm.time"
              type="time"
              class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="editErrors.time" class="text-red-500 text-xs mt-1">{{ editErrors.time }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Slots Available</label>
            <input
              v-model.number="singleEditForm.slots_available"
              type="number"
              min="0"
              step="1"
              class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="editErrors.slots_available" class="text-red-500 text-xs mt-1">
              {{ editErrors.slots_available }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
            <select
              v-model="form.status"
              class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white"
            >
              <option value="Active">Active</option>
              <option value="Cancelled">Cancelled</option>
            </select>
            <p v-if="editErrors.status" class="text-red-500 text-xs mt-1">
              {{ editErrors.status }}
            </p>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              @click="isEditModalOpen = false"
              class="px-4 py-2 rounded bg-white border border-slate-300 hover:bg-slate-700 hover:text-white transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.class_type === 'Reformer Class' && !form.reformer_type"
              class="px-4 py-2 rounded bg-white border border-slate-300 hover:bg-slate-700 hover:text-white transition disabled:opacity-50"
            >
              Update
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal (unchanged) -->
    <div
      v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-2 font-oswald">Confirm Deletion</h2>
        <p class="text-sm text-slate-600 mb-4">
          Are you sure you want to delete this schedule of Coach
          <strong>{{ deleteCandidate?.coach }}</strong> on
          <strong>{{ formatDate(deleteCandidate?.date) }}</strong> at
          <strong>{{ formatTime(deleteCandidate?.time) }}</strong
          >? This action cannot be undone.
        </p>

        <div class="flex justify-end gap-2">
          <button
            @click="isDeleteModalOpen = false"
            class="px-4 py-2 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition font-oswald"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition font-oswald"
          >
            Delete
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
    const today = new Date()
    return {
      schedules: [],
      coaches: [],
      search: '',
      filters: {
        coach: '',
        class_type: 'All',
        reformer_type: 'All',
      },
      pendingFilters: {
        coach: '',
        class_type: 'All',
        reformer_type: 'All',
      },
      // calendar state
      viewYear: today.getFullYear(),
      viewMonth: today.getMonth(), // 0-11
      // modals / forms
      currentPage: 1, // legacy (unused now)
      perPage: 10, // legacy (unused now)
      isModalOpen: false,
      isEditModalOpen: false,
      singleEditForm: {
        id: null,
        class_type: '',
        reformer_type: '',
        coach: '',
        date: '',
        time: '',
        slots_available: 1,
      },
      editErrors: {},
      form: {
        id: null,
        class_type: '',
        reformer_type: '',
        coach: '',
        date: '',
        time_slots: [''],
        slots_available: 1,
        status: 'Active',
      },
      errors: {},
      showToast: null,
      deleteCandidate: null,
      isDeleteModalOpen: false,

      // Day modal
      dayModal: {
        open: false,
        dateISO: null, // 'YYYY-MM-DD'
      },

      // backend base
      baseURL: import.meta.env.VITE_API_BASE_URL,

      // feature flags
      useRangeFetch: false, // will fallback to single fetch if range endpoint not available
    }
  },
  created() {
    this.showToast = inject('showToast')
    this.fetchCoaches()
    this.loadMonth()
  },
  computed: {
    monthLabel() {
      const d = new Date(this.viewYear, this.viewMonth, 1)
      return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
    },
    weekdayLabels() {
      return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    },
    firstWeekdayIndex() {
      const d = new Date(this.viewYear, this.viewMonth, 1)
      return d.getDay()
    },
    daysInMonth() {
      return new Date(this.viewYear, this.viewMonth + 1, 0).getDate()
    },
    schedulesByDate() {
      const map = {}
      for (const s of this.schedules) {
        const key = (s.date || '').slice(0, 10) // ensure 'YYYY-MM-DD'
        if (!key) continue
        ;(map[key] ||= []).push(s)
      }
      for (const k in map) {
        map[k].sort((a, b) => (a.time || '').localeCompare(b.time || ''))
      }
      return map
    },
    coachOptions() {
      // Unique, sorted coach names present in current month’s schedules
      const set = new Set((this.schedules || []).map((s) => (s.coach || '').trim()).filter(Boolean))
      return Array.from(set).sort((a, b) => a.localeCompare(b))
    },

    filteredSchedules() {
      return (this.schedules || []).filter((s) => {
        const coachOk = this.filters.coach ? s.coach === this.filters.coach : true
        const classOk =
          this.filters.class_type === 'All' ? true : s.class_type === this.filters.class_type
        const reformerOk =
          this.filters.class_type === 'Reformer Class'
            ? this.filters.reformer_type === 'All'
              ? true
              : s.reformer_type === this.filters.reformer_type
            : true
        return coachOk && classOk && reformerOk
      })
    },

    filteredSchedulesByDate() {
      // Same shape as schedulesByDate but after filters
      const map = {}
      for (const s of this.filteredSchedules) {
        const key = (s.date || '').slice(0, 10)
        if (!key) continue
        ;(map[key] ||= []).push(s)
      }
      for (const k in map) {
        map[k].sort((a, b) => (a.time || '').localeCompare(b.time || ''))
      }
      return map
    },

    filteredDaySchedules() {
      // Use filteredSchedulesByDate instead of free-text search
      if (!this.dayModal.open || !this.dayModal.dateISO) return []
      return this.filteredSchedulesByDate[this.dayModal.dateISO] || []
    },
  },
  watch: {
    'pendingFilters.class_type'(val) {
      if (val !== 'Reformer Class') this.pendingFilters.reformer_type = 'All'
    },
    'form.class_type'(newVal) {
      if (newVal !== 'Reformer Class') {
        this.form.reformer_type = ''
      }
    },
  },
  methods: {
    // ---------- Calendar navigation ----------
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
    chipTitle(s) {
      const parts = [
        this.formatTime(s.time),
        s.coach ? `Coach ${s.coach}` : null,
        s.class_type === 'Reformer Class'
          ? this.shortClassLabel(s) // e.g. "Reformer Duo"
          : s.class_type || null,
        s.slots_available != null ? `Slots ${s.slots_available}` : null,
        s.status ? `Status ${s.status}` : null,
      ].filter(Boolean)
      return parts.join(' • ')
    },
    applyFilters() {
      this.filters = {
        coach: this.pendingFilters.coach || '',
        class_type: this.pendingFilters.class_type || 'All',
        reformer_type:
          this.pendingFilters.class_type === 'Reformer Class'
            ? this.pendingFilters.reformer_type || 'All'
            : 'All',
      }
    },

    clearFilters() {
      this.pendingFilters = { coach: '', class_type: 'All', reformer_type: 'All' }
      this.filters = { coach: '', class_type: 'All', reformer_type: 'All' }
    },

    isoOf(day) {
      const d = new Date(this.viewYear, this.viewMonth, day)
      const y = d.getFullYear()
      const m = String(d.getMonth() + 1).padStart(2, '0')
      const dd = String(d.getDate()).padStart(2, '0')
      return `${y}-${m}-${dd}`
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

    // ---------- Fetch ----------
    async loadMonth() {
      const { startISO, endISO } = this.currentMonthRangeISO()
      this.schedules = []

      const toHHMM = (t) => {
        if (!t || typeof t !== 'string') return ''
        const [hh = '', mm = ''] = t.trim().split(':')
        return `${hh}:${mm}`
      }

      const toISODate = (d) => {
        if (!d) return ''
        // Handles 'YYYY-MM-DD', 'YYYY-MM-DDTHH:mm:ss', or any extra whitespace
        const s = String(d).trim()
        return s.slice(0, 10) // e.g. '2025-10-03'
      }

      try {
        const res = await fetch(`${this.baseURL}/php/schedules/fetch.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ start_date: startISO, end_date: endISO }),
        })
        if (!res.ok) throw new Error(`HTTP ${res.status}`)

        const data = await res.json()

        this.schedules = Array.isArray(data)
          ? data.map((s) => ({
              ...s,
              date: toISODate(s.date),
              time: toHHMM(s.time),
            }))
          : []
      } catch (err) {
        this.schedules = []
        this.showToast?.(`Failed to fetch schedules for month. ${err.message || ''}`, 'error')
      }
    },

    currentMonthRangeISO() {
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

    async fetchCoaches() {
      try {
        const res = await fetch(`${this.baseURL}/php/coaches/fetch.php`, { credentials: 'include' })
        this.coaches = await res.json()
      } catch {
        this.showToast?.('Failed to fetch coaches.', 'error')
      }
    },

    // ---------- Helpers for rendering ----------
    shortClassLabel(s) {
      if (s.class_type === 'Yoga Class') return 'Yoga'
      // Reformer
      const r = s.reformer_type === 'Private' ? 'Solo' : s.reformer_type || '—'
      return `Reformer ${r}`
    },
    chipClass(s) {
      // subtle colors by status
      if (s.status === 'Cancelled') return 'bg-red-50 text-red-700 border border-red-200'
      return 'bg-slate-100 text-slate-700'
    },
    statusBadgeClass(status) {
      return status === 'Cancelled' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'
    },

    // ---------- Existing methods you already had (kept) ----------
    openCreateModal() {
      // prefill date with current day in view if day modal is focused
      const prefillDate = this.dayModal.open ? this.dayModal.dateISO : ''
      this.form = {
        id: null,
        class_type: '',
        reformer_type: '',
        coach: '',
        date: prefillDate,
        time_slots: [''],
        slots_available: 1,
        status: 'Active',
      }
      this.errors = {}
      this.isEditMode = false
      this.isModalOpen = true
    },
    closeModal() {
      this.isModalOpen = false
    },
    displayReformerLabel(type) {
      if (!type) return '—'
      return type === 'Private' ? 'Solo' : type
    },
    normalizeErrors(payload) {
      if (payload && typeof payload === 'object' && !Array.isArray(payload)) return payload
      const err = {}
      ;(payload || []).forEach((e) => {
        const key = (e.split(' ')[0] || 'general').toLowerCase()
        err[key] = e
      })
      return err
    },
    openEditModal(schedule) {
      this.form = {
        class_type: schedule.class_type,
        reformer_type: schedule.reformer_type,
        coach: schedule.coach,
        date: schedule.date,
        status: schedule.status,
      }
      this.singleEditForm = {
        id: schedule.id,
        time: schedule.time,
        slots_available: Number(schedule.slots_available ?? 0),
      }
      this.editErrors = {}
      this.isEditModalOpen = true
    },
    async createSchedule() {
      if (!this.validateForm()) return
      try {
        const res = await fetch(`${this.baseURL}/php/schedules/create.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ...this.form, time_slots: this.form.time_slots }),
        })
        const data = await res.json()
        if (data.success) {
          await this.loadMonth()
          this.closeModal()
          this.showToast('Schedule created successfully.', 'success')
        } else {
          this.errors = this.normalizeErrors(data.errors || [])
          const msg = this.errors.time_slots || data.message || 'Validation error.'
          this.showToast(msg, 'error')
        }
      } catch {
        this.showToast('Failed to create schedule.', 'error')
      }
    },
    async updateSchedule() {
      if (!this.validateForm(true)) return
      try {
        const res = await fetch(`${this.baseURL}/php/schedules/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })
        const data = await res.json()
        if (data.success) {
          await this.loadMonth()
          this.closeModal()
          this.showToast('Schedule updated successfully.', 'success')
        } else {
          this.errors = this.normalizeErrors(data.errors || [])
          const msg = this.errors.time || data.message || 'Validation error.'
          this.showToast(msg, 'error')
        }
      } catch {
        this.showToast('Failed to update schedule.', 'error')
      }
    },
    async submitEdit() {
      this.editErrors = {}
      if (!this.singleEditForm.time) this.editErrors.time = 'Time is required'

      // allow 0, block negatives and blank
      if (
        this.singleEditForm.slots_available === '' ||
        this.singleEditForm.slots_available == null
      ) {
        this.editErrors.slots_available = 'Slots is required'
      } else if (Number(this.singleEditForm.slots_available) < 0) {
        this.editErrors.slots_available = 'Slots must be at least 0'
      }

      if (this.form.class_type === 'Reformer Class' && !this.form.reformer_type)
        this.editErrors.reformer_type = 'Reformer type is required for Reformer Class'
      if (Object.keys(this.editErrors).length > 0) return

      const payload = {
        id: this.singleEditForm.id,
        class_type: this.form.class_type,
        reformer_type: this.form.reformer_type,
        coach: this.form.coach,
        date: this.form.date,
        time: this.singleEditForm.time,
        slots_available: this.singleEditForm.slots_available,
        status: this.form.status,
      }

      try {
        const res = await fetch(`${this.baseURL}/php/schedules/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        })
        const data = await res.json()
        if (data.success) {
          await this.loadMonth()
          this.isEditModalOpen = false
          this.showToast('Schedule updated successfully.', 'success')
        } else {
          this.editErrors = this.normalizeErrors(data.errors || [])
          const msg = this.editErrors.time || data.message || 'Update failed.'
          this.showToast(msg, 'error')
        }
      } catch {
        this.showToast('Failed to update.', 'error')
      }
    },
    deleteSchedule(id) {
      const schedule = this.schedules.find((s) => s.id === id)
      if (!schedule) return
      this.deleteCandidate = schedule
      this.isDeleteModalOpen = true
    },
    async confirmDelete() {
      try {
        const res = await fetch(`${this.baseURL}/php/schedules/delete.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: this.deleteCandidate.id }),
        })
        const data = await res.json()
        if (data.success) {
          await this.loadMonth()
          this.showToast('Schedule deleted successfully.', 'success')
        } else {
          this.showToast(data.message || 'Delete failed.', 'error')
        }
      } catch {
        this.showToast('Delete failed.', 'error')
      } finally {
        this.isDeleteModalOpen = false
        this.deleteCandidate = null
      }
    },
    async exportSchedules() {
      try {
        const res = await fetch(`${this.baseURL}/php/schedules/export.php`, {
          method: 'GET',
          credentials: 'include',
        })
        const blob = await res.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'schedules.csv')
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch {
        this.showToast('Failed to export CSV.', 'error')
      }
    },

    // validation + misc
    validateForm() {
      this.errors = {}
      if (!this.form.class_type) this.errors.class_type = 'Class type is required'
      if (this.form.class_type === 'Reformer Class' && !this.form.reformer_type)
        this.errors.reformer_type = 'Reformer type is required for Reformer Class'
      if (!this.form.coach) this.errors.coach = 'Coach is required'
      if (!this.form.date) this.errors.date = 'Date is required'
      if (!this.form.time_slots.length || this.form.time_slots.some((t) => !t)) {
        this.errors.time_slots = 'At least one valid time slot is required'
      } else {
        const seen = new Set()
        for (const time of this.form.time_slots) {
          if (seen.has(time)) {
            this.errors.time_slots = 'Duplicate time slots are not allowed'
            break
          }
          seen.add(time)
        }
      }

      // allow 0, block negatives and blank
      if (this.form.slots_available === '' || this.form.slots_available == null) {
        this.errors.slots_available = 'Slots is required'
      } else if (Number(this.form.slots_available) < 0) {
        this.errors.slots_available = 'Slots must be at least 0'
      }

      return Object.keys(this.errors).length === 0
    },
    addTimeSlot() {
      if (!this.form.time_slots.length || this.form.time_slots[this.form.time_slots.length - 1]) {
        this.form.time_slots.push('')
      }
    },
    removeTimeSlot(index) {
      this.form.time_slots.splice(index, 1)
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
  },
}
</script>
