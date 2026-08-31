<template>
  <div class="bg-white overflow-hidden min-h-screen">
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

    <!-- Mobile spacer -->
    <div class="bg-[#eeccdb] h-16 md:hidden"></div>

    <!-- HERO -->
    <section
      class="relative min-h-91.25 overflow-hidden bg-[#fff8fb] pt-20 pb-12 sm:min-h-100 sm:pt-24 sm:pb-16 md:min-h-120 md:pt-28 md:pb-18"
    >
      <!-- Background image layer -->
      <div class="fake-seller-hero-bg absolute inset-0 scale-[1.01] bg-cover bg-no-repeat"></div>

      <!-- Responsive overlay for readability -->
      <div class="fake-seller-hero-overlay absolute inset-0"></div>

      <!-- Soft warning glow -->
      <div
        class="pointer-events-none absolute -left-20 top-14 hidden h-72 w-72 rounded-full bg-[#f8bfd3]/35 blur-3xl sm:block"
      ></div>
      <div
        class="pointer-events-none absolute left-1/3 bottom-0 hidden h-52 w-52 rounded-full bg-white/60 blur-3xl sm:block"
      ></div>

      <!-- Bottom fade -->
      <div
        class="absolute inset-x-0 bottom-0 h-28 bg-linear-to-b from-transparent via-white/80 to-white sm:h-32"
      ></div>

      <!-- CONTENT -->
      <div
        class="relative z-10 mx-auto flex min-h-75 w-full max-w-295 items-center px-6 sm:min-h-78.75 sm:px-8 md:min-h-95 lg:px-10"
      >
        <div class="mx-auto max-w-xl text-center md:mx-0 md:max-w-2xl md:text-left">
          <div
            class="inline-flex max-w-[92vw] items-center rounded-full border border-[#f0d8e2] bg-white/90 px-3 py-2 shadow-[0_8px_24px_rgba(201,79,119,0.08)] backdrop-blur sm:px-4"
          >
            <span
              class="mr-2 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#fff0f5] text-[#c94f77] sm:h-7 sm:w-7"
            >
              <i class="fa-solid fa-triangle-exclamation text-[11px] sm:text-[12px]"></i>
            </span>

            <span
              class="font-nunito text-[9px] font-bold uppercase tracking-[0.14em] text-[#c94f77] sm:text-[11px] sm:tracking-[0.2em]"
            >
              Consumer Protection Directory
            </span>
          </div>

          <h1
            class="mx-auto mt-5 max-w-82.5 font-playfair text-[36px] leading-[1.05] text-[#4f5968] sm:max-w-none sm:text-[52px] md:mx-0 md:text-[64px]"
          >
            Fake Seller Watchlist
          </h1>

          <p
            class="mx-auto mt-4 max-w-82.5 font-nunito text-[14px] leading-7 text-[#6f7681] sm:max-w-xl sm:text-[16px] md:mx-0"
          >
            Check reported unofficial or unauthorized online sellers before purchasing You Glow Babe
            products.
          </p>

          <div class="mt-6 hidden flex-wrap justify-center gap-3 sm:flex md:justify-start">
            <span class="fake-hero-chip">
              <i class="fa-solid fa-shield-heart"></i>
              Buyer Awareness
            </span>

            <span class="fake-hero-chip">
              <i class="fa-solid fa-ban"></i>
              Reported Shops
            </span>

            <span class="fake-hero-chip">
              <i class="fa-solid fa-bag-shopping"></i>
              Platform Listings
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- SEARCH -->
    <section class="relative bg-[#fffdfd] py-10 sm:py-12 md:py-14">
      <div class="max-w-295 mx-auto px-6 sm:px-8 lg:px-10">
        <div
          class="relative overflow-hidden rounded-[30px] border border-[#f1e3e9] bg-white shadow-[0_20px_60px_rgba(0,0,0,0.06)]"
        >
          <div
            class="absolute inset-0 bg-linear-to-br from-[#fffafb] via-[#fffefe] to-[#fff4f8]"
          ></div>
          <div
            class="absolute -top-12 -right-10 h-36 w-36 rounded-full bg-[#f9d3e1]/30 blur-3xl"
          ></div>
          <div
            class="absolute bottom-0 left-0 h-36 w-36 rounded-full bg-[#f3c8d9]/25 blur-3xl"
          ></div>

          <div class="relative z-10 p-5 sm:p-6 md:p-8">
            <div class="text-center mb-6">
              <p
                class="font-nunito text-[11px] sm:text-[12px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.3em] text-[#b38497]"
              >
                Search Fake Seller Directory
              </p>
              <h2
                class="mt-2 font-playfair text-[26px] sm:text-[30px] md:text-[34px] text-[#5f6670]"
              >
                Search Reported Listings
              </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
              <div class="lg:col-span-6">
                <div
                  class="rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                >
                  <label
                    class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                  >
                    Search Shop Name
                  </label>
                  <input
                    v-model="filters.q"
                    type="text"
                    placeholder="Enter shop name"
                    class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] placeholder:text-[#a2a8b1] outline-none"
                    @keyup.enter="fetchFakeSellers(1)"
                  />
                </div>
              </div>

              <div class="lg:col-span-3">
                <div
                  class="rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                >
                  <label
                    class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                  >
                    Platform
                  </label>
                  <select
                    v-model="filters.platform"
                    class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] outline-none"
                  >
                    <option value="">All Platforms</option>
                    <option v-for="platform in platformOptions" :key="platform" :value="platform">
                      {{ platform }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="lg:col-span-3">
                <div
                  class="rounded-[20px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                >
                  <label
                    class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                  >
                    Product
                  </label>
                  <select
                    v-model="filters.product_type"
                    class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] outline-none"
                  >
                    <option value="">All Products</option>
                    <option v-for="product in productOptions" :key="product" :value="product">
                      {{ product }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="mt-5 flex flex-row items-center justify-center gap-3">
              <button
                @click="fetchFakeSellers(1)"
                :disabled="loading"
                class="inline-flex h-12 items-center justify-center rounded-full bg-[#c94f77] px-7 font-nunito text-[14px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#b64269] hover:shadow-[0_12px_24px_rgba(201,79,119,0.22)] disabled:opacity-70 disabled:cursor-not-allowed"
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

        <!-- RESULTS -->
        <div class="mt-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
          <div>
            <p
              class="font-nunito text-[11px] sm:text-[12px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.3em] text-[#b38497]"
            >
              Watchlist Results
            </p>
            <h3 class="mt-2 font-playfair text-[26px] sm:text-[30px] text-[#5f6670]">
              Reported Fake Sellers
            </h3>
          </div>

          <div class="font-nunito text-[14px] text-[#8a8f99]">
            <span class="font-semibold text-[#5f6670]">{{ total }}</span> result<span
              v-if="total !== 1"
              >s</span
            >
            found
          </div>
        </div>

        <!-- CARD GRID -->
        <div class="mt-6">
          <div v-if="loading" class="py-16 text-center">
            <div class="flex flex-col items-center justify-center">
              <div class="loader-pink"></div>
              <p class="mt-4 font-nunito text-[14px] text-[#8a8f99]">Loading watchlist...</p>
            </div>
          </div>

          <div
            v-else-if="rows.length > 0"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"
          >
            <article
              v-for="item in rows"
              :key="item.id"
              class="group relative overflow-hidden rounded-[26px] border border-[#f1e3e9] bg-white shadow-[0_16px_36px_rgba(0,0,0,0.05)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_46px_rgba(0,0,0,0.08)]"
            >
              <div
                class="absolute inset-x-0 top-0 h-1.5 bg-linear-to-r from-[#f2bfd0] via-[#e97aac] to-[#d24f7e]"
              ></div>

              <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <span
                      class="inline-flex items-center rounded-full border border-[#f0d8e2] bg-[#fff7fa] px-3 py-1 font-nunito text-[11px] font-semibold uppercase tracking-[0.12em] text-[#b06a86]"
                    >
                      {{ item.platform }}
                    </span>

                    <h4
                      class="mt-4 font-playfair text-[24px] leading-tight text-[#5f6670] wrap-break-word"
                    >
                      {{ item.shop_name }}
                    </h4>
                  </div>

                  <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#fff0f5] text-[#d14b7f]"
                  >
                    <i class="fa-solid fa-ban text-[16px]"></i>
                  </div>
                </div>

                <div class="mt-5 space-y-3">
                  <div class="rounded-[18px] border border-[#f5e7ec] bg-[#fffafb] p-4">
                    <p class="font-nunito text-[11px] uppercase tracking-[0.14em] text-[#b38497]">
                      Product
                    </p>
                    <p class="mt-1 font-nunito text-[15px] font-semibold text-[#5f6670]">
                      {{ item.product_type }}
                    </p>
                  </div>

                  <div class="rounded-[18px] border border-[#f5e7ec] bg-[#fffafb] p-4">
                    <p class="font-nunito text-[11px] uppercase tracking-[0.14em] text-[#b38497]">
                      Shop Link
                    </p>
                    <p class="mt-1 font-nunito text-[14px] break-all text-[#6f7680]">
                      {{ item.shop_link }}
                    </p>
                  </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                  <a
                    :href="normalizeLink(item.shop_link)"
                    target="_blank"
                    rel="noopener noreferrer nofollow"
                    class="inline-flex h-11 items-center justify-center rounded-full bg-[#c94f77] px-5 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#b64269]"
                  >
                    Visit Link
                  </a>
                </div>
              </div>
            </article>
          </div>

          <div v-else class="py-16 text-center">
            <div class="max-w-md mx-auto">
              <div
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#fff0f5] text-[#d14b7f]"
              >
                <i class="fa-solid fa-circle-exclamation text-[24px]"></i>
              </div>
              <h4 class="mt-4 font-playfair text-[24px] text-[#5f6670]">No Fake Sellers Found</h4>
              <p class="mt-3 font-nunito text-[14px] leading-7 text-[#8a8f99]">
                We couldn’t find any reported fake seller matching your search criteria.
              </p>
            </div>
          </div>
        </div>

        <!-- PAGINATION -->
        <div
          v-if="!loading && totalPages > 1"
          class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-3"
        >
          <div class="font-nunito text-[14px] text-[#8a8f99]">
            Showing page <span class="font-semibold text-[#5f6670]">{{ page }}</span> of
            <span class="font-semibold text-[#5f6670]">{{ totalPages }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="fetchFakeSellers(page - 1)"
              :disabled="page <= 1"
              class="inline-flex h-10 items-center justify-center rounded-full border border-[#e8dbe1] bg-white px-4 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac] disabled:opacity-40 disabled:cursor-not-allowed"
            >
              Prev
            </button>

            <button
              @click="fetchFakeSellers(page + 1)"
              :disabled="page >= totalPages"
              class="inline-flex h-10 items-center justify-center rounded-full bg-[#c94f77] px-4 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#b64269] disabled:opacity-40 disabled:cursor-not-allowed"
            >
              Next
            </button>
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
  name: 'FakeSeller',
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
      rows: [],

      filters: {
        q: '',
        platform: '',
        product_type: '',
      },

      page: 1,
      total: 0,
      totalPages: 1,
      limit: 6,

      platformOptions: [],
      productOptions: [],
    }
  },

  methods: {
    async fetchFakeSellers(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
          platform: this.filters.platform || '',
          product_type: this.filters.product_type || '',
        })

        const res = await fetch(`${baseURL}/api/fakeseller/list.php?${params.toString()}`)
        const data = await res.json()

        if (data.success) {
          this.rows = Array.isArray(data.rows) ? data.rows : []
          this.page = Number(data.page || 1)
          this.total = Number(data.total || 0)
          this.totalPages = Number(data.totalPages || 1)
          this.platformOptions = Array.isArray(data.platformOptions) ? data.platformOptions : []
          this.productOptions = Array.isArray(data.productOptions) ? data.productOptions : []
        } else {
          this.rows = []
          this.total = 0
          this.totalPages = 1
        }
      } catch (error) {
        console.error('Failed to fetch fake sellers:', error)
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
    resetFilters() {
      this.filters.q = ''
      this.filters.platform = ''
      this.filters.product_type = ''
      this.fetchFakeSellers(1)
    },

    normalizeLink(link) {
      if (!link) return '#'
      const trimmed = String(link).trim()

      if (/^https?:\/\//i.test(trimmed)) {
        return trimmed
      }

      return `https://${trimmed}`
    },
  },

  mounted() {
    this.fetchFakeSellers()
  },
}
</script>

