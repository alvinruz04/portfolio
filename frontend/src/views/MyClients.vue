<template>
  <AdminLayout>
    <div class="flex justify-between items-center mb-2">
      <!-- Left: Title -->
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold mb-2">Client Management</h1>

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
          @click="openCreateModal"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="fa-solid fa-plus mr-1"></i>
          Add Client
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

    <!-- Clients Table -->
    <div class="overflow-x-auto rounded-md shadow-lg border border-slate-200">
      <table class="min-w-full text-left text-sm text-slate-700 font-medium bg-white">
        <thead
          class="bg-slate-700 font-sans font-extrabold text-white uppercase text-xs border-b border-slate-200"
        >
          <tr>
            <th class="p-3">
              <span class="inline-flex items-center gap-2 whitespace-nowrap">
                Name
                <button
                  @click="toggleSort('name')"
                  class="text-xs text-slate-300 hover:text-white transition"
                  title="Sort by Name"
                >
                  <i
                    :class="[
                      'fas',
                      sortField === 'name'
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
                Email
                <button
                  @click="toggleSort('email')"
                  class="text-xs text-slate-300 hover:text-white transition"
                  title="Sort by Email"
                >
                  <i
                    :class="[
                      'fas',
                      sortField === 'email'
                        ? sortDirection === 'asc'
                          ? 'fa-sort-up'
                          : 'fa-sort-down'
                        : 'fa-sort',
                    ]"
                  ></i>
                </button>
              </span>
            </th>
            <th class="p-3">Phone</th>
            <th class="p-3">Status</th>
            <th class="p-3">Notes</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="client in paginatedClients"
            :key="client.id"
            class="font-quicksand border-b border-slate-100 hover:bg-slate-100 transition"
          >
            <td class="p-3">{{ client.name }}</td>
            <td class="p-3">{{ client.email }}</td>
            <td class="p-3">{{ client.phone }}</td>
            <td class="p-3">
              <span
                class="inline-block px-2 py-1 rounded-full text-xs font-semibold"
                :class="
                  client.status === 'Guest'
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-green-100 text-green-800'
                "
              >
                {{ client.status }}
              </span>
            </td>
            <td class="p-3">
              <button
                type="button"
                @click="openNotesModal(client)"
                :disabled="!client.notes"
                class="block w-full text-left max-w-[280px] truncate disabled:cursor-not-allowed disabled:opacity-50 hover:underline decoration-dotted"
                :title="client.notes || 'No notes'"
              >
                {{ client.notes || '—' }}
              </button>
            </td>
            <td class="p-3 flex flex-wrap gap-3">
              <button
                @click="openEditModal(client)"
                class="flex items-center gap-1 text-sm px-3 py-1 transition-colors duration-500 bg-white border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-sans"
              >
                <i class="fa-solid fa-pen"></i> Edit
              </button>
              <button
                @click="deleteClient(client.id)"
                class="flex items-center gap-1 text-sm px-3 py-1 transition-colors duration-500 bg-white border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-sans"
              >
                <i class="fa-solid fa-trash-can"></i> Delete
              </button>
              <button
                @click="resetPassword(client)"
                class="flex items-center gap-1 text-sm px-3 py-1 transition-colors duration-500 bg-white border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-sans"
              >
                <i class="fa-solid fa-key"></i> Reset
              </button>
            </td>
          </tr>
          <tr v-if="paginatedClients.length === 0">
            <td colspan="6" class="text-center py-4 text-slate-400">No matching results</td>
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
        <span class="font-semibold">{{ filteredClients.length }}</span> entries
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
          :disabled="endIndex >= filteredClients.length"
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
        <h2 class="text-xl font-semibold text-slate-800 mb-2 font-quicksand">
          {{
            isEditMode
              ? onlyPasswordReset
                ? 'Reset Password'
                : 'Edit Client'
              : 'Client Registration'
          }}
        </h2>
        <p class="text-sm text-slate-500 mb-4">
          {{
            isEditMode
              ? 'Modify existing client details.'
              : 'Fill out the form to add a new client.'
          }}
        </p>

        <form @submit.prevent="isEditMode ? updateClient() : createClient()" class="space-y-4">
          <template v-if="!onlyPasswordReset">
            <!-- Name -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
              />

              <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
              />
              <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
              />
              <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone }}</p>
            </div>

            <!-- Status -->
            <div v-if="isEditMode && !onlyPasswordReset">
              <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
              >
                <option value="Registered">Registered</option>
                <option value="Guest">Guest</option>
              </select>
              <p v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</p>
            </div>

            <!-- Notes (Edit mode only) -->
            <div v-if="isEditMode && !onlyPasswordReset">
              <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
              <textarea
                v-model="form.notes"
                maxlength="255"
                placeholder="Optional (max 255 characters)"
                class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
                rows="3"
              ></textarea>
              <p v-if="errors.notes" class="text-red-500 text-xs mt-1">{{ errors.notes }}</p>
            </div>
          </template>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              {{
                !isEditMode
                  ? 'Password'
                  : onlyPasswordReset
                    ? 'New Password'
                    : 'New Password (optional)'
              }}
            </label>

            <input
              v-model="form.password"
              type="password"
              :placeholder="
                !isEditMode
                  ? 'Enter password'
                  : onlyPasswordReset
                    ? 'Enter new password'
                    : 'Leave blank to keep current password'
              "
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />

            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
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
              type="submit"
              class="px-4 py-2 rounded transition-colors duration-500 bg-white border shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald"
            >
              {{ isEditMode ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-2 font-oswald">Confirm Deletion</h2>
        <p class="text-sm text-slate-600 mb-4">
          Are you sure you want to delete
          <strong>{{ deleteCandidate?.name }}</strong
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
    <!-- Notes View Modal -->
    <div
      v-if="isNotesModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
      @keydown.escape="closeNotesModal"
    >
      <div
        class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-lg relative"
      >
        <h2 class="text-lg font-semibold text-slate-800 mb-2 font-oswald">Client Notes</h2>
        <p class="text-xs text-slate-500 mb-4">
          {{ notesClient?.name || '—' }}
        </p>

        <div
          class="text-sm text-slate-700 whitespace-pre-wrap break-words max-h-[50vh] overflow-auto border border-slate-200 rounded p-3 bg-slate-50"
        >
          {{ notesContent || 'No notes.' }}
        </div>

        <div class="mt-4 flex justify-end">
          <button
            @click="closeNotesModal"
            class="px-4 py-2 rounded transition-colors duration-500 bg-white border shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald"
          >
            Close
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
      clients: [],
      isModalOpen: false,
      isEditMode: false,
      isNotesModalOpen: false,
      notesContent: '',
      notesClient: null,
      onlyPasswordReset: false,
      search: '',
      currentPage: 1,
      perPage: 10,
      sortField: '', // <- NEW
      sortDirection: 'asc', // <- NEW
      form: {
        id: null,
        name: '',
        email: '',
        phone: '',
        status: '',
        notes: '',
        password: '',
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
    filteredClients() {
      const term = this.search.trim().toLowerCase()

      // filter by name (same as before)
      let results =
        term.length < 1
          ? this.clients
          : this.clients.filter((c) => (c.name ?? '').toString().toLowerCase().includes(term))

      // apply sorting if a field is selected
      if (this.sortField) {
        const field = this.sortField
        const dir = this.sortDirection

        results = [...results].sort((a, b) => {
          let valA = (a?.[field] ?? '').toString().toLowerCase()
          let valB = (b?.[field] ?? '').toString().toLowerCase()

          if (valA > valB) return dir === 'asc' ? 1 : -1
          if (valA < valB) return dir === 'asc' ? -1 : 1
          return 0
        })
      }

      return results
    },
    startIndex() {
      return (this.currentPage - 1) * this.perPage
    },
    endIndex() {
      return Math.min(this.startIndex + this.perPage, this.filteredClients.length)
    },
    paginatedClients() {
      return this.filteredClients.slice(this.startIndex, this.endIndex)
    },
  },
  created() {
    this.showToast = inject('showToast')
    this.fetchClients()
  },
  methods: {
    async fetchClients() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/client/fetch.php`, {
          credentials: 'include',
        })
        const data = await res.json()
        this.clients = data
      } catch {
        this.showToast('Failed to fetch clients.', 'error')
      }
    },
    openCreateModal() {
      this.resetForm()
      this.isEditMode = false
      this.isModalOpen = true
    },
    openEditModal(client) {
      this.errors = {}
      this.onlyPasswordReset = false
      this.form = {
        ...client,
        password: '',
        status: client.status || 'Registered',
        notes: client.notes || '',
      }
      this.isEditMode = true
      this.isModalOpen = true
    },
    openNotesModal(client) {
      if (!client?.notes) return
      this.notesClient = client
      this.notesContent = client.notes
      this.isNotesModalOpen = true
    },

    closeNotesModal() {
      this.isNotesModalOpen = false
      this.notesContent = ''
      this.notesClient = null
    },
    resetPassword(client) {
      this.form = { id: client.id, name: '', email: '', phone: '', password: '' }
      this.errors = {}
      this.onlyPasswordReset = true
      this.isEditMode = true
      this.isModalOpen = true
    },
    closeModal() {
      this.isModalOpen = false
    },
    toggleSort(field) {
      if (this.sortField === field) {
        // same field → flip direction
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      } else {
        // new field → set and default to ascending
        this.sortField = field
        this.sortDirection = 'asc'
      }
      // optional: snap back to page 1 so users always see the start of the sorted list
      this.currentPage = 1
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        email: '',
        phone: '',
        password: '',
        status: 'Registered',
        notes: '',
      }
      this.errors = {}
      this.onlyPasswordReset = false
    },
    validateForm(isEdit = false) {
      this.errors = {}

      if (!this.form.name.trim()) this.errors.name = 'Name is required'
      if (!this.form.email.trim()) this.errors.email = 'Email is required'
      else if (!/\S+@\S+\.\S+/.test(this.form.email)) this.errors.email = 'Invalid email format'
      if (!this.form.phone.trim()) this.errors.phone = 'Phone is required'
      if (!['Registered', 'Guest'].includes(this.form.status)) {
        this.errors.status = 'Status must be Registered or Guest'
      }

      // If it's not edit mode → we're adding → password is required
      if (!isEdit && !this.form.password.trim()) {
        this.errors.password = 'Password is required'
      }

      if (isEdit && this.form.notes && this.form.notes.length > 255) {
        this.errors.notes = 'Notes must be 0–255 characters.'
      }

      return Object.keys(this.errors).length === 0
    },
    deleteClient(id) {
      const client = this.clients.find((c) => c.id === id)
      if (!client) return
      this.deleteCandidate = client
      this.isDeleteModalOpen = true
    },
    async createClient() {
      if (!this.validateForm()) return

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/client/create.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchClients()
          this.closeModal()
          this.showToast('Client created successfully.', 'success')
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
    async updateClient() {
      if (this.onlyPasswordReset) {
        if (!this.form.password || this.form.password.length < 6) {
          this.errors = { password: 'Password must be at least 6 characters' }
          return
        }
      } else {
        if (!this.validateForm(true)) return
      }

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/client/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchClients()
          this.closeModal()
          this.showToast('Client updated successfully.', 'success')
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
      }
    },
    async confirmDelete() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/client/delete.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: this.deleteCandidate.id }),
        })

        const data = await res.json()

        if (data.success) {
          this.fetchClients()
          this.showToast('Client deleted successfully.', 'success')
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

    async exportClients() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/client/export.php`, {
          method: 'GET',
          credentials: 'include',
        })

        const blob = await res.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'clients.csv')
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
