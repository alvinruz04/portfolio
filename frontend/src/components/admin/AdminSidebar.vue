<template>
  <aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 transform flex-col overflow-hidden border-r border-white/12 bg-linear-to-b from-[#062B4C] via-[#025199] to-[#073D68] text-white shadow-[18px_0_50px_rgba(5,35,61,0.14)] transition-transform duration-300 lg:translate-x-0"
    :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
  >
    <div
      class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"
    ></div>
    <div
      class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-[#F27C29]/18 blur-3xl"
    ></div>
    <div class="sidebar-dot-pattern pointer-events-none absolute inset-0 opacity-[0.10]"></div>

    <div
      class="relative z-10 flex h-20 shrink-0 items-center justify-between border-b border-white/12 px-5"
    >
      <button
        type="button"
        class="flex min-w-0 items-center gap-3 text-left"
        @click="$emit('navigate', 'dashboard')"
      >
        <span
          class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[15px] border border-white/20 bg-white shadow-[0_10px_24px_rgba(0,0,0,0.16)]"
        >
          <img
            src="/assets/apescon_logo_transparent.png"
            alt="APESCON"
            class="h-10 w-10 object-contain"
            draggable="false"
          />
        </span>
        <span class="min-w-0">
          <span class="block font-montserrat text-[14px] font-bold tracking-[0.04em] text-white"
            >APESCON</span
          >
          <span class="mt-0.5 block truncate font-nunito text-[10px] text-white/55"
            >Service Management Portal</span
          >
        </span>
      </button>

      <button
        type="button"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/65 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30 lg:hidden"
        aria-label="Close sidebar"
        @click="$emit('close')"
      >
        <i class="fa-solid fa-xmark text-[17px]"></i>
      </button>
    </div>

    <nav
      class="relative z-10 min-h-0 flex-1 overflow-y-auto px-3 py-5"
      aria-label="Administration navigation"
    >
      <p class="sidebar-section-label">Overview</p>

      <button
        type="button"
        class="sidebar-link"
        :class="active === 'dashboard' ? 'sidebar-link-active' : ''"
        @click="$emit('navigate', 'dashboard')"
      >
        <span class="sidebar-icon"><i class="fa-solid fa-chart-pie"></i></span>
        <span>Dashboard</span>
      </button>

      <p class="sidebar-section-label mt-6">Operations</p>

      <button
        v-for="item in operationItems"
        :key="item.key"
        type="button"
        class="sidebar-link"
        :class="active === item.key ? 'sidebar-link-active' : ''"
        @click="$emit('navigate', item.key)"
      >
        <span class="sidebar-icon"><i :class="item.icon"></i></span>
        <span class="min-w-0 flex-1 truncate">{{ item.label }}</span>
        <span
          v-if="item.comingSoon"
          class="rounded-full border border-white/12 bg-white/8 px-2 py-0.5 font-montserrat text-[8px] font-semibold uppercase tracking-widest text-white/50"
        >
          Soon
        </span>
      </button>

      <p class="sidebar-section-label mt-6">System</p>

      <button
        type="button"
        class="sidebar-link"
        :class="active === 'settings' ? 'sidebar-link-active' : ''"
        @click="$emit('navigate', 'settings')"
      >
        <span class="sidebar-icon">
          <i class="fa-solid fa-user-gear"></i>
        </span>

        <span>Account Settings</span>
      </button>

      <button type="button" class="sidebar-link" @click="$emit('navigate', 'website')">
        <span class="sidebar-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
        <span>View Website</span>
      </button>
    </nav>

    <div class="relative z-10 shrink-0 border-t border-white/12 p-4">
      <div class="rounded-[18px] border border-white/12 bg-white/8 p-3 backdrop-blur-sm">
        <div class="flex items-center gap-3">
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F27C29] text-white shadow-[0_8px_20px_rgba(242,124,41,0.22)]"
          >
            <i class="fa-solid fa-user-shield text-[13px]"></i>
          </div>
          <div class="min-w-0 flex-1">
            <p class="truncate font-montserrat text-[11px] font-semibold text-white">
              {{ user?.name || 'APESCON Administrator' }}
            </p>
            <p class="mt-0.5 truncate font-nunito text-[10px] text-white/55">
              {{ user?.email_address || roleLabel }}
            </p>
          </div>
        </div>

        <button
          type="button"
          class="mt-3 flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-white/12 bg-white/8 font-montserrat text-[9px] font-semibold uppercase tracking-[0.13em] text-white/70 transition-all duration-200 hover:border-[#F27C29]/60 hover:bg-[#F27C29] hover:text-white focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-white/10"
          @click="$emit('navigate', 'logout')"
        >
          <i class="fa-solid fa-right-from-bracket text-[11px]"></i>
          Log Out
        </button>
      </div>

      <p class="mt-3 text-center font-nunito text-[9px] uppercase tracking-[0.14em] text-white/35">
        © 2026 Associated Pest Control
      </p>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'AdminSidebar',

  props: {
    open: { type: Boolean, default: false },
    active: { type: String, default: 'dashboard' },
    user: { type: Object, default: null },
  },

  emits: ['close', 'navigate'],

  computed: {
    roleLabel() {
      if (this.user?.role === 'admin') return 'Administrator'
      return this.user?.role || 'Administrator'
    },

    operationItems() {
      return [
        {
          key: 'transactions',
          label: 'Service Transactions',
          icon: 'fa-solid fa-file-invoice-dollar',
          comingSoon: false,
        },
        {
          key: 'customers',
          label: 'Customers',
          icon: 'fa-solid fa-users',
          comingSoon: true,
        },
        {
          key: 'quotations',
          label: 'Quotations',
          icon: 'fa-regular fa-file-lines',
          comingSoon: true,
        },
        {
          key: 'schedules',
          label: 'Service Schedule',
          icon: 'fa-regular fa-calendar-check',
          comingSoon: true,
        },
        {
          key: 'reports',
          label: 'Reports',
          icon: 'fa-solid fa-chart-column',
          comingSoon: true,
        },
      ]
    },
  },
}
</script>

