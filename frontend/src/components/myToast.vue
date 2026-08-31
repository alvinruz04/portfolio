<template>
  <div
    class="pointer-events-none fixed inset-x-3 top-3 z-9999 sm:left-auto sm:right-5 sm:top-5 sm:w-full sm:max-w-md"
    aria-live="polite"
    aria-relevant="additions removals"
  >
    <transition-group name="toast" tag="div" class="space-y-3">
      <article
        v-for="(toast, index) in toasts"
        :key="toast.id || index"
        :role="toast.type === 'error' ? 'alert' : 'status'"
        aria-atomic="true"
        class="pointer-events-auto relative overflow-hidden rounded-2xl border bg-white/95 shadow-[0_18px_45px_rgba(5,35,61,0.14)] backdrop-blur-xl"
        :class="toneClass(toast.type)"
      >
        <!-- Left status accent -->
        <div class="absolute inset-y-0 left-0 w-1" :class="accentClass(toast.type)"></div>

        <!-- Subtle APESCON top line -->
        <div class="absolute inset-x-0 top-0 h-px bg-[#025199]/18"></div>

        <!-- Very subtle background glow -->
        <div
          class="pointer-events-none absolute -right-12 -top-12 h-32 w-32 rounded-full blur-3xl"
          :class="glowClass(toast.type)"
        ></div>

        <div class="relative z-10 flex items-start gap-3.5 px-5 py-4">
          <!-- Icon -->
          <div
            class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border"
            :class="iconWrapClass(toast.type)"
          >
            <i :class="[iconClass(toast.type), 'text-[15px] leading-none']" aria-hidden="true"></i>
          </div>

          <!-- Text -->
          <div class="min-w-0 flex-1">
            <p
              class="font-montserrat text-[10px] font-bold uppercase tracking-[0.17em]"
              :class="labelClass(toast.type)"
            >
              {{ labelFor(toast.type) }}
            </p>

            <p class="mt-1.5 wrap-break-word font-nunito text-[14px] leading-6 text-[#526274]">
              {{ toast.message }}
            </p>
          </div>
        </div>
      </article>
    </transition-group>
  </div>
</template>

<script>
export default {
  name: 'ToastNotification',

  props: {
    toasts: {
      type: Array,
      required: true,
    },
  },

  methods: {
    normalizedType(type) {
      const allowedTypes = ['success', 'error', 'info', 'warning']

      return allowedTypes.includes(type) ? type : 'info'
    },

    labelFor(type) {
      const labels = {
        success: 'Success',
        error: 'Error',
        info: 'Notice',
        warning: 'Warning',
      }

      return labels[this.normalizedType(type)]
    },

    iconClass(type) {
      const icons = {
        success: 'fa-solid fa-check',
        error: 'fa-solid fa-xmark',
        info: 'fa-solid fa-info',
        warning: 'fa-solid fa-exclamation',
      }

      return icons[this.normalizedType(type)]
    },

    toneClass(type) {
      const classes = {
        success: 'border-[#D5E9DD]',
        error: 'border-[#F0D2D5]',
        info: 'border-[#D2E0EB]',
        warning: 'border-[#F1DDC9]',
      }

      return classes[this.normalizedType(type)]
    },

    accentClass(type) {
      const classes = {
        success: 'bg-[#2F8A5B]',
        error: 'bg-[#D51C26]',
        info: 'bg-[#025199]',
        warning: 'bg-[#F27C29]',
      }

      return classes[this.normalizedType(type)]
    },

    glowClass(type) {
      const classes = {
        success: 'bg-[#2F8A5B]/8',
        error: 'bg-[#D51C26]/8',
        info: 'bg-[#025199]/9',
        warning: 'bg-[#F27C29]/9',
      }

      return classes[this.normalizedType(type)]
    },

    iconWrapClass(type) {
      const classes = {
        success: 'border-[#D5E9DD] bg-[#F3FAF6] text-[#2F8A5B]',
        error: 'border-[#F0D2D5] bg-[#FFF5F5] text-[#D51C26]',
        info: 'border-[#D2E0EB] bg-[#F2F7FB] text-[#025199]',
        warning: 'border-[#F1DDC9] bg-[#FFF8F1] text-[#F27C29]',
      }

      return classes[this.normalizedType(type)]
    },

    labelClass(type) {
      const classes = {
        success: 'text-[#2F7953]',
        error: 'text-[#B91F28]',
        info: 'text-[#025199]',
        warning: 'text-[#C96520]',
      }

      return classes[this.normalizedType(type)]
    },
  },
}
</script>

<style scoped>
.toast-move,
.toast-enter-active,
.toast-leave-active {
  transition:
    opacity 280ms ease,
    transform 280ms cubic-bezier(0.22, 1, 0.36, 1);
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(24px) scale(0.98);
}

.toast-enter-to {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.toast-leave-from {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(20px) scale(0.98);
}

.toast-leave-active {
  position: absolute;
  width: 100%;
}

@media (max-width: 639px) {
  .toast-enter-from {
    transform: translateY(-14px) scale(0.98);
  }

  .toast-leave-to {
    transform: translateY(-10px) scale(0.98);
  }
}

@media (prefers-reduced-motion: reduce) {
  .toast-move,
  .toast-enter-active,
  .toast-leave-active {
    transition-duration: 0.01ms;
  }
}
</style>
