<template>
  <transition name="coming-soon-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-220 flex items-center justify-center overflow-hidden p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="coming-soon-title"
      aria-describedby="coming-soon-description"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-[#071A2B]/70 backdrop-blur-md" @click="handleClose"></div>

      <!-- Modal -->
      <div
        class="relative z-10 w-full max-w-md overflow-hidden rounded-[28px] border border-white/15 bg-white shadow-[0_32px_100px_rgba(3,28,50,0.38)]"
        :class="isClosing ? 'animate-modal-out' : 'animate-modal-in'"
      >
        <!-- Brand accent -->
        <div
          class="absolute inset-x-0 top-0 z-30 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
        ></div>

        <!-- Header -->
        <div
          class="relative overflow-hidden bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68] px-6 pb-10 pt-9 text-center sm:px-9"
        >
          <!-- Background decorations -->
          <div
            class="pointer-events-none absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"
          ></div>

          <div
            class="pointer-events-none absolute -bottom-24 -right-20 h-72 w-72 rounded-full bg-[#F27C29]/22 blur-3xl"
          ></div>

          <div
            class="pointer-events-none absolute inset-0 opacity-[0.11]"
            style="
              background-image: radial-gradient(
                circle at 1px 1px,
                rgba(255, 255, 255, 0.75) 1px,
                transparent 0
              );
              background-size: 24px 24px;
            "
          ></div>

          <div class="relative z-10">
            <!-- Icon -->
            <div
              class="mx-auto flex h-18 w-18 items-center justify-center rounded-[22px] border border-white/20 bg-white/10 text-[#F7B267] shadow-[0_16px_35px_rgba(0,0,0,0.16)] backdrop-blur-md"
            >
              <i :class="[iconClass, 'text-[26px]']" aria-hidden="true"></i>
            </div>

            <!-- Status -->
            <div
              class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 backdrop-blur-md"
            >
              <span
                class="h-2 w-2 rounded-full bg-[#F27C29] shadow-[0_0_0_5px_rgba(242,124,41,0.14)]"
              ></span>

              <span
                class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.22em] text-[#F7B267]"
              >
                {{ eyebrow }}
              </span>
            </div>

            <!-- Title -->
            <h2
              id="coming-soon-title"
              class="mx-auto mt-5 max-w-sm font-montserrat text-[26px] font-bold leading-tight text-white sm:text-[32px]"
            >
              {{ featureTitle }}
            </h2>
          </div>
        </div>

        <!-- Content -->
        <div
          class="relative overflow-hidden bg-linear-to-br from-white via-[#FCFDFE] to-[#F3F7FA] px-6 pb-7 pt-6 text-center sm:px-9 sm:pb-8"
        >
          <div
            class="pointer-events-none absolute -bottom-20 -right-16 h-56 w-56 rounded-full bg-[#F27C29]/7 blur-3xl"
          ></div>

          <div class="relative z-10">
            <p
              v-if="description"
              id="coming-soon-description"
              class="mx-auto max-w-sm font-nunito text-[14px] leading-7 text-[#64748B] sm:text-[15px]"
            >
              {{ description }}
            </p>

            <button
              type="button"
              class="group mt-6 inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-xl bg-[#025199] px-6 font-montserrat text-[12px] font-semibold uppercase tracking-[0.14em] text-white shadow-[0_12px_28px_rgba(2,81,153,0.24)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#063F73] hover:shadow-[0_16px_34px_rgba(2,81,153,0.30)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/20"
              @click="handleClose"
            >
              <span>Got It</span>

              <span
                class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15 transition-transform duration-200 group-hover:translate-x-0.5"
              >
                <i class="fa-solid fa-check text-[11px]" aria-hidden="true"></i>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'ComingSoonModal',

  props: {
    show: {
      type: Boolean,
      default: false,
    },

    featureTitle: {
      type: String,
      default: 'This Feature Is Coming Soon',
    },

    description: {
      type: String,
      default:
        'This feature is not available yet. We are currently working on it and will make it accessible in a future update.',
    },

    eyebrow: {
      type: String,
      default: 'Coming Soon',
    },

    iconClass: {
      type: String,
      default: 'fa-solid fa-screwdriver-wrench',
    },
  },

  emits: ['close'],

  data() {
    return {
      isClosing: false,
      previousBodyOverflow: '',
    }
  },

  watch: {
    show: {
      immediate: true,

      handler(value) {
        if (value) {
          this.lockBody()
          document.addEventListener('keydown', this.handleEscape)

          this.$nextTick(() => {
            this.$refs.closeButton?.focus()
          })
        } else {
          this.unlockBody()
          document.removeEventListener('keydown', this.handleEscape)
          this.isClosing = false
        }
      },
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
      if (event.key === 'Escape') {
        this.handleClose()
      }
    },

    async handleClose() {
      if (this.isClosing) return

      this.isClosing = true

      await new Promise((resolve) => setTimeout(resolve, 200))

      this.$emit('close')
      this.isClosing = false
    },
  },

  beforeUnmount() {
    this.unlockBody()
    document.removeEventListener('keydown', this.handleEscape)
  },
}
</script>

<style scoped>
.coming-soon-fade-enter-active,
.coming-soon-fade-leave-active {
  transition: opacity 0.22s ease;
}

.coming-soon-fade-enter-from,
.coming-soon-fade-leave-to {
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
    transform: translateY(12px) scale(0.985);
  }
}

.animate-modal-in {
  animation: modalIn 0.24s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.animate-modal-out {
  animation: modalOut 0.2s ease-in forwards;
}

@media (prefers-reduced-motion: reduce) {
  .animate-modal-in,
  .animate-modal-out {
    animation-duration: 0.01ms;
  }

  .coming-soon-fade-enter-active,
  .coming-soon-fade-leave-active {
    transition-duration: 0.01ms;
  }
}
</style>
