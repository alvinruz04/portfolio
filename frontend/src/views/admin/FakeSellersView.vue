<template>
  <UserLayout
    title="Fake Seller Management"
    subtitle="Manage reported fake seller shops"
    active="fake-sellers"
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
              placeholder="Search shop name / link..."
            />
          </div>

          <select
            v-model="filters.platform"
            @change="fetchFakeSellers(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="">All Platform</option>
            <option v-for="item in platformOptions" :key="item" :value="item">
              {{ item }}
            </option>
          </select>

          <select
            v-model="filters.product_type"
            @change="fetchFakeSellers(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="">All Products</option>
            <option v-for="item in productTypeOptions" :key="item" :value="item">
              {{ item }}
            </option>
          </select>

          <select
            v-model="filters.is_active"
            @change="fetchFakeSellers(1)"
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
            <i class="fas fa-plus mr-2"></i>New Fake Seller
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
                  Shop
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Platform
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Product Type
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Status
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
                  Loading fake sellers...
                </td>
              </tr>

              <tr
                v-for="row in rows"
                :key="row.id"
                class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
              >
                <td class="px-5 py-4 text-[14px] text-[#7d838d]">#{{ row.id }}</td>

                <td class="px-5 py-4">
                  <div class="min-w-55">
                    <div class="text-[15px] font-semibold text-[#5f6670]">
                      {{ row.shop_name }}
                    </div>
                    <a
                      v-if="row.shop_link"
                      :href="row.shop_link"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="mt-1 inline-block break-all text-[13px] text-[#e97aac] hover:underline"
                    >
                      {{ row.shop_link }}
                    </a>
                    <div v-else class="mt-1 text-[13px] text-[#9aa0a9]">—</div>
                  </div>
                </td>

                <td class="px-5 py-4 text-[14px] text-[#7d838d]">
                  {{ row.platform || '—' }}
                </td>

                <td class="px-5 py-4 text-[14px] text-[#7d838d]">
                  {{ row.product_type || '—' }}
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
                  No fake sellers found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

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
              @click="fetchFakeSellers(page - 1)"
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
              @click="fetchFakeSellers(page + 1)"
              :disabled="page >= totalPages"
              class="inline-flex h-10 items-center justify-center rounded-full bg-[#e97aac] px-4 text-[12px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] disabled:cursor-not-allowed disabled:opacity-40"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Form modal -->
      <div v-if="formOpen" class="fixed inset-0 z-50 grid place-items-center bg-black/40 px-4">
        <div
          class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-[28px] border border-[#f0e4e9] bg-white p-4 sm:p-6 shadow-[0_20px_60px_rgba(0,0,0,0.16)]"
        >
          <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
              <h3 class="font-playfair text-[24px] sm:text-[28px] text-[#5f6670]">
                {{ editingId ? 'Edit Fake Seller' : 'New Fake Seller' }}
              </h3>
              <p class="mt-1 text-[13px] text-[#8a8f99]">Manage fake seller shop information.</p>
            </div>

            <button
              @click="closeForm"
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#eadce2] text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-4">
            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Shop Name <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.shop_name"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Shop Link <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.shop_link"
                maxlength="500"
                placeholder="https://..."
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Platform <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.platform"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              >
                <option value="">Select Platform</option>
                <option v-for="item in platformOptions" :key="item" :value="item">
                  {{ item }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Product Type <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.product_type"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              >
                <option value="">Select Product Type</option>
                <option v-for="item in productTypeOptions" :key="item" :value="item">
                  {{ item }}
                </option>
              </select>
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
                    ? 'Update Fake Seller'
                    : 'Save Fake Seller'
              }}
            </button>
          </div>
        </div>
      </div>

      <ConfirmDialog
        :show="deleteOpen"
        title="Delete fake seller?"
        message="This will permanently remove the fake seller record."
        confirmText="Delete"
        confirmIcon="fas fa-trash"
        variant="danger"
        @confirm="confirmDelete"
        @cancel="deleteOpen = false"
      />
    </template>
  </UserLayout>
</template>

<script>
import { inject } from 'vue'
import UserLayout from '@/layouts/UserLayout.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'

const PLATFORM_OPTIONS = ['TikTok', 'Shopee', 'Lazada']
const PRODUCT_TYPE_OPTIONS = [
  'Beauty White Capsule',
  'Beauty White Soap',
  'Beauty White Sunscreen',
  'Shepu Appu',
  'All Products',
]

