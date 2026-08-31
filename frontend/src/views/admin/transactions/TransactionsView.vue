<template>
  <AdminLayout
    title="Service Transactions"
    subtitle="Manage contracts, payments, scheduled services and warranty coverage"
    active="transactions"
    v-model:sidebar-open="sidebarOpen"
  >
    <div class="space-y-6">
      <!-- Header actions -->
      <section
        class="rounded-3xl border border-[#D8E4ED] bg-white p-5 shadow-[0_12px_32px_rgba(2,81,153,0.05)] sm:p-6"
      >
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <p
              class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.18em] text-[#F27C29]"
            >
              Operations Registry
            </p>
            <h2 class="mt-2 font-montserrat text-[22px] font-bold text-[#163A5F] sm:text-[25px]">
              Master Service Transactions
            </h2>
          </div>

          <button type="button" class="primary-action" @click="createTransaction">
            <i class="fa-solid fa-plus text-[11px]"></i>
            New Transaction
          </button>
        </div>
      </section>

      <!-- Summary -->
      <!-- <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article v-for="card in summaryCards" :key="card.label" class="summary-card">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p
                class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.14em] text-[#64748B]"
              >
                {{ card.label }}
              </p>
              <p class="mt-3 font-montserrat text-[27px] font-bold text-[#163A5F]">
                {{ card.value }}
              </p>
              <p class="mt-1 font-nunito text-[11px] text-[#94A3B8]">{{ card.note }}</p>
            </div>
            <span
              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#025199]/8 text-[#025199]"
            >
              <i :class="[card.icon, 'text-[14px]']"></i>
            </span>
          </div>
        </article>
      </section> -->

      <!-- Filters + table -->
      <section
        class="overflow-hidden rounded-3xl border border-[#D8E4ED] bg-white shadow-[0_12px_32px_rgba(2,81,153,0.05)]"
      >
        <div class="border-b border-[#E6EEF4] p-5 sm:p-6">
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
            <div class="relative md:col-span-2 xl:col-span-2">
              <i
                class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 z-10 -translate-y-1/2 text-[12px] text-[#94A3B8]"
              ></i>

              <input
                v-model.trim="filters.q"
                type="search"
                class="filter-input search-filter-input"
                placeholder="Search transaction, customer, contract..."
                @keyup.enter="applyFilters"
              />
            </div>

            <select v-model="filters.status" class="filter-input" @change="applyFilters">
              <option value="">All statuses</option>
              <option value="draft">Draft</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>

            <select v-model="filters.customer_type" class="filter-input" @change="applyFilters">
              <option value="">All customer types</option>
              <option value="residential">Residential</option>
              <option value="commercial">Commercial</option>
              <option value="industrial">Industrial</option>
              <option value="institutional">Institutional</option>
              <option value="government">Government</option>
              <option value="agricultural">Agricultural</option>
            </select>

            <div class="flex gap-2">
              <select
                v-model="filters.payment_status"
                class="filter-input flex-1"
                @change="applyFilters"
              >
                <option value="">All payments</option>
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
              </select>

              <button
                type="button"
                class="reset-button"
                title="Reset filters"
                @click="resetFilters"
              >
                <i class="fa-solid fa-rotate-left"></i>
              </button>
            </div>
          </div>
        </div>

        <div v-if="loading" class="flex min-h-72 items-center justify-center">
          <div class="text-center">
            <i class="fa-solid fa-spinner animate-spin text-[24px] text-[#025199]"></i>
            <p class="mt-3 font-nunito text-[13px] text-[#64748B]">Loading transactions...</p>
          </div>
        </div>

        <div
          v-else-if="transactions.length === 0"
          class="flex min-h-80 items-center justify-center px-6 py-12 text-center"
        >
          <div class="max-w-md">
            <span
              class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#025199]/8 text-[#025199]"
            >
              <i class="fa-regular fa-folder-open text-[22px]"></i>
            </span>
            <h3 class="mt-5 font-montserrat text-[17px] font-bold text-[#163A5F]">
              No transactions found
            </h3>
            <button type="button" class="primary-action mt-5" @click="createTransaction">
              <i class="fa-solid fa-plus"></i>
              New Transaction
            </button>
          </div>
        </div>

        <template v-else>
          <!-- Desktop table -->
          <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-275 border-collapse">
              <thead class="bg-[#F8FBFD]">
                <tr>
                  <th class="table-head">Transaction</th>
                  <th class="table-head">Customer</th>
                  <th class="table-head">Service</th>
                  <th class="table-head text-right">Contract</th>
                  <th class="table-head">Payment</th>
                  <th class="table-head">Next Service</th>
                  <th class="table-head">Warranty</th>
                  <th class="table-head">Status</th>
                  <th class="table-head text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#E6EEF4]">
                <tr v-for="tx in transactions" :key="tx.id" class="transition hover:bg-[#FBFDFE]">
                  <td class="table-cell">
                    <button class="text-left" type="button" @click="viewTransaction(tx.id)">
                      <p
                        class="font-montserrat text-[11px] font-semibold text-[#025199] hover:underline"
                      >
                        {{ tx.transaction_number }}
                      </p>
                      <p class="mt-1 font-nunito text-[10px] text-[#94A3B8]">
                        {{ tx.contract_number || 'No contract no.' }}
                      </p>
                    </button>
                  </td>
                  <td class="table-cell">
                    <p
                      class="max-w-48 truncate font-montserrat text-[11px] font-semibold text-[#163A5F]"
                    >
                      {{ tx.customer_name }}
                    </p>
                    <p class="mt-1 max-w-48 truncate font-nunito text-[10px] text-[#64748B]">
                      {{ customerTypeLabel(tx.customer_type) }} ·
                      {{ tx.site_name || tx.city || 'Service site' }}
                    </p>
                  </td>
                  <td class="table-cell">
                    <p
                      class="max-w-44 truncate font-nunito text-[12px] font-semibold text-[#526274]"
                    >
                      {{ tx.service_type_name }}
                    </p>
                  </td>
                  <td class="table-cell text-right">
                    <p class="font-montserrat text-[11px] font-semibold text-[#163A5F]">
                      {{ formatMoney(tx.contract_amount) }}
                    </p>
                    <p v-if="tx.balance > 0" class="mt-1 font-nunito text-[10px] text-[#B4232D]">
                      {{ formatMoney(tx.balance) }} balance
                    </p>
                  </td>
                  <td class="table-cell">
                    <PaymentStatusBadge :status="tx.payment_status" />
                  </td>
                  <td class="table-cell">
                    <p class="font-nunito text-[11px] font-semibold text-[#526274]">
                      {{ tx.next_service_date ? formatDate(tx.next_service_date) : '—' }}
                    </p>
                  </td>
                  <td class="table-cell">
                    <p class="font-nunito text-[11px] text-[#526274]">
                      {{
                        tx.warranty_included
                          ? tx.warranty_end_date
                            ? formatDate(tx.warranty_end_date)
                            : 'Included'
                          : '—'
                      }}
                    </p>
                  </td>
                  <td class="table-cell">
                    <TransactionStatusBadge :status="tx.transaction_status" />
                  </td>
                  <td class="table-cell text-right">
                    <div class="inline-flex items-center gap-1.5">
                      <button
                        type="button"
                        class="icon-button"
                        title="View"
                        @click="viewTransaction(tx.id)"
                      >
                        <i class="fa-regular fa-eye"></i>
                      </button>
                      <button
                        v-if="tx.transaction_status !== 'cancelled'"
                        type="button"
                        class="icon-button"
                        title="Edit"
                        @click="editTransaction(tx.id)"
                      >
                        <i class="fa-regular fa-pen-to-square"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile cards -->
          <div class="divide-y divide-[#E6EEF4] lg:hidden">
            <article v-for="tx in transactions" :key="tx.id" class="p-5">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <button
                    type="button"
                    class="truncate font-montserrat text-[12px] font-semibold text-[#025199]"
                    @click="viewTransaction(tx.id)"
                  >
                    {{ tx.transaction_number }}
                  </button>
                  <h3 class="mt-2 truncate font-montserrat text-[14px] font-bold text-[#163A5F]">
                    {{ tx.customer_name }}
                  </h3>
                  <p class="mt-1 truncate font-nunito text-[11px] text-[#64748B]">
                    {{ tx.service_type_name }}
                  </p>
                </div>
                <TransactionStatusBadge :status="tx.transaction_status" />
              </div>

              <div class="mt-4 grid grid-cols-2 gap-3 rounded-2xl bg-[#F8FBFD] p-4">
                <div>
                  <p class="mini-label">Contract</p>
                  <p class="mini-value">{{ formatMoney(tx.contract_amount) }}</p>
                </div>
                <div>
                  <p class="mini-label">Payment</p>
                  <PaymentStatusBadge class="mt-1" :status="tx.payment_status" />
                </div>
                <div>
                  <p class="mini-label">Next Service</p>
                  <p class="mini-value">
                    {{ tx.next_service_date ? formatDate(tx.next_service_date) : '—' }}
                  </p>
                </div>
                <div>
                  <p class="mini-label">Warranty</p>
                  <p class="mini-value">
                    {{ tx.warranty_end_date ? formatDate(tx.warranty_end_date) : '—' }}
                  </p>
                </div>
              </div>

              <div class="mt-4 flex gap-2">
                <button type="button" class="mobile-action" @click="viewTransaction(tx.id)">
                  <i class="fa-regular fa-eye"></i> View
                </button>
                <button
                  v-if="tx.transaction_status !== 'cancelled'"
                  type="button"
                  class="mobile-action"
                  @click="editTransaction(tx.id)"
                >
                  <i class="fa-regular fa-pen-to-square"></i> Edit
                </button>
              </div>
            </article>
          </div>

          <!-- Pagination -->
          <div
            class="flex flex-col gap-3 border-t border-[#E6EEF4] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6"
          >
            <p class="font-nunito text-[11px] text-[#64748B]">
              Showing page {{ pagination.page }} of {{ pagination.pages }} ·
              {{ pagination.total }} transaction{{ pagination.total === 1 ? '' : 's' }}
            </p>
            <div class="flex gap-2">
              <button
                type="button"
                class="page-button"
                :disabled="pagination.page <= 1"
                @click="goToPage(pagination.page - 1)"
              >
                <i class="fa-solid fa-chevron-left"></i> Previous
              </button>
              <button
                type="button"
                class="page-button"
                :disabled="pagination.page >= pagination.pages"
                @click="goToPage(pagination.page + 1)"
              >
                Next <i class="fa-solid fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </template>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, inject, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '@/layouts/AdminLayout.vue'
import PaymentStatusBadge from '@/components/admin/transactions/PaymentStatusBadge.vue'
import TransactionStatusBadge from '@/components/admin/transactions/TransactionStatusBadge.vue'
import { transactionService } from '@/services/transactionService'

const router = useRouter()
const showToast = inject('showToast', null)

const sidebarOpen = ref(false)
const loading = ref(false)
const transactions = ref([])
const summary = ref({
  active_transactions: 0,
  outstanding_payments: 0,
  visits_due_next_30_days: 0,
  visits_this_month: 0,
})
const pagination = reactive({ page: 1, limit: 15, total: 0, pages: 1 })
const filters = reactive({
  q: '',
  status: '',
  customer_type: '',
  payment_status: '',
})

const summaryCards = computed(() => [
  {
    label: 'Active Transactions',
    value: summary.value.active_transactions,
    note: 'Current service commitments',
    icon: 'fa-solid fa-file-signature',
  },
  {
    label: 'Outstanding Payments',
    value: summary.value.outstanding_payments,
    note: 'Transactions with remaining balance',
    icon: 'fa-solid fa-peso-sign',
  },
  {
    label: 'Due in 30 Days',
    value: summary.value.visits_due_next_30_days,
    note: 'Upcoming scheduled services',
    icon: 'fa-regular fa-calendar-check',
  },
  {
    label: 'Visits This Month',
    value: summary.value.visits_this_month,
    note: 'Scheduled and completed visits',
    icon: 'fa-solid fa-calendar-days',
  },
])

const loadTransactions = async () => {
  loading.value = true

  try {
    const data = await transactionService.list({
      page: pagination.page,
      limit: pagination.limit,
      ...filters,
    })

    transactions.value = data.transactions || []
    Object.assign(pagination, data.pagination || {})
    summary.value = data.summary || summary.value
  } catch (error) {
    console.error('Unable to load transactions:', error)
    showToast?.(error?.message || 'Unable to load service transactions.', 'error')
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  pagination.page = 1
  loadTransactions()
}

const resetFilters = () => {
  filters.q = ''
  filters.status = ''
  filters.customer_type = ''
  filters.payment_status = ''
  pagination.page = 1
  loadTransactions()
}

const goToPage = (page) => {
  pagination.page = Math.min(Math.max(1, page), pagination.pages)
  loadTransactions()
}

const createTransaction = () => router.push('/admin/transactions/new')
const viewTransaction = (id) => router.push(`/admin/transactions/${id}`)
const editTransaction = (id) => router.push(`/admin/transactions/${id}/edit`)

const customerTypeLabel = (type) => {
  const labels = {
    residential: 'Residential',
    commercial: 'Commercial',
    industrial: 'Industrial',
    institutional: 'Institutional',
    government: 'Government',
    agricultural: 'Agricultural',
  }

  return labels[type] || type || 'Customer'
}

const parseLocalDate = (value) => {
  const [year, month, day] = String(value || '')
    .split('-')
    .map(Number)
  return year && month && day ? new Date(year, month - 1, day, 12, 0, 0) : null
}

const formatDate = (value) => {
  const date = parseLocalDate(value)
  if (!date) return '—'
  return new Intl.DateTimeFormat('en-PH', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  }).format(date)
}

const formatMoney = (value) =>
  new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2,
  }).format(Number(value || 0))

