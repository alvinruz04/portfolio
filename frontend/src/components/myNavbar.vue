<template>
  <header
    class="fixed top-0 left-0 z-50 w-full border-b border-white/20 bg-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.04)] backdrop-blur-md"
  >
    <div class="mx-auto w-full max-w-295 px-3 min-[360px]:px-4 sm:px-8 lg:px-10">
      <div class="relative flex h-16 items-center justify-between md:h-18">
        <!-- LEFT: Hamburger -->
        <div class="z-10 flex items-center">
          <button
            ref="menuButton"
            type="button"
            class="flex h-9 w-9 items-center justify-center text-[#025199] transition-all duration-200 hover:text-[#F27C29] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#025199] focus-visible:ring-offset-2"
            aria-label="Open menu"
            aria-controls="mobile-sidebar"
            :aria-expanded="menuOpen"
            @click="openMenu"
          >
            <i class="fa-solid fa-bars text-[20px] md:text-[22px]"></i>
          </button>
        </div>

        <!-- CENTER: Logo -->
        <div
          class="pointer-events-none absolute left-1/2 flex -translate-x-1/2 items-center justify-center"
        >
          <router-link to="/" class="pointer-events-auto inline-flex items-center justify-center">
            <img
              src="/assets/apescon_logo_transparent.png"
              alt="APESCON"
              class="h-11 w-auto max-w-26.25 object-contain min-[360px]:h-13 min-[360px]:max-w-31.25 sm:h-16 sm:max-w-42.5 md:h-19 md:max-w-none"
            />
          </router-link>
        </div>

        <!-- RIGHT: Icons -->
        <div class="z-10 flex items-center gap-0.5 text-[#333] min-[360px]:gap-1.5 md:gap-4">
          <!-- Contact -->
          <button
            type="button"
            class="flex h-8 w-8 items-center justify-center transition duration-200 hover:scale-105 text-[#025199] hover:text-[#F27C29] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#025199] md:h-9 md:w-9"
            aria-label="Contact us"
            @click="$emit('open-contact-modal')"
          >
            <i class="fa-regular fa-envelope text-[17px] md:text-[18px]"></i>
          </button>

          <!-- User -->
          <button
            type="button"
            class="flex h-8 w-8 items-center justify-center transition duration-200 text-[#025199] hover:scale-105 hover:text-[#F27C29] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#025199] md:h-9 md:w-9"
            aria-label="Account"
            @click="handleAccountClick"
          >
            <i class="fa-regular fa-circle-user text-[19px] md:text-[20px]"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Overlay -->
  <transition name="overlay-fade">
    <div
      v-if="menuOpen"
      class="fixed inset-0 z-60 bg-black/40 backdrop-blur-[1px]"
      aria-hidden="true"
      @click="closeMenu"
    ></div>
  </transition>

  <!-- Sidebar -->
  <transition name="sidebar-slide">
    <aside
      v-if="menuOpen"
      id="mobile-sidebar"
      class="sidebar-panel fixed inset-y-0 left-0 z-70 flex w-[calc(100%-1.5rem)] max-w-100 flex-col overflow-hidden bg-[#f6f6f4] shadow-2xl sm:w-90"
      role="dialog"
      aria-modal="true"
      aria-label="Navigation menu"
    >
      <!-- Top -->
      <div class="sidebar-top shrink-0 px-4 pb-2 sm:px-6 sm:pb-4">
        <button
          ref="closeMenuButton"
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-full text-[#2f2f2f] transition hover:bg-black/5 hover:text-[#025199] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#025199]"
          aria-label="Close menu"
          @click="closeMenu"
        >
          <svg
            viewBox="0 0 24 24"
            class="h-7 w-7"
            fill="none"
            stroke="currentColor"
            stroke-width="1.4"
            stroke-linecap="round"
            aria-hidden="true"
          >
            <path d="M6 6L18 18M18 6L6 18" />
          </svg>
        </button>
      </div>

      <!-- Scrollable Menu -->
      <nav
        class="sidebar-nav min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 sm:px-7"
        aria-label="Main navigation"
      >
        <ul class="sidebar-menu-list">
          <li
            v-for="(item, index) in menuItems"
            :key="item.label"
            class="border-b border-[#d9d9d9]"
            :style="{ animationDelay: `${index * 180}ms` }"
          >
            <!-- Router item -->
            <router-link
              v-if="!item.action"
              :to="item.to"
              @click="closeMenu"
              class="group flex items-center justify-between py-5 text-[18px] tracking-[0.18em] uppercase text-[#1f2937] font-montserrat sidebar-item"
            >
              <span class="transition duration-200 group-hover:text-[#025199]">
                {{ item.label }}
              </span>

              <i
                v-if="item.hasArrow"
                class="fa-solid fa-chevron-right text-[13px] text-[#F27C29] transition duration-200 group-hover:text-[#025199] group-hover:translate-x-1"
              ></i>
            </router-link>

            <!-- Action item -->
            <button
              v-else
              type="button"
              @click="handleMenuAction(item.action)"
              class="group flex w-full items-center justify-between py-5 text-left text-[18px] tracking-[0.18em] uppercase text-[#1f2937] font-montserrat sidebar-item"
            >
              <span class="transition duration-200 group-hover:text-[#025199]">
                {{ item.label }}
              </span>

              <i
                v-if="item.hasArrow"
                class="fa-solid fa-chevron-right text-[13px] text-[#F27C29] transition duration-200 group-hover:text-[#025199] group-hover:translate-x-1"
              ></i>
            </button>
          </li>
        </ul>
      </nav>

      <!-- Bottom -->
      <div class="sidebar-bottom shrink-0 border-t border-[#d9d9d9] px-4 py-4 sm:px-7 sm:py-6">
        <p
          class="font-nunito text-[11px] uppercase leading-relaxed tracking-widest text-[#4b5563] sm:text-[13px] sm:tracking-[0.16em]"
        >
          © 2026 Associated Pest Control
        </p>
      </div>
    </aside>
  </transition>
</template>

<script>
export default {
  name: 'NavbarVue',

  emits: ['open-contact-modal', 'open-login-modal', 'open-coming-soon-modal'],

  data() {
    return {
      menuOpen: false,
      isAuthenticated: false,
      currentRole: null,

      menuItems: [
        {
          label: 'Home',
          to: '/',
          hasArrow: false,
        },
        {
          label: 'Our Services',
          to: {
            path: '/',
            hash: '#bestsellers',
          },
          hasArrow: true,
        },
        {
          label: 'Announcement',
          action: 'announcement',
          hasArrow: true,
        },
        {
          label: 'Contact Us',
          action: 'contact',
          hasArrow: false,
        },
        {
          label: 'Account',
          action: 'account',
          hasArrow: false,
        },
      ],
    }
  },

  watch: {
    '$route.fullPath'() {
      if (this.menuOpen) {
        this.closeMenu(false)
      }
    },
  },

  methods: {
    async checkAuth() {
      const baseURL = import.meta.env.VITE_API_BASE_URL

      try {
        const res = await fetch(`${baseURL}/api/auth/check.php`, {
          credentials: 'include',
        })

        if (!res.ok) {
          throw new Error(`Authentication request failed: ${res.status}`)
        }

        const data = await res.json()

        this.isAuthenticated = Boolean(data.authenticated)
        this.currentRole = data.role || null
      } catch (error) {
        console.error('Navbar auth check failed:', error)

        this.isAuthenticated = false
        this.currentRole = null
      }
    },

    redirectToAccount() {
      if (!this.isAuthenticated) {
        this.$emit('open-login-modal')
        return
      }

      const roleRoutes = {
        user: '/user/dashboard',
        admin: '/admin/dashboard',
        superadmin: '/superadmin/dashboard',
        cashier: '/cashier/dashboard',
      }

      const destination = roleRoutes[this.currentRole]

      if (destination) {
        this.$router.push(destination)
        return
      }

      this.$emit('open-login-modal')
    },

    openMenu() {
      this.menuOpen = true
      document.body.classList.add('no-scroll')

      this.$nextTick(() => {
        this.$refs.closeMenuButton?.focus()
      })
    },

    closeMenu(restoreFocus = true) {
      this.menuOpen = false
      document.body.classList.remove('no-scroll')

      if (restoreFocus) {
        this.$nextTick(() => {
          this.$refs.menuButton?.focus()
        })
      }
    },

    handleEscape(event) {
      if (event.key === 'Escape' && this.menuOpen) {
        this.closeMenu()
      }
    },

    async handleAccountClick() {
      await this.checkAuth()
      this.redirectToAccount()
    },

    async handleMenuAction(action) {
      if (action === 'contact') {
        this.closeMenu(false)
        this.$emit('open-contact-modal')
        return
      }

      if (action === 'announcement') {
        this.closeMenu(false)

        this.$emit('open-coming-soon-modal', {
          featureTitle: 'Announcements',
          description: 'The APESCON announcements page is currently being prepared.',
          iconClass: 'fa-solid fa-bullhorn',
        })

        return
      }

      if (action === 'account') {
        this.closeMenu(false)
        await this.checkAuth()
        this.redirectToAccount()
      }
    },
  },

  async mounted() {
    window.addEventListener('keydown', this.handleEscape)
    await this.checkAuth()
  },

  beforeUnmount() {
    window.removeEventListener('keydown', this.handleEscape)
    document.body.classList.remove('no-scroll')
  },
}
</script>

<style scoped>
/* Mobile sidebar viewport height */
.sidebar-panel {
  height: 100vh;
  height: 100svh;
  padding-bottom: env(safe-area-inset-bottom);
}

/* Use dynamic viewport height on supported browsers */
@supports (height: 100dvh) {
  .sidebar-panel {
    height: 100dvh;
  }
}

/* Prevent the close button from being covered by an iPhone notch */
.sidebar-top {
  padding-top: max(1rem, env(safe-area-inset-top));
}

/* Allow menu items to scroll on short mobile screens */
.sidebar-nav {
  min-height: 0;
  overflow-y: auto;
  overscroll-behavior-y: contain;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}

/* Original sidebar animation speed */
.sidebar-slide-enter-active,
.sidebar-slide-leave-active {
  transition: all 0.35s ease;
}

.sidebar-slide-enter-from,
.sidebar-slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

.sidebar-slide-enter-to,
.sidebar-slide-leave-from {
  transform: translateX(0);
  opacity: 1;
}

/* Original overlay animation speed */
.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 0.25s ease;
}

.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}

/* Original menu item animation */
.sidebar-menu-list li {
  opacity: 0;
  transform: translateY(12px);
  animation: menuItemFade 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes menuItemFade {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Prevent the page behind the sidebar from scrolling */
:global(body.no-scroll) {
  overflow: hidden;
  overscroll-behavior: none;
}

/* Improve sidebar usability on very short screens */
@media (max-height: 500px) {
  .sidebar-top {
    padding-top: max(0.5rem, env(safe-area-inset-top));
    padding-bottom: 0.25rem;
  }

  .sidebar-bottom {
    padding-top: 0.75rem;
    padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
  }
}
</style>
