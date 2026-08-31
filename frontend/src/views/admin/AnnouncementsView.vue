<template>
  <UserLayout
    title="Announcement Management"
    subtitle="Manage You Glow Babe image announcements"
    active="announcements"
    v-model:sidebarOpen="sidebarOpen"
  >
    <template #default>
      <!-- Toolbar -->
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
          <div
            class="relative rounded-2xl border border-[#eadce2] bg-white shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <i
              class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#b38497] text-xs"
            ></i>
            <input
              v-model="filters.q"
              class="h-11 w-full rounded-2xl bg-transparent pl-9 pr-3 text-sm text-[#5f6670] outline-none md:w-80"
              placeholder="Search announcement title..."
            />
          </div>

          <select
            v-model="filters.is_active"
            @change="fetchAnnouncements(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>

          <button
            @click="resetFilters"
            class="h-11 rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm font-semibold text-[#9a6c80] transition hover:border-[#e97aac] hover:bg-white hover:text-[#e97aac]"
          >
            <i class="fas fa-rotate-left mr-2"></i>Reset Filters
          </button>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="exportCSV"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 text-sm font-semibold text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
          >
            <i class="fas fa-download mr-2"></i>Export
          </button>

          <button
            @click="openCreate"
            class="h-11 rounded-2xl bg-[#e97aac] px-5 text-sm font-semibold text-white transition hover:bg-[#d96799] shadow-[0_12px_24px_rgba(233,122,172,0.20)]"
          >
            <i class="fas fa-plus mr-2"></i>New Announcement
          </button>
        </div>
      </div>

      <!-- Table -->
      <div
        class="mt-5 overflow-hidden rounded-[28px] border border-[#f0e4e9] bg-white shadow-[0_18px_40px_rgba(0,0,0,0.05)]"
      >
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-[#fcf4f7]">
              <tr class="text-left">
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">ID</th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Image
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Announcement
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Status
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Created
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
                  Loading announcements...
                </td>
              </tr>

              <tr
                v-for="row in rows"
                :key="row.id"
                class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
              >
                <td class="px-5 py-4 text-[14px] text-[#7d838d]">#{{ row.id }}</td>

                <td class="px-5 py-4">
                  <div
                    class="h-20 w-16 overflow-hidden rounded-2xl border border-[#f0e4e9] bg-[#fffafb]"
                  >
                    <img
                      :src="getImage(row.image)"
                      :alt="row.title"
                      class="h-full w-full object-cover"
                    />
                  </div>
                </td>

                <td class="px-5 py-4">
                  <div class="text-[15px] font-semibold text-[#5f6670]">
                    {{ row.title }}
                  </div>
                  <div class="mt-1 text-[12px] text-[#9aa0a9]">
                    {{ row.image_original_name || row.image }}
                  </div>
                </td>

                <td class="px-5 py-4">
                  <span
                    class="rounded-full px-3 py-1 text-[11px] uppercase tracking-[0.08em]"
                    :class="
                      Number(row.is_active) === 1
                        ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                        : 'border border-gray-200 bg-gray-50 text-gray-600'
                    "
                  >
                    {{ Number(row.is_active) === 1 ? 'active' : 'inactive' }}
                  </span>
                </td>

                <td class="px-5 py-4 text-[14px] text-[#7d838d]">
                  {{ formatDate(row.created_at) }}
                </td>

                <td class="px-5 py-4">
                  <div class="flex justify-end gap-2">
                    <button
                      @click="openEdit(row)"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-[#eadce2] bg-white px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
                    >
                      Edit
                    </button>

                    <button
                      @click="askDelete(row)"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-rose-600 transition hover:bg-rose-100"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!loading && rows.length === 0">
                <td colspan="6" class="px-5 py-14 text-center text-[#8a8f99]">
                  No announcements found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          class="flex flex-col gap-3 border-t border-[#f3e9ee] px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div class="text-[14px] text-[#8a8f99]">
            Showing <span class="font-semibold text-[#5f6670]">{{ startRow }}</span> to
            <span class="font-semibold text-[#5f6670]">{{ endRow }}</span> of
            <span class="font-semibold text-[#5f6670]">{{ total }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="fetchAnnouncements(page - 1)"
              :disabled="page <= 1"
              class="inline-flex h-10 items-center justify-center rounded-full border border-[#e8dbe1] bg-white px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac] disabled:cursor-not-allowed disabled:opacity-40"
            >
              Prev
            </button>

            <div class="px-2 text-[12px] text-[#8a8f99]">
              Page <span class="font-semibold text-[#5f6670]">{{ page }}</span> /
              <span class="font-semibold text-[#5f6670]">{{ totalPages }}</span>
            </div>

            <button
              @click="fetchAnnouncements(page + 1)"
              :disabled="page >= totalPages"
              class="inline-flex h-10 items-center justify-center rounded-full bg-[#e97aac] px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] disabled:cursor-not-allowed disabled:opacity-40"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Form Modal -->
      <div v-if="formOpen" class="fixed inset-0 z-50 grid place-items-center bg-black/40 px-4">
        <div
          class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-[28px] border border-[#f0e4e9] bg-white p-4 shadow-[0_20px_60px_rgba(0,0,0,0.16)] sm:p-6"
        >
          <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
              <h3 class="font-playfair text-[24px] text-[#5f6670] sm:text-[28px]">
                {{ editingId ? 'Edit Announcement' : 'New Announcement' }}
              </h3>
              <p class="mt-1 text-[13px] text-[#8a8f99]">
                Upload a 1080x1350 px announcement image for consistent display.
              </p>
            </div>

            <button
              @click="closeForm"
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#eadce2] text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-5">
            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Announcement Title <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.title"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
                placeholder="Example: Holiday Sale Promo"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Announcement Image <span class="text-rose-500">*</span>
              </label>

              <div
                class="mx-auto mt-2 max-w-105 overflow-hidden rounded-3xl border border-[#f0d4e0] bg-[#fffafb]"
              >
                <div class="aspect-4/5 w-full bg-[#fff7fa]">
                  <img
                    v-if="imagePreview || form.image"
                    :src="imagePreview || getImage(form.image)"
                    alt="Announcement preview"
                    class="h-full w-full object-cover"
                  />
                  <div v-else class="flex h-full w-full flex-col items-center justify-center">
                    <i class="fas fa-image text-3xl text-[#d3b2c1]"></i>
                    <p class="mt-2 text-sm text-[#9aa0a9]">No image selected</p>
                  </div>
                </div>
              </div>

              <label
                for="announcement-image-input"
                class="mt-3 flex cursor-pointer flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-[#efc7d8] bg-[#fff7fa] px-4 py-4 text-center transition hover:border-[#e97aac] hover:bg-white sm:flex-row sm:items-center sm:justify-between sm:text-left"
              >
                <div class="flex flex-col items-center gap-3 sm:flex-row sm:items-center">
                  <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e97aac] text-white shadow-[0_10px_20px_rgba(233,122,172,0.25)]"
                  >
                    <i class="fas fa-upload"></i>
                  </div>

                  <div class="min-w-0">
                    <div class="text-sm font-semibold leading-5 text-[#5f6670]">
                      {{ imageFile ? 'Change selected image' : 'Tap here to upload image' }}
                    </div>
                    <div class="mt-1 wrap-break-word text-[13px] leading-5 text-[#8a8f99]">
                      {{ selectedImageName || 'No file selected yet' }}
                    </div>
                  </div>
                </div>

                <span
                  class="inline-flex shrink-0 items-center justify-center rounded-full border border-[#e7c9d6] bg-white px-4 py-2 text-[12px] font-semibold uppercase tracking-[0.08em] text-[#a76f86]"
                >
                  Browse File
                </span>
              </label>

              <input
                id="announcement-image-input"
                ref="imageInput"
                type="file"
                accept="image/webp,image/jpeg,image/png"
                class="hidden"
                @change="onImageChange"
              />

              <p class="mt-2 text-[12px] leading-5 text-[#9aa0a9]">
                Required: WEBP, JPG, JPEG, or PNG. Exact size: 1080x1350 px. Recommended below
                500KB. Max file size: 3MB.
              </p>
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Status
              </label>
              <select
                v-model.number="form.is_active"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              >
                <option :value="1">Active</option>
                <option :value="0">Inactive</option>
              </select>
            </div>
          </div>

          <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
            <button
              @click="closeForm"
              class="h-11 rounded-full border border-[#e8dbe1] bg-white px-5 text-sm font-semibold text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              Cancel
            </button>

            <button
              @click="save"
              :disabled="saving"
              class="h-11 rounded-full bg-[#e97aac] px-6 text-sm font-semibold text-white transition hover:bg-[#d96799] disabled:opacity-60"
            >
              {{
                saving
                  ? editingId
                    ? 'Updating...'
                    : 'Saving...'
                  : editingId
                    ? 'Update Announcement'
                    : 'Save Announcement'
              }}
            </button>
          </div>
        </div>
      </div>

      <ConfirmDialog
        :show="deleteOpen"
        title="Delete announcement?"
        message="This will permanently remove the announcement and its image."
        confirmText="Delete"
        confirmIcon="fas fa-trash"
        variant="danger"
        @confirm="confirmDelete"
        @cancel="deleteOpen = false"
      />

      <ConfirmDialog
        :show="showLogoutConfirm"
        title="Log out?"
        message="You will be returned to the login screen."
        confirmText="Log out"
        confirmIcon="fas fa-sign-out-alt"
        variant="danger"
        @confirm="confirmLogout"
        @cancel="cancelLogout"
      />
    </template>
  </UserLayout>
</template>

<script>
import { inject } from 'vue'
import UserLayout from '@/layouts/UserLayout.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'

const REQUIRED_WIDTH = 1080
const REQUIRED_HEIGHT = 1350
const MAX_FILE_SIZE = 3 * 1024 * 1024
const ALLOWED_TYPES = ['image/webp', 'image/jpeg', 'image/png']

export default {
  name: 'AnnouncementsView',
  components: { UserLayout, ConfirmDialog },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  data() {
    return {
      sidebarOpen: false,
      showLogoutConfirm: false,
      loading: false,
      saving: false,
      searchDebounce: null,
      error: '',
      csrf: '',

      page: 1,
      limit: 10,
      total: 0,
      totalPages: 1,
      rows: [],

      filters: {
        q: '',
        is_active: '',
      },

      formOpen: false,
      editingId: null,
      imageFile: null,
      imagePreview: '',

      form: {
        title: '',
        image: '',
        image_original_name: '',
        is_active: 1,
      },

      deleteOpen: false,
      deleting: null,
    }
  },

  watch: {
    'filters.q'(val) {
      clearTimeout(this.searchDebounce)
      this.searchDebounce = setTimeout(() => {
        const q = (val || '').trim()
        if (q.length === 0 || q.length >= 2) this.fetchAnnouncements(1)
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

    selectedImageName() {
      return this.imageFile?.name || ''
    },
  },

  methods: {
    emptyForm() {
      return {
        title: '',
        image: '',
        image_original_name: '',
        is_active: 1,
      }
    },

    getImage(file) {
      if (!file) return ''
      return `/uploads/announcements/${file}`
    },

    formatDate(value) {
      if (!value) return '—'

      const date = new Date(value)
      if (Number.isNaN(date.getTime())) return value

      return date.toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      })
    },

    cleanupImagePreview() {
      if (this.imagePreview) {
        URL.revokeObjectURL(this.imagePreview)
        this.imagePreview = ''
      }
    },

    getImageDimensions(file) {
      return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file)
        const img = new Image()

        img.onload = () => {
          const dimensions = {
            width: img.naturalWidth,
            height: img.naturalHeight,
          }

          URL.revokeObjectURL(url)
          resolve(dimensions)
        }

        img.onerror = () => {
          URL.revokeObjectURL(url)
          reject(new Error('Failed to read image dimensions.'))
        }

        img.src = url
      })
    },

    async validateImageFile(file) {
      if (!file) throw new Error('Announcement image is required.')

      if (!ALLOWED_TYPES.includes(file.type)) {
        throw new Error('Invalid file type. Allowed: WEBP, JPG, JPEG, PNG.')
      }

      if (file.size > MAX_FILE_SIZE) {
        throw new Error('Image must not exceed 3MB.')
      }

      const dimensions = await this.getImageDimensions(file)

      if (dimensions.width !== REQUIRED_WIDTH || dimensions.height !== REQUIRED_HEIGHT) {
        throw new Error(
          `Invalid image dimension. Required size is ${REQUIRED_WIDTH}x${REQUIRED_HEIGHT} px.`,
        )
      }
    },

    async onImageChange(e) {
      const file = e.target.files?.[0] || null

      this.cleanupImagePreview()
      this.imageFile = null

      if (!file) return

      try {
        await this.validateImageFile(file)
        this.imageFile = file
        this.imagePreview = URL.createObjectURL(file)
      } catch (err) {
        if (this.$refs.imageInput) {
          this.$refs.imageInput.value = ''
        }

        this.showToast?.(err.message || 'Invalid image.', 'error')
      }
    },

    resetFilters() {
      this.filters = {
        q: '',
        is_active: '',
      }

      this.fetchAnnouncements(1)
      this.showToast?.('Filters have been cleared.', 'info')
    },

    async ensureCSRF() {
      if (this.csrf) return this.csrf
      const baseURL = import.meta.env.VITE_API_BASE_URL

      try {
        const res = await fetch(`${baseURL}/api/auth/check.php`, {
          credentials: 'include',
        })
        const data = await res.json()

        if (data?.authenticated && data?.csrf) {
          this.csrf = data.csrf
          return this.csrf
        }
      } catch {}

      const res2 = await fetch(`${baseURL}/api/auth/csrf.php`, {
        credentials: 'include',
      })
      const data2 = await res2.json()

      if (!data2.success) {
        throw new Error(data2.message || 'Failed to get CSRF token.')
      }

      this.csrf = data2.csrf
      return this.csrf
    },

    async apiFetch(url, { method = 'GET', headers = {}, body = null } = {}) {
      const finalHeaders = { ...headers }
      const m = method.toUpperCase()

      if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(m)) {
        const token = await this.ensureCSRF()
        finalHeaders['X-CSRF-Token'] = token
      }

      const doFetch = async () =>
        fetch(url, {
          method,
          headers: finalHeaders,
          credentials: 'include',
          body: body == null ? undefined : body,
        })

      let res = await doFetch()

      if (res.status === 419) {
        this.csrf = ''
        const token = await this.ensureCSRF()
        finalHeaders['X-CSRF-Token'] = token
        res = await doFetch()
      }

      return res
    },

    async fetchAnnouncements(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true
      this.error = ''

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
          is_active: this.filters.is_active || '',
          sort: 'created_at',
          dir: 'desc',
        })

        const res = await fetch(`${baseURL}/api/announcements/list.php?${params.toString()}`, {
          credentials: 'include',
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Failed to load announcements.')

        this.rows = data.rows || []
        this.page = Number(data.page || 1)
        this.limit = Number(data.limit || 10)
        this.total = Number(data.total || 0)
        this.totalPages = Number(data.totalPages || 1)
      } catch (e) {
        this.error = e.message || 'Network/server error.'
        this.showToast?.(this.error, 'error')
      } finally {
        this.loading = false
      }
    },

    openCreate() {
      this.error = ''
      this.editingId = null
      this.form = this.emptyForm()
      this.imageFile = null
      this.cleanupImagePreview()

      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = ''
      }

      this.formOpen = true
    },

    openEdit(row) {
      this.error = ''
      this.editingId = Number(row.id)
      this.imageFile = null
      this.cleanupImagePreview()

      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = ''
      }

      this.form = {
        title: row.title || '',
        image: row.image || '',
        image_original_name: row.image_original_name || '',
        is_active: Number(row.is_active ?? 1),
      }

      this.formOpen = true
    },

    closeForm() {
      this.formOpen = false
      this.error = ''
      this.editingId = null
      this.imageFile = null
      this.cleanupImagePreview()
      this.form = this.emptyForm()

      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = ''
      }
    },

    async save() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.saving = true
      this.error = ''

      try {
        if (!this.form.title.trim()) {
          throw new Error('Announcement title is required.')
        }

        if (!this.editingId && !this.imageFile) {
          throw new Error('Announcement image is required.')
        }

        if (this.imageFile) {
          await this.validateImageFile(this.imageFile)
        }

        const fd = new FormData()

        if (this.editingId) {
          fd.append('id', String(this.editingId))
        }

        fd.append('title', this.form.title.trim())
        fd.append('is_active', String(this.form.is_active))

        if (this.imageFile) {
          fd.append('image', this.imageFile)
        }

        const endpoint = this.editingId
          ? `${baseURL}/api/announcements/update.php`
          : `${baseURL}/api/announcements/create.php`

        const res = await this.apiFetch(endpoint, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Save failed.')

        const currentPage = this.editingId ? this.page : 1
        const wasEditing = !!this.editingId

        this.closeForm()
        await this.fetchAnnouncements(currentPage)

        this.showToast?.(
          wasEditing ? 'Announcement updated successfully.' : 'Announcement created successfully.',
          'success',
        )
      } catch (e) {
        this.error = e.message || 'Save error.'
        this.showToast?.(this.error, 'error')
      } finally {
        this.saving = false
      }
    },

    askDelete(row) {
      this.deleting = row
      this.deleteOpen = true
    },

    async confirmDelete() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      if (!this.deleting) return

      try {
        const fd = new FormData()
        fd.append('id', String(this.deleting.id))

        const res = await this.apiFetch(`${baseURL}/api/announcements/delete.php`, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Delete failed.')

        this.deleteOpen = false
        this.deleting = null

        if (this.rows.length === 1 && this.page > 1) {
          await this.fetchAnnouncements(this.page - 1)
        } else {
          await this.fetchAnnouncements(this.page)
        }

        this.showToast?.('Announcement deleted.', 'success')
      } catch (e) {
        this.error = e.message || 'Delete error.'
        this.showToast?.(this.error, 'error')
      }
    },

    exportCSV() {
      const baseURL = import.meta.env.VITE_API_BASE_URL

      const params = new URLSearchParams({
        q: this.filters.q || '',
        is_active: this.filters.is_active || '',
      })

      window.open(`${baseURL}/api/announcements/export.php?${params.toString()}`, '_blank')
      this.showToast?.('Export started...', 'info')
    },

    askLogout() {
      this.showLogoutConfirm = true
    },

    async logout() {
      const baseURL = import.meta.env.VITE_API_BASE_URL

      try {
        await this.ensureCSRF()
        await this.apiFetch(`${baseURL}/api/auth/logout.php`, {
          method: 'POST',
        })
      } finally {
        this.csrf = ''
        this.showToast?.('Logged out.', 'info')
        this.$router.replace('/login')
      }
    },

    async confirmLogout() {
      this.showLogoutConfirm = false
      await this.logout()
    },

    cancelLogout() {
      this.showLogoutConfirm = false
    },
  },

  async mounted() {
    try {
      await this.ensureCSRF()
    } catch {}

    this.fetchAnnouncements(1)
  },

  beforeUnmount() {
    clearTimeout(this.searchDebounce)
    this.cleanupImagePreview()
  },
}
</script>
