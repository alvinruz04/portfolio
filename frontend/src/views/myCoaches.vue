<template>
  <AdminLayout>
    <div class="flex justify-between items-center mb-2">
      <h1 class="text-3xl font-oswald text-slate-600 font-semibold mb-2">Coach Management</h1>
      <div class="flex space-x-2">
        <button
          @click="exportCoaches"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="mr-2 fas fa-table text-md"></i>
          Generate Excel
        </button>
        <button
          @click="openCreateModal"
          class="text-sm font-medium transition-colors duration-500 bg-white border rounded-md shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white focus:outline-none font-oswald px-4 py-2"
        >
          <i class="fa-solid fa-plus mr-1"></i>
          Add Coach
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

    <!-- Coaches Table -->
    <div class="overflow-x-auto rounded-md shadow-lg border border-slate-200">
      <table class="min-w-full text-left text-sm text-slate-700 font-medium bg-white">
        <thead
          class="bg-slate-700 font-sans font-extrabold text-white uppercase text-xs border-b border-slate-200"
        >
          <tr>
            <th class="p-3">Name</th>
            <th class="p-3">Date Registered</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="coach in paginatedCoaches"
            :key="coach.id"
            class="font-quicksand border-b border-slate-100 hover:bg-slate-100 transition"
          >
            <td class="p-3">{{ coach.name }}</td>
            <td class="p-3">{{ formatDate(coach.created_at) }}</td>
            <td class="p-3 flex gap-2 flex-wrap">
              <button
                @click="openEditModal(coach)"
                class="flex items-center gap-1 text-sm px-3 py-1 bg-white border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition font-sans"
              >
                <i class="fa-solid fa-pen"></i> Edit
              </button>
              <button
                @click="deleteCoach(coach)"
                class="flex items-center gap-1 text-sm px-3 py-1 bg-white border rounded-full shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition font-sans"
              >
                <i class="fa-solid fa-trash-can"></i> Delete
              </button>
            </td>
          </tr>
          <tr v-if="paginatedCoaches.length === 0">
            <td colspan="3" class="text-center py-4 text-slate-400">No matching results</td>
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
        <span class="font-semibold">{{ filteredCoaches.length }}</span> entries
      </div>
      <div class="flex items-center font-oswald gap-2">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-3 py-1.5 border rounded bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <i class="fa-solid fa-angle-left"></i> Prev
        </button>
        <span class="px-3 py-1.5 rounded bg-slate-200 text-slate-800 font-semibold">
          Page {{ currentPage }}
        </span>
        <button
          @click="currentPage++"
          :disabled="endIndex >= filteredCoaches.length"
          class="px-3 py-1.5 border rounded bg-white shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Next <i class="fa-solid fa-angle-right"></i>
        </button>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
    >
      <div class="bg-white p-6 rounded-md border border-slate-300 shadow-2xl w-full max-w-md">
        <h2 class="text-xl font-semibold text-slate-800 mb-2 font-quicksand">
          {{ isEditMode ? 'Edit Coach' : 'Register Coach' }}
        </h2>
        <form @submit.prevent="isEditMode ? updateCoach() : createCoach()" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none focus:border-slate-500 border-slate-300 text-slate-800"
            />
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
          </div>
          <div class="flex justify-end space-x-2 pt-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 rounded bg-white border shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition font-oswald"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded bg-white border shadow text-slate-600 border-slate-200 hover:bg-slate-700 hover:text-white transition font-oswald"
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
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/components/AdminLayout.vue'
import { inject } from 'vue'

export default {
  components: { AdminLayout },
  data() {
    return {
      coaches: [],
      search: '',
      currentPage: 1,
      perPage: 10,
      form: { id: null, name: '' },
      isModalOpen: false,
      isEditMode: false,
      isDeleteModalOpen: false,
      deleteCandidate: null,
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
    filteredCoaches() {
      const term = this.search.toLowerCase()
      return this.coaches.filter((c) => c.name.toLowerCase().includes(term))
    },
    startIndex() {
      return (this.currentPage - 1) * this.perPage
    },
    endIndex() {
      return Math.min(this.startIndex + this.perPage, this.filteredCoaches.length)
    },
    paginatedCoaches() {
      return this.filteredCoaches.slice(this.startIndex, this.endIndex)
    },
  },
  created() {
    this.showToast = inject('showToast')
    this.fetchCoaches()
  },
  methods: {
    async fetchCoaches() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/coaches/fetch.php`, {
          credentials: 'include',
        })
        this.coaches = await res.json()
      } catch {
        this.showToast('Failed to fetch coaches.', 'error')
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      const options = { year: 'numeric', month: 'long', day: 'numeric' }
      return new Date(dateStr).toLocaleDateString('en-US', options)
    },
    openCreateModal() {
      this.form = { id: null, name: '' }
      this.errors = {}
      this.isEditMode = false
      this.isModalOpen = true
    },
    openEditModal(coach) {
      this.form = { ...coach }
      this.errors = {}
      this.isEditMode = true
      this.isModalOpen = true
    },
    closeModal() {
      this.isModalOpen = false
    },
    validateForm() {
      this.errors = {}
      if (!this.form.name.trim()) this.errors.name = 'Name is required.'
      return Object.keys(this.errors).length === 0
    },
    async createCoach() {
      if (!this.validateForm()) return
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/coaches/create.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })
        const data = await res.json()
        if (data.success) {
          this.fetchCoaches()
          this.closeModal()
          this.showToast('Coach created successfully.', 'success')
        } else {
          this.errors = Object.fromEntries((data.errors || []).map((e) => ['name', e]))
          this.showToast(data.message || 'Create failed.', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      }
    },
    async updateCoach() {
      if (!this.validateForm()) return
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/coaches/update.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })
        const data = await res.json()
        if (data.success) {
          this.fetchCoaches()
          this.closeModal()
          this.showToast('Coach updated successfully.', 'success')
        } else {
          this.errors = Object.fromEntries((data.errors || []).map((e) => ['name', e]))
          this.showToast(data.message || 'Update failed.', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      }
    },
    deleteCoach(coach) {
      this.deleteCandidate = coach
      this.isDeleteModalOpen = true
    },
    async confirmDelete() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/coaches/delete.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: this.deleteCandidate.id }),
        })
        const data = await res.json()
        if (data.success) {
          this.fetchCoaches()
          this.showToast('Coach deleted successfully.', 'success')
        } else {
          this.showToast(data.message || 'Delete failed.', 'error')
        }
      } catch {
        this.showToast('Request failed. Please try again.', 'error')
      } finally {
        this.isDeleteModalOpen = false
        this.deleteCandidate = null
      }
    },
    async exportCoaches() {
      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL
        const res = await fetch(`${baseURL}/php/coaches/export.php`, {
          method: 'GET',
          credentials: 'include',
        })
        const blob = await res.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'coaches.csv')
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