export default {
  name: 'FakeSellersView',
  components: { UserLayout, ConfirmDialog },

  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },

  data() {
    return {
      sidebarOpen: false,
      loading: false,
      saving: false,
      searchDebounce: null,
      csrf: '',
      page: 1,
      limit: 10,
      total: 0,
      totalPages: 1,
      rows: [],

      platformOptions: PLATFORM_OPTIONS,
      productTypeOptions: PRODUCT_TYPE_OPTIONS,

      filters: {
        q: '',
        platform: '',
        product_type: '',
        is_active: '',
      },

      formOpen: false,
      editingId: null,
      form: {
        shop_name: '',
        shop_link: '',
        platform: '',
        product_type: '',
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
        if (q.length === 0 || q.length >= 2) this.fetchFakeSellers(1)
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
    emptyForm() {
      return {
        shop_name: '',
        shop_link: '',
        platform: '',
        product_type: '',
        is_active: 1,
      }
    },

    normalizeLink(link) {
      const value = (link || '').trim()
      if (!value) return ''
      if (/^https?:\/\//i.test(value)) return value
      return `https://${value}`
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
      if (!data2.success) throw new Error(data2.message || 'Failed to get CSRF token.')
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

    async fetchFakeSellers(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
          platform: this.filters.platform || '',
          product_type: this.filters.product_type || '',
          is_active: this.filters.is_active || '',
          sort: 'shop_name',
          dir: 'asc',
        })

        const res = await fetch(`${baseURL}/api/fake_sellers/list.php?${params.toString()}`, {
          credentials: 'include',
        })
        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Failed to load fake sellers.')

        this.rows = data.rows || []
        this.page = Number(data.page || 1)
        this.limit = Number(data.limit || 10)
        this.total = Number(data.total || 0)
        this.totalPages = Number(data.totalPages || 1)
      } catch (e) {
        this.showToast?.(e.message || 'Failed to load fake sellers.', 'error')
      } finally {
        this.loading = false
      }
    },

    resetFilters() {
      this.filters = {
        q: '',
        platform: '',
        product_type: '',
        is_active: '',
      }
      this.fetchFakeSellers(1)
      this.showToast?.('Filters have been cleared.', 'info')
    },

    openCreate() {
      this.editingId = null
      this.form = this.emptyForm()
      this.formOpen = true
    },

    openEdit(row) {
      this.editingId = Number(row.id)
      this.form = {
        shop_name: row.shop_name || '',
        shop_link: row.shop_link || '',
        platform: row.platform || '',
        product_type: row.product_type || '',
        is_active: Number(row.is_active ?? 1),
      }
      this.formOpen = true
    },

    closeForm() {
      this.formOpen = false
      this.editingId = null
      this.form = this.emptyForm()
    },

    async save() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.saving = true

      try {
        if (!this.form.shop_name.trim()) throw new Error('Shop name is required.')
        if (!this.form.shop_link.trim()) throw new Error('Shop link is required.')
        if (!this.form.platform.trim()) throw new Error('Platform is required.')
        if (!this.form.product_type.trim()) throw new Error('Product type is required.')

        const fd = new FormData()

        if (this.editingId) {
          fd.append('id', String(this.editingId))
        }

        fd.append('shop_name', this.form.shop_name.trim())
        fd.append('shop_link', this.normalizeLink(this.form.shop_link))
        fd.append('platform', this.form.platform.trim())
        fd.append('product_type', this.form.product_type.trim())
        fd.append('is_active', String(this.form.is_active))

        const endpoint = this.editingId
          ? `${baseURL}/api/fake_sellers/update.php`
          : `${baseURL}/api/fake_sellers/create.php`

        const res = await this.apiFetch(endpoint, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Save failed.')

        const currentPage = this.editingId ? this.page : 1
        const wasEditing = !!this.editingId

        this.closeForm()
        await this.fetchFakeSellers(currentPage)

        this.showToast?.(
          wasEditing ? 'Fake seller updated successfully.' : 'Fake seller created successfully.',
          'success',
        )
      } catch (e) {
        this.showToast?.(e.message || 'Save failed.', 'error')
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

        const res = await this.apiFetch(`${baseURL}/api/fake_sellers/delete.php`, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Delete failed.')

        this.deleteOpen = false
        this.deleting = null

        if (this.rows.length === 1 && this.page > 1) await this.fetchFakeSellers(this.page - 1)
        else await this.fetchFakeSellers(this.page)

        this.showToast?.('Fake seller deleted.', 'success')
      } catch (e) {
        this.showToast?.(e.message || 'Delete failed.', 'error')
      }
    },

    exportCSV() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      const params = new URLSearchParams({
        q: this.filters.q || '',
        platform: this.filters.platform || '',
        product_type: this.filters.product_type || '',
        is_active: this.filters.is_active || '',
      })

      window.open(`${baseURL}/api/fake_sellers/export.php?${params.toString()}`, '_blank')
      this.showToast?.('Export started...', 'info')
    },
  },

  async mounted() {
    try {
      await this.ensureCSRF()
    } catch {}
    this.fetchFakeSellers(1)
  },

  beforeUnmount() {
    clearTimeout(this.searchDebounce)
  },
}
</script>
