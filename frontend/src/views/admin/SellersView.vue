<template>
  <UserLayout
    title="Seller Management"
    subtitle="Manage authorized seller profiles"
    active="sellers"
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
              placeholder="Search seller code / name..."
            />
          </div>

          <select
            v-model="filters.position"
            @change="fetchSellers(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="">All Position</option>
            <option value="Depot">Depot</option>
            <option value="Reseller">Reseller</option>
            <option value="Regional Distributor">Regional Distributor</option>
            <option value="Provincial Distributor">Provincial Distributor</option>
          </select>

          <select
            v-model="filters.area_of_distribution"
            @change="fetchSellers(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="">All Area</option>
            <option v-for="area in areaOptions" :key="area" :value="area">
              {{ area }}
            </option>
          </select>

          <select
            v-model="filters.product_type"
            @change="fetchSellers(1)"
            class="h-11 rounded-2xl border border-[#eadce2] bg-white px-4 pr-10 text-sm text-[#5f6670] outline-none shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
          >
            <option value="">All Products</option>
            <option value="All Products">All Products</option>
            <option value="Beauty White Caps & Skin">Beauty White Caps & Skin</option>
            <option value="Shepu">Shepu</option>
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
            <i class="fas fa-plus mr-2"></i>New Seller
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
                  Seller
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Position
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Area
                </th>
                <th class="px-5 py-4 text-[11px] uppercase tracking-[0.18em] text-[#a8768d]">
                  Product
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
                <td colspan="7" class="px-5 py-14 text-center text-[#8a8f99]">
                  Loading sellers...
                </td>
              </tr>

              <tr
                v-for="row in rows"
                :key="row.id"
                class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
              >
                <td class="px-5 py-4 text-[14px] text-[#7d838d]">#{{ row.id }}</td>

                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img
                      v-if="row.avatar"
                      :src="getImage(row.avatar)"
                      alt="avatar"
                      class="h-12 w-12 rounded-full border border-[#f0e4e9] bg-[#fffafb] object-cover"
                    />
                    <div
                      v-else
                      class="flex h-12 w-12 items-center justify-center rounded-full border border-[#f0e4e9] bg-[#fffafb]"
                    >
                      <i class="fas fa-user text-[#d3b2c1]"></i>
                    </div>

                    <div>
                      <div class="text-[15px] font-semibold text-[#5f6670]">
                        {{ row.full_name }}
                      </div>
                      <div class="text-[13px] text-[#9aa0a9]">
                        {{ row.seller_code || '—' }}
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-5 py-4 text-[14px] text-[#7d838d]">{{ row.position || '—' }}</td>
                <td class="px-5 py-4 text-[14px] text-[#7d838d]">
                  {{ row.area_of_distribution || '—' }}
                </td>
                <td class="px-5 py-4 text-[14px] text-[#7d838d]">{{ row.product_type || '—' }}</td>
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
                <td colspan="7" class="px-5 py-14 text-center text-[#8a8f99]">No sellers found.</td>
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
              @click="fetchSellers(page - 1)"
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
              @click="fetchSellers(page + 1)"
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
          class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-[28px] border border-[#f0e4e9] bg-white p-4 sm:p-6 shadow-[0_20px_60px_rgba(0,0,0,0.16)]"
        >
          <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
              <h3 class="font-playfair text-[24px] sm:text-[28px] text-[#5f6670]">
                {{ editingId ? 'Edit Seller' : 'New Seller' }}
              </h3>
              <p class="mt-1 text-[13px] text-[#8a8f99]">
                {{
                  editingId
                    ? 'Update seller information and avatar.'
                    : 'Manage seller information and avatar.'
                }}
              </p>
            </div>

            <button
              @click="closeForm"
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#eadce2] text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- MOBILE-OPTIMIZED AVATAR UPLOAD -->
            <div class="md:col-span-2 flex flex-col gap-4 sm:flex-row sm:items-start">
              <div class="mx-auto sm:mx-0">
                <div
                  class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-[#f0d4e0] bg-[#fffafb] sm:h-28 sm:w-28"
                >
                  <img
                    v-if="avatarPreview || form.avatar"
                    :src="avatarPreview || getImage(form.avatar)"
                    alt="preview"
                    class="h-full w-full object-cover"
                  />
                  <i v-else class="fas fa-user text-2xl text-[#d3b2c1]"></i>
                </div>
              </div>

              <div class="min-w-0 flex-1">
                <label
                  class="block text-center text-[11px] uppercase tracking-[0.16em] text-[#b38497] sm:text-left"
                >
                  Avatar
                </label>

                <label
                  for="seller-avatar-input"
                  class="mt-2 flex cursor-pointer flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-[#efc7d8] bg-[#fff7fa] px-4 py-4 text-center transition hover:border-[#e97aac] hover:bg-white sm:flex-row sm:items-center sm:justify-between sm:text-left"
                >
                  <div class="flex flex-col items-center gap-3 sm:flex-row sm:items-center">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e97aac] text-white shadow-[0_10px_20px_rgba(233,122,172,0.25)]"
                    >
                      <i class="fas fa-upload"></i>
                    </div>

                    <div class="min-w-0">
                      <div class="text-sm font-semibold leading-5 text-[#5f6670]">
                        {{
                          avatarFile ? 'Change selected file' : 'Tap here to upload seller avatar'
                        }}
                      </div>
                      <div
                        class="mt-1 wrap-break-word text-[13px] leading-5 text-[#8a8f99] sm:truncate"
                      >
                        {{ selectedAvatarName || 'No file selected yet' }}
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
                  id="seller-avatar-input"
                  type="file"
                  accept="image/png,image/jpeg,image/webp"
                  class="hidden"
                  @change="onFileChange"
                />

                <p class="mt-2 text-center text-[12px] text-[#9aa0a9] sm:text-left">
                  Accepted: JPG, PNG, WEBP. Max 5MB.
                </p>
              </div>
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Seller Code <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.seller_code"
                maxlength="20"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Full Name <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.full_name"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Position <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.position"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              >
                <option value="">Select Position</option>
                <option value="Depot">Depot</option>
                <option value="Regional Distributor">Regional Distributor</option>
                <option value="Reseller">Reseller</option>
                <option value="Provincial Distributor">Provincial Distributor</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Market Scope
              </label>
              <select
                v-model="form.market_scope"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              >
                <option value="Local">Local</option>
                <option value="International">International</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Area of Distribution <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.area_of_distribution"
                maxlength="255"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Contact Number <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.contact_number"
                maxlength="20"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Product Type
              </label>
              <select
                v-model="form.product_type"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              >
                <option value="All Products">All Products</option>
                <option value="Beauty White Caps & Skin">Beauty White Caps & Skin</option>
                <option value="Shepu">Shepu</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Facebook Name
              </label>
              <input
                v-model="form.facebook_name"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Facebook Link
              </label>
              <input
                v-model="form.facebook_link"
                maxlength="500"
                placeholder="https://facebook.com/..."
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Shopee Shop Name
              </label>
              <input
                v-model="form.shopee_shop_name"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Shopee Shop Link
              </label>
              <input
                v-model="form.shopee_shop_link"
                maxlength="500"
                placeholder="https://shopee.ph/..."
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                TikTok Shop Name
              </label>
              <input
                v-model="form.tiktok_shop_name"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                TikTok Shop Link
              </label>
              <input
                v-model="form.tiktok_shop_link"
                maxlength="500"
                placeholder="https://www.tiktok.com/..."
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Lazada Shop Name
              </label>
              <input
                v-model="form.lazada_shop_name"
                maxlength="150"
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div>
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Lazada Shop Link
              </label>
              <input
                v-model="form.lazada_shop_link"
                maxlength="500"
                placeholder="https://www.lazada.com.ph/..."
                class="mt-2 h-11 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-[11px] uppercase tracking-[0.16em] text-[#b38497]">
                Physical Store Address
              </label>
              <textarea
                v-model="form.physical_store_address"
                maxlength="255"
                class="mt-2 min-h-25 w-full rounded-2xl border border-[#eadce2] bg-[#fffafb] px-4 py-3 text-sm text-[#5f6670] outline-none focus:border-[#e97aac]"
              ></textarea>
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
                    ? 'Update Seller'
                    : 'Save Seller'
              }}
            </button>
          </div>
        </div>
      </div>

      <ConfirmDialog
        :show="deleteOpen"
        title="Delete seller?"
        message="This will permanently remove the seller record."
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

