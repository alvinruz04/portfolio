<template>
  <header
    class="sticky top-0 z-30 border-b border-[#D8E4ED] bg-white/90 shadow-[0_2px_14px_rgba(5,35,61,0.04)] backdrop-blur-xl"
  >
    <div class="absolute inset-x-0 top-0 h-0.5 bg-white/90"></div>

    <div class="flex min-h-18 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
      <!-- Left -->
      <div class="flex min-w-0 items-center gap-3">
        <button
          type="button"
          class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#D8E4ED] bg-white text-[#025199] shadow-sm transition-all duration-200 hover:border-[#025199] hover:bg-[#F2F7FB] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/10 lg:hidden"
          aria-label="Open navigation"
          @click="$emit('toggle-sidebar')"
        >
          <i class="fa-solid fa-bars text-[15px]"></i>
        </button>

        <div class="min-w-0">
          <p
            class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.18em] text-[#F27C29]"
          >
            Administration
          </p>

          <div class="mt-0.5 flex min-w-0 items-baseline gap-3">
            <h1
              class="truncate font-montserrat text-[18px] font-bold text-[#163A5F] sm:text-[20px]"
            >
              {{ title }}
            </h1>
          </div>

          <p
            v-if="subtitle"
            class="mt-0.5 hidden truncate font-nunito text-[11px] text-[#64748B] sm:block"
          >
            {{ subtitle }}
          </p>
        </div>
      </div>

      <!-- Right -->
      <div class="flex shrink-0 items-center gap-3">
        <!-- Secure indicator -->
        <div
          class="hidden items-center gap-2 rounded-full border border-[#D8E4ED] bg-[#F8FBFD] px-3 py-2 lg:flex"
        >
          <span class="relative flex h-2 w-2">
            <span
              class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#2F8A5B] opacity-25"
            ></span>

            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#2F8A5B]"></span>
          </span>

          <span
            class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.12em] text-[#64748B]"
          >
            Secure Session
          </span>
        </div>

        <!-- User -->
        <div class="flex items-center gap-3">
          <div class="hidden max-w-52 text-right sm:block">
            <p class="truncate font-montserrat text-[11px] font-semibold text-[#163A5F]">
              {{ user?.name || 'APESCON Administrator' }}
            </p>

            <p class="mt-0.5 truncate font-nunito text-[10px] text-[#64748B]">
              {{ user?.email_address || roleLabel }}
            </p>
          </div>

          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl border border-[#D8E4ED] bg-linear-to-br from-[#F8FBFD] to-[#EEF5FA] text-[#025199] shadow-sm"
            :title="user?.name || 'Administrator'"
          >
            <i class="fa-solid fa-user-shield text-[14px]"></i>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'AdminTopbar',

  props: {
    title: {
      type: String,
      default: 'Dashboard',
    },

    subtitle: {
      type: String,
      default: '',
    },

    user: {
      type: Object,
      default: null,
    },
  },

  emits: ['toggle-sidebar'],

  computed: {
    roleLabel() {
      if (this.user?.role === 'admin') {
        return 'Administrator'
      }

      return this.user?.role || ''
    },
  },
}
</script>
