import { apiGet, apiPost } from '@/services/api'

export const accountService = {
  getAccount() {
    return apiGet('/api/auth/account.php')
  },

  updateProfile(name) {
    return apiPost('/api/auth/update_profile.php', {
      name,
    })
  },

  changePassword(payload) {
    return apiPost('/api/auth/change_password.php', payload)
  },
}