<style scoped>
.fake-seller-hero-bg {
  background-image: url('/assets/fake-seller-background.webp');
  background-position: 70% center;
  opacity: 0.82;
}

.fake-seller-hero-overlay {
  background:
    linear-gradient(
      180deg,
      rgba(255, 248, 251, 0.48) 0%,
      rgba(255, 248, 251, 0.42) 48%,
      rgba(255, 255, 255, 0.88) 100%
    ),
    linear-gradient(
      90deg,
      rgba(255, 248, 251, 0.56) 0%,
      rgba(255, 248, 251, 0.36) 55%,
      rgba(255, 248, 251, 0.18) 100%
    );
}

.fake-hero-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  border-radius: 999px;
  border: 1px solid #f0d8e2;
  background: rgba(255, 255, 255, 0.78);
  padding: 0.55rem 0.9rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #c94f77;
  box-shadow: 0 10px 24px rgba(201, 79, 119, 0.08);
  backdrop-filter: blur(8px);
}

.fake-hero-chip i {
  font-size: 11px;
  color: #c94f77;
}

@media (min-width: 640px) {
  .fake-seller-hero-bg {
    background-position: center right;
    opacity: 0.82;
  }

  .fake-seller-hero-overlay {
    background: linear-gradient(
      90deg,
      rgba(255, 248, 251, 0.98) 0%,
      rgba(255, 248, 251, 0.94) 30%,
      rgba(255, 248, 251, 0.72) 55%,
      rgba(255, 248, 251, 0.22) 100%
    );
  }
}

@media (min-width: 640px) {
  .fake-seller-hero-bg {
    background-position: center right;
    opacity: 0.92;
  }

  .fake-seller-hero-overlay {
    background: linear-gradient(
      90deg,
      rgba(255, 248, 251, 0.97) 0%,
      rgba(255, 248, 251, 0.9) 30%,
      rgba(255, 248, 251, 0.58) 55%,
      rgba(255, 248, 251, 0.14) 100%
    );
  }
}

@media (min-width: 768px) {
  .fake-seller-hero-bg {
    opacity: 1;
  }
}

.loader-pink {
  width: 38px;
  height: 38px;
  border: 3px solid rgba(233, 122, 172, 0.18);
  border-top-color: #c94f77;
  border-radius: 9999px;
  animation: spinLoader 0.8s linear infinite;
}

@keyframes spinLoader {
  to {
    transform: rotate(360deg);
  }
}
</style>
