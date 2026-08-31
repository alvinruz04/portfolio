<template>
  <main
    class="relative flex min-h-dvh items-center justify-center overflow-hidden bg-linear-to-br from-[#EEF5FA] via-white to-[#F7FAFC] px-4 py-8 sm:px-6"
  >
    <!-- APESCON top accent -->
    <div
      class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
    ></div>

    <!-- Decorative background -->
    <div
      class="pointer-events-none absolute -left-32 -top-28 h-96 w-96 rounded-full bg-[#025199]/8 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -bottom-32 -right-28 h-96 w-96 rounded-full bg-[#F27C29]/9 blur-3xl"
    ></div>

    <div class="brand-dot-pattern pointer-events-none absolute inset-0 opacity-[0.05]"></div>

    <!-- Page content -->
    <div class="relative z-10 w-full max-w-lg">
      <!-- Back / security -->
      <div class="mb-5 flex items-center justify-between gap-4">
        <router-link
          to="/"
          class="group inline-flex items-center gap-2 rounded-full border border-[#D8E4ED] bg-white/85 px-4 py-2.5 font-montserrat text-[10px] font-semibold uppercase tracking-[0.13em] text-[#163A5F] shadow-sm backdrop-blur-sm transition-all duration-200 hover:border-[#025199] hover:text-[#025199] hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10"
        >
          <i
            class="fa-solid fa-arrow-left text-[10px] transition-transform duration-200 group-hover:-translate-x-0.5"
          ></i>

          Back to Website
        </router-link>

        <div class="hidden items-center gap-2 font-nunito text-[12px] text-[#64748B] sm:flex">
          <i class="fa-solid fa-shield-halved text-[#025199]"></i>
          Secure Access
        </div>
      </div>

      <!-- Login card -->
      <section
        class="login-shell relative overflow-hidden rounded-[28px] border border-[#D8E4ED] bg-linear-to-br from-white via-[#FCFDFE] to-[#F3F7FA] shadow-[0_30px_90px_rgba(5,35,61,0.18)] sm:rounded-4xl"
        aria-labelledby="login-title"
      >
        <!-- Card accent -->
        <div
          class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
        ></div>

        <!-- Card decoration -->
        <div
          class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#025199]/6 blur-3xl"
        ></div>

        <div
          class="pointer-events-none absolute -bottom-24 -left-20 h-64 w-64 rounded-full bg-[#F27C29]/5 blur-3xl"
        ></div>

        <div class="relative z-10 px-5 py-8 sm:px-9 sm:py-10">
          <!-- Logo -->
          <div class="flex justify-center">
            <div
              class="flex h-22 w-22 items-center justify-center rounded-3xl border border-[#D8E4ED] bg-white shadow-[0_14px_34px_rgba(2,81,153,0.12)]"
            >
              <img
                src="/assets/apescon_logo_transparent.png"
                alt="APESCON Associated Pest Control"
                class="h-19 w-19 object-contain"
                draggable="false"
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

            <h1
              id="login-title"
              class="mt-3 font-montserrat text-[30px] font-bold leading-tight text-[#163A5F] sm:text-[36px]"
            >
              Welcome Back
            </h1>

            <p
              class="mx-auto mt-3 max-w-sm font-nunito text-[14px] leading-7 text-[#64748B] sm:text-[15px]"
            >
              Enter the email address and password assigned to your APESCON account.
            </p>
          </div>

          <!-- Login form -->
          <form class="mt-8 space-y-4" :aria-busy="isSubmitting" novalidate @submit.prevent="login">
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
                  v-model.trim="email"
                  type="email"
                  inputmode="email"
                  autocomplete="email"
                  spellcheck="false"
                  maxlength="191"
                  placeholder="Enter your email address"
                  class="min-w-0 flex-1 bg-transparent font-nunito text-[15px] text-[#334155] outline-none placeholder:text-[#94A3B8]"
                  :disabled="isSubmitting"
                  required
                  @input="clearError"
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
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  maxlength="1024"
                  placeholder="Enter your password"
                  class="min-w-0 flex-1 bg-transparent font-nunito text-[15px] text-[#334155] outline-none placeholder:text-[#94A3B8]"
                  :disabled="isSubmitting"
                  required
                  @input="clearError"
                />

                <button
                  type="button"
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[#64748B] transition-all duration-200 hover:bg-[#025199]/8 hover:text-[#025199] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10"
                  :aria-label="showPassword ? 'Hide password' : 'Show password'"
                  :aria-pressed="showPassword"
                  :disabled="isSubmitting"
                  @click="showPassword = !showPassword"
                >
                  <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Remember / forgot -->
            <div class="flex items-center justify-between gap-4">
              <label
                class="inline-flex cursor-pointer items-center gap-2 font-nunito text-[13px] text-[#64748B]"
              >
                <input
                  v-model="rememberEmail"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#CBD8E2] text-[#025199] focus:ring-[#025199]"
                />

                Remember email
              </label>

              <button
                type="button"
                class="font-nunito text-[13px] font-semibold text-[#025199] transition-colors duration-200 hover:text-[#F27C29] hover:underline hover:underline-offset-4"
                @click="showForgotPasswordNotice"
              >
                Forgot Password?
              </button>
            </div>

            <!-- Error -->
            <div
              v-if="errorMessage"
              role="alert"
              aria-live="assertive"
              class="flex items-start gap-3 rounded-xl border border-[#F0D2D5] bg-[#FFF5F5] px-4 py-3"
            >
              <i class="fa-solid fa-circle-exclamation mt-0.5 text-[14px] text-[#D51C26]"></i>

              <p class="font-nunito text-[13px] leading-5 text-[#8E2530]">
                {{ errorMessage }}
              </p>
            </div>

            <!-- Rate limit -->
            <div
              v-if="rateLimitSeconds > 0"
              role="status"
              aria-live="polite"
              class="flex items-start gap-3 rounded-xl border border-[#F1DDC9] bg-[#FFF8F1] px-4 py-3"
            >
              <i class="fa-solid fa-clock mt-0.5 text-[14px] text-[#F27C29]"></i>

              <p class="font-nunito text-[13px] leading-5 text-[#9A5525]">
                Sign-in is temporarily limited. Try again in
                <strong>{{ formattedWait }}</strong
                >.
              </p>
            </div>

            <!-- Sign in -->
            <button
              type="submit"
              :disabled="isSubmitting || rateLimitSeconds > 0"
              class="group inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-xl bg-[#025199] px-6 font-montserrat text-[12px] font-semibold uppercase tracking-[0.14em] text-white shadow-[0_12px_28px_rgba(2,81,153,0.24)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#063F73] hover:shadow-[0_16px_34px_rgba(2,81,153,0.30)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/20 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
            >
              <i v-if="isSubmitting" class="fa-solid fa-spinner animate-spin text-[13px]"></i>

              <template v-if="!isSubmitting">
                <span>Sign In</span>

                <span
                  class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15 transition-transform duration-200 group-hover:translate-x-0.5"
                >
                  <i class="fa-solid fa-arrow-right text-[11px]"></i>
                </span>
              </template>

              <span v-else> Signing In... </span>
            </button>
          </form>

          <!-- Help -->
          <div class="mt-6 border-t border-[#D8E4ED] pt-5">
            <div class="flex items-start gap-3">
              <span
                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#025199]/8 text-[#025199]"
              >
                <i class="fa-solid fa-headset text-[12px]"></i>
              </span>

              <p class="font-nunito text-[12px] leading-5 text-[#64748B]">
                Need account assistance? Contact the APESCON administrator or email

                <a
                  href="mailto:apesconservice@gmail.com"
                  class="font-semibold text-[#025199] transition-colors hover:text-[#F27C29]"
                >
                  apesconservice@gmail.com
                </a>
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Copyright -->
      <p class="pt-5 text-center font-nunito text-[12px] text-[#64748B]">
        © 2026 APESCON Associated Pest Control. Authorized access only.
      </p>
    </div>
  </main>
</template>

<script setup>
import { computed, inject, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const showToast = inject('showToast', null)

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberEmail = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')
const rateLimitSeconds = ref(0)

let countdownTimer = null

const configuredBaseURL = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '')

const formattedWait = computed(() => {
  const minutes = Math.floor(rateLimitSeconds.value / 60)
  const seconds = rateLimitSeconds.value % 60

  if (minutes <= 0) {
    return `${seconds} second${seconds === 1 ? '' : 's'}`
  }

  return `${minutes}:${String(seconds).padStart(2, '0')}`
})

const clearError = () => {
  errorMessage.value = ''
}

const startRateLimitCountdown = (seconds) => {
  window.clearInterval(countdownTimer)

  rateLimitSeconds.value = Math.max(1, Number(seconds) || 60)

  countdownTimer = window.setInterval(() => {
    rateLimitSeconds.value -= 1

    if (rateLimitSeconds.value <= 0) {
      window.clearInterval(countdownTimer)
      countdownTimer = null
    }
  }, 1000)
}

const destinationForRole = (role) => {
  const defaults = {
    admin: '/admin/dashboard',
    user: '/user/dashboard',
  }

  const requestedRedirect =
    typeof route.query.redirect === 'string' ? route.query.redirect.trim() : ''

  const allowedPrefix = role === 'admin' ? '/admin/' : role === 'user' ? '/user/' : ''

  if (
    allowedPrefix &&
    requestedRedirect.startsWith(allowedPrefix) &&
    !requestedRedirect.startsWith('//')
  ) {
    return requestedRedirect
  }

  return defaults[role] || '/'
}

const showForgotPasswordNotice = () => {
  showToast?.(
    'Password reset is not available yet. Please contact the APESCON administrator.',
    'info',
    4500,
  )
}

const login = async () => {
  if (isSubmitting.value || rateLimitSeconds.value > 0) return

  const normalizedEmail = email.value.trim().toLowerCase()

  if (!normalizedEmail || !password.value) {
    errorMessage.value = 'Please enter your email address and password.'
    showToast?.(errorMessage.value, 'warning')
    return
  }

  if (!configuredBaseURL) {
    errorMessage.value = 'The APESCON login service is not configured.'
    showToast?.(errorMessage.value, 'error')
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''

  const controller = new AbortController()
  const timeoutId = window.setTimeout(() => controller.abort(), 15000)

  try {
    const response = await fetch(`${configuredBaseURL}/api/auth/login.php`, {
      method: 'POST',

      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },

      credentials: 'include',
      cache: 'no-store',
      signal: controller.signal,

      body: JSON.stringify({
        email: normalizedEmail,
        password: password.value,
      }),
    })

    const responseText = await response.text()

    let data = {}

    try {
      data = responseText ? JSON.parse(responseText) : {}
    } catch {
      data = {}
    }

    if (response.status === 429) {
      const retryAfterHeader = Number(response.headers.get('Retry-After'))
      const retryAfter = Number(data.retry_after) || retryAfterHeader || 60

      startRateLimitCountdown(retryAfter)

      errorMessage.value =
        data.message || 'Too many sign-in attempts. Please wait before trying again.'

      password.value = ''

      showToast?.(errorMessage.value, 'warning')

      return
    }

    if (!response.ok || !data.success) {
      errorMessage.value = data.message || 'Unable to sign in with those credentials.'

      password.value = ''

      showToast?.(errorMessage.value, 'error')

      return
    }

    const destination = destinationForRole(data.role)

    if (destination === '/') {
      errorMessage.value = 'This account role is not configured for the APESCON portal.'

      showToast?.(errorMessage.value, 'error')

      return
    }

    if (rememberEmail.value) {
      window.localStorage.setItem('apescon_login_email', normalizedEmail)
    } else {
      window.localStorage.removeItem('apescon_login_email')
    }

    showToast?.(`Welcome back, ${data.name || 'APESCON user'}.`, 'success')

    await router.replace(destination)
  } catch (error) {
    console.error('APESCON login failed:', error)

    errorMessage.value =
      error?.name === 'AbortError'
        ? 'The login request timed out. Please try again.'
        : 'Unable to reach the login service. Please check your connection and try again.'

    showToast?.(errorMessage.value, 'error')
  } finally {
    window.clearTimeout(timeoutId)
    isSubmitting.value = false
  }
}

onMounted(() => {
  const savedEmail = window.localStorage.getItem('apescon_login_email')

  if (savedEmail) {
    email.value = savedEmail
    rememberEmail.value = true
  }
})

onBeforeUnmount(() => {
  window.clearInterval(countdownTimer)
})
</script>

<style scoped>
.brand-dot-pattern {
  background-image: radial-gradient(circle at 1px 1px, rgba(2, 81, 153, 0.55) 1px, transparent 0);
  background-size: 24px 24px;
}

.login-shell {
  animation: loginShellIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
}

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

@keyframes loginShellIn {
  from {
    opacity: 0;
    transform: translateY(16px) scale(0.985);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@media (min-width: 640px) {
  .login-field {
    padding: 1rem 1.125rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .login-shell {
    animation: none;
  }

  .login-field {
    transition: none;
  }
}
</style>
