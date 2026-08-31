<template>
  <AdminLayout>
    <div class="flex justify-between items-center mb-2">
      <!-- Left: Title -->
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold mb-2">Code Management</h1>

      <!-- Right: Buttons grouped side by side -->
      <div class="flex space-x-2">
        <button
          @click="exportcodes"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="mr-2 fas fa-regular fa-table text-md"></i>
          Generate Excel
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
        placeholder="Search by code"
        class="w-full py-2 pl-10 pr-3 text-sm bg-white border rounded-md shadow-sm text-slate-700 placeholder-slate-400 border-slate-200 focus:outline-none focus:border-slate-300 focus:ring-1 focus:ring-slate-200"
      />
    </div>

    <!-- codes Table -->
    <div class="overflow-x-auto rounded-md shadow-lg border border-slate-200">
      <table class="min-w-full text-left text-sm text-slate-700 font-medium bg-white">
        <thead
          class="bg-slate-700 font-sans font-extrabold text-white uppercase text-xs border-b border-slate-200"
        >
          <tr>
            <th class="p-3">Code</th>
            <th class="p-3">
              <span class="inline-flex items-center gap-2 whitespace-nowrap">
                Client's Name
                <button
                  @click="toggleSort('client_name')"
                  class="text-xs text-slate-300 hover:text-white transition"
                  title="Sort by Client's Name"
                >
                  <i
                    :class="[
                      'fas',
                      sortField === 'client_name'
                        ? sortDirection === 'asc'
                          ? 'fa-sort-up'
                          : 'fa-sort-down'
                        : 'fa-sort',
                    ]"
                  ></i>
                </button>
              </span>
            </th>

            <th class="p-3">
              <span class="inline-flex items-center gap-2 whitespace-nowrap">
                Class Type
                <button
                  @click="toggleSort('schedule_class')"
                  class="text-xs text-slate-300 hover:text-white transition"
                  title="Sort by Class Type"
                >
                  <i
                    :class="[
                      'fas',
                      sortField === 'schedule_class'
                        ? sortDirection === 'asc'
                          ? 'fa-sort-up'
                          : 'fa-sort-down'
                        : 'fa-sort',
                    ]"
                  ></i>
                </button>
              </span>
            </th>

            <th class="p-3">Sessions Left</th>
            <!-- Status -->
            <th class="p-3">
              <span class="inline-flex items-center gap-2 whitespace-nowrap">
                Status
                <button
                  @click="toggleSort('status')"
                  class="text-xs text-slate-300 hover:text-white transition"
                  title="Sort by Status"
                >
                  <i
                    :class="[
                      'fas',
                      sortField === 'status'
                        ? sortDirection === 'asc'
                          ? 'fa-sort-up'
                          : 'fa-sort-down'
                        : 'fa-sort',
                    ]"
                  ></i>
                </button>
              </span>
            </th>
            <!-- Expiration Date -->
            <th class="p-3">
              <span class="inline-flex items-center gap-2 whitespace-nowrap">
                Expiration Date
                <button
                  @click="toggleSort('valid_until')"
                  class="text-xs text-slate-300 hover:text-white transition"
                  title="Sort by Expiration Date"
                >
                  <i
                    :class="[
                      'fas',
                      sortField === 'valid_until'
                        ? sortDirection === 'asc'
                          ? 'fa-sort-up'
                          : 'fa-sort-down'
                        : 'fa-sort',
                    ]"
                  ></i>
                </button>
              </span>
            </th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="code in paginatedcodes"
            :key="code.id"
            class="font-quicksand border-b border-slate-100 hover:bg-slate-100 transition"
          >
            <td class="p-3">{{ code.code }}</td>
            <td class="p-3">{{ code.client_name || 'N/A' }}</td>

            <td class="p-3">
              {{ code.schedule_class || '—' }}
              <template v-if="code.schedule_class && code.schedule_class !== 'Yoga Class'">
                <br />
                ( {{ displayReformerLabel(code.schedule_type) }} )
              </template>
            </td>

            <td class="p-3">
              <!-- If sessions_left > 100 or is "Unlimited", show "Unlimited" -->
              {{
                code.sessions_left > 100 || code.sessions_left === 'Unlimited'
                  ? 'Unlimited'
                  : code.sessions_left
              }}
            </td>

            <td
              :class="[
                'p-3',
                (code.status || '').toLowerCase() === 'expired' ? 'text-red-600 font-normal' : '',
              ]"
            >
              {{ code.status }}
            </td>

            <td class="p-3">{{ formatDate(code.valid_until) }}</td>
            <td class="p-3 flex flex-wrap gap-3">
              <button
                @click="openEditModal(code)"
                class="flex items-center gap-1 text-sm px-3 py-1 transition-colors duration-500 bg-white border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-sans"
              >
                <i class="fa-solid fa-pen"></i> Edit
              </button>
            </td>
          </tr>
          <tr v-if="paginatedcodes.length === 0">
            <td colspan="4" class="text-center py-4 text-slate-400">No matching results</td>
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
        <span class="font-semibold">{{ filteredcodes.length }}</span> entries
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
          :disabled="endIndex >= filteredcodes.length"
          class="flex text-md items-center gap-1 px-3 py-1.5 border rounded transition-colors duration-500 bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Next <i class="fa-solid fa-angle-right"></i>
        </button>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-cover bg-center backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-md">
        <h2 class="text-xl font-semibold text-slate-800 mb-2 font-quicksand">Edit Code</h2>
        <p class="text-sm text-slate-500 mb-4">Modify the existing code details below.</p>

        <form @submit.prevent="confirmUpdate" class="space-y-4">
          <!-- Sessions Left -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              Sessions Left
              <span class="text-slate-400 text-xs">( Number or "Unlimited" only )</span>
            </label>

            <!-- Always provide an editable input field -->
            <input
              v-model="form.sessions_left"
              type="text"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="errors.sessions_left" class="text-red-500 text-xs mt-1">
              {{ errors.sessions_left }}
            </p>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
            <select
              v-model="form.status"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            >
              <option value="On-going">On-going</option>
              <option value="Expired">Expired</option>
            </select>
            <p v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</p>
          </div>

          <!-- Expiration Date -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Expiration Date</label>
            <input
              v-model="form.valid_until"
              type="date"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="errors.valid_until" class="text-red-500 text-xs mt-1">
              {{ errors.valid_until }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-2 pt-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 rounded transition-colors duration-500 bg-white border shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald"
            >
              Cancel
            </button>
            <button
              type="button"
              @click="showConfirmationDialog"
              class="px-4 py-2 rounded transition-colors duration-500 bg-white border shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald"
            >
              Update
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation Dialog -->
    <div
      v-if="isConfirmationDialogOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Confirm Update</h2>
        <p class="text-sm text-slate-600 mb-4">
          Are you sure you want to update the code details? This action cannot be undone.
        </p>

        <div class="flex justify-end gap-2">
          <button
            @click="isConfirmationDialogOpen = false"
            class="px-4 py-2 rounded bg-white border border-slate-300 text-slate-600 hover:bg-slate-700 hover:text-white transition font-oswald"
          >
            Cancel
          </button>
          <button
            @click="updateCode"
            class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition font-oswald"
          >
            Confirm
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
      codes: [],
      isModalOpen: false,
      isEditMode: false,
      onlyPasswordReset: false,
      isConfirmationDialogOpen: false,
      sortDirection: 'asc', // asc or desc
      sortField: '',
      search: '',
      currentPage: 1,
      perPage: 10,
      form: {
        sessions_left: 'Unlimited', // Default value to "Unlimited"
        status: 'On-going',
        valid_until: '',
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
    filteredcodes() {
      const term = this.search.toLowerCase()
      let results = this.codes.filter((c) => c.code.toLowerCase().includes(term))

      // Apply sorting if active
      if (this.sortField) {
        results = [...results].sort((a, b) => {
          let valA = a[this.sortField] ?? ''
          let valB = b[this.sortField] ?? ''

          // Convert to lowercase for string comparison
          if (typeof valA === 'string') valA = valA.toLowerCase()
          if (typeof valB === 'string') valB = valB.toLowerCase()

          // Special case for date sorting
          if (this.sortField === 'valid_until') {
            valA = new Date(a.valid_until)
            valB = new Date(b.valid_until)
          }

          if (this.sortDirection === 'asc') {
            return valA > valB ? 1 : valA < valB ? -1 : 0
          } else {
            return valA < valB ? 1 : valA > valB ? -1 : 0
          }
        })
      }

      return results
    },
    startIndex() {
      return (this.currentPage - 1) * this.perPage
    },
    endIndex() {
      return Math.min(this.startIndex + this.perPage, this.filteredcodes.length)
    },
    paginatedcodes() {
      return this.filteredcodes.slice(this.startIndex, this.endIndex)
    },
  },
  created() {
    this.showToast = inject('showToast')
    this.fetchcodes()
  },
  methods: {
    async fetchcodes() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/codes/fetch.php`, {
          credentials: 'include',
        })
        const data = await res.json()
        this.codes = data
      } catch {
        this.showToast('Failed to fetch codes.', 'error')
      }
    },
    openCreateModal() {
      this.resetForm()
      this.isEditMode = false
      this.isModalOpen = true
    },
    openEditModal(code) {
      this.form = {
        id: code.id,
        sessions_left: code.sessions_left, // Updated to sessions_left
        status: code.status,
        valid_until: code.valid_until,
      }
      this.errors = {}
      this.isEditMode = true
      this.isModalOpen = true
    },
    resetPassword(code) {
      this.form = { id: code.id, name: '', email: '', phone: '', password: '' }
      this.errors = {}
      this.onlyPasswordReset = true
      this.isEditMode = true
      this.isModalOpen = true
    },
    showConfirmationDialog() {
      this.isConfirmationDialogOpen = true
    },
    toggleSort(field) {
      if (this.sortField === field) {
        // Toggle direction if same column is clicked again
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      } else {
        // Switch to new field and default to ascending
        this.sortField = field
        this.sortDirection = 'asc'
      }
    },

    async confirmUpdate() {
      try {
        // Ensure that if the value is "Unlimited", we send it as string
        if (this.form.sessions_left === 'Unlimited') {
          this.form.sessions_left = 'Unlimited' // Explicitly set it as string
        }
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const response = await fetch(`${baseURL}/php/codes/update.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })

        const result = await response.json()
        if (result.success) {
          // Success logic here
          this.showToast('Code updated successfully!', 'success')
        } else {
          this.errors = result.errors || { general: 'Update failed' }
        }
      } catch {
        this.showToast('Error updating code.', 'error')
      }
    },
    closeModal() {
      this.isModalOpen = false
    },
    displayReformerLabel(type) {
      if (!type) return '—'
      return type === 'Private' ? 'Solo' : type
    },

    resetForm() {
      this.form = { id: null, name: '', email: '', phone: '', password: '' }
      this.errors = {}
      this.onlyPasswordReset = false
    },
    validateForm(isEdit = false) {
      this.errors = {}

      if (!this.form.name.trim()) this.errors.name = 'Name is required'
      if (!this.form.email.trim()) this.errors.email = 'Email is required'
      else if (!/\S+@\S+\.\S+/.test(this.form.email)) this.errors.email = 'Invalid email format'
      if (!this.form.phone.trim()) this.errors.phone = 'Phone is required'

      // If it's not edit mode → we're adding → password is required
      if (!isEdit && !this.form.password.trim()) {
        this.errors.password = 'Password is required'
      }

      return Object.keys(this.errors).length === 0
    },
    deletecode(id) {
      const code = this.codes.find((c) => c.id === id)
      if (!code) return
      this.deleteCandidate = code
      this.isDeleteModalOpen = true
    },
    async createcode() {
      if (!this.validateForm()) return

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/code/create.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchcodes()
          this.closeModal()
          this.showToast('code created successfully.', 'success')
        } else if (data.errors) {
          this.errors = {}

          data.errors.forEach((err) => {
            if (err.toLowerCase().includes('sessions_left')) {
              this.errors.sessions_left = err
            } else if (err.toLowerCase().includes('status')) {
              this.errors.status = err
            } else if (err.toLowerCase().includes('expiration')) {
              this.errors.valid_until = err
            } else {
              this.errors.general = err
            }
          })
          this.showToast('Validation error.', 'error')
        } else {
          this.showToast(data.message || 'Create failed', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      }
    },
    async updateCode() {
      console.log(this.form)
      try {
        // Perform the code update operation
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/codes/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchcodes()
          this.closeModal()
          this.showToast('Code updated successfully.', 'success')
        } else if (data.errors) {
          this.errors = Object.fromEntries(
            data.errors.map((e) => [e.split(' ')[0].toLowerCase(), e]),
          )
          this.showToast('Validation error.', 'error')
        } else {
          this.showToast(data.message || 'Update failed', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      } finally {
        this.isConfirmationDialogOpen = false // Close the confirmation dialog
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      const options = { year: 'numeric', month: 'long', day: 'numeric' }
      return new Date(dateStr).toLocaleDateString('en-US', options)
    },
    async confirmDelete() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/code/delete.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: this.deleteCandidate.id }),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchcodes()
          this.showToast('code deleted successfully.', 'success')
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

    async exportcodes() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/codes/export.php`, {
          method: 'GET',
          credentials: 'include',
        })

        const blob = await res.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'codes.csv') // Correct filename
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch {
        this.showToast('Failed to export codes.', 'error')
      }
    },
  },
}
</script>
