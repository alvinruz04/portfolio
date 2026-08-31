import { apiGet, apiPost } from '@/services/api'

const queryString = (params = {}) => {
  const search = new URLSearchParams()

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null || value === '') return
    search.set(key, String(value))
  })

  const encoded = search.toString()
  return encoded ? `?${encoded}` : ''
}

export const transactionService = {
  list(params = {}) {
    return apiGet(`/api/transactions/index.php${queryString(params)}`)
  },

  show(id) {
    return apiGet(`/api/transactions/show.php?id=${encodeURIComponent(id)}`)
  },

  referenceData() {
    return apiGet('/api/transactions/reference_data.php')
  },

  customers(params = {}) {
    return apiGet(`/api/customers/index.php${queryString(params)}`)
  },

  create(payload) {
    return apiPost('/api/transactions/create.php', payload)
  },

  update(payload) {
    return apiPost('/api/transactions/update.php', payload)
  },

  cancel(id, reason) {
    return apiPost('/api/transactions/cancel.php', { id, reason })
  },

  recordPayment(payload) {
    return apiPost('/api/payments/create.php', payload)
  },

  voidPayment(paymentId, reason) {
    return apiPost('/api/payments/void.php', {
      payment_id: paymentId,
      reason,
    })
  },

  listServiceVisits(params = {}) {
    return apiGet(`/api/service-visits/index.php${queryString(params)}`)
  },

  rescheduleVisit(payload) {
    return apiPost('/api/service-visits/reschedule.php', payload)
  },

  completeVisit(payload) {
    return apiPost('/api/service-visits/complete.php', payload)
  },
}
