<template>
  <AdminLayout
    title="Account Settings"
    subtitle="Manage your administrator profile and account security"
    active="settings"
    v-model:sidebar-open="sidebarOpen"
    v-slot="{ refreshUser }"
  >
    <div class="space-y-6">
      <!-- Page introduction -->
      <section
        class="relative overflow-hidden rounded-[26px] border border-[#D8E4ED] bg-white p-5 shadow-[0_12px_32px_rgba(2,81,153,0.05)] sm:p-6"
      >
        <div
          class="pointer-events-none absolute -right-16 -top-20 h-52 w-52 rounded-full bg-[#025199]/6 blur-3xl"
        ></div>

        <div
          class="pointer-events-none absolute -bottom-20 left-1/3 h-44 w-44 rounded-full bg-[#F27C29]/6 blur-3xl"
        ></div>

        <div
          class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
        >
          <div class="flex min-w-0 items-start gap-4">
            <div
              class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[17px] bg-linear-to-br from-[#062B4C] to-[#025199] text-white shadow-[0_12px_28px_rgba(2,81,153,0.20)]"
            >
              <i class="fa-solid fa-user-shield text-[18px]"></i>
            </div>

            <div class="min-w-0">
              <p
                class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.18em] text-[#F27C29]"
              >
                Administrator Account
              </p>

              <h2 class="mt-2 font-montserrat text-[21px] font-bold text-[#163A5F] sm:text-[24px]">
                Personal account and security
              </h2>
            </div>
          </div>

          <div
            v-if="account"
            class="flex shrink-0 items-center gap-2 rounded-full border border-[#D8E4ED] bg-[#F8FBFD] px-3.5 py-2"
          >
            <span
              class="h-2 w-2 rounded-full"
              :class="account.status === 'active' ? 'bg-[#2F8A5B]' : 'bg-[#D51C26]'"
            ></span>

            <span
              class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.13em] text-[#64748B]"
            >
              {{ account.status === 'active' ? 'Active Account' : 'Inactive Account' }}
            </span>
          </div>
        </div>
      </section>

      <!-- Loading -->
      <section
        v-if="loading"
        class="flex min-h-72 items-center justify-center rounded-3xl border border-[#D8E4ED] bg-white"
      >
        <div class="text-center">
          <i class="fa-solid fa-spinner animate-spin text-[25px] text-[#025199]"></i>

          <p class="mt-3 font-nunito text-[13px] text-[#64748B]">Loading account information...</p>
        </div>
      </section>

      <!-- Load error -->
      <section
        v-else-if="!account"
        class="rounded-3xl border border-[#F0D2D5] bg-[#FFF5F5] p-7 text-center"
      >
        <span
          class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#D51C26]/8 text-[#D51C26]"
        >
          <i class="fa-solid fa-circle-exclamation"></i>
        </span>

        <h3 class="mt-4 font-montserrat text-[16px] font-bold text-[#8E2530]">
          Unable to load account
        </h3>

        <p class="mt-2 font-nunito text-[13px] text-[#64748B]">
          {{ loadError || 'The administrator account information could not be loaded.' }}
        </p>

        <button type="button" class="secondary-button mt-5" @click="loadAccount">
          <i class="fa-solid fa-rotate-right"></i>
          Try Again
        </button>
      </section>

      <template v-else>
        <!-- Profile + restricted information -->
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
          <!-- Editable profile -->
          <article
            class="rounded-3xl border border-[#D8E4ED] bg-white p-5 shadow-[0_12px_32px_rgba(2,81,153,0.05)] sm:p-6 xl:col-span-7"
          >
            <div class="flex items-start gap-4">
              <span class="card-icon">
                <i class="fa-regular fa-id-card"></i>
              </span>

              <div>
                <h3 class="font-montserrat text-[15px] font-bold text-[#163A5F]">
                  Profile Information
                </h3>

                <p class="mt-1.5 font-nunito text-[12px] leading-5 text-[#64748B]">
                  This name is displayed in the APESCON sidebar, top navigation and administrative
                  records associated with your account.
                </p>
              </div>
            </div>

            <form class="mt-6" @submit.prevent="saveProfile(refreshUser)">
              <label class="form-label" for="account-name">
                Display Name
                <span class="text-[#D51C26]">*</span>
              </label>

              <div class="relative mt-2">
                <i
                  class="fa-regular fa-user pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[12px] text-[#94A3B8]"
                ></i>

                <input
                  id="account-name"
                  v-model="profileName"
                  type="text"
                  maxlength="150"
                  autocomplete="name"
                  class="form-input profile-name-input"
                  placeholder="Administrator name"
                  :disabled="savingProfile"
                />
              </div>

              <div v-if="profileError" class="error-panel mt-4">
                <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                <span>{{ profileError }}</span>
              </div>

              <div
                class="mt-5 flex flex-col gap-3 border-t border-[#E6EEF4] pt-5 sm:flex-row sm:items-center sm:justify-between"
              >
                <p class="font-nunito text-[11px] leading-5 text-[#94A3B8]">
                  Only your display name can be changed from this page.
                </p>

                <button
                  type="submit"
                  class="primary-button"
                  :disabled="savingProfile || !profileDirty"
                >
                  <i
                    :class="
                      savingProfile
                        ? 'fa-solid fa-spinner animate-spin'
                        : 'fa-regular fa-floppy-disk'
                    "
                  ></i>

                  {{ savingProfile ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </article>

          <!-- Restricted fields -->
          <article
            class="overflow-hidden rounded-3xl border border-[#D8E4ED] bg-white shadow-[0_12px_32px_rgba(2,81,153,0.05)] xl:col-span-5"
          >
            <div class="border-b border-[#E6EEF4] p-5 sm:p-6">
              <div class="flex items-start gap-4">
                <span class="card-icon card-icon-orange">
                  <i class="fa-solid fa-lock"></i>
                </span>

                <div>
                  <h3 class="font-montserrat text-[15px] font-bold text-[#163A5F]">
                    Account Access
                  </h3>

                  <p class="mt-1.5 font-nunito text-[12px] leading-5 text-[#64748B]">
                    These values identify your administrator account and cannot be modified here.
                  </p>
                </div>
              </div>
            </div>

            <dl class="divide-y divide-[#E6EEF4]">
              <div class="readonly-row">
                <dt class="readonly-label">
                  <i class="fa-solid fa-hashtag"></i>
                  User ID
                </dt>

                <dd class="readonly-value">#{{ account.id }}</dd>
              </div>

              <div class="readonly-row">
                <dt class="readonly-label">
                  <i class="fa-regular fa-envelope"></i>
                  Email Address
                </dt>

                <dd class="readonly-value break-all">
                  {{ account.email_address }}
                </dd>
              </div>

              <div class="readonly-row">
                <dt class="readonly-label">
                  <i class="fa-solid fa-user-shield"></i>
                  Role
                </dt>

                <dd class="readonly-value">
                  {{ roleLabel }}
                </dd>
              </div>

              <div class="readonly-row">
                <dt class="readonly-label">
                  <i class="fa-solid fa-circle-check"></i>
                  Status
                </dt>

                <dd>
                  <span
                    class="inline-flex rounded-full px-2.5 py-1 font-montserrat text-[8px] font-semibold uppercase tracking-[0.12em]"
                    :class="
                      account.status === 'active'
                        ? 'bg-[#EAF7F0] text-[#2F7650]'
                        : 'bg-[#FFF0F1] text-[#B4232D]'
                    "
                  >
                    {{ account.status }}
                  </span>
                </dd>
              </div>
            </dl>
          </article>
        </section>

        <!-- Security -->
        <section
          class="rounded-3xl border border-[#D8E4ED] bg-white shadow-[0_12px_32px_rgba(2,81,153,0.05)]"
        >
          <div
            class="flex flex-col gap-5 border-b border-[#E6EEF4] p-5 sm:p-6 lg:flex-row lg:items-start lg:justify-between"
          >
            <div class="flex items-start gap-4">
              <span class="card-icon">
                <i class="fa-solid fa-key"></i>
              </span>

              <div>
                <h3 class="font-montserrat text-[15px] font-bold text-[#163A5F]">
                  Change Password
                </h3>

                <p class="mt-1.5 max-w-2xl font-nunito text-[12px] leading-5 text-[#64748B]">
                  Enter your current password before creating a new password for this administrator
                  account.
                </p>
              </div>
            </div>

            <div class="grid shrink-0 grid-cols-1 gap-2 sm:grid-cols-2 lg:min-w-84">
              <div class="activity-box">
                <p class="activity-label">Last Sign In</p>
                <p class="activity-value">
                  {{ formatDateTime(account.last_login_at) }}
                </p>
              </div>

              <div class="activity-box">
                <p class="activity-label">Password Changed</p>
                <p class="activity-value">
                  {{ formatDateTime(account.password_changed_at) }}
                </p>
              </div>
            </div>
          </div>

          <form class="p-5 sm:p-6" @submit.prevent="preparePasswordChange">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
              <!-- Current -->
              <div>
                <label class="form-label" for="current-password">
                  Current Password
                  <span class="text-[#D51C26]">*</span>
                </label>

                <div class="relative mt-2">
                  <input
                    id="current-password"
                    v-model="passwordForm.current_password"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    autocomplete="current-password"
                    class="form-input password-input"
                    placeholder="Enter current password"
                    :disabled="savingPassword"
                  />

                  <button
                    type="button"
                    class="password-toggle"
                    :aria-label="
                      showCurrentPassword ? 'Hide current password' : 'Show current password'
                    "
                    @click="showCurrentPassword = !showCurrentPassword"
                  >
                    <i
                      :class="showCurrentPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                    ></i>
                  </button>
                </div>
              </div>

              <!-- New -->
              <div>
                <label class="form-label" for="new-password">
                  New Password
                  <span class="text-[#D51C26]">*</span>
                </label>

                <div class="relative mt-2">
                  <input
                    id="new-password"
                    v-model="passwordForm.new_password"
                    :type="showNewPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    minlength="12"
                    maxlength="128"
                    class="form-input password-input"
                    placeholder="At least 12 characters"
                    :disabled="savingPassword"
                  />

                  <button
                    type="button"
                    class="password-toggle"
                    :aria-label="showNewPassword ? 'Hide new password' : 'Show new password'"
                    @click="showNewPassword = !showNewPassword"
                  >
                    <i
                      :class="showNewPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                    ></i>
                  </button>
                </div>
              </div>

              <!-- Confirm -->
              <div>
                <label class="form-label" for="confirm-password">
                  Confirm New Password
                  <span class="text-[#D51C26]">*</span>
                </label>

                <div class="relative mt-2">
                  <input
                    id="confirm-password"
                    v-model="passwordForm.confirm_password"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    minlength="12"
                    maxlength="128"
                    class="form-input password-input"
                    placeholder="Repeat new password"
                    :disabled="savingPassword"
                  />

                  <button
                    type="button"
                    class="password-toggle"
                    :aria-label="
                      showConfirmPassword
                        ? 'Hide confirmation password'
                        : 'Show confirmation password'
                    "
                    @click="showConfirmPassword = !showConfirmPassword"
                  >
                    <i
                      :class="showConfirmPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                    ></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Requirement -->
            <div
              class="mt-5 flex items-start gap-3 rounded-2xl border border-[#D8E4ED] bg-[#F8FBFD] px-4 py-3"
            >
              <span
                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#025199]/8 text-[#025199]"
              >
                <i class="fa-solid fa-shield-halved text-[10px]"></i>
              </span>

              <div>
                <p class="font-montserrat text-[10px] font-semibold text-[#163A5F]">
                  Password Requirement
                </p>

                <p class="mt-1 font-nunito text-[11px] leading-5 text-[#64748B]">
                  Use 12 to 128 characters with at least one uppercase letter, one lowercase letter,
                  one number and one special character. Avoid common words, predictable sequences
                  and the company name.
                </p>
              </div>
            </div>

            <div v-if="passwordError" class="error-panel mt-4">
              <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
              <span>{{ passwordError }}</span>
            </div>

            <div
              class="mt-5 flex flex-col gap-3 border-t border-[#E6EEF4] pt-5 sm:flex-row sm:items-center sm:justify-between"
            >
              <p class="font-nunito text-[11px] leading-5 text-[#94A3B8]">
                Changing your password does not change your email address or account role.
              </p>

              <button type="submit" class="primary-button" :disabled="savingPassword">
                <i class="fa-solid fa-key"></i>
                Change Password
              </button>
            </div>
          </form>
        </section>
      </template>
    </div>

    <!-- Password confirmation -->
    <ConfirmDialog
      :show="showPasswordConfirm"
      :loading="savingPassword"
      title="Change account password?"
      message="Your new password will become effective immediately for future APESCON sign-ins."
      confirm-text="Change Password"
      loading-text="Updating Password..."
      confirm-icon="fa-solid fa-key"
      variant="primary"
      @confirm="changePassword"
      @cancel="cancelPasswordConfirmation"
    />
  </AdminLayout>
</template>

<script setup>
import { computed, inject, onMounted, reactive, ref } from 'vue'

import AdminLayout from '@/layouts/AdminLayout.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'
import { accountService } from '@/services/accountService'

const showToast = inject('showToast', null)

const sidebarOpen = ref(false)

const loading = ref(true)
const loadError = ref('')
const account = ref(null)

const profileName = ref('')
const savingProfile = ref(false)
const profileError = ref('')

const savingPassword = ref(false)
const passwordError = ref('')
const showPasswordConfirm = ref(false)

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  confirm_password: '',
})

const profileDirty = computed(() => {
  if (!account.value) return false

  return profileName.value.trim() !== String(account.value.name || '').trim()
})

const roleLabel = computed(() => {
  if (account.value?.role === 'admin') return 'Administrator'
  if (account.value?.role === 'user') return 'User'

  return account.value?.role || '—'
})

const loadAccount = async () => {
  loading.value = true
  loadError.value = ''

  try {
    const data = await accountService.getAccount()

    account.value = data.user || null
    profileName.value = account.value?.name || ''
  } catch (error) {
    console.error('Unable to load account settings:', error)

    loadError.value = error?.message || 'Unable to retrieve your administrator account.'

    account.value = null
  } finally {
    loading.value = false
  }
}

const saveProfile = async (refreshUser) => {
  if (savingProfile.value || !account.value) return

  profileError.value = ''

  const name = profileName.value.trim().replace(/\s+/g, ' ')

  if (!name) {
    profileError.value = 'Display name is required.'
    return
  }

  if (name.length < 2) {
    profileError.value = 'Display name must contain at least 2 characters.'
    return
  }

  if (name.length > 150) {
    profileError.value = 'Display name must not exceed 150 characters.'
    return
  }

  if (name === String(account.value.name || '').trim()) {
    profileError.value = 'No changes were made to your display name.'
    return
  }

  savingProfile.value = true

  try {
    const data = await accountService.updateProfile(name)

    account.value.name = data.user?.name || name
    profileName.value = account.value.name

    if (typeof refreshUser === 'function') {
      await refreshUser()
    }

    showToast?.(data.message || 'Account name updated successfully.', 'success')
  } catch (error) {
    console.error('Unable to update account name:', error)

    profileError.value = error?.message || 'Unable to update your account name.'

    showToast?.(profileError.value, 'error', 5000)
  } finally {
    savingProfile.value = false
  }
}

const preparePasswordChange = () => {
  if (savingPassword.value) return

  passwordError.value = ''

  const currentPassword = passwordForm.current_password
  const newPassword = passwordForm.new_password
  const confirmPassword = passwordForm.confirm_password

  /*
   * Required
   */
  if (!currentPassword || !newPassword || !confirmPassword) {
    passwordError.value = 'Please complete all password fields.'

    return
  }

  /*
   * Length
   */
  if (newPassword.length < 12) {
    passwordError.value = 'Your new password must contain at least 12 characters.'

    return
  }

  if (newPassword.length > 128) {
    passwordError.value = 'Your new password must not exceed 128 characters.'

    return
  }

  /*
   * Uppercase
   */
  if (!/[A-Z]/.test(newPassword)) {
    passwordError.value = 'Your new password must contain at least one uppercase letter.'

    return
  }

  /*
   * Lowercase
   */
  if (!/[a-z]/.test(newPassword)) {
    passwordError.value = 'Your new password must contain at least one lowercase letter.'

    return
  }

  /*
   * Number
   */
  if (!/[0-9]/.test(newPassword)) {
    passwordError.value = 'Your new password must contain at least one number.'

    return
  }

  /*
   * Special character
   */
  if (!/[^A-Za-z0-9\s]/.test(newPassword)) {
    passwordError.value = 'Your new password must contain at least one special character.'

    return
  }

  /*
   * Confirmation
   */
  if (newPassword !== confirmPassword) {
    passwordError.value = 'New password and confirmation password do not match.'

    return
  }

  /*
   * Current password reuse
   */
  if (currentPassword === newPassword) {
    passwordError.value = 'Your new password must be different from your current password.'

    return
  }

  /*
   * Repeated character
   */
  if (/^(.)\1+$/.test(newPassword)) {
    passwordError.value = 'Your new password is too easy to guess.'

    return
  }

  showPasswordConfirm.value = true
}

const cancelPasswordConfirmation = () => {
  if (savingPassword.value) return

  showPasswordConfirm.value = false
}

const changePassword = async () => {
  if (savingPassword.value) return

  savingPassword.value = true
  passwordError.value = ''

  try {
    const data = await accountService.changePassword({
      current_password: passwordForm.current_password,
      new_password: passwordForm.new_password,
      confirm_password: passwordForm.confirm_password,
    })

    showPasswordConfirm.value = false

    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.confirm_password = ''

    showCurrentPassword.value = false
    showNewPassword.value = false
    showConfirmPassword.value = false

    await loadAccount()

    showToast?.(data.message || 'Password changed successfully.', 'success', 4500)
  } catch (error) {
    console.error('Unable to change password:', error)

    showPasswordConfirm.value = false

    passwordError.value = error?.message || 'Unable to change your password.'

    showToast?.(passwordError.value, 'error', 5000)
  } finally {
    savingPassword.value = false
  }
}

const formatDateTime = (value) => {
  if (!value) return 'Not available'

  /*
   * APESCON backend stores authentication timestamps in UTC.
   * MySQL returns "YYYY-MM-DD HH:MM:SS", so convert it to ISO UTC
   * before giving it to the browser.
   */
  const normalized = String(value).includes('T')
    ? String(value)
    : `${String(value).replace(' ', 'T')}Z`

  const date = new Date(normalized)

  if (Number.isNaN(date.getTime())) {
    return String(value)
  }

  return new Intl.DateTimeFormat('en-PH', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date)
}

onMounted(loadAccount)
</script>

<style scoped>
.card-icon {
  display: flex;
  width: 2.5rem;
  height: 2.5rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  background: rgba(2, 81, 153, 0.08);
  color: #025199;
  font-size: 0.75rem;
}

.card-icon-orange {
  background: rgba(242, 124, 41, 0.1);
  color: #f27c29;
}

.form-label {
  display: block;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #526274;
}

.form-input {
  min-height: 2.875rem;
  width: 100%;
  border: 1px solid #d8e4ed;
  border-radius: 0.875rem;
  background: #fff;
  padding: 0.75rem 1rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.8125rem;
  color: #334155;
  outline: none;

  transition:
    border-color 180ms ease,
    box-shadow 180ms ease,
    background-color 180ms ease;
}

.form-input::placeholder {
  color: #94a3b8;
}

.form-input:hover:not(:disabled) {
  border-color: rgba(2, 81, 153, 0.3);
}

.form-input:focus {
  border-color: #025199;
  box-shadow: 0 0 0 4px rgba(2, 81, 153, 0.08);
}

.form-input:disabled {
  cursor: not-allowed;
  background: #f8fbfd;
  opacity: 0.7;
}

.profile-name-input {
  padding-left: 2.75rem;
}

.password-input {
  padding-right: 3rem;
}

.password-toggle {
  position: absolute;
  top: 50%;
  right: 0.9rem;
  display: flex;
  width: 2rem;
  height: 2rem;
  transform: translateY(-50%);
  align-items: center;
  justify-content: center;
  border-radius: 0.5rem;
  color: #94a3b8;
  font-size: 0.75rem;

  transition:
    background-color 180ms ease,
    color 180ms ease;
}

.password-toggle:hover {
  background: #f2f7fb;
  color: #025199;
}

.primary-button {
  display: inline-flex;
  min-height: 2.75rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 0.75rem;
  background: #025199;
  padding: 0.65rem 1rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.11em;
  text-transform: uppercase;
  color: white;
  box-shadow: 0 8px 18px rgba(2, 81, 153, 0.15);

  transition:
    background-color 180ms ease,
    transform 180ms ease,
    box-shadow 180ms ease;
}

.primary-button:hover:not(:disabled) {
  background: #063f73;
  transform: translateY(-1px);
  box-shadow: 0 10px 24px rgba(2, 81, 153, 0.22);
}

.primary-button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.secondary-button {
  display: inline-flex;
  min-height: 2.625rem;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border: 1px solid #d8e4ed;
  border-radius: 0.75rem;
  background: white;
  padding: 0.6rem 0.9rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #526274;

  transition:
    border-color 180ms ease,
    color 180ms ease,
    background-color 180ms ease;
}

.secondary-button:hover {
  border-color: #025199;
  background: #f8fbfd;
  color: #025199;
}

.readonly-row {
  display: grid;
  grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
  gap: 1rem;
  padding: 1rem 1.25rem;
}

.readonly-label {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: #94a3b8;
}

.readonly-label i {
  width: 0.875rem;
  color: #025199;
}

.readonly-value {
  text-align: right;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  color: #526274;
}

.activity-box {
  border: 1px solid #d8e4ed;
  border-radius: 0.875rem;
  background: #f8fbfd;
  padding: 0.75rem 0.9rem;
}

.activity-label {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #94a3b8;
}

.activity-value {
  margin-top: 0.35rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.6875rem;
  font-weight: 700;
  color: #526274;
}

.error-panel {
  display: flex;
  align-items: flex-start;
  gap: 0.6rem;
  border: 1px solid #f0d2d5;
  border-radius: 0.875rem;
  background: #fff5f5;
  padding: 0.75rem 0.9rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.75rem;
  line-height: 1.3rem;
  color: #b4232d;
}

@media (max-width: 639px) {
  .readonly-row {
    grid-template-columns: 1fr;
    gap: 0.4rem;
  }

  .readonly-value {
    text-align: left;
  }
}

@media (prefers-reduced-motion: reduce) {
  .form-input,
  .password-toggle,
  .primary-button,
  .secondary-button {
    transition: none;
  }
}
</style>
