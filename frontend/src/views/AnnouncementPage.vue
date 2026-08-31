<template>
  <div class="min-h-screen overflow-hidden bg-white" @contextmenu.prevent>
    <Navbar
      @open-contact-modal="showContactModal = true"
      @open-login-modal="showLoginModal = true"
    />

    <ContactUsModal :show="showContactModal" @close="showContactModal = false" />
    <LoginModal :show="showLoginModal" @close="showLoginModal = false" />

    <!-- Privacy Policy / Terms & Conditions Modal -->
    <PrivacyTermsModal
      :show="showLegalModal"
      :initial-tab="legalInitialTab"
      @close="showLegalModal = false"
    />

    <!-- Help & FAQs Modal -->
    <HelpFaqsModal :show="showFaqsModal" @close="showFaqsModal = false" />

    <!-- Mobile spacer for fixed navbar -->
    <div class="h-16 bg-[#eeccdb] md:hidden"></div>

    <!-- HERO -->
    <section
      class="relative min-h-97.5 overflow-hidden bg-[#fff8fb] pt-20 pb-12 sm:min-h-107.5 sm:pt-24 sm:pb-16 md:min-h-125 md:pt-28 md:pb-18"
    >
      <!-- Background image layer -->
      <div class="announcement-hero-bg absolute inset-0 scale-[1.01] bg-cover bg-no-repeat"></div>

      <!-- Premium overlay for readability -->
      <div class="announcement-hero-overlay absolute inset-0"></div>

      <!-- Soft glow accents -->
      <div
        class="pointer-events-none absolute -left-20 top-12 h-72 w-72 rounded-full bg-[#f3c8d9]/35 blur-3xl"
      ></div>

      <div
        class="pointer-events-none absolute left-1/3 bottom-0 hidden h-52 w-52 rounded-full bg-white/60 blur-3xl sm:block"
      ></div>

      <!-- Bottom fade -->
      <div
        class="absolute inset-x-0 bottom-0 h-30 bg-linear-to-b from-transparent via-white/75 to-white sm:h-32"
      ></div>

      <!-- CONTENT -->
      <div
        class="relative z-10 mx-auto flex min-h-77.5 w-full max-w-295 items-center px-6 sm:min-h-85 sm:px-8 md:min-h-97.5 lg:px-10"
      >
        <div class="mx-auto max-w-xl text-center md:mx-0 md:max-w-2xl md:text-left">
          <div
            class="inline-flex max-w-[92vw] items-center rounded-full border border-[#f1d8e2] bg-white/88 px-3 py-2 shadow-[0_8px_24px_rgba(214,112,151,0.08)] backdrop-blur sm:px-4"
          >
            <span
              class="mr-2 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#ffe8f1] text-[#e97aac] sm:h-7 sm:w-7"
            >
              <i class="fa-solid fa-bullhorn text-[11px] sm:text-[12px]"></i>
            </span>

            <span
              class="font-nunito text-[9px] font-bold uppercase tracking-[0.16em] text-[#e97aac] sm:text-[11px] sm:tracking-[0.2em]"
            >
              You Glow Babe Updates
            </span>
          </div>

          <h1
            class="mx-auto mt-5 max-w-85 font-playfair text-[40px] leading-[1.05] text-[#4f5968] sm:max-w-none sm:text-[54px] md:mx-0 md:text-[66px]"
          >
            Announcements
          </h1>

          <p
            class="mx-auto mt-4 max-w-85 font-nunito text-[14px] leading-7 text-[#6f7681] sm:max-w-xl sm:text-[16px] md:mx-0"
          >
            View the latest official promos, product updates, notices, and important announcements
            from You Glow Babe.
          </p>

          <div class="mt-6 hidden flex-wrap justify-center gap-3 sm:flex md:justify-start">
            <span class="announcement-hero-chip">
              <i class="fa-solid fa-star"></i>
              Official Updates
            </span>

            <span class="announcement-hero-chip">
              <i class="fa-solid fa-tags"></i>
              Promos & Notices
            </span>

            <span class="announcement-hero-chip">
              <i class="fa-solid fa-box-open"></i>
              Product News
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- MAIN -->
    <section class="relative bg-[#fffdfd] py-10 sm:py-12 md:py-14">
      <div class="mx-auto max-w-295 px-6 sm:px-8 lg:px-10">
        <!-- SEARCH CARD -->
        <div
          class="relative overflow-hidden rounded-[28px] border border-white/50 bg-white/80 shadow-[0_20px_60px_rgba(0,0,0,0.07)] backdrop-blur-xl"
        >
          <div
            class="absolute inset-0 bg-linear-to-br from-[#f8edf2] via-[#fffafb] to-[#f4e5ec]"
          ></div>
          <div
            class="absolute -top-10 -left-10 h-32 w-32 rounded-full bg-[#f3c8d9]/35 blur-3xl"
          ></div>
          <div
            class="absolute bottom-0 right-0 h-40 w-40 rounded-full bg-[#edd6df]/40 blur-3xl"
          ></div>

          <div class="relative z-10 p-5 sm:p-6 md:p-8">
            <div class="mx-auto max-w-4xl">
              <div class="mb-6 text-center">
                <p
                  class="font-nunito text-[11px] font-semibold uppercase tracking-[0.22em] text-[#b38497] sm:text-[12px] sm:tracking-[0.3em]"
                >
                  Search Announcements
                </p>
                <h2
                  class="mt-2 font-playfair text-[26px] text-[#5f6670] sm:text-[30px] md:text-[34px]"
                >
                  Find an Official Update
                </h2>
              </div>

              <div
                class="rounded-[18px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)] sm:px-5 sm:py-4"
              >
                <label
                  class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                >
                  Search Title
                </label>
                <input
                  v-model="filters.q"
                  type="text"
                  placeholder="Enter announcement title"
                  class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] outline-none placeholder:text-[#a2a8b1]"
                  @keyup.enter="fetchAnnouncements(1)"
                />
              </div>

              <div class="mt-5 flex flex-row items-center justify-center gap-3">
                <button
                  @click="fetchAnnouncements(1)"
                  :disabled="loading"
                  class="inline-flex h-12 items-center justify-center rounded-full bg-[#e97aac] px-7 font-nunito text-[14px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] hover:shadow-[0_12px_24px_rgba(233,122,172,0.24)] disabled:cursor-not-allowed disabled:opacity-70"
                >
                  <i class="fa-solid fa-magnifying-glass mr-2"></i>
                  {{ loading ? 'Searching...' : 'Search' }}
                </button>

                <button
                  @click="resetFilters"
                  class="inline-flex h-12 items-center justify-center rounded-full border border-[#e8dbe1] bg-white px-7 font-nunito text-[13px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
                >
                  Reset
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- RESULTS HEADER -->
        <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p
              class="font-nunito text-[11px] font-semibold uppercase tracking-[0.22em] text-[#b38497] sm:text-[12px] sm:tracking-[0.3em]"
            >
              Official Announcements
            </p>
            <h3 class="mt-2 font-playfair text-[26px] text-[#5f6670] sm:text-[30px]">
              Latest Updates
            </h3>
          </div>

          <div class="font-nunito text-[14px] text-[#8a8f99]">
            <span class="font-semibold text-[#5f6670]">{{ total }}</span>
            announcement<span v-if="total !== 1">s</span> found
          </div>
        </div>

        <!-- TABLE CARD -->
        <div
          class="mt-5 overflow-hidden rounded-[28px] border border-[#f0e4e9] bg-white shadow-[0_18px_40px_rgba(0,0,0,0.05)]"
        >
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-[#fcf4f7]">
                <tr class="text-left">
                  <th
                    class="px-5 py-4 font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    No.
                  </th>
                  <th
                    class="px-5 py-4 font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Announcement Title
                  </th>
                  <th
                    class="px-5 py-4 font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Date Posted
                  </th>
                  <th
                    class="px-5 py-4 text-right font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Action
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr v-if="loading">
                  <td colspan="4" class="px-5 py-14 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <div class="loader-pink"></div>
                      <p class="mt-4 font-nunito text-[14px] text-[#8a8f99]">
                        Loading announcements...
                      </p>
                    </div>
                  </td>
                </tr>

                <tr
                  v-for="(announcement, index) in rows"
                  :key="announcement.id"
                  class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
                >
                  <td class="px-5 py-4 font-nunito text-[14px] text-[#7d838d]">
                    #{{ rowNumber(index) }}
                  </td>

                  <td class="px-5 py-4">
                    <p class="font-nunito text-[15px] font-semibold text-[#5f6670]">
                      {{ announcement.title }}
                    </p>
                    <p class="mt-1 font-nunito text-[13px] text-[#9aa0a9]">
                      Official You Glow Babe announcement
                    </p>
                  </td>

                  <td class="px-5 py-4 font-nunito text-[14px] text-[#7d838d]">
                    {{ formatDate(announcement.created_at) }}
                  </td>

                  <td class="px-5 py-4 text-right">
                    <button
                      @click="viewAnnouncement(announcement.id)"
                      :disabled="viewLoadingId === Number(announcement.id)"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-[#eadce2] bg-white px-5 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                      <i class="fa-regular fa-eye mr-2"></i>
                      {{ viewLoadingId === Number(announcement.id) ? 'Loading...' : 'View' }}
                    </button>
                  </td>
                </tr>

                <tr v-if="!loading && rows.length === 0">
                  <td colspan="4" class="px-5 py-14 text-center">
                    <div class="mx-auto max-w-md">
                      <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#f8edf2] text-[#e97aac]"
                      >
                        <i class="fa-solid fa-bullhorn text-[24px]"></i>
                      </div>

                      <h4 class="mt-4 font-playfair text-[24px] text-[#5f6670]">
                        No Announcements Found
                      </h4>

                      <p class="mt-3 font-nunito text-[14px] leading-7 text-[#8a8f99]">
                        We couldn’t find any active announcements matching your search.
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- PAGINATION -->
          <div
            v-if="!loading && totalPages > 1"
            class="flex flex-col items-center justify-between gap-3 border-t border-[#f3e9ee] px-5 py-4 sm:flex-row"
          >
            <div class="font-nunito text-[14px] text-[#8a8f99]">
              Showing page <span class="font-semibold text-[#5f6670]">{{ page }}</span> of
              <span class="font-semibold text-[#5f6670]">{{ totalPages }}</span>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="fetchAnnouncements(page - 1)"
                :disabled="page <= 1"
                class="inline-flex h-10 items-center justify-center rounded-full border border-[#e8dbe1] bg-white px-4 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac] disabled:cursor-not-allowed disabled:opacity-40"
              >
                Prev
              </button>

              <button
                @click="fetchAnnouncements(page + 1)"
                :disabled="page >= totalPages"
                class="inline-flex h-10 items-center justify-center rounded-full bg-[#e97aac] px-4 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] disabled:cursor-not-allowed disabled:opacity-40"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <myFooterShort
      @open-contact-modal="showContactModal = true"
      @open-faqs-modal="showFaqsModal = true"
      @open-privacy-modal="openPrivacyModal"
      @open-terms-modal="openTermsModal"
    />

    <!-- ANNOUNCEMENT MODAL -->
    <transition name="announcement-fade">
      <div
        v-if="selected"
        class="fixed inset-0 z-210 flex items-center justify-center bg-[#2b1f25]/35 p-3 backdrop-blur-md sm:p-4 md:p-6"
        @click.self="closeAnnouncementModal"
      >
        <div
          class="relative z-10 max-h-[92vh] w-[94vw] overflow-y-auto rounded-[28px] border border-[#f0e4e9] bg-linear-to-br from-[#f8edf2] via-[#fffafb] to-[#f4e5ec] shadow-[0_20px_60px_rgba(0,0,0,0.16)] sm:w-[90vw] md:w-[86vw] lg:w-250"
          :class="announcementModalClosing ? 'animate-modal-out' : 'animate-modal-in'"
        >
          <div
            class="pointer-events-none absolute -top-10 -left-10 h-36 w-36 rounded-full bg-[#f3c8d9]/35 blur-3xl"
          ></div>
          <div
            class="pointer-events-none absolute bottom-0 right-0 h-44 w-44 rounded-full bg-[#edd6df]/40 blur-3xl"
          ></div>

          <div class="relative z-10 px-4 pb-5 pt-5 sm:px-6 sm:pb-7 sm:pt-6 md:px-8 md:pb-8">
            <button
              @click="closeAnnouncementModal"
              aria-label="Close announcement"
              class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-[#eadce2] bg-white/90 text-[#6b7280] transition duration-200 hover:border-[#e97aac] hover:text-[#e97aac] hover:shadow-md sm:right-5 sm:top-5"
            >
              <i class="fa-solid fa-xmark text-[16px] sm:text-[18px]"></i>
            </button>

            <div class="pr-12">
              <p
                class="font-nunito text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b38497] sm:text-[12px]"
              >
                Official Announcement
              </p>

              <h3
                class="mt-2 font-playfair text-[26px] leading-tight text-[#5f6670] sm:text-[34px] md:text-[40px]"
              >
                {{ selected.title }}
              </h3>

              <p class="mt-2 font-nunito text-[14px] text-[#8a8f99]">
                Posted on {{ formatDate(selected.created_at) }}
              </p>
            </div>

            <!-- This image only exists after user clicks View -->
            <div
              class="mx-auto mt-6 max-w-135 overflow-hidden rounded-3xl border border-[#f0d4e0] bg-white shadow-[0_16px_40px_rgba(0,0,0,0.08)]"
            >
              <div class="aspect-4/5 w-full bg-[#fff7fa]">
                <img
                  v-if="selected.image"
                  :src="getAnnouncementImage(selected.image)"
                  :alt="selected.title"
                  class="h-full w-full object-contain"
                  loading="lazy"
                  draggable="false"
                  @contextmenu.prevent
                  @dragstart.prevent
                />
              </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <!-- <p class="font-nunito text-[13px] leading-6 text-[#8a8f99]">
                This announcement is displayed directly from the official You Glow Babe update
                records.
              </p> -->

              <button
                @click="closeAnnouncementModal"
                class="inline-flex h-11 items-center justify-center rounded-full bg-[#e97aac] px-6 font-nunito text-[13px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799]"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import Navbar from '../components/myNavbar.vue'
import myFooterShort from '../components/myFooter.vue'
import LoginModal from '../components/LoginModal.vue'
import ContactUsModal from '@/components/ContactUsModal.vue'
import PrivacyTermsModal from '@/components/PrivacyTermsModal.vue'
import HelpFaqsModal from '@/components/HelpFaqsModal.vue'

export default {
  name: 'AnnouncementPage',

  components: {
    Navbar,
    myFooterShort,
    ContactUsModal,
    LoginModal,
    PrivacyTermsModal,
    HelpFaqsModal,
  },

  data() {
    return {
      showContactModal: false,
      showLoginModal: false,
      showFaqsModal: false,
      showLegalModal: false,
      legalInitialTab: 'privacy',
      loading: false,
      viewLoadingId: null,

      rows: [],
      selected: null,
      announcementModalClosing: false,

      filters: {
        q: '',
      },

      page: 1,
      total: 0,
      totalPages: 1,
      limit: 10,
    }
  },

  methods: {
    async fetchAnnouncements(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
        })

        const res = await fetch(`${baseURL}/api/announcement/list.php?${params.toString()}`)
        const data = await res.json()

        if (data.success) {
          this.rows = data.rows || []
          this.page = Number(data.page || 1)
          this.total = Number(data.total || 0)
          this.totalPages = Number(data.totalPages || 1)
        } else {
          this.rows = []
          this.total = 0
          this.totalPages = 1
        }
      } catch (error) {
        console.error('Failed to fetch announcements:', error)
        this.rows = []
        this.total = 0
        this.totalPages = 1
      } finally {
        this.loading = false
      }
    },

    openPrivacyModal() {
      this.legalInitialTab = 'privacy'
      this.showLegalModal = true
    },

    openTermsModal() {
      this.legalInitialTab = 'terms'
      this.showLegalModal = true
    },

    async viewAnnouncement(id) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.viewLoadingId = Number(id)

      try {
        const res = await fetch(`${baseURL}/api/announcement/view.php?id=${encodeURIComponent(id)}`)
        const data = await res.json()

        if (data.success) {
          this.selected = data.data
          this.announcementModalClosing = false
          document.body.style.overflow = 'hidden'
        }
      } catch (error) {
        console.error('Failed to load announcement details:', error)
      } finally {
        this.viewLoadingId = null
      }
    },

    async closeAnnouncementModal() {
      if (this.announcementModalClosing) return

      this.announcementModalClosing = true
      await new Promise((resolve) => setTimeout(resolve, 220))

      this.selected = null
      this.announcementModalClosing = false
      document.body.style.overflow = ''
    },

    resetFilters() {
      this.filters.q = ''
      this.fetchAnnouncements(1)
    },

    rowNumber(index) {
      return (this.page - 1) * this.limit + index + 1
    },

    formatDate(value) {
      if (!value) return '—'

      const date = new Date(value)
      if (Number.isNaN(date.getTime())) return value

      return date.toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      })
    },

    getAnnouncementImage(file) {
      if (!file) return ''
      return `/uploads/announcements/${file}`
    },
  },

  mounted() {
    this.fetchAnnouncements(1)
  },

  beforeUnmount() {
    document.body.style.overflow = ''
  },
}
</script>