export default {
  name: 'SellersView',
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
      areaOptions: [],
      page: 1,
      limit: 10,
      total: 0,
      totalPages: 1,
      rows: [],

      filters: {
        q: '',
        position: '',
        area_of_distribution: '',
        product_type: '',
      },

      formOpen: false,
      editingId: null,
      avatarFile: null,
      avatarPreview: '',
      form: {
        seller_code: '',
        full_name: '',
        position: '',
        area_of_distribution: '',
        market_scope: 'Local',
        facebook_name: '',
        facebook_link: '',
        shopee_shop_name: '',
        shopee_shop_link: '',
        tiktok_shop_name: '',
        tiktok_shop_link: '',
        lazada_shop_name: '',
        lazada_shop_link: '',
        physical_store_address: '',
        contact_number: '',
        product_type: 'All Products',
        avatar: '',
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
        if (q.length === 0 || q.length >= 2) this.fetchSellers(1)
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
    selectedAvatarName() {
      return this.avatarFile?.name || ''
    },
  },

  methods: {
    emptyForm() {
      return {
        seller_code: '',
        full_name: '',
        position: '',
        area_of_distribution: '',
        market_scope: 'Local',
        facebook_name: '',
        facebook_link: '',
        shopee_shop_name: '',
        shopee_shop_link: '',
        tiktok_shop_name: '',
        tiktok_shop_link: '',
        lazada_shop_name: '',
        lazada_shop_link: '',
        physical_store_address: '',
        contact_number: '',
        product_type: 'All Products',
        avatar: '',
        is_active: 1,
      }
    },

    getImage(file) {
      if (!file) return ''
      return `/uploads/sellers/${file}`
    },

    normalizeLink(link) {
      const value = (link || '').trim()
      if (!value) return ''
      if (/^https?:\/\//i.test(value)) return value
      return `https://${value}`
    },

    cleanupAvatarPreview() {
      if (this.avatarPreview) {
        URL.revokeObjectURL(this.avatarPreview)
        this.avatarPreview = ''
      }
    },

    onFileChange(e) {
      const file = e.target.files?.[0] || null
      this.cleanupAvatarPreview()
      this.avatarFile = file
      if (file) {
        this.avatarPreview = URL.createObjectURL(file)
      }
    },

    resetFilters() {
      this.filters = {
        q: '',
        position: '',
        area_of_distribution: '',
        product_type: '',
      }
      this.fetchSellers(1)
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

    async fetchSellers(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true
      this.error = ''

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
          position: this.filters.position || '',
          area_of_distribution: this.filters.area_of_distribution || '',
          product_type: this.filters.product_type || '',
          sort: 'full_name',
          dir: 'asc',
        })

        const res = await fetch(`${baseURL}/api/sellers/list.php?${params.toString()}`, {
          credentials: 'include',
        })
        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Failed to load sellers.')

        this.rows = data.rows || []
        this.areaOptions = data.areas || []
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
      this.avatarFile = null
      this.cleanupAvatarPreview()
      this.formOpen = true
    },

    openEdit(row) {
      this.error = ''
      this.editingId = Number(row.id)
      this.avatarFile = null
      this.cleanupAvatarPreview()

      this.form = {
        seller_code: row.seller_code || '',
        full_name: row.full_name || '',
        position: row.position || '',
        area_of_distribution: row.area_of_distribution || '',
        market_scope: row.market_scope || 'Local',
        facebook_name: row.facebook_name || '',
        facebook_link: row.facebook_link || '',
        shopee_shop_name: row.shopee_shop_name || '',
        shopee_shop_link: row.shopee_shop_link || '',
        tiktok_shop_name: row.tiktok_shop_name || '',
        tiktok_shop_link: row.tiktok_shop_link || '',
        lazada_shop_name: row.lazada_shop_name || '',
        lazada_shop_link: row.lazada_shop_link || '',
        physical_store_address: row.physical_store_address || '',
        contact_number: row.contact_number || '',
        product_type: row.product_type || 'All Products',
        avatar: row.avatar || '',
        is_active: Number(row.is_active ?? 1),
      }

      this.formOpen = true
    },

    closeForm() {
      this.formOpen = false
      this.error = ''
      this.editingId = null
      this.avatarFile = null
      this.cleanupAvatarPreview()
      this.form = this.emptyForm()
    },

    async save() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.saving = true
      this.error = ''

      try {
        if (!this.form.seller_code.trim()) throw new Error('Seller code is required.')
        if (!this.form.full_name.trim()) throw new Error('Full name is required.')
        if (!this.form.position.trim()) throw new Error('Position is required.')
        if (!this.form.area_of_distribution.trim()) {
          throw new Error('Area of distribution is required.')
        }
        if (!this.form.contact_number.trim()) throw new Error('Contact number is required.')

        const fd = new FormData()

        const payload = {
          seller_code: this.form.seller_code.trim(),
          full_name: this.form.full_name.trim(),
          position: this.form.position.trim(),
          area_of_distribution: this.form.area_of_distribution.trim(),
          market_scope: this.form.market_scope.trim(),
          facebook_name: this.form.facebook_name.trim(),
          facebook_link: this.normalizeLink(this.form.facebook_link),
          shopee_shop_name: this.form.shopee_shop_name.trim(),
          shopee_shop_link: this.normalizeLink(this.form.shopee_shop_link),
          tiktok_shop_name: this.form.tiktok_shop_name.trim(),
          tiktok_shop_link: this.normalizeLink(this.form.tiktok_shop_link),
          lazada_shop_name: this.form.lazada_shop_name.trim(),
          lazada_shop_link: this.normalizeLink(this.form.lazada_shop_link),
          physical_store_address: this.form.physical_store_address.trim(),
          contact_number: this.form.contact_number.trim(),
          product_type: this.form.product_type.trim(),
          is_active: String(this.form.is_active),
        }

        if (this.editingId) {
          fd.append('id', String(this.editingId))
        }

        Object.entries(payload).forEach(([key, value]) => {
          fd.append(key, value ?? '')
        })

        if (this.avatarFile) {
          fd.append('avatar', this.avatarFile)
        }

        const endpoint = this.editingId
          ? `${baseURL}/api/sellers/update.php`
          : `${baseURL}/api/sellers/create.php`

        const res = await this.apiFetch(endpoint, {
          method: 'POST',
          body: fd,
        })

        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Save failed.')

        const currentPage = this.editingId ? this.page : 1
        const wasEditing = !!this.editingId

        this.closeForm()
        await this.fetchSellers(currentPage)
        this.showToast?.(
          wasEditing ? 'Seller updated successfully.' : 'Seller created successfully.',
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

        const res = await this.apiFetch(`${baseURL}/api/sellers/delete.php`, {
          method: 'POST',
          body: fd,
        })
        const data = await res.json()
        if (!data.success) throw new Error(data.message || 'Delete failed.')

        this.deleteOpen = false
        this.deleting = null

        if (this.rows.length === 1 && this.page > 1) await this.fetchSellers(this.page - 1)
        else await this.fetchSellers(this.page)

        this.showToast?.('Seller deleted.', 'success')
      } catch (e) {
        this.error = e.message || 'Delete error.'
        this.showToast?.(this.error, 'error')
      }
    },

    exportCSV() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      const params = new URLSearchParams({
        q: this.filters.q || '',
        position: this.filters.position || '',
        area_of_distribution: this.filters.area_of_distribution || '',
        product_type: this.filters.product_type || '',
      })

      window.open(`${baseURL}/api/sellers/export.php?${params.toString()}`, '_blank')
      this.showToast?.('Export started...', 'info')
    },

    askLogout() {
      this.showLogoutConfirm = true
    },

    async logout() {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      try {
        await this.ensureCSRF()
        await this.apiFetch(`${baseURL}/api/auth/logout.php`, { method: 'POST' })
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
    this.fetchSellers(1)
  },

  beforeUnmount() {
    clearTimeout(this.searchDebounce)
    this.cleanupAvatarPreview()
  },
}
</script>