<style scoped>
.sidebar-dot-pattern {
  background-image: radial-gradient(
    circle at 1px 1px,
    rgba(255, 255, 255, 0.72) 1px,
    transparent 0
  );
  background-size: 24px 24px;
}

.sidebar-section-label {
  padding: 0 0.75rem 0.5rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.19em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.38);
}

.sidebar-link {
  display: flex;
  min-height: 2.875rem;
  width: 100%;
  align-items: center;
  gap: 0.75rem;
  border: 1px solid transparent;
  border-radius: 0.875rem;
  padding: 0.5rem 0.75rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.8125rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.68);
  text-align: left;
  transition:
    border-color 200ms ease,
    background-color 200ms ease,
    color 200ms ease,
    transform 200ms ease;
}

.sidebar-link:hover {
  background: rgba(255, 255, 255, 0.08);
  color: white;
  transform: translateX(2px);
}

.sidebar-link-active {
  border-color: rgba(255, 255, 255, 0.14);
  background: rgba(255, 255, 255, 0.12);
  color: white;
  box-shadow: 0 8px 24px rgba(4, 31, 55, 0.12);
}

.sidebar-icon {
  display: flex;
  width: 2rem;
  height: 2rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 0.625rem;
  background: rgba(255, 255, 255, 0.08);
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.75);
  transition:
    background-color 200ms ease,
    color 200ms ease;
}

.sidebar-link-active .sidebar-icon {
  background: #f27c29;
  color: white;
}

nav {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.14) transparent;
}

@media (prefers-reduced-motion: reduce) {
  .sidebar-link,
  .sidebar-icon {
    transition: none;
  }
}
</style>
