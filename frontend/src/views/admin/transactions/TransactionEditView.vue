<template>
  <AdminLayout
    title="Edit Service Transaction"
    :subtitle="
      transactionNumber
        ? `Update ${transactionNumber} while preserving its history`
        : 'Update transaction details'
    "
    active="transactions"
    v-model:sidebar-open="sidebarOpen"
  >
    <div class="mb-5 flex items-center gap-3">
      <button type="button" class="back-button" @click="goBack">
        <i class="fa-solid fa-arrow-left"></i>
        Transaction
      </button>
      <span class="font-nunito text-[11px] text-[#94A3B8]">/</span>
      <span class="font-nunito text-[11px] text-[#64748B]">Edit</span>
    </div>

    <div
      v-if="loading"
      class="flex min-h-72 items-center justify-center rounded-3xl border border-[#D8E4ED] bg-white"
    >
      <div class="text-center">
        <i class="fa-solid fa-spinner animate-spin text-[24px] text-[#025199]"></i>
        <p class="mt-3 font-nunito text-[13px] text-[#64748B]">Loading transaction...</p>
      </div>
    </div>

    <div
      v-else-if="!transactionData"
      class="rounded-3xl border border-[#F0D2D5] bg-[#FFF5F5] p-6 text-center"
    >
      <p class="font-montserrat text-[14px] font-bold text-[#8E2530]">Transaction unavailable</p>
      <button type="button" class="back-button mt-4" @click="router.push('/admin/transactions')">
        Back to Transactions
      </button>
    </div>

    <TransactionForm
      v-else
      mode="edit"
      :initial-data="transactionData"
      :saving="saving"
      @submit="updateTransaction"
      @cancel="goBack"
    />
  </AdminLayout>
</template>

<script setup>
import { computed, inject, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/layouts/AdminLayout.vue'
import TransactionForm from '@/components/admin/transactions/TransactionForm.vue'
import { transactionService } from '@/services/transactionService'

const route = useRoute()
const router = useRouter()
const showToast = inject('showToast', null)

const sidebarOpen = ref(false)
const loading = ref(true)
const saving = ref(false)
const transactionData = ref(null)

const transactionId = computed(() => Number(route.params.id || 0))
const transactionNumber = computed(
  () => transactionData.value?.transaction?.transaction_number || '',
)

const loadTransaction = async () => {
  loading.value = true

  try {
    transactionData.value = await transactionService.show(transactionId.value)

    if (transactionData.value?.transaction?.transaction_status === 'cancelled') {
      showToast?.('Cancelled transactions are read-only.', 'warning')
      await router.replace(`/admin/transactions/${transactionId.value}`)
    }
  } catch (error) {
    console.error('Unable to load transaction:', error)
    showToast?.(error?.message || 'Unable to load the transaction.', 'error')
    transactionData.value = null
  } finally {
    loading.value = false
  }
}

const goBack = () => router.push(`/admin/transactions/${transactionId.value}`)

const updateTransaction = async (payload) => {
  if (saving.value) return
  saving.value = true

  try {
    const data = await transactionService.update({
      id: transactionId.value,
      ...payload,
    })

    showToast?.(data.message || 'Transaction updated successfully.', 'success')
    await router.replace(`/admin/transactions/${transactionId.value}`)
  } catch (error) {
    console.error('Unable to update transaction:', error)
    showToast?.(error?.message || 'Unable to update the transaction.', 'error', 5500)
  } finally {
    saving.value = false
  }
}

onMounted(loadTransaction)
</script>

<style scoped>
.back-button {
  display: inline-flex;
  min-height: 2.5rem;
  align-items: center;
  gap: 0.5rem;
  border: 1px solid #d8e4ed;
  border-radius: 0.75rem;
  background: #fff;
  padding: 0.55rem 0.8rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #526274;
  transition: all 180ms ease;
}

.back-button:hover {
  border-color: #025199;
  color: #025199;
}
</style>