<style scoped>
.loader-pink {
  width: 38px;
  height: 38px;
  border: 3px solid rgba(233, 122, 172, 0.18);
  border-top-color: #e97aac;
  border-radius: 9999px;
  animation: spinLoader 0.8s linear infinite;
}

@keyframes spinLoader {
  to {
    transform: rotate(360deg);
  }
}

.announcement-fade-enter-active,
.announcement-fade-leave-active {
  transition: opacity 0.22s ease;
}

.announcement-fade-enter-from,
.announcement-fade-leave-to {
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

.announcement-hero-bg {
  background-image: url('/assets/announcement-hero-background.webp');
  background-position: 72% center;
  opacity: 0.82;
}

.announcement-hero-overlay {
  background:
    linear-gradient(
      180deg,
      rgba(255, 248, 251, 0.54) 0%,
      rgba(255, 248, 251, 0.44) 50%,
      rgba(255, 255, 255, 0.9) 100%
    ),
    linear-gradient(
      90deg,
      rgba(255, 248, 251, 0.76) 0%,
      rgba(255, 248, 251, 0.48) 52%,
      rgba(255, 248, 251, 0.18) 100%
    );
}

.announcement-hero-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  border-radius: 999px;
  border: 1px solid #f1d8e2;
  background: rgba(255, 255, 255, 0.78);
  padding: 0.55rem 0.9rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #d96799;
  box-shadow: 0 10px 24px rgba(214, 112, 151, 0.08);
  backdrop-filter: blur(8px);
}

.announcement-hero-chip i {
  font-size: 11px;
  color: #e97aac;
}

@media (min-width: 640px) {
  .announcement-hero-bg {
    background-position: center right;
    opacity: 0.92;
  }

  .announcement-hero-overlay {
    background: linear-gradient(
      90deg,
      rgba(255, 248, 251, 0.98) 0%,
      rgba(255, 248, 251, 0.92) 30%,
      rgba(255, 248, 251, 0.62) 55%,
      rgba(255, 248, 251, 0.16) 100%
    );
  }
}

@media (min-width: 768px) {
  .announcement-hero-bg {
    opacity: 1;
  }
}
</style>
