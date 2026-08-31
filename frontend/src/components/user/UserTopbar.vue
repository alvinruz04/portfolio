<template>
  <header class="sticky top-0 z-20 border-b border-[#f2e3ea] bg-white/72 backdrop-blur-xl">
    <div class="flex h-22 items-center justify-between px-6 lg:px-8">
      <div class="flex items-center gap-3">
        <button
          class="text-[#7d838d] transition hover:text-[#e97aac] lg:hidden"
          @click="$emit('toggleSidebar')"
          aria-label="Toggle sidebar"
        >
          <i class="fas fa-bars text-[18px]"></i>
        </button>

        <div>
          <div class="text-sm font-semibold tracking-wide text-[#5f6670] sm:text-base">
            {{ title }}
          </div>
          <div class="text-[11px] text-[#9aa0a9]">
            {{ subtitle }}
          </div>
        </div>
      </div>

      <div class="hidden items-center gap-3 sm:flex">
        <div class="text-right leading-tight">
          <div class="text-xs font-semibold text-[#5f6670]">
            {{ user?.name || defaultName }}
          </div>
          <div class="text-[11px] text-[#9aa0a9]">{{ roleLabel }}</div>
        </div>

        <div
          class="grid h-10 w-10 place-items-center rounded-2xl border border-[#efd7e1] bg-[#fff7fa] text-[#b38497]"
        >
          <i :class="roleIcon"></i>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'UserTopbar',
  props: {
    title: { type: String, default: 'Dashboard' },
    subtitle: { type: String, default: 'Overview & quick controls' },
    user: { type: Object, default: null },
  },
  emits: ['toggleSidebar'],
  computed: {
    isAdmin() {
      return this.user?.role === 'admin'
    },

    roleLabel() {
      return this.isAdmin ? 'You Glow Babe Administrator' : 'You Glow Babe Member'
    },

    defaultName() {
      return this.isAdmin ? 'Administrator' : 'Member'
    },

    roleIcon() {
      return this.isAdmin ? 'fas fa-user-shield' : 'fas fa-user'
    },
  },
}
</script>
