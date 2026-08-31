<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-cover bg-center backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-xl p-8 shadow-lg relative">
      <h2 class="text-2xl font-normal font-garamond text-center text-[#1A1A1A] mb-2">
        Booking Form
      </h2>
      <p class="text-center font-quicksand font-normal text-sm text-gray-500 mb-6">
        Please complete the form below to proceed with your booking.
      </p>

      <!-- Stepper Indicator -->
      <div>
        <div class="w-full bg-gray-200 rounded-full h-1.5">
          <div :style="{ width: `${stepProgress}%` }" class="bg-[#d1b38e] h-1.5 rounded-full transition-all"></div>
        </div>
        <p class="text-sm text-center font-quicksand text-gray-500 mt-2">
          Step <strong>{{ step }}</strong> of 2
        </p>
      </div>

      <!-- Step 1: Code Information -->
      <div v-if="step === 1">
        <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2">Code:</label>
        <input v-model="form.code" type="text" placeholder="Enter code to proceed"
          class="input text-sm mb-4 font-quicksand focus:outline-none focus:ring-0 focus:border-slate-400" />

        <div class="flex justify-between">
          <button @click="goBack" class="text-gray-500 hover:text-gray-700">Back</button>
          <button @click="handleStep1Next"
            class="text-gray-700 py-2 px-10 rounded-lg bg-[#E8CEB0] hover:bg-[#d1b38e] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            Next
          </button>
        </div>
      </div>

      <!-- Step 2: Schedule Selection (+ Companions for Duo/Trio) -->
      <div v-else-if="step === 2">
        <!-- Companions (only for Duo/Trio) -->
        <div v-if="showCompanionsField" class="mb-4">
          <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2">
            Companion Name(s)
          </label>
          <input v-model="form.companions" @input="sanitizeCompanionsInput" @blur="sanitizeCompanionsBlur" type="text"
            inputmode="text" maxlength="255"
            class="w-full appearance-none border font-quicksand border-slate-300 rounded-md px-3 py-2 text-md focus:outline-none focus:ring-0 focus:border-slate-400 bg-white"
            placeholder="e.g., Jane Dela Cruz, John Santos" />
          <p class="text-xs text-gray-500 mt-1">
            {{ reformerType === 'Duo' ? 'Enter 1 name.' : 'Enter 2 names, separated by a comma.' }}
          </p>
          <p v-if="companionsError" class="text-sm text-red-500 mt-1">{{ companionsError }}</p>
        </div>

        <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2">Select Date</label>
        <input type="date" v-model="form.date"
          class="w-full border font-quicksand border-slate-300 rounded-md px-3 py-2 text-md focus:outline-none focus:ring-0 focus:border-slate-400 bg-white mb-3" />

        <label class="block text-md font-oswald font-normal tracking-wide text-gray-700 mb-2">Select Time Slot</label>
        <!-- Time Slot Dropdown -->
        <div class="relative">
          <select v-model="form.time" :disabled="!form.date"
            class="w-full appearance-none border font-quicksand border-slate-300 rounded-md px-3 pr-10 py-2 text-md focus:outline-none focus:ring-0 focus:border-slate-400 bg-white mb-3">
            <option disabled value="">-- Choose a time slot --</option>
            <option v-for="(slot, index) in availableTimeSlots" :key="index" :value="slot.time">
              {{ formatTime(slot.time) }} - Coach {{ slot.coach }} (
              {{ slot.slots_available }} slot{{ slot.slots_available == 1 ? '' : 's' }} available )
            </option>
          </select>

          <!-- Custom arrow -->
          <div class="pointer-events-none absolute top-1/2 right-3 transform -translate-y-3/4 text-gray-500">
            <i class="fas fa-chevron-down text-sm"></i>
          </div>
        </div>

        <!-- Checkbox -->
        <div class="mb-6">
          <label class="inline-flex items-center space-x-2 text-sm font-quicksand text-gray-700">
            <input type="checkbox" v-model="form.agreeToTerms"
              class="rounded border-gray-300 text-[#E8CEB0] shadow-sm focus:ring-[#E8CEB0]" />
            <span>
              I agree to the
              <button type="button" @click="showTermsModal = true" class="text-blue-600 hover:underline">
                terms and conditions.
              </button>
            </span>
          </label>
        </div>

        <div class="flex justify-between">
          <button @click="goToStep(1)" class="text-gray-500 hover:text-gray-700">Back</button>
          <button @click="submitBooking" :disabled="disableSubmit"
            class="py-2 px-6 rounded-lg flex items-center gap-2 transition-colors duration-200" :class="disableSubmit
                ? 'bg-gray-300 text-gray-400 cursor-not-allowed'
                : 'bg-[#E8CEB0] hover:bg-[#d1b38e] text-gray-800'
              ">
            <i class="fa-regular fa-square-check"></i>
            Finish Booking
          </button>
        </div>
      </div>
    </div>
  </div>

  <TermsModal v-model="showTermsModal" />
