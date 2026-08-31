<template>
  <transition name="faq-modal-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-200 flex items-start justify-center overflow-y-auto bg-[#2b1f25]/40 p-3 py-4 backdrop-blur-md sm:p-4 sm:py-7 md:items-center md:p-6"
      @click.self="handleClose"
    >
      <div
        class="relative w-[95vw] max-w-285 overflow-hidden rounded-[28px] border border-white/60 bg-[#fffdfd] shadow-[0_30px_90px_rgba(82,45,61,0.24)] sm:rounded-[34px] lg:max-h-[86vh]"
        :class="isClosing ? 'animate-faq-modal-out' : 'animate-faq-modal-in'"
      >
        <!-- Close -->
        <button
          type="button"
          @click="handleClose"
          aria-label="Close Help and FAQs modal"
          class="absolute right-4 top-4 z-30 flex h-10 w-10 items-center justify-center rounded-full border border-[#efdde5] bg-white/95 text-[#7b8190] shadow-sm transition hover:border-[#e97aac] hover:text-[#e97aac] hover:shadow-md sm:right-5 sm:top-5 sm:h-11 sm:w-11"
        >
          <i class="fa-solid fa-xmark text-[16px]"></i>
        </button>

        <div class="grid grid-cols-1 lg:max-h-[86vh] lg:grid-cols-[0.78fr_1.22fr]">
          <!-- LEFT PANEL -->
          <aside
            class="relative hidden overflow-hidden bg-linear-to-br from-[#fff0f6] via-[#fff8fb] to-[#f9dce8] px-5 py-7 sm:px-7 md:px-8 lg:block lg:min-h-[78vh] lg:py-9"
          >
            <!-- decorative background -->
            <div
              class="pointer-events-none absolute -left-20 top-0 h-64 w-64 rounded-full bg-[#f4bfd3]/45 blur-3xl"
            ></div>
            <div
              class="pointer-events-none absolute -bottom-20 right-0 h-72 w-72 rounded-full bg-[#e97aac]/18 blur-3xl"
            ></div>
            <div
              class="pointer-events-none absolute inset-0 opacity-[0.11]"
              style="
                background-image: radial-gradient(
                  circle at 1px 1px,
                  rgba(120, 120, 120, 0.16) 1px,
                  transparent 0
                );
                background-size: 22px 22px;
              "
            ></div>

            <div class="relative z-10">
              <span
                class="inline-flex items-center rounded-full border border-[#e8bdd0] bg-white/75 px-3.5 py-1.5 font-nunito text-[10px] font-extrabold uppercase tracking-[0.22em] text-[#b86a8a]"
              >
                Help Center
              </span>

              <h2
                class="mt-5 font-playfair text-[34px] leading-tight text-[#4f5968] sm:text-[44px] md:text-[50px]"
              >
                Product<br class="hidden lg:block" />
                Questions?
              </h2>

              <p
                class="mt-4 max-w-md font-nunito text-[14px] leading-7 text-[#747b86] sm:text-[15px]"
              >
                Learn more about You Glow Babe products before purchasing. These FAQs are written
                for quick customer guidance based on the brand’s product profile.
              </p>

              <!-- quick cards -->
              <div class="mt-7 grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1">
                <div
                  class="rounded-3xl border border-white/70 bg-white/75 p-4 shadow-[0_12px_28px_rgba(233,122,172,0.08)] backdrop-blur"
                >
                  <div class="flex items-start gap-3">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#e97aac] text-white shadow-[0_12px_24px_rgba(233,122,172,0.25)]"
                    >
                      <i class="fa-solid fa-heart-pulse text-[15px]"></i>
                    </div>
                    <div>
                      <p
                        class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#d96799]"
                      >
                        Inside-Out Care
                      </p>
                      <p class="mt-1 font-nunito text-[13px] leading-6 text-[#747b86]">
                        Skincare and wellness products for glow support.
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="rounded-3xl border border-white/70 bg-white/75 p-4 shadow-[0_12px_28px_rgba(233,122,172,0.08)] backdrop-blur"
                >
                  <div class="flex items-start gap-3">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#e97aac] text-white shadow-[0_12px_24px_rgba(233,122,172,0.25)]"
                    >
                      <i class="fa-solid fa-shield-halved text-[15px]"></i>
                    </div>
                    <div>
                      <p
                        class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#d96799]"
                      >
                        Safety First
                      </p>
                      <p class="mt-1 font-nunito text-[13px] leading-6 text-[#747b86]">
                        Check age limits, pregnancy notes, and skin concerns.
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="rounded-3xl border border-white/70 bg-white/75 p-4 shadow-[0_12px_28px_rgba(233,122,172,0.08)] backdrop-blur"
                >
                  <div class="flex items-start gap-3">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#e97aac] text-white shadow-[0_12px_24px_rgba(233,122,172,0.25)]"
                    >
                      <i class="fa-solid fa-store text-[15px]"></i>
                    </div>
                    <div>
                      <p
                        class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#d96799]"
                      >
                        Buy Authentic
                      </p>
                      <p class="mt-1 font-nunito text-[13px] leading-6 text-[#747b86]">
                        Use the Seller Locator before buying.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div
                class="mt-3 rounded-[26px] border border-[#f2dbe4] bg-white/65 p-5 shadow-[0_14px_34px_rgba(0,0,0,0.04)]"
              >
                <p
                  class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.18em] text-[#b86a8a]"
                >
                  Customer Reminder
                </p>
                <p class="mt-2 font-nunito text-[13px] leading-6 text-[#747b86]">
                  Results may vary per person. Customers with allergies, skin conditions, medical
                  conditions, pregnancy, or maintenance medication should consult a qualified
                  professional before use.
                </p>
              </div>
            </div>
          </aside>

          <!-- RIGHT PANEL -->
          <main
            class="relative flex flex-col rounded-[28px] bg-[#fffdfd] px-5 py-7 sm:rounded-[34px] sm:px-7 md:px-8 lg:min-h-[78vh] lg:max-h-[86vh] lg:rounded-none lg:overflow-hidden lg:py-9"
          >
            <div class="pr-12">
              <p
                class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.26em] text-[#e97aac]"
              >
                Frequently Asked Questions
              </p>

              <h3
                class="mt-2 font-playfair text-[31px] leading-tight text-[#4f5968] sm:text-[40px] md:text-[46px]"
              >
                Help & FAQs
              </h3>

              <p class="mt-3 max-w-2xl font-nunito text-[14px] leading-7 text-[#7b8190]">
                Browse common questions about You Glow Babe skincare, supplements, usage reminders,
                safety precautions, and authentic sellers.
              </p>
            </div>

            <!-- Category filters -->
            <div class="faq-category-scroll mt-5 flex gap-2 overflow-x-auto pb-2">
              <button
                v-for="category in categories"
                :key="category"
                type="button"
                @click="setCategory(category)"
                class="shrink-0 rounded-full border px-4 py-2 font-nunito text-[11px] font-extrabold uppercase tracking-[0.12em] transition"
                :class="
                  activeCategory === category
                    ? 'border-[#e97aac] bg-[#e97aac] text-white shadow-[0_10px_22px_rgba(233,122,172,0.24)]'
                    : 'border-[#f1dce5] bg-white text-[#b86a8a] hover:border-[#e97aac] hover:text-[#e97aac]'
                "
              >
                {{ category }}
              </button>
            </div>

            <!-- FAQ list -->
            <div
              class="faq-scroll mt-4 min-h-0 flex-1 overflow-visible pr-1 lg:overflow-y-auto lg:pr-2"
            >
              <div v-if="filteredFaqs.length" class="space-y-3">
                <article
                  v-for="faq in filteredFaqs"
                  :key="faq.id"
                  class="overflow-hidden rounded-3xl border border-[#f1dce5] bg-white shadow-[0_12px_30px_rgba(233,122,172,0.06)]"
                >
                  <button
                    type="button"
                    class="flex w-full items-start justify-between gap-4 px-4 py-4 text-left sm:px-5"
                    @click="toggleFaq(faq.id)"
                  >
                    <div class="flex items-start gap-3">
                      <div
                        class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#fff1f6] text-[#e97aac]"
                      >
                        <i :class="faq.icon" class="text-[14px]"></i>
                      </div>

                      <div>
                        <span
                          class="inline-flex rounded-full bg-[#fff7fa] px-2.5 py-1 font-nunito text-[9px] font-extrabold uppercase tracking-[0.14em] text-[#d96799]"
                        >
                          {{ faq.category }}
                        </span>

                        <h4
                          class="mt-2 font-nunito text-[14px] font-extrabold leading-6 text-[#4f5968] sm:text-[15px]"
                        >
                          {{ faq.question }}
                        </h4>
                      </div>
                    </div>

                    <span
                      class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[#f1dce5] bg-white text-[#d96799] transition"
                      :class="openFaqId === faq.id ? 'rotate-180' : ''"
                    >
                      <i class="fa-solid fa-chevron-down text-[12px]"></i>
                    </span>
                  </button>

                  <transition name="faq-answer">
                    <div
                      v-if="openFaqId === faq.id"
                      class="border-t border-[#f5e3ea] px-4 pb-5 pt-4 sm:px-5"
                    >
                      <p class="font-nunito text-[14px] leading-7 text-[#747b86]">
                        {{ faq.answer }}
                      </p>

                      <div
                        v-if="faq.note"
                        class="mt-4 rounded-2xl border border-[#f2dbe4] bg-[#fff8fb] px-4 py-3"
                      >
                        <p
                          class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#d96799]"
                        >
                          Note
                        </p>
                        <p class="mt-1 font-nunito text-[13px] leading-6 text-[#7b8190]">
                          {{ faq.note }}
                        </p>
                      </div>
                    </div>
                  </transition>
                </article>
              </div>

              <div
                v-else
                class="rounded-3xl border border-[#f1dce5] bg-white px-5 py-10 text-center shadow-[0_12px_30px_rgba(233,122,172,0.06)]"
              >
                <div
                  class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#fff1f6] text-[#e97aac]"
                >
                  <i class="fa-regular fa-face-smile text-[20px]"></i>
                </div>

                <p class="mt-4 font-playfair text-[24px] text-[#4f5968]">No FAQ found</p>

                <p class="mt-2 font-nunito text-[14px] leading-7 text-[#7b8190]">
                  Try another keyword or choose a different category.
                </p>
              </div>
            </div>

            <!-- Bottom reminder -->
            <div
              class="mt-5 shrink-0 rounded-[22px] border border-[#f1dce5] bg-linear-to-r from-[#fff7fa] to-white px-4 py-4"
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#e97aac]/12 text-[#e97aac]"
                >
                  <i class="fa-solid fa-circle-info text-[14px]"></i>
                </div>

                <p class="font-nunito text-[13px] leading-6 text-[#747b86]">
                  This FAQ is for general product awareness only. Always check the official product
                  label, product packaging, and professional advice when needed.
                </p>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'HelpFaqsModal',

  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['close'],

  data() {
    return {
      isClosing: false,
      previousBodyOverflow: '',
      activeCategory: 'All',
      searchTerm: '',
      openFaqId: 'what-is-ygb',

      faqs: [
        {
          id: 'brand-goal-affordable',
          category: 'Brand',
          icon: 'fa-solid fa-seedling',
          question: 'What is the main goal of You Glow Babe?',
          answer:
            'You Glow Babe aims to help Filipinos feel more confident by offering skincare and wellness products that are positioned as affordable yet effective. The brand wants customers to know that skincare does not always need to be expensive to be useful in a daily routine.',
        },
        {
          id: 'brand-started',
          category: 'Brand',
          icon: 'fa-solid fa-calendar-days',
          question: 'When did You Glow Babe start?',
          answer:
            'You Glow Babe started in June 2020 during the pandemic. It began with 1,000 Skin Care Maintenance Sets and later expanded into more skincare products and food-grade supplements.',
        },
        {
          id: 'product-for-skin-concerns',
          category: 'Products',
          icon: 'fa-solid fa-face-smile-beam',
          question: 'Are You Glow Babe products only for whitening?',
          answer:
            'No. While some products are positioned for brightening or improving dull-looking skin, it is also used for moisturizing, nourishment, sunscreen protection, skincare routine support, healthy weight management, cravings support, immune health, and inside-out glow.',
          note: 'Customers should choose products based on their needs, skin type, lifestyle, and personal safety considerations.',
        },
        {
          id: 'product-results-vary',
          category: 'Results',
          icon: 'fa-solid fa-hourglass-half',
          question: 'Why do results vary per person?',
          answer:
            'Results may vary because each person has a different skin type, lifestyle, diet, body response, consistency level, and existing skin or health condition. With proper exercise, healthy diet, and consistency can support faster results for supplement products.',
          note: 'No skincare or supplement result is guaranteed to be the same for everyone.',
        },
        {
          id: 'soap-ingredients',
          category: 'Soap',
          icon: 'fa-solid fa-leaf',
          question: 'What are the key ingredients of Beauty White Soap?',
          answer:
            'Beauty White Soap is described as containing sea buckthorn, niacinamide or nicotinamide, and snail filtrate. These ingredients are positioned to help nourish, moisturize, brighten the appearance of dull skin, and support uneven-looking skin tone improvement.',
          note: 'Always follow the actual product label and do a patch test if you have sensitive skin.',
        },
        {
          id: 'soap-how-long',
          category: 'Soap',
          icon: 'fa-solid fa-stopwatch',
          question: 'How long should I leave Beauty White Soap on my skin?',
          answer:
            'Users may lather the soap onto the skin and let it sit for about 1 to 3 minutes before rinsing. For the face, underarm, bikini area, and neck, with a shorter contact time of about 5 seconds only.',
          note: 'Do not overuse or leave it longer than recommended, especially on sensitive areas.',
        },
        {
          id: 'soap-sensitive-skin',
          category: 'Soap',
          icon: 'fa-solid fa-hand-holding-heart',
          question: 'Can people with skin conditions use Beauty White Soap?',
          answer:
            'The soap is formulated with beneficial ingredients aimed at promoting healthy skin. However, users with allergies, psoriasis, or other pre-existing skin conditions should consult a healthcare professional or dermatologist before using new skincare products.',
          note: 'This is important for customers with sensitive or reactive skin.',
        },
        {
          id: 'capsule-age',
          category: 'Capsules',
          icon: 'fa-solid fa-user-clock',
          question: 'What age can start taking Beauty White Capsules?',
          answer: 'Beauty White Capsules are safe to consume for users 15 years old and older.',
          note: 'For minors, it is still best to ask guidance from a parent or guardian before taking supplements.',
        },
        {
          id: 'capsule-ingredients',
          category: 'Capsules',
          icon: 'fa-solid fa-vial-circle-check',
          question: 'What ingredients are highlighted in Beauty White Capsules?',
          answer:
            'Beauty White Capsules are described as an all-in-one capsule packed with collagen, glutathione, vitamin C, and garcinia cambogia. These ingredients are for skin care benefits, immune health, healthy weight support, and cravings support.',
          note: 'Supplements should not replace a healthy diet, exercise, or professional medical advice.',
        },
        {
          id: 'capsule-pregnant',
          category: 'Capsules',
          icon: 'fa-solid fa-user-doctor',
          question: 'Can pregnant or lactating moms take Beauty White Capsules?',
          answer:
            'Pregnant or lactating moms to consult their obstetrician or OB first before consuming Beauty White Capsules.',
          note: 'This also applies to users with medical conditions or special health concerns.',
        },
        {
          id: 'shepu-age',
          category: 'Shepu Appu',
          icon: 'fa-solid fa-id-card',
          question: 'Who can drink Shepu Appu Juice?',
          answer:
            'Shepu Appu Juice can be taken by users 15 years old and above. Minors aged 15 to 17 should seek approval from their guardians before intake.',
          note: 'This is a supplement drink, so customers should use it responsibly.',
        },
        {
          id: 'shepu-combine-capsule',
          category: 'Shepu Appu',
          icon: 'fa-solid fa-layer-group',
          question: 'Can Shepu Appu Juice be taken with Beauty White Capsules?',
          answer: 'Yes. Shepu Appu Juice is safe to consume together with Beauty White Capsules.',
          note: 'Customers with medical conditions, maintenance medication, or special health concerns should still consult a physician first.',
        },
        {
          id: 'shepu-halal-fda',
          category: 'Shepu Appu',
          icon: 'fa-solid fa-certificate',
          question: 'Is Shepu Appu Juice FDA-approved and Halal-certified?',
          answer:
            'Shepu Appu Juice has been determined by the FDA as safe to consume for its intended use. It passed and met the requirements of Islamic law and is suitable for Muslim consumers.',
          note: 'Customers may check the actual packaging, batch details, and official announcements for updated product verification.',
        },
        {
          id: 'shepu-stevia',
          category: 'Shepu Appu',
          icon: 'fa-solid fa-martini-glass-citrus',
          question: 'Is Shepu Appu Juice sweetened?',
          answer:
            'Shepu Appu Juice is sweetened by stevia. It is positioned as a slimming drink that can be mixed with water and taken before meals or during workout/daily activities.',
          note: 'Taste and tolerance may still vary depending on the user.',
        },
        {
          id: 'shepu-ice',
          category: 'Shepu Appu',
          icon: 'fa-solid fa-snowflake',
          question: 'Can I add ice to Shepu Appu Juice?',
          answer:
            'Yes. Mix 1 sachet of Shepu Appu Juice with 250 ml of water or 1 glass, and you may add ice for a more refreshing experience.',
          note: 'Use clean drinking water and follow the serving instructions on the packaging.',
        },
        {
          id: 'rejuv-age',
          category: 'Rejuv Set',
          icon: 'fa-solid fa-user-check',
          question: 'What age can start using Beauty White Rejuv Set?',
          answer: 'Beauty White Rejuv Set is safe to use for users 15 years old and older.',
          note: 'Rejuv routines may be stronger than regular skincare, so customers should follow instructions carefully.',
        },
        {
          id: 'rejuv-sensitive',
          category: 'Rejuv Set',
          icon: 'fa-solid fa-face-tired',
          question: 'Can people with sensitive or acne-prone skin use the Rejuv Set?',
          answer:
            'Beauty White Rejuv Set is formulated with skin-loving ingredients and may be used even by those with acne-prone skin. However, users with sensitive skin or underlying skin conditions should consult their dermatologist first.',
          note: 'A patch test and professional guidance are recommended before starting any rejuv routine.',
        },
        {
          id: 'rejuv-sun-exposure',
          category: 'Rejuv Set',
          icon: 'fa-solid fa-umbrella-beach',
          question: 'What should I do if I will be exposed to the sun while using the Rejuv Set?',
          answer:
            'Users who will be exposed under the sun to apply an ample amount of sunscreen, which is already included in the set.',
          note: 'Sun protection is very important when using products with peeling or renewal support.',
        },
        {
          id: 'rejuv-fda',
          category: 'Rejuv Set',
          icon: 'fa-solid fa-clipboard-check',
          question: 'Is Beauty White Rejuv Set FDA-approved?',
          answer:
            'The FDA has confirmed that Beauty White Rejuv Set is safe to use and effective for its intended use.',
          note: 'Customers should still follow proper usage instructions and check the actual product packaging.',
        },
        {
          id: 'sunscreen-spf',
          category: 'Sunscreen',
          icon: 'fa-solid fa-sun',
          question: 'What protection does Beauty White Sunscreen provide?',
          answer:
            'Beauty White Sunscreen is described as SPF 50 PA+++ with UVA/UVB protection. It is a broad-spectrum protection against UVB rays that cause sunburn and UVA rays associated with skin aging and darkening.',
          note: 'Use sunscreen daily, especially when using brightening or rejuv products.',
        },
        {
          id: 'sunscreen-ingredients',
          category: 'Sunscreen',
          icon: 'fa-solid fa-spa',
          question: 'What skincare ingredients are highlighted in Beauty White Sunscreen?',
          answer:
            'Sea buckthorn and Polymnia sonchifolia. Sea buckthorn is described as rich in antioxidants, vitamins, and omega fatty acids, while Polymnia sonchifolia is described as helping maintain healthy skin microbiota and supporting moisture retention.',
          note: 'Ingredient benefits are for general product awareness and may vary depending on skin type.',
        },
        {
          id: 'sunscreen-sensitive',
          category: 'Sunscreen',
          icon: 'fa-solid fa-hand-sparkles',
          question: 'Can people with skin conditions use Beauty White Sunscreen?',
          answer:
            'People with existing skin conditions to do a patch test first to check for adverse reactions. It also notes that the sunscreen does not contain harmful ingredients and is described as compatible with all skin types, including sensitive skin.',
          note: 'For severe allergies or diagnosed skin conditions, consult a dermatologist before use.',
        },
        {
          id: 'sunscreen-face-body',
          category: 'Sunscreen',
          icon: 'fa-solid fa-person-rays',
          question: 'Can Beauty White Sunscreen be used on both face and body?',
          answer:
            'Yes. Beauty White Sunscreen is made not only to protect the face but also to provide whole-body protection from sun rays.',
          note: 'Apply evenly and reapply as needed, especially when outdoors.',
        },
        {
          id: 'buying-authorized',
          category: 'Buying Guide',
          icon: 'fa-solid fa-store-slash',
          question: 'Why should I buy only from authorized sellers?',
          answer:
            'Buying from authorized sellers helps customers avoid fake, suspicious, or misleading listings. It also helps ensure that the product details, packaging, and customer support are aligned with official You Glow Babe information.',
          note: 'Use the Seller Locator and Fake Seller page together before purchasing online.',
        },
        {
          id: 'buying-no-fake-list',
          category: 'Buying Guide',
          icon: 'fa-solid fa-magnifying-glass-location',
          question: 'If a shop is not listed as fake, does that mean it is authorized?',
          answer:
            'Not automatically. A shop may not appear in the Fake Seller list but still may not be authorized. Customers should still verify sellers through the Seller Locator or official You Glow Babe channels before purchasing.',
          note: 'This helps prevent buyers from relying only on the fake seller list.',
        },
        {
          id: 'product-medical-advice',
          category: 'Products',
          icon: 'fa-solid fa-notes-medical',
          question: 'Are the FAQs a replacement for medical or dermatology advice?',
          answer:
            'No. The FAQs are only for general product awareness. Customers with allergies, sensitive skin, pregnancy, lactation, medical conditions, maintenance medication, or other health concerns should consult a qualified professional before using skincare or supplement products.',
          note: 'This reminder is especially important for rejuv products and wellness supplements.',
        },
      ],
    }
  },

  computed: {
    categories() {
      const uniqueCategories = [...new Set(this.faqs.map((faq) => faq.category))]
      return ['All', ...uniqueCategories]
    },

    filteredFaqs() {
      const keyword = this.searchTerm.toLowerCase()

      return this.faqs.filter((faq) => {
        const matchesCategory =
          this.activeCategory === 'All' || faq.category === this.activeCategory

        const searchableText =
          `${faq.category} ${faq.question} ${faq.answer} ${faq.note || ''}`.toLowerCase()
        const matchesSearch = !keyword || searchableText.includes(keyword)

        return matchesCategory && matchesSearch
      })
    },
  },

  watch: {
    show(value) {
      if (value) {
        this.isClosing = false
        this.lockBody()
        document.addEventListener('keydown', this.handleEscape)
      } else {
        this.unlockBody()
        document.removeEventListener('keydown', this.handleEscape)
      }
    },

    filteredFaqs() {
      if (!this.filteredFaqs.some((faq) => faq.id === this.openFaqId)) {
        this.openFaqId = this.filteredFaqs[0]?.id || null
      }
    },
  },

  methods: {
    setCategory(category) {
      this.activeCategory = category
    },

    toggleFaq(id) {
      this.openFaqId = this.openFaqId === id ? null : id
    },

    lockBody() {
      this.previousBodyOverflow = document.body.style.overflow
      document.body.style.overflow = 'hidden'
    },

    unlockBody() {
      document.body.style.overflow = this.previousBodyOverflow || ''
    },

    handleEscape(event) {
      if (event.key === 'Escape') {
        this.handleClose()
      }
    },

    async handleClose() {
      if (this.isClosing) return

      this.isClosing = true
      await new Promise((resolve) => setTimeout(resolve, 220))

      this.$emit('close')
      this.isClosing = false
    },
  },

  beforeUnmount() {
    this.unlockBody()
    document.removeEventListener('keydown', this.handleEscape)
  },
}
</script>

<style scoped>
.faq-modal-fade-enter-active,
.faq-modal-fade-leave-active {
  transition: opacity 0.22s ease;
}

.faq-modal-fade-enter-from,
.faq-modal-fade-leave-to {
  opacity: 0;
}

@keyframes faqModalIn {
  from {
    opacity: 0;
    transform: translateY(18px) scale(0.97);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes faqModalOut {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  to {
    opacity: 0;
    transform: translateY(18px) scale(0.97);
  }
}

.animate-faq-modal-in {
  animation: faqModalIn 0.26s ease-out both;
}

.animate-faq-modal-out {
  animation: faqModalOut 0.2s ease-in both;
}

.faq-answer-enter-active,
.faq-answer-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.faq-answer-enter-from,
.faq-answer-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.faq-scroll,
.faq-category-scroll {
  scrollbar-width: thin;
  scrollbar-color: #e97aac #fff1f6;
}

.faq-scroll::-webkit-scrollbar,
.faq-category-scroll::-webkit-scrollbar {
  width: 8px;
  height: 6px;
}

.faq-scroll::-webkit-scrollbar-track,
.faq-category-scroll::-webkit-scrollbar-track {
  background: #fff1f6;
  border-radius: 999px;
}

.faq-scroll::-webkit-scrollbar-thumb,
.faq-category-scroll::-webkit-scrollbar-thumb {
  background: #e97aac;
  border-radius: 999px;
}
</style>
