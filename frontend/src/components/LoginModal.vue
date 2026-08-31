<template>
  <transition name="login-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-210 flex items-center justify-center overflow-hidden p-3 sm:p-4 md:p-6"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-[#071A2B]/65 backdrop-blur-sm" @click="handleClose"></div>

      <!-- MAIN LOGIN MODAL -->
      <div
        v-if="!showUserRegistrationModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="login-modal-title"
        aria-describedby="login-modal-description"
        class="relative z-10 w-full max-w-xl overflow-hidden rounded-[26px] border border-[#D8E4ED] bg-linear-to-br from-white via-[#FCFDFE] to-[#F3F7FA] shadow-[0_30px_90px_rgba(5,35,61,0.28)] sm:rounded-[30px]"
        :class="isClosing ? 'animate-modal-out' : 'animate-modal-in'"
      >
        <!-- Brand accent -->
        <div
          class="absolute inset-x-0 top-0 z-20 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
        ></div>

        <!-- Decorative background -->
        <div
          class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-[#025199]/7 blur-3xl"
        ></div>

        <div
          class="pointer-events-none absolute -bottom-24 -right-20 h-72 w-72 rounded-full bg-[#F27C29]/8 blur-3xl"
        ></div>

        <div
          class="pointer-events-none absolute inset-0 opacity-[0.05]"
          style="
            background-image: radial-gradient(
              circle at 1px 1px,
              rgba(2, 81, 153, 0.55) 1px,
              transparent 0
            );
            background-size: 24px 24px;
          "
        ></div>

        <!-- Close -->
        <button
          type="button"
          @click="handleClose"
          aria-label="Close login modal"
          class="absolute right-4 top-4 z-30 flex h-10 w-10 items-center justify-center rounded-full border border-[#D8E4ED] bg-white/90 text-[#475569] shadow-sm backdrop-blur-sm transition-all duration-200 hover:border-[#025199] hover:text-[#025199] hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10 sm:right-5 sm:top-5"
        >
          <svg
            viewBox="0 0 24 24"
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            stroke-width="1.4"
            stroke-linecap="round"
            aria-hidden="true"
          >
            <path d="M6 6L18 18M18 6L6 18" />
          </svg>
        </button>
        <div
          class="login-modal-scroll relative z-10 max-h-[calc(100dvh-1.5rem)] overflow-x-hidden overflow-y-auto overscroll-contain sm:max-h-[calc(100dvh-2rem)]"
        >
          <div class="px-5 pb-5 pt-8 sm:px-8 sm:pb-6 sm:pt-9">
            <div class="relative z-10 px-5 pb-6 pt-8 sm:px-8 sm:pb-8 sm:pt-9">
              <!-- Logo -->
              <div class="flex justify-center">
                <div
                  class="flex h-20 w-20 items-center justify-center rounded-[22px] border border-[#D8E4ED] bg-white shadow-[0_14px_34px_rgba(2,81,153,0.12)] sm:h-22 sm:w-22"
                >
                  <img
                    src="/assets/apescon_logo_transparent.png"
                    alt="APESCON Associated Pest Control"
                    class="h-17 w-17 object-contain sm:h-19 sm:w-19"
                  />
                </div>
              </div>

              <!-- Heading -->
              <div class="mt-6 text-center">
                <p
                  class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.22em] text-[#F27C29] sm:text-[11px]"
                >
                  Secure Account Access
                </p>

                <h2
                  id="login-modal-title"
                  class="mt-3 font-montserrat text-[28px] font-bold leading-tight text-[#163A5F] sm:text-[34px]"
                >
                  Welcome Back
                </h2>
              </div>

              <!-- Form -->
              <form class="mt-7 space-y-4" :aria-busy="isSubmitting" @submit.prevent="submitLogin">
                <!-- Email -->
                <div class="login-field">
                  <label for="login-email" class="login-field-label"> Email Address </label>

                  <div class="mt-2 flex items-center gap-3">
                    <span
                      class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#025199]/8 text-[#025199]"
                    >
                      <i class="fa-regular fa-envelope text-[13px]"></i>
                    </span>

                    <input
                      id="login-email"
                      v-model="login.email"
                      type="email"
                      inputmode="email"
                      autocomplete="email"
                      spellcheck="false"
                      placeholder="Enter your email address"
                      class="min-w-0 flex-1 bg-transparent font-nunito text-[15px] text-[#334155] placeholder:text-[#94A3B8] outline-none"
                      required
                    />
                  </div>
                </div>

                <!-- Password -->
                <div class="login-field">
                  <label for="login-password" class="login-field-label"> Password </label>

                  <div class="mt-2 flex items-center gap-3">
                    <span
                      class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#025199]/8 text-[#025199]"
                    >
                      <i class="fa-solid fa-lock text-[12px]"></i>
                    </span>

                    <input
                      id="login-password"
                      v-model="login.password"
                      :type="showPassword ? 'text' : 'password'"
                      autocomplete="current-password"
                      placeholder="Enter your password"
                      class="min-w-0 flex-1 bg-transparent font-nunito text-[15px] text-[#334155] placeholder:text-[#94A3B8] outline-none"
                      required
                    />

                    <button
                      type="button"
                      class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[#64748B] transition-all duration-200 hover:bg-[#025199]/8 hover:text-[#025199] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10"
                      :aria-label="showPassword ? 'Hide password' : 'Show password'"
                      :aria-pressed="showPassword"
                      @click="showPassword = !showPassword"
                    >
                      <i
                        :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                      ></i>
                    </button>
                  </div>
                </div>

                <!-- Forgot password -->
                <div class="flex justify-end">
                  <button
                    type="button"
                    class="font-nunito text-[13px] font-semibold text-[#025199] transition-colors duration-200 hover:text-[#F27C29] hover:underline hover:underline-offset-4"
                    @click="openForgotPassword"
                  >
                    Forgot Password?
                  </button>
                </div>

                <!-- Sign in -->
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="group inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-xl bg-[#025199] px-6 font-montserrat text-[12px] font-semibold uppercase tracking-[0.14em] text-white shadow-[0_12px_28px_rgba(2,81,153,0.24)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#063F73] hover:shadow-[0_16px_34px_rgba(2,81,153,0.30)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/20 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
                >
                  <i v-if="isSubmitting" class="fa-solid fa-spinner animate-spin text-[13px]"></i>

                  <template v-else>
                    <span>Sign In</span>

                    <span
                      class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15 transition-transform duration-200 group-hover:translate-x-0.5"
                    >
                      <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </span>
                  </template>

                  <span v-if="isSubmitting">Signing In...</span>
                </button>

                <!-- Divider -->
                <div class="flex items-center gap-4 py-1">
                  <span class="h-px flex-1 bg-[#D8E4ED]"></span>

                  <span
                    class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.17em] text-[#94A3B8] sm:text-[10px]"
                  >
                    New to APESCON?
                  </span>

                  <span class="h-px flex-1 bg-[#D8E4ED]"></span>
                </div>

                <!-- Create account -->
                <button
                  type="button"
                  :disabled="isSubmitting"
                  class="group inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-xl border border-[#CBD8E2] bg-white px-6 font-montserrat text-[12px] font-semibold uppercase tracking-[0.13em] text-[#163A5F] transition-all duration-300 hover:-translate-y-0.5 hover:border-[#F27C29] hover:text-[#025199] hover:shadow-[0_10px_24px_rgba(2,81,153,0.08)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10 disabled:cursor-not-allowed disabled:opacity-60"
                  @click="openCreateAccountComingSoon"
                >
                  <i class="fa-regular fa-user text-[13px]"></i>
                  <span>Create Account</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Registration Modal -->
      <UserRegistrationModal
        v-if="showUserRegistrationModal"
        @close="showUserRegistrationModal = false"
      />
    </div>
  </transition>
</template>

<script>
import { inject } from 'vue'
import UserRegistrationModal from './UserRegistrationModal.vue'

export default {
  name: 'LoginModal',

  components: {
    UserRegistrationModal,
  },

  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['close', 'open-coming-soon-modal'],

  setup() {
    const showToast = inject('showToast', null)

    return {
      showToast,
    }
  },

  data() {
    return {
      login: {
        email: '',
        password: '',
      },

      showPassword: false,
      showUserRegistrationModal: false,
      isClosing: false,
      isSubmitting: false,
      previousBodyOverflow: '',
    }
  },

  watch: {
    show(value) {
      if (value) {
        this.lockBody()
        document.addEventListener('keydown', this.handleEscape)
      } else {
        this.unlockBody()
        document.removeEventListener('keydown', this.handleEscape)
        this.showUserRegistrationModal = false
        this.resetForm()
      }
    },
  },

  methods: {
    lockBody() {
      this.previousBodyOverflow = document.body.style.overflow
      document.body.style.overflow = 'hidden'
    },

    unlockBody() {
      document.body.style.overflow = this.previousBodyOverflow || ''
    },

    handleEscape(event) {
      if (event.key !== 'Escape') return

      if (this.showUserRegistrationModal) {
        this.showUserRegistrationModal = false
        return
      }

      this.handleClose()
    },

    resetForm() {
      this.login.email = ''
      this.login.password = ''
      this.showPassword = false
      this.isSubmitting = false
    },

    async handleClose() {
      if (this.isClosing) return

      this.isClosing = true

      await new Promise((resolve) => setTimeout(resolve, 220))

      this.$emit('close')

      this.isClosing = false
      this.showUserRegistrationModal = false
      this.resetForm()
    },

    async openCreateAccountComingSoon() {
      if (this.isSubmitting || this.isClosing) return

      // Close the login modal first for a cleaner transition.
      await this.handleClose()

      this.$emit('open-coming-soon-modal', {
        featureTitle: 'Account Registration',
        description: 'Online account registration is currently being prepared.',
        iconClass: 'fa-regular fa-user',
      })
    },

    async submitLogin() {
      if (this.isSubmitting) return

      const email = this.login.email.trim()
      const password = this.login.password
      const configuredBaseURL = import.meta.env.VITE_API_BASE_URL

      if (!email || !password) {
        this.showToast?.('Please enter your email address and password.', 'warning')
        return
      }

      if (!configuredBaseURL) {
        console.error('VITE_API_BASE_URL is not configured.')

        this.showToast?.('The login service is not configured correctly.', 'error')
        return
      }

      const baseURL = configuredBaseURL.replace(/\/$/, '')

      this.isSubmitting = true

      try {
        const response = await fetch(`${baseURL}/api/auth/login.php`, {
          method: 'POST',

          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },

          credentials: 'include',

          body: JSON.stringify({
            email,
            password,
          }),
        })

        let data = {}

        try {
          data = await response.json()
        } catch {
          data = {}
        }

        if (!response.ok) {
          this.showToast?.(data.message || 'The email address or password is incorrect.', 'error')
          return
        }

        const destinationByRole = {
          user: '/user/dashboard',
          admin: '/admin/dashboard',
          superadmin: '/superadmin/dashboard',
          cashier: '/cashier/dashboard',
        }

        const destination = destinationByRole[data.role]

        if (!destination) {
          console.error('Unsupported account role:', data.role)

          this.showToast?.('Your account role is not configured correctly.', 'error')
          return
        }

        this.showToast?.('Login successful.', 'success')

        await this.handleClose()
        await this.$router.push(destination)
      } catch (error) {
        console.error('Unexpected login error:', error)

        this.showToast?.('Unable to sign in right now. Please try again.', 'error')
      } finally {
        this.isSubmitting = false
      }
    },

    openUserRegistration() {
      if (this.isSubmitting) return

      this.showUserRegistrationModal = true
    },

    openForgotPassword() {
      this.showToast?.(
        'Password reset is not available yet. Please contact APESCON support for assistance.',
        'info',
        4500,
      )
    },
  },

  beforeUnmount() {
    this.unlockBody()
    document.removeEventListener('keydown', this.handleEscape)
  },
}
</script>

<style scoped>
.login-field {
  border: 1px solid #d8e4ed;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.95);
  padding: 0.875rem 1rem;
  transition:
    border-color 220ms ease,
    box-shadow 220ms ease,
    transform 220ms ease;
}

.login-field:hover {
  border-color: rgba(2, 81, 153, 0.24);
}

.login-field:focus-within {
  border-color: #025199;
  box-shadow: 0 0 0 4px rgba(2, 81, 153, 0.09);
  transform: translateY(-1px);
}

.login-field-label {
  display: block;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.6875rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #163a5f;
}

.login-fade-enter-active,
.login-fade-leave-active {
  transition: opacity 0.22s ease;
}

.login-fade-enter-from,
.login-fade-leave-to {
  opacity: 0;
}

.login-modal-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.login-modal-scroll::-webkit-scrollbar {
  display: none;
  width: 0;
  height: 0;
}

@keyframes modalIn {
  0% {
    opacity: 0;
    transform: translateY(14px) scale(0.98);
  }

  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes modalOut {
  0% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  100% {
    opacity: 0;
    transform: translateY(12px) scale(0.985);
  }
}

.animate-modal-in {
  animation: modalIn 0.24s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.animate-modal-out {
  animation: modalOut 0.2s ease-in forwards;
}

@media (min-width: 640px) {
  .login-field {
    padding: 1rem 1.125rem;
  }
}
</style>