</template>

<script>
import { inject } from 'vue'
import TermsModal from './TermsModal.vue'

// simple debounce
function debounce(fn, delay) {
  let timeout
  return function (...args) {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn.apply(this, args), delay)
  }
}

export default {
  components: {
    TermsModal,
  },
  emits: ['close'],
  data() {
    return {
      step: 1,
      validCodeData: null,
      slotsReady: false,
      form: {
        code: '',
        date: '',
        time: '',
        agreeToTerms: false,
        companions: '',
      },
      showTermsModal: false,
      schedules: [],
      companionsError: '',
      package: '',
      reformerType: '',
      validUntil: '',
    }
  },
  created() {
    this.showToast = inject('showToast')
  },
  computed: {
    stepProgress() {
      return (this.step / 2) * 100
    },
    availableTimeSlots() {
      if (!this.package || !this.form.date) return []

      // still keep validity check
      if (this.validUntil && this.form.date > this.validUntil) return []

      const classType = this.package
      const reformerType = this.reformerType

      return this.schedules.filter((s) => {
        const isSameDate = (s.date || '').slice(0, 10) === this.form.date
        const isSameClass = s.class_type === classType
        const isSameReformer =
          classType === 'Reformer Class' ? s.reformer_type === reformerType : true
        const hasSlots = parseInt(s.slots_available) > 0

        // ✅ NEW: only Active schedules
        const isActive = (s.status || '').toLowerCase() === 'active'

        return isSameDate && isSameClass && isSameReformer && hasSlots && isActive
      })
    },
    isSlotAvailable() {
      if (!this.form.time || !this.form.date || this.availableTimeSlots.length === 0) return false
      const selected = this.availableTimeSlots.find((slot) => slot.time === this.form.time)
      return selected && parseInt(selected.slots_available) > 0
    },
    // NEW: only show companions when Duo/Trio
    showCompanionsField() {
      return (
        this.package === 'Reformer Class' &&
        (this.reformerType === 'Duo' || this.reformerType === 'Trio')
      )
    },
    // NEW: gate the submit button
    disableSubmit() {
      if (!this.form.agreeToTerms || !this.form.date || !this.form.time || !this.isSlotAvailable)
        return true
      if (this.showCompanionsField && !this.validateCompanions(true)) return true
      return false
    },
  },
  mounted() {
    // initialize with today (or keep whatever is already in v-model)
    const initDate = this.form.date || new Date().toISOString().slice(0, 10)
    this.form.date = initDate
    this.fetchSchedulesFor(initDate)
  },
  methods: {
    async handleStep1Next() {
      if (!this.form.code || this.form.code.trim() === '') {
        this.showToast?.('No code provided', 'error')
        return
      }

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/codes/verify_code.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ code: this.form.code.trim() }),
        })
        const result = await res.json()

        if (!result.success) {
          this.showToast?.(result.message || 'Invalid code.', 'error')
          return
        }

        const { code, booking } = result.data
        this.validCodeData = code

        // NEW: store code validity, strip to YYYY-MM-DD
        this.validUntil = code.valid_until ? code.valid_until.slice(0, 10) : ''

        this.package = booking.class_type
        this.reformerType = booking.reformer_type || ''

        this.slotsReady = true
        await this.fetchSchedulesFor(this.form.date || new Date().toISOString().slice(0, 10))

        this.goToStep(2)
      } catch (err) {
        console.error('Code verification failed:', err)
        this.showToast?.('Something went wrong verifying the code.', 'error')
      }
    },
    goToStep(n) {
      this.step = n
    },
    async fetchSchedulesFor(dateStr) {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL

        const d = dateStr ? new Date(dateStr) : new Date()
        const start = new Date(d.getFullYear(), d.getMonth(), 1)
        const end = new Date(d.getFullYear(), d.getMonth() + 1, 0)

        const fmt = (x) =>
          `${x.getFullYear()}-${String(x.getMonth() + 1).padStart(2, '0')}-${String(x.getDate()).padStart(2, '0')}`

        const start_date = fmt(start)
        const end_date = fmt(end)

        const res = await fetch(`${baseURL}/php/schedules/fetch.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ start_date, end_date }),
        })
        const data = await res.json()

        const toISODate = (s) => String(s || '').slice(0, 10) // YYYY-MM-DD
        const toHHMM = (t) => (t && typeof t === 'string' ? t.trim().slice(0, 5) : '') // HH:MM

        this.schedules = Array.isArray(data)
          ? data.map((s) => ({ ...s, date: toISODate(s.date), time: toHHMM(s.time) }))
          : []
      } catch (e) {
        console.error('Failed to fetch schedules:', e)
        this.schedules = []
      }
    },
    formatTime(timeStr) {
      const [hour, minute] = timeStr.split(':')
      const date = new Date()
      date.setHours(+hour)
      date.setMinutes(+minute)
      return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })
    },
    goBack() {
      this.$emit('close')
      this.validCodeData = null
      this.form.code = ''
      this.form.date = ''
      this.form.time = ''
      this.form.agreeToTerms = false
      this.form.companions = ''
      this.companionsError = ''
      this.package = ''
      this.reformerType = ''
      this.step = 1
    },

    // NEW — companions helpers (mirrors your user booking)
    sanitizeCompanionsInput() {
      if (!this.form.companions) {
        this.companionsError = ''
        return
      }
      let s = this.form.companions
        .replace(/[^-A-Za-zÀ-ÖØ-öø-ÿ' .,]/gu, '')
        .replace(/\s+/g, ' ')
        .replace(/\s*,\s*/g, ', ')
        .replace(/,{2,}/g, ',')
        .trim()
      if (s.length > 255) s = s.slice(0, 255).trim()
      this.form.companions = s
      this.companionsError = ''
    },
    sanitizeCompanionsBlur() {
      if (!this.form.companions) return
      this.form.companions = this.form.companions.replace(/^,|,$/g, '').trim()
    },
    validateCompanions(silent = false) {
      if (!this.showCompanionsField) {
        this.companionsError = ''
        return true
      }
      const needed = this.reformerType === 'Duo' ? 1 : 2
      if (!this.form.companions) {
        if (!silent)
          this.companionsError = `Please enter ${needed} companion name${needed === 1 ? '' : 's'}.`
        return false
      }
      const names = this.form.companions
        .split(',')
        .map((n) => n.trim())
        .filter(Boolean)

      if (names.length !== needed) {
        if (!silent)
          this.companionsError = `Please enter exactly ${needed} name${needed === 1 ? '' : 's'}.`
        return false
      }
      const nameOk = /^[A-Za-zÀ-ÖØ-öø-ÿ' .-]{2,60}$/
      for (const n of names) {
        if (!nameOk.test(n)) {
          if (!silent) this.companionsError = `Name "${n}" contains invalid characters or length.`
          return false
        }
      }
      const joined = names.join(', ')
      if (joined.length > 255) {
        if (!silent) this.companionsError = 'Names are too long. Please shorten.'
        return false
      }
      // overwrite normalized
      this.form.companions = joined
      this.companionsError = ''
      return true
    },

    async submitBooking() {
      if (this.showCompanionsField && !this.validateCompanions()) return
      if (!this.form.date || !this.form.time || !this.isSlotAvailable) {
        this.showToast?.('Please select a valid date/time.', 'error')
        return
      }
      if (!this.form.agreeToTerms) {
        this.showToast?.('Please agree to the terms and conditions.', 'error')
        return
      }

      const payload = {
        code: this.form.code.trim(),
        date: this.form.date,
        time: this.form.time,
        // NEW: send companions only when Duo/Trio
        companions: this.showCompanionsField ? this.form.companions : '',
      }

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/bookings/create_booking_code.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify(payload),
        })
        const result = await res.json()
        if (result.success) {
          this.showToast?.(
            'Booking successfully submitted! Please wait for our admin to approve. Thank you!',
            'success',
          )
          this.goBack()
        } else {
          this.showToast?.(result.message || 'Booking failed.', 'error')
        }
      } catch (err) {
        console.error(err)
        this.showToast?.('An error occurred during booking.', 'error')
      }
    },
  },
  watch: {
    'form.date': {
      handler: debounce(async function (newDate) {
        this.form.time = ''

        if (!newDate) return

        // NEW: if user picks a date after code validity, stop here
        if (this.validUntil && newDate > this.validUntil) {
          this.showToast?.(
            `Your code is only valid for bookings on or before ${this.validUntil}. Please choose an earlier date.`,
            'error',
          )
          // Option 1: reset to last valid date
          this.form.date = this.validUntil
          return
        }

        await this.fetchSchedulesFor(newDate)

        if (!this.slotsReady) return

        if (this.availableTimeSlots.length === 0) {
          this.showToast?.('No available time slots for the selected date.', 'error')
        }
      }, 500),
    },
  },
}
</script>

<style scoped>
.input {
  display: block;
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 0.375rem;
}
</style>
