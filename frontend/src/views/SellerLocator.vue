<template>
  <div class="bg-white overflow-hidden min-h-screen" @contextmenu.prevent>
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
    <div class="bg-[#eeccdb] h-16 md:hidden"></div>

    <!-- HERO -->
    <section
      class="relative min-h-105 overflow-hidden bg-[#fff8fb] pt-22 pb-14 sm:pt-24 sm:pb-16 md:min-h-120 md:pt-28 md:pb-18"
    >
      <!-- Background image layer -->
      <div
        class="absolute inset-0 scale-[1.01] bg-cover bg-no-repeat opacity-100"
        style="
          background-image: url('/assets/seller-locator-background.webp');
          background-position: center right;
        "
      ></div>

      <!-- Premium overlay: clear left side, visible products on right -->
      <div
        class="absolute inset-0"
        style="
          background: linear-gradient(
            90deg,
            rgba(255, 248, 251, 0.98) 0%,
            rgba(255, 248, 251, 0.96) 30%,
            rgba(255, 248, 251, 0.78) 52%,
            rgba(255, 248, 251, 0.28) 100%
          );
        "
      ></div>

      <!-- Soft glow accents -->
      <div class="absolute -left-20 top-12 h-72 w-72 rounded-full bg-[#f3c8d9]/35 blur-3xl"></div>
      <div class="absolute left-1/3 bottom-0 h-52 w-52 rounded-full bg-white/60 blur-3xl"></div>

      <!-- Bottom fade -->
      <div
        class="absolute inset-x-0 bottom-0 h-32 bg-linear-to-b from-transparent via-white/75 to-white"
      ></div>

      <!-- CONTENT -->
      <div
        class="relative z-10 mx-auto flex min-h-82.5 w-full max-w-295 items-center px-6 sm:px-8 md:min-h-95 lg:px-10"
      >
        <div class="max-w-2xl text-center md:text-left">
          <div
            class="inline-flex items-center rounded-full border border-[#f1d8e2] bg-white/80 px-4 py-2 shadow-[0_8px_24px_rgba(214,112,151,0.08)] backdrop-blur"
          >
            <span
              class="mr-2 flex h-7 w-7 items-center justify-center rounded-full bg-[#ffe8f1] text-[#e97aac]"
            >
              <i class="fa-solid fa-location-dot text-[12px]"></i>
            </span>

            <span
              class="font-nunito text-[10px] font-bold uppercase tracking-[0.2em] text-[#e97aac] sm:text-[11px]"
            >
              Official Seller Directory
            </span>
          </div>

          <h1
            class="mt-5 font-playfair text-[42px] leading-[1.05] text-[#4f5968] sm:text-[54px] md:text-[66px]"
          >
            Sellers Locator
          </h1>

          <p
            class="mx-auto mt-4 max-w-xl font-nunito text-[15px] leading-7 text-[#6f7681] sm:text-[16px] md:mx-0"
          >
            Find official and authorized You Glow Babe distributors near you through our verified
            seller directory.
          </p>

          <div class="mt-6 hidden flex-wrap justify-center gap-3 sm:flex md:justify-start">
            <span
              class="inline-flex items-center gap-2 rounded-full border border-[#f1d8e2] bg-white/75 px-4 py-2 font-nunito text-[11px] font-bold uppercase tracking-[0.12em] text-[#d96799] shadow-[0_10px_24px_rgba(214,112,151,0.08)] backdrop-blur"
            >
              <i class="fa-solid fa-circle-check text-[#e97aac]"></i>
              Verified Sellers
            </span>

            <span
              class="inline-flex items-center gap-2 rounded-full border border-[#f1d8e2] bg-white/75 px-4 py-2 font-nunito text-[11px] font-bold uppercase tracking-[0.12em] text-[#d96799] shadow-[0_10px_24px_rgba(214,112,151,0.08)] backdrop-blur"
            >
              <i class="fa-solid fa-map-location-dot text-[#e97aac]"></i>
              Area-Based Search
            </span>

            <span
              class="inline-flex items-center gap-2 rounded-full border border-[#f1d8e2] bg-white/75 px-4 py-2 font-nunito text-[11px] font-bold uppercase tracking-[0.12em] text-[#d96799] shadow-[0_10px_24px_rgba(214,112,151,0.08)] backdrop-blur"
            >
              <i class="fa-solid fa-box-open text-[#e97aac]"></i>
              Product Filters
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- MAIN -->
    <section class="relative bg-[#fffdfd] py-10 sm:py-12 md:py-14">
      <div class="max-w-295 mx-auto px-6 sm:px-8 lg:px-10">
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
            <div class="max-w-5xl mx-auto">
              <div class="text-center mb-6">
                <p
                  class="font-nunito text-[11px] sm:text-[12px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.3em] text-[#b38497]"
                >
                  Search Seller Directory
                </p>
                <h2
                  class="mt-2 font-playfair text-[26px] sm:text-[30px] md:text-[34px] text-[#5f6670]"
                >
                  Find a Verified Seller
                </h2>
              </div>

              <!-- TOP ROW -->
              <div class="grid grid-cols-1 gap-4">
                <div
                  class="rounded-[18px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                >
                  <label
                    class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                  >
                    Search Name or ID
                  </label>
                  <input
                    v-model="filters.q"
                    type="text"
                    placeholder="Enter seller name or seller code"
                    class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] placeholder:text-[#a2a8b1] outline-none"
                    @keyup.enter="fetchSellers(1)"
                  />
                </div>
              </div>

              <!-- BOTTOM ROW -->
              <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Market Scope -->
                <div
                  class="rounded-[18px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                >
                  <label
                    class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                  >
                    Market Scope
                  </label>
                  <select
                    v-model="filters.market_scope"
                    class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] outline-none"
                  >
                    <option value="">All Market Scope</option>
                    <option v-for="scope in marketScopes" :key="scope" :value="scope">
                      {{ scope }}
                    </option>
                  </select>
                </div>

                <!-- Area of Distribution -->
                <div
                  class="rounded-[18px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                >
                  <label
                    class="block font-nunito text-[11px] uppercase tracking-[0.16em] text-[#b38497]"
                  >
                    Area of Distribution
                  </label>
                  <select
                    v-model="filters.area_of_distribution"
                    class="mt-2 w-full bg-transparent font-nunito text-[15px] text-[#5f6670] outline-none"
                  >
                    <option value="">All Areas</option>
                    <option v-for="area in areaOptions" :key="area" :value="area">
                      {{ area }}
                    </option>
                  </select>
                </div>

                <!-- Product -->
                <div
                  class="rounded-[18px] border border-[#f0e4e9] bg-[#fffafb] px-4 py-3 sm:px-5 sm:py-4 transition duration-200 focus-within:border-[#e97aac] focus-within:shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
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
                    <option
                      v-for="product in filteredProductOptions"
                      :key="product"
                      :value="product"
                    >
                      {{ product }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="mt-5 flex flex-row items-center justify-center gap-3">
                <button
                  @click="fetchSellers(1)"
                  :disabled="loading"
                  class="inline-flex h-12 items-center justify-center rounded-full bg-[#e97aac] px-7 font-nunito text-[14px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] hover:shadow-[0_12px_24px_rgba(233,122,172,0.24)] disabled:opacity-70 disabled:cursor-not-allowed"
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
        <div class="mt-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
          <div>
            <p
              class="font-nunito text-[11px] sm:text-[12px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.3em] text-[#b38497]"
            >
              Authorized Sellers
            </p>
            <h3 class="mt-2 font-playfair text-[26px] sm:text-[30px] text-[#5f6670]">
              Directory Results
            </h3>
          </div>

          <div class="font-nunito text-[14px] text-[#8a8f99]">
            <span class="font-semibold text-[#5f6670]">{{ total }}</span> seller<span
              v-if="total !== 1"
              >s</span
            >
            found
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
                    Seller ID
                  </th>
                  <th
                    class="px-5 py-4 font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Full Name
                  </th>
                  <th
                    class="px-5 py-4 font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Contact Number
                  </th>
                  <th
                    class="px-5 py-4 font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Area of Distribution
                  </th>
                  <th
                    class="px-5 py-4 text-center font-nunito text-[11px] uppercase tracking-[0.18em] text-[#a8768d]"
                  >
                    Action
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr v-if="loading">
                  <td colspan="5" class="px-5 py-14 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <div class="loader-pink"></div>
                      <p class="mt-4 font-nunito text-[14px] text-[#8a8f99]">Loading sellers...</p>
                    </div>
                  </td>
                </tr>

                <tr
                  v-for="seller in rows"
                  :key="seller.id"
                  class="border-t border-[#f3e9ee] transition hover:bg-[#fffafb]"
                >
                  <td class="px-5 py-4 font-nunito text-[14px] text-[#7d838d]">
                    {{ seller.seller_code || '—' }}
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <div>
                        <p class="font-nunito text-[15px] font-semibold text-[#5f6670]">
                          {{ seller.full_name }}
                        </p>
                        <p class="font-nunito text-[13px] text-[#9aa0a9]">
                          {{ seller.position || 'Authorized Seller' }}
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4 font-nunito text-[14px] text-[#7d838d]">
                    {{ seller.contact_number || '—' }}
                  </td>
                  <td class="px-5 py-4 font-nunito text-[14px] text-[#7d838d]">
                    {{ seller.area_of_distribution || '—' }}
                  </td>
                  <td class="px-5 py-4 text-right">
                    <button
                      @click="viewSeller(seller.id)"
                      class="inline-flex h-10 items-center justify-center rounded-full border border-[#eadce2] bg-white px-5 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
                    >
                      View Details
                    </button>
                  </td>
                </tr>

                <tr v-if="!loading && rows.length === 0">
                  <td colspan="5" class="px-5 py-14 text-center">
                    <div class="max-w-md mx-auto">
                      <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#f8edf2] text-[#e97aac]"
                      >
                        <i class="fa-solid fa-user-group text-[24px]"></i>
                      </div>
                      <h4 class="mt-4 font-playfair text-[24px] text-[#5f6670]">
                        No Sellers Found
                      </h4>
                      <p class="mt-3 font-nunito text-[14px] leading-7 text-[#8a8f99]">
                        We couldn’t find any sellers matching your search criteria. Try adjusting
                        the search or filter options.
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
            class="border-t border-[#f3e9ee] px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-3"
          >
            <div class="font-nunito text-[14px] text-[#8a8f99]">
              Showing page <span class="font-semibold text-[#5f6670]">{{ page }}</span> of
              <span class="font-semibold text-[#5f6670]">{{ totalPages }}</span>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="fetchSellers(page - 1)"
                :disabled="page <= 1"
                class="inline-flex h-10 items-center justify-center rounded-full border border-[#e8dbe1] bg-white px-4 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac] disabled:opacity-40 disabled:cursor-not-allowed"
              >
                Prev
              </button>

              <button
                @click="fetchSellers(page + 1)"
                :disabled="page >= totalPages"
                class="inline-flex h-10 items-center justify-center rounded-full bg-[#e97aac] px-4 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#d96799] disabled:opacity-40 disabled:cursor-not-allowed"
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

    <!-- SELLER DETAILS MODAL -->
    <transition name="seller-fade">
      <div
        v-if="selected"
        class="fixed inset-0 z-210 flex items-center justify-center bg-[#2b1f25]/28 p-3 sm:p-4 md:p-6 backdrop-blur-md"
        @click.self="closeSellerModal"
      >
        <!-- <div class="absolute inset-0 bg-[#2b1f25]/35 backdrop-blur-md"></div> -->

        <div
          class="relative z-10 w-[94vw] sm:w-[90vw] md:w-[86vw] lg:w-225 max-h-[92vh] overflow-y-auto rounded-[28px] border border-[#f0e4e9] bg-linear-to-br from-[#f8edf2] via-[#fffafb] to-[#f4e5ec] shadow-[0_20px_60px_rgba(0,0,0,0.16)]"
          :class="sellerModalClosing ? 'animate-modal-out' : 'animate-modal-in'"
        >
          <!-- soft glow -->
          <div
            class="pointer-events-none absolute -top-10 -left-10 h-36 w-36 rounded-full bg-[#f3c8d9]/35 blur-3xl"
          ></div>
          <div
            class="pointer-events-none absolute bottom-0 right-0 h-44 w-44 rounded-full bg-[#edd6df]/40 blur-3xl"
          ></div>

          <!-- subtle pattern -->
          <div
            class="pointer-events-none absolute inset-0 rounded-[28px] opacity-[0.10]"
            style="
              background-image: radial-gradient(
                circle at 1px 1px,
                rgba(120, 120, 120, 0.14) 1px,
                transparent 0
              );
              background-size: 24px 24px;
            "
          ></div>

          <div class="relative z-10 px-5 pb-6 pt-5 sm:px-7 sm:pb-7 sm:pt-6 md:px-8 md:pb-8 md:pt-7">
            <button
              @click="closeSellerModal"
              aria-label="Close seller details"
              class="absolute right-4 top-4 sm:right-5 sm:top-5 flex h-10 w-10 items-center justify-center rounded-full border border-[#eadce2] bg-white/90 text-[#6b7280] transition duration-200 hover:border-[#e97aac] hover:text-[#e97aac] hover:shadow-md"
            >
              <i class="fa-solid fa-xmark text-[16px] sm:text-[18px]"></i>
            </button>

            <div class="modal-single-panel">
              <!-- HEADER -->
              <div class="text-center">
                <p
                  class="font-nunito text-[11px] sm:text-[12px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.3em] text-[#b38497]"
                >
                  Seller Details
                </p>

                <div
                  class="mx-auto mt-5 w-36 h-36 sm:w-40 sm:h-40 rounded-full overflow-hidden border-[6px] border-white shadow-[0_12px_28px_rgba(0,0,0,0.10)] bg-[#fffafb]"
                >
                  <img
                    v-if="selected.avatar"
                    :src="getImage(selected.avatar)"
                    :alt="selected.full_name"
                    class="w-full h-full object-cover protected-image"
                    draggable="false"
                    @contextmenu.prevent
                    @dragstart.prevent
                    @mousedown.prevent
                  />

                  <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-linear-to-br from-[#fff7fa] via-[#fffafb] to-[#f8edf2]"
                  >
                    <div
                      class="flex h-20 w-20 sm:h-22 sm:w-22 items-center justify-center rounded-full border border-[#f0dce5] bg-white/80 shadow-[0_10px_24px_rgba(233,122,172,0.10)]"
                    >
                      <i class="fas fa-user text-[30px] sm:text-[34px] text-[#d8a9bc]"></i>
                    </div>
                  </div>
                </div>

                <!-- <div
                  class="mt-5 inline-flex items-center rounded-full border border-[#e9c4d3] bg-white/70 px-4 py-1.5 text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.28em] text-[#9c6b80]"
                >
                  {{ selected.seller_code || 'Authorized Seller' }}
                </div> -->

                <h3
                  class="mt-5 font-playfair text-[28px] leading-tight text-[#5f6670] sm:text-[34px] md:text-[40px]"
                >
                  {{ selected.full_name }}
                </h3>

                <p
                  class="mt-3 font-nunito text-[14px] sm:text-[15px] text-[#9c6b80] font-semibold uppercase tracking-[0.14em]"
                >
                  {{ selected.position || 'Authorized Seller' }}
                </p>
              </div>

              <!-- DETAILS -->
              <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="detail-card">
                  <div class="detail-icon">
                    <i class="fa-solid fa-earth-asia"></i>
                  </div>
                  <div>
                    <p class="detail-label">ID No.</p>
                    <p class="detail-value">{{ selected.seller_code || '—' }}</p>
                  </div>
                </div>

                <div class="detail-card">
                  <div class="detail-icon">
                    <i class="fa-solid fa-location-dot"></i>
                  </div>
                  <div>
                    <p class="detail-label">Area of Distribution</p>
                    <p class="detail-value">{{ selected.area_of_distribution || '—' }}</p>
                  </div>
                </div>

                <div class="detail-card">
                  <div class="detail-icon">
                    <i class="fa-solid fa-phone"></i>
                  </div>
                  <div>
                    <p class="detail-label">Contact Number</p>
                    <p class="detail-value">{{ selected.contact_number || '—' }}</p>
                  </div>
                </div>

                <div class="detail-card">
                  <div class="detail-icon">
                    <i class="fa-solid fa-box-open"></i>
                  </div>
                  <div>
                    <p class="detail-label">Products</p>
                    <p class="detail-value">{{ selected.product_type || 'All Products' }}</p>
                  </div>
                </div>

                <div class="detail-card sm:col-span-2">
                  <div class="detail-icon">
                    <i class="fa-solid fa-shop"></i>
                  </div>
                  <div>
                    <p class="detail-label">Physical Store Address</p>
                    <p class="detail-value">
                      {{ selected.physical_store_address || 'Not Available' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- SOCIAL / SHOP LINKS -->
              <div
                v-if="
                  (selected.facebook_name && selected.facebook_link) ||
                  (selected.shopee_shop_name && selected.shopee_shop_link) ||
                  (selected.tiktok_shop_name && selected.tiktok_shop_link) ||
                  (selected.lazada_shop_name && selected.lazada_shop_link)
                "
                class="mt-8"
              >
                <div class="text-center mb-4">
                  <p
                    class="font-nunito text-[11px] sm:text-[12px] font-semibold uppercase tracking-[0.22em] sm:tracking-[0.3em] text-[#b38497]"
                  >
                    Official Seller Links
                  </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <a
                    v-if="selected.facebook_name && selected.facebook_link"
                    :href="selected.facebook_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="detail-card detail-link-card"
                  >
                    <div class="detail-icon">
                      <i class="fa-brands fa-facebook-f"></i>
                    </div>
                    <div class="min-w-0">
                      <p class="detail-label">Facebook</p>
                      <p class="detail-value truncate">{{ selected.facebook_name }}</p>
                    </div>
                  </a>

                  <a
                    v-if="selected.shopee_shop_name && selected.shopee_shop_link"
                    :href="selected.shopee_shop_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="detail-card detail-link-card"
                  >
                    <div class="detail-icon">
                      <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div class="min-w-0">
                      <p class="detail-label">Shopee Shop</p>
                      <p class="detail-value truncate">{{ selected.shopee_shop_name }}</p>
                    </div>
                  </a>

                  <a
                    v-if="selected.tiktok_shop_name && selected.tiktok_shop_link"
                    :href="selected.tiktok_shop_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="detail-card detail-link-card"
                  >
                    <div class="detail-icon">
                      <i class="fa-brands fa-tiktok"></i>
                    </div>
                    <div class="min-w-0">
                      <p class="detail-label">TikTok Shop</p>
                      <p class="detail-value truncate">{{ selected.tiktok_shop_name }}</p>
                    </div>
                  </a>

                  <a
                    v-if="selected.lazada_shop_name && selected.lazada_shop_link"
                    :href="selected.lazada_shop_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="detail-card detail-link-card"
                  >
                    <div class="detail-icon">
                      <i class="fa-solid fa-store"></i>
                    </div>
                    <div class="min-w-0">
                      <p class="detail-label">Lazada Shop</p>
                      <p class="detail-value truncate">{{ selected.lazada_shop_name }}</p>
                    </div>
                  </a>
                </div>
              </div>
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
  name: 'SellerLocator',
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
      selected: null,
      sellerModalClosing: false,

      filters: {
        q: '',
        market_scope: '',
        area_of_distribution: '',
        product_type: '',
      },

      page: 1,
      total: 0,
      totalPages: 1,
      limit: 10,

      marketScopes: [],
      areaOptions: [],
      productOptions: [],
    }
  },

  computed: {
    filteredProductOptions() {
      return this.productOptions.filter((product) => product !== 'All Products')
    },
  },

  methods: {
    async fetchSellers(page = 1) {
      const baseURL = import.meta.env.VITE_API_BASE_URL
      this.loading = true

      try {
        const params = new URLSearchParams({
          page: String(page),
          limit: String(this.limit),
          q: this.filters.q || '',
          market_scope: this.filters.market_scope || '',
          area_of_distribution: this.filters.area_of_distribution || '',
          product_type: this.filters.product_type || '',
        })

        const res = await fetch(`${baseURL}/api/sellerlocator/list.php?${params.toString()}`)
        const data = await res.json()

        if (data.success) {
          this.rows = data.rows || []
          this.page = Number(data.page || 1)
          this.total = Number(data.total || 0)
          this.totalPages = Number(data.totalPages || 1)

          this.marketScopes = Array.isArray(data.marketScopes) ? data.marketScopes : []
          this.areaOptions = Array.isArray(data.areaOptions) ? data.areaOptions : []
          this.productOptions = Array.isArray(data.productOptions) ? data.productOptions : []
        } else {
          this.rows = []
          this.total = 0
          this.totalPages = 1
        }
      } catch (error) {
        console.error('Failed to fetch sellers:', error)
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

    blockShortcuts(e) {
      const key = e.key.toLowerCase()

      if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(key)) ||
        (e.ctrlKey && ['s', 'u', 'p'].includes(key))
      ) {
        e.preventDefault()
      }
    },

    async viewSeller(id) {
      const baseURL = import.meta.env.VITE_API_BASE_URL

      try {
        const res = await fetch(`${baseURL}/api/sellerlocator/view.php?id=${id}`)
        const data = await res.json()

        if (data.success) {
          this.selected = data.data
          this.sellerModalClosing = false
          document.body.style.overflow = 'hidden'
        }
      } catch (error) {
        console.error('Failed to load seller details:', error)
      }
    },

    async closeSellerModal() {
      if (this.sellerModalClosing) return
      this.sellerModalClosing = true
      await new Promise((resolve) => setTimeout(resolve, 220))
      this.selected = null
      this.sellerModalClosing = false
      document.body.style.overflow = ''
    },

    resetFilters() {
      this.filters.q = ''
      this.filters.market_scope = ''
      this.filters.area_of_distribution = ''
      this.filters.product_type = ''
      this.fetchSellers(1)
    },

    getImage(file) {
      if (!file) return ''
      return `/uploads/sellers/${file}`
    },
  },

  mounted() {
    this.fetchSellers()
    window.addEventListener('keydown', this.blockShortcuts)
  },

  beforeUnmount() {
    document.body.style.overflow = ''
    window.removeEventListener('keydown', this.blockShortcuts)
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

.modal-single-panel {
  max-width: 760px;
  margin: 0 auto;
}

.detail-card {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
  border: 1px solid #f0e4e9;
  background: #fffafb;
  border-radius: 1.25rem;
  padding: 1rem 1rem;
  transition:
    0.25s ease,
    transform 0.2s ease;
}

.detail-card:hover {
  box-shadow: 0 12px 24px rgba(233, 122, 172, 0.08);
}

.detail-icon {
  width: 2.75rem;
  height: 2.75rem;
  flex-shrink: 0;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(233, 122, 172, 0.12);
  color: #e97aac;
  font-size: 1rem;
}

.detail-label {
  font-family: 'Nunito Sans', sans-serif;
  font-size: 11px;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: #b38497;
}

.detail-value {
  margin-top: 0.25rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 15px;
  line-height: 1.6;
  color: #5f6670;
}

.detail-link-card {
  text-decoration: none;
  cursor: pointer;
}

.detail-link-card:hover {
  border-color: #e9c4d3;
  box-shadow: 0 12px 24px rgba(233, 122, 172, 0.08);
  transform: translateY(-1px);
}

.detail-link-card .detail-value {
  font-weight: 700;
}

.seller-fade-enter-active,
.seller-fade-leave-active {
  transition: opacity 0.22s ease;
}

.seller-fade-enter-from,
.seller-fade-leave-to {
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

.protected-image {
  -webkit-user-drag: none;
  user-select: none;
  -webkit-touch-callout: none;
  pointer-events: auto;
}
</style>
