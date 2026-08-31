<template>
  <Teleport to="body">
    <transition name="confirm-fade">
      <div
        v-if="show"
        class="fixed inset-0 z-9998 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="titleId"
      >
        <div
          class="absolute inset-0 bg-[#071A2B]/60 backdrop-blur-sm"
          @click="handleBackdrop"
        ></div>

        <div
          class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl border border-[#D8E4ED] bg-white shadow-[0_30px_90px_rgba(5,35,61,0.28)]"
        >
          <div
            class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
          ></div>
          <div
            class="pointer-events-none absolute -right-16 -top-16 h-44 w-44 rounded-full bg-[#025199]/6 blur-3xl"
          ></div>

          <div class="relative z-10 p-6 sm:p-7">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-[15px] border"
              :class="iconWrapperClass"
            >
              <i :class="[dialogIcon, 'text-[16px]']"></i>
            </div>

            <h2 :id="titleId" class="mt-5 font-montserrat text-[21px] font-bold text-[#163A5F]">
              {{ title }}
            </h2>
            <p class="mt-2 font-nunito text-[14px] leading-6 text-[#64748B]">{{ message }}</p>

            <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
              <button
                type="button"
                :disabled="loading"
                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#CBD8E2] bg-white px-5 font-montserrat text-[10px] font-semibold uppercase tracking-[0.12em] text-[#64748B] transition-all duration-200 hover:border-[#025199] hover:text-[#025199] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10 disabled:cursor-not-allowed disabled:opacity-50"
                @click="$emit('cancel')"
              >
                Cancel
              </button>

              <button
                type="button"
                :disabled="loading"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-5 font-montserrat text-[10px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm transition-all duration-200 focus-visible:outline-none focus-visible:ring-4 disabled:cursor-not-allowed disabled:opacity-60"
                :class="confirmButtonClass"
                @click="$emit('confirm')"
              >
                <i v-if="loading" class="fa-solid fa-spinner animate-spin text-[11px]"></i>
                <i v-else :class="[confirmIcon, 'text-[11px]']"></i>
                {{ loading ? loadingText : confirmText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script>
export default {
  name: 'ConfirmDialog',

  props: {
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Are you sure?' },
    message: { type: String, default: '' },
    confirmText: { type: String, default: 'Confirm' },
    loadingText: { type: String, default: 'Processing...' },
    confirmIcon: { type: String, default: 'fa-solid fa-check' },
    variant: { type: String, default: 'primary' },
    loading: { type: Boolean, default: false },
  },

  emits: ['confirm', 'cancel'],

  computed: {
    titleId() {
      return 'apescon-confirm-dialog-title'
    },
    dialogIcon() {
      return this.variant === 'danger'
        ? 'fa-solid fa-right-from-bracket'
        : 'fa-solid fa-circle-question'
    },
    iconWrapperClass() {
      return this.variant === 'danger'
        ? 'border-[#F0D2D5] bg-[#FFF5F5] text-[#D51C26]'
        : 'border-[#D2E0EB] bg-[#F2F7FB] text-[#025199]'
    },
    confirmButtonClass() {
      return this.variant === 'danger'
        ? 'bg-[#D51C26] hover:bg-[#B91F28] focus-visible:ring-[#D51C26]/20'
        : 'bg-[#025199] hover:bg-[#063F73] focus-visible:ring-[#025199]/20'
    },
  },

  methods: {
    handleBackdrop() {
      if (this.loading) return
      this.$emit('cancel')
    },
  },
}
</script>

<style scoped>
.confirm-fade-enter-active,
.confirm-fade-leave-active {
  transition: opacity 220ms ease;
}

.confirm-fade-enter-from,
.confirm-fade-leave-to {
  opacity: 0;
}
</style>
