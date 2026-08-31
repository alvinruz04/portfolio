<template>
  <aside
    class="fixed inset-y-0 left-0 z-40 w-[320px] transform border-r border-[#f0dce5] bg-white/88 backdrop-blur-xl transition-transform duration-200 lg:translate-x-0"
    :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
  >
    <div class="flex h-22.25 items-center justify-between border-b border-[#f2e3ea] px-6">
      <div class="flex min-w-0 items-center gap-4">
        <div class="min-w-0 leading-tight">
          <div class="truncate font-playfair text-[22px] text-[#5f6670]">You Glow Babe</div>
          <div class="mt-1 text-[11px] uppercase tracking-[0.2em] text-[#b38497]">
            {{ areaLabel }}
          </div>
        </div>
      </div>

      <button
        class="text-[#7d838d] transition hover:text-[#e97aac] lg:hidden"
        @click="$emit('close')"
        aria-label="Close sidebar"
      >
        <i class="fas fa-times text-[18px]"></i>
      </button>
    </div>

    <nav class="px-4 py-5">
      <div class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b38497]">
        Navigation
      </div>

      <div class="space-y-2">
        <button
          v-for="item in items"
          :key="item.key"
          class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left transition"
          :class="
            active === item.key
              ? 'bg-[#e97aac] text-white shadow-[0_12px_24px_rgba(233,122,172,0.22)]'
              : 'border border-[#eadce2] bg-white text-[#6b7280] hover:border-[#e97aac] hover:text-[#e97aac]'
          "
          @click="$emit('navigate', item.key)"
        >
          <i :class="item.icon + ' w-5 text-center'"></i>
          <span class="text-sm font-semibold">{{ item.label }}</span>
        </button>
      </div>
    </nav>

    <div class="absolute bottom-0 left-0 right-0 border-t border-[#f2e3ea] px-5 py-4">
      <div class="text-[11px] tracking-[0.14em] text-[#b0b6bf]">© 2026 You Glow Babe</div>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'UserSidebar',
  props: {
    open: { type: Boolean, default: false },
    active: { type: String, default: 'dashboard' },
    user: { type: Object, default: null },
  },
  emits: ['close', 'navigate'],
  computed: {
    isAdmin() {
      return this.user?.role === 'admin'
    },

    areaLabel() {
      return this.isAdmin ? 'Administrator Panel' : 'Member Area'
    },

    items() {
      const baseItems = [
        { key: 'dashboard', label: 'Dashboard', icon: 'fas fa-border-all' },
        { key: 'settings', label: 'Settings', icon: 'fas fa-gear' },
        { key: 'logout', label: 'Logout', icon: 'fas fa-sign-out-alt' },
      ]

      if (this.isAdmin) {
        baseItems.splice(
          1,
          0,
          {
            key: 'proofs',
            label: 'Proof Reviews',
            icon: 'fas fa-receipt',
          },
          {
            key: 'sellers',
            label: 'Sellers',
            icon: 'fas fa-user-tag',
          },
          {
            key: 'fake-sellers',
            label: 'Fake Sellers',
            icon: 'fas fa-user-slash',
          },
          {
            key: 'announcements',
            label: 'Announcements',
            icon: 'fas fa-bullhorn',
          },
        )
      } else {
        baseItems.splice(1, 0, {
          key: 'proofs',
          label: 'Proof Upload',
          icon: 'fas fa-receipt',
        })
      }

      return baseItems
    },
  },
}
</script>