onMounted(loadTransactions)
</script>

<style scoped>
.summary-card {
  border: 1px solid #d8e4ed;
  border-radius: 20px;
  background: #fff;
  padding: 1.125rem;
  box-shadow: 0 10px 28px rgba(2, 81, 153, 0.045);
}

.filter-input {
  min-height: 2.75rem;
  width: 100%;
  border: 1px solid #d8e4ed;
  border-radius: 0.875rem;
  background: #fff;
  padding: 0.7rem 0.875rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.75rem;
  color: #334155;
  outline: none;
  transition:
    border-color 180ms ease,
    box-shadow 180ms ease;
}

.filter-input:focus {
  border-color: #025199;
  box-shadow: 0 0 0 4px rgba(2, 81, 153, 0.07);
}

.primary-action,
.mobile-action,
.page-button,
.reset-button,
.icon-button {
  transition: all 180ms ease;
}

.primary-action {
  display: inline-flex;
  min-height: 2.875rem;
  align-items: center;
  justify-content: center;
  gap: 0.625rem;
  border-radius: 0.875rem;
  background: #025199;
  padding: 0.75rem 1.125rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #fff;
  box-shadow: 0 10px 24px rgba(2, 81, 153, 0.2);
}

.primary-action:hover {
  background: #063f73;
  transform: translateY(-1px);
}

.reset-button,
.icon-button {
  display: inline-flex;
  height: 2.75rem;
  width: 2.75rem;
  align-items: center;
  justify-content: center;
  border: 1px solid #d8e4ed;
  border-radius: 0.875rem;
  background: #fff;
  font-size: 0.75rem;
  color: #64748b;
}

.reset-button:hover,
.icon-button:hover {
  border-color: #025199;
  color: #025199;
}

.table-head {
  padding: 0.875rem 1rem;
  text-align: left;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #64748b;
}

.table-cell {
  padding: 1rem;
  vertical-align: middle;
}

.mini-label {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #94a3b8;
}

.mini-value {
  margin-top: 0.3rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  color: #526274;
}

.mobile-action,
.page-button {
  display: inline-flex;
  min-height: 2.5rem;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  border: 1px solid #d8e4ed;
  border-radius: 0.75rem;
  background: #fff;
  padding: 0.55rem 0.8rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: #526274;
}

.mobile-action:hover,
.page-button:hover:not(:disabled) {
  border-color: #025199;
  color: #025199;
}

.page-button:disabled {
  cursor: not-allowed;
  opacity: 0.4;
}

.search-filter-input {
  padding-left: 2.75rem;
  padding-right: 1rem;
}
</style>
