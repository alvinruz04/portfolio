<template>
  <transition name="register-fade">
    <div
      class="fixed inset-0 z-260 flex items-center justify-center p-3 sm:p-4 md:p-6"
      :class="isClosing ? 'pointer-events-none' : ''"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-[#2b1f25]/35 backdrop-blur-md" @click="handleClose"></div>

      <!-- Modal -->
      <div
        class="relative z-10 w-[94vw] sm:w-[90vw] md:w-[86vw] lg:w-2xl max-h-[92vh] overflow-y-auto rounded-[28px] border border-white/40 bg-white/88 shadow-[0_20px_60px_rgba(0,0,0,0.16)] backdrop-blur-xl"
        :class="isClosing ? 'animate-modal-out' : 'animate-modal-in'"
      >
        <!-- Soft premium background -->
        <div
          class="absolute inset-0 rounded-[28px] bg-linear-to-br from-[#f8edf2] via-[#fffafb] to-[#f4e5ec]"
        ></div>
        <div
          class="absolute -top-10 -left-10 h-36 w-36 rounded-full bg-[#f3c8d9]/40 blur-3xl"
        ></div>
        <div
          class="absolute bottom-0 right-0 h-44 w-44 rounded-full bg-[#edd6df]/45 blur-3xl"
        ></div>
        <div
          class="absolute inset-0 rounded-[28px] opacity-[0.12]"
          style="
            background-image: radial-gradient(
              circle at 1px 1px,
              rgba(120, 120, 120, 0.16) 1px,
              transparent 0
            );
            background-size: 24px 24px;
          "
        ></div>

        <div class="relative z-10 px-5 pb-6 pt-5 sm:px-7 sm:pb-7 sm:pt-6 md:px-8 md:pb-8 md:pt-7">
          <!-- Close -->
          <button
            @click="handleClose"
            aria-label="Close registration modal"
            class="absolute right-4 top-4 sm:right-5 sm:top-5 flex h-10 w-10 items-center justify-center rounded-full border border-[#eadce2] bg-white/90 text-[#6b7280] transition duration-200 hover:border-[#e97aac] hover:text-[#e97aac] hover:shadow-md"
          >
            <i class="fa-solid fa-xmark text-[16px] sm:text-[18px]"></i>
          </button>

          <!-- Header -->
          <div class="pr-12">
            <span
              class="inline-flex items-center rounded-full border border-[#e9c4d3] bg-white/65 px-3 py-1 text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.28em] text-[#9c6b80]"
            >
              You Glow Babe
            </span>

            <h2
              class="mt-4 font-playfair text-[30px] leading-[1.05] text-[#5f6670] sm:text-[36px] md:text-[40px]"
            >
              Create Account
            </h2>

            <p
              class="mt-3 max-w-xl font-nunito text-[14px] leading-6 sm:text-[15px] sm:leading-7 text-[#7d838d]"
            >
              Register your account to access your dashboard, manage your profile, and enjoy a more
              personal You Glow Babe experience.
            </p>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="mt-6 space-y-4">
            <!-- Name -->
            <div
              class="rounded-[18px] sm:rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
            >
              <label
                class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
              >
                Name
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Enter your full name"
                class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] placeholder:text-[#a2a8b1] outline-none"
              />
            </div>

            <!-- Email -->
            <div
              class="rounded-[18px] sm:rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
            >
              <label
                class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
              >
                Email Address
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="Enter your email"
                class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] placeholder:text-[#a2a8b1] outline-none"
              />
            </div>

            <!-- Password -->
            <div
              class="rounded-[18px] sm:rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
            >
              <label
                class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
              >
                Password
              </label>
              <div class="mt-2 flex items-center gap-3">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model="form.password"
                  placeholder="Enter your password"
                  class="w-full bg-transparent font-nunito text-[15px] text-[#5f6670] placeholder:text-[#a2a8b1] outline-none"
                />
                <button
                  type="button"
                  class="text-[#7d838d] transition hover:text-[#e97aac]"
                  @click="showPassword = !showPassword"
                >
                  <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Confirm Password -->
            <div
              class="rounded-[18px] sm:rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
            >
              <label
                class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
              >
                Confirm Password
              </label>
              <div class="mt-2 flex items-center gap-3">
                <input
                  :type="showConfirmPassword ? 'text' : 'password'"
                  v-model="form.confirm_password"
                  placeholder="Confirm your password"
                  class="w-full bg-transparent font-nunito text-[15px] text-[#5f6670] placeholder:text-[#a2a8b1] outline-none"
                />
                <button
                  type="button"
                  class="text-[#7d838d] transition hover:text-[#e97aac]"
                  @click="showConfirmPassword = !showConfirmPassword"
                >
                  <i
                    :class="showConfirmPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                  ></i>
                </button>
              </div>
            </div>

            <!-- Password Note -->
            <div
              class="rounded-[18px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 text-[13px] leading-6 text-[#8a8f99]"
            >
              Password must include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1
              symbol.
            </div>

            <!-- Buttons -->
            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end">
              <button
                type="button"
                @click="handleClose"
                class="inline-flex h-11 items-center justify-center rounded-full border border-[#e8dbe1] bg-white px-5 font-nunito text-[13px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
              >
                Cancel
              </button>

              <button
                type="submit"
                :disabled="loading"
                class="inline-flex h-11 items-center justify-center rounded-full bg-[#e97aac] px-5 font-nunito text-[13px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] hover:shadow-[0_12px_24px_rgba(233,122,172,0.24)] disabled:opacity-60 disabled:cursor-not-allowed"
              >
                <i class="fa-regular fa-user mr-2"></i>
                {{ loading ? 'Creating...' : 'Create Account' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { inject } from 'vue'

export default {
  name: 'UserRegistrationModal',
  emits: ['close'],
  setup() {
    const showToast = inject('showToast')
    return { showToast }
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        confirm_password: '',
      },
      loading: false,
      showPassword: false,
      showConfirmPassword: false,
      isClosing: false,
    }
  },
  methods: {
    resetForm() {
      this.form.name = ''
      this.form.email = ''
      this.form.password = ''
      this.form.confirm_password = ''
      this.showPassword = false
      this.showConfirmPassword = false
    },

    validate() {
      const name = this.form.name.trim()
      const email = this.form.email.trim()
      const password = this.form.password
      const confirmPassword = this.form.confirm_password

      if (!name) {
        this.showToast?.('Name is required.', 'error')
        return false
      }

      if (name.length > 150) {
        this.showToast?.('Name must not exceed 150 characters.', 'error')
        return false
      }

      if (!email) {
        this.showToast?.('Email is required.', 'error')
        return false
      }

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailPattern.test(email)) {
        this.showToast?.('Invalid email format.', 'error')
        return false
      }

      if (!password || !confirmPassword) {
        this.showToast?.('Password and confirm password are required.', 'error')
        return false
      }

      if (password !== confirmPassword) {
        this.showToast?.('Passwords do not match.', 'error')
        return false
      }

      return true
    },

    async handleClose() {
      if (this.isClosing) return
      this.isClosing = true
      await new Promise((resolve) => setTimeout(resolve, 240))
      this.$emit('close')
      this.isClosing = false
      this.resetForm()
    },

    async submitForm() {
      if (!this.validate()) return
      this.loading = true

      try {
        const baseURL = import.meta.env.VITE_API_BASE_URL

        const res = await fetch(`${baseURL}/api/auth/user_registration.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            name: this.form.name.trim(),
            email: this.form.email.trim(),
            password: this.form.password,
            confirm_password: this.form.confirm_password,
          }),
        })

        const data = await res.json()

        if (!res.ok) {
          this.showToast?.(data.message || 'Registration failed.', 'error')
          return
        }

        this.showToast?.('Account created successfully. You can now sign in.', 'success')
        await this.handleClose()
      } catch (err) {
        console.error(err)
        this.showToast?.('An error occurred during registration.', 'error')
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.login-fade-enter-active,
.login-fade-leave-active {
  transition: opacity 0.22s ease;
}

.login-fade-enter-from,
.login-fade-leave-to {
  opacity: 0;
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
    transform: translateY(14px) scale(0.98);
  }
}

.animate-modal-in {
  animation: modalIn 0.24s ease-out forwards;
}

.animate-modal-out {
  animation: modalOut 0.2s ease-in forwards;
}
</style>
