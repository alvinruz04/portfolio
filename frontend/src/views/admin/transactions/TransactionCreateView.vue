<template>
  <AdminLayout
    title="New Service Transaction"
    subtitle="Create the master record for an approved APESCON service engagement"
    active="transactions"
    v-model:sidebar-open="sidebarOpen"
  >
    <div class="mb-5 flex items-center gap-3">
      <button type="button" class="back-button" @click="goBack">
        <i class="fa-solid fa-arrow-left"></i>
        Transactions
      </button>
      <span class="font-nunito text-[11px] text-[#94A3B8]">/</span>
      <span class="font-nunito text-[11px] text-[#64748B]">New</span>
    </div>

    <TransactionForm mode="create" :saving="saving" @submit="createTransaction" @cancel="goBack" />
  </AdminLayout>
</template>

<script setup>
import { inject, ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '@/layouts/AdminLayout.vue'
import TransactionForm from '@/components/admin/transactions/TransactionForm.vue'
import { transactionService } from '@/services/transactionService'

const router = useRouter()
const showToast = inject('showToast', null)
const sidebarOpen = ref(false)
const saving = ref(false)

const goBack = () => router.push('/admin/transactions')

const createTransaction = async (payload) => {
  if (saving.value) return

  saving.value = true

  try {
    const data = await transactionService.create(payload)
    showToast?.(data.message || 'Transaction created successfully.', 'success')
    await router.replace(`/admin/transactions/${data.transaction_id}`)
  } catch (error) {
    console.error('Unable to create transaction:', error)
    showToast?.(error?.message || 'Unable to create the transaction.', 'error', 5000)
  } finally {
    saving.value = false
  }
}
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
