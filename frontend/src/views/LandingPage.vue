<template>
  <div class="overflow-x-hidden bg-white">
    <Navbar
      @open-contact-modal="showContactModal = true"
      @open-login-modal="showLoginModal = true"
      @open-coming-soon-modal="handleComingSoonRequest"
    />

    <ContactUsModal :show="showContactModal" @close="showContactModal = false" />
    <LoginModal
      :show="showLoginModal"
      @close="showLoginModal = false"
      @open-coming-soon-modal="handleComingSoonRequest"
    />

    <ComingSoonModal
      :show="showComingSoonModal"
      :feature-title="comingSoonFeatureTitle"
      :description="comingSoonDescription"
      :icon-class="comingSoonIcon"
      @close="showComingSoonModal = false"
    />

    <PrivacyTermsModal
      :show="showLegalModal"
      :initial-tab="legalInitialTab"
      @close="showLegalModal = false"
    />

    <HelpFaqsModal :show="showFaqsModal" @close="showFaqsModal = false" />

    <!-- Mobile spacer for fixed navbar -->
    <div class="h-16 bg-white md:hidden"></div>

    <!-- HERO SECTION: retained exactly from the current APESCON landing page -->
    <section id="home" class="relative w-full aspect-1440/725 overflow-hidden bg-[#F3F7FA]">
      <div class="absolute inset-0">
        <div
          v-for="(slide, index) in heroSlides"
          :key="slide.alt"
          class="absolute inset-0 bg-[#F3F7FA] transition-opacity duration-1400 ease-in-out"
          :class="activeHeroSlide === index ? 'z-10 opacity-100' : 'z-0 opacity-0'"
        >
          <picture class="block h-full w-full">
            <source
              type="image/webp"
              :srcset="`
            ${slide.xs} 480w,
            ${slide.sm} 640w,
            ${slide.md} 768w,
            ${slide.lg} 1024w,
            ${slide.xl} 1440w
          `"
              sizes="100vw"
            />

            <img
              :src="slide.lg"
              :alt="slide.alt"
              class="block h-full w-full select-none object-cover object-center pointer-events-none"
              loading="eager"
              :fetchpriority="index === 0 ? 'high' : 'auto'"
              draggable="false"
            />
          </picture>
        </div>
      </div>

      <div class="absolute inset-0 z-20 bg-black/5"></div>

      <div
        class="absolute bottom-3 left-1/2 z-40 flex -translate-x-1/2 items-center gap-2 sm:bottom-5"
      >
        <button
          v-for="(slide, index) in heroSlides"
          :key="`dot-${index}`"
          type="button"
          class="h-2.5 rounded-full transition-all duration-300"
          :class="
            activeHeroSlide === index
              ? 'w-8 bg-white shadow-md'
              : 'w-2.5 bg-white/60 hover:bg-white/80'
          "
          :aria-label="`Go to slide ${index + 1}`"
          @click="goToHeroSlide(index)"
        ></button>
      </div>
    </section>

    <!-- Introduction -->
    <section class="relative overflow-hidden bg-white py-16 sm:py-20 lg:py-26">
      <div class="absolute -left-28 top-10 h-80 w-80 rounded-full bg-[#025199]/5 blur-3xl"></div>
      <div class="absolute -right-28 bottom-0 h-80 w-80 rounded-full bg-[#F27C29]/7 blur-3xl"></div>
      <div class="brand-dot-pattern absolute inset-0 opacity-[0.05]"></div>

      <div
        class="relative z-10 mx-auto grid w-full max-w-295 grid-cols-1 items-center gap-12 px-6 sm:px-8 lg:grid-cols-12 lg:gap-14 lg:px-10"
      >
        <div class="lg:col-span-6">
          <p class="section-eyebrow">Professional Pest Management</p>

          <h2 class="section-title mt-4">Protection that starts with inspection not guesswork</h2>

          <p class="section-copy mt-5">
            APESCON delivers structured pest-control solutions for homes, businesses, industrial
            facilities, institutions, and government properties. Every engagement begins with a
            proper assessment so the recommended treatment matches the actual condition of the
            property.
          </p>

          <p class="section-copy mt-4">
            Our goal is not only to address visible activity, but also to identify access points,
            nesting areas, contributing conditions, and practical preventive measures.
          </p>

          <div class="mt-7 flex flex-col gap-3 sm:flex-row">
            <button type="button" class="primary-cta" @click="showContactModal = true">
              Request an Inspection
              <i class="fa-solid fa-arrow-right text-[12px]"></i>
            </button>

            <button type="button" class="secondary-cta" @click="scrollToServices">
              <i class="fa-solid fa-bug-slash text-[13px]"></i>
              Explore Services
            </button>
          </div>
        </div>

        <div class="lg:col-span-6">
          <div
            class="relative overflow-hidden rounded-[28px] border border-[#D8E4ED] bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68] p-5 shadow-[0_24px_70px_rgba(5,35,61,0.22)] sm:p-7"
          >
            <div
              class="absolute -right-18 -top-18 h-56 w-56 rounded-full bg-white/10 blur-3xl"
            ></div>
            <div
              class="absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-[#F27C29]/18 blur-3xl"
            ></div>
            <div class="brand-dot-pattern-light absolute inset-0 opacity-[0.12]"></div>

            <div class="relative z-10">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p
                    class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.2em] text-[#F7B267]"
                  >
                    The APESCON Approach
                  </p>

                  <h3
                    class="mt-2 font-montserrat text-[23px] font-bold leading-tight text-white sm:text-[28px]"
                  >
                    Inspect. Identify. Treat. Verify.
                  </h3>
                </div>

                <div
                  class="flex h-16 w-16 shrink-0 items-center justify-center rounded-[18px] border border-white/20 bg-white shadow-[0_12px_28px_rgba(0,0,0,0.18)]"
                >
                  <img
                    src="/assets/apescon_logo_transparent.png"
                    alt="APESCON"
                    class="h-13 w-13 object-contain"
                  />
                </div>
              </div>

              <div class="mt-7 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <article
                  v-for="item in approach"
                  :key="item.title"
                  class="rounded-[18px] border border-white/14 bg-white/8 p-4 backdrop-blur-sm"
                >
                  <div class="flex items-start gap-3">
                    <span
                      class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#F27C29] text-white shadow-[0_8px_20px_rgba(242,124,41,0.25)]"
                    >
                      <i :class="[item.icon, 'text-[13px]']"></i>
                    </span>

                    <div>
                      <h4
                        class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.12em] text-white"
                      >
                        {{ item.title }}
                      </h4>
                      <p class="mt-1 font-nunito text-[12px] leading-5 text-white/68">
                        {{ item.text }}
                      </p>
                    </div>
                  </div>
                </article>
              </div>

              <div
                class="mt-5 flex items-start gap-3 rounded-[18px] border border-white/15 bg-[#041F37]/35 px-4 py-4"
              >
                <i class="fa-solid fa-shield-halved mt-1 text-[14px] text-[#F7B267]"></i>
                <p class="font-nunito text-[12px] leading-5 text-white/70">
                  Treatment coverage, warranty conditions, and follow-up schedules are documented in
                  the approved service agreement.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services teaser. ID retained for current navbar/footer links. -->
    <section
      id="bestsellers"
      class="relative scroll-mt-20 overflow-hidden bg-linear-to-br from-[#F4F8FB] via-white to-[#EEF5FA] py-16 sm:py-20 lg:py-26"
    >
      <div class="absolute -left-20 top-20 h-64 w-64 rounded-full bg-[#025199]/6 blur-3xl"></div>
      <div
        class="absolute -right-20 bottom-12 h-72 w-72 rounded-full bg-[#F27C29]/7 blur-3xl"
      ></div>
      <div class="brand-dot-pattern absolute inset-0 opacity-[0.045]"></div>

      <div class="relative z-10 mx-auto w-full max-w-295 px-6 sm:px-8 lg:px-10">
        <div class="mx-auto max-w-3xl text-center">
          <p class="section-eyebrow">Our Services</p>
          <h2 class="section-title mt-4">
            Targeted solutions for common and complex pest concerns.
          </h2>
          <p class="section-copy mx-auto mt-5 max-w-2xl">
            The final treatment plan is recommended after a site inspection and property evaluation.
          </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="(service, index) in services"
            :key="service.title"
            class="service-card group"
          >
            <div
              class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26] transition-transform duration-500 group-hover:scale-x-100"
            ></div>

            <div class="flex items-start justify-between gap-4">
              <span class="service-icon">
                <i :class="[service.icon, 'text-[18px]']"></i>
              </span>

              <span
                class="font-montserrat text-[11px] font-semibold tracking-[0.14em] text-[#94A3B8]"
              >
                {{ String(index + 1).padStart(2, '0') }}
              </span>
            </div>

            <p
              class="mt-6 font-montserrat text-[10px] font-semibold uppercase tracking-[0.18em] text-[#F27C29]"
            >
              {{ service.category }}
            </p>

            <h3 class="mt-2 font-montserrat text-[20px] font-bold leading-snug text-[#163A5F]">
              {{ service.title }}
            </h3>

            <p class="mt-3 font-nunito text-[14px] leading-7 text-[#64748B]">
              {{ service.description }}
            </p>

            <ul class="mt-5 space-y-2">
              <li
                v-for="point in service.points"
                :key="point"
                class="flex items-start gap-2.5 font-nunito text-[13px] leading-6 text-[#526274]"
              >
                <i class="fa-solid fa-circle-check mt-1.5 text-[10px] text-[#025199]"></i>
                <span>{{ point }}</span>
              </li>
            </ul>

            <button
              type="button"
              class="mt-6 inline-flex items-center gap-2 border-b border-[#D8E4ED] pb-1 font-montserrat text-[11px] font-semibold uppercase tracking-[0.12em] text-[#025199] transition-all duration-200 hover:border-[#F27C29] hover:text-[#F27C29]"
              @click="showContactModal = true"
            >
              Ask About This Service
              <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </button>
          </article>
        </div>

        <div
          class="mt-10 flex flex-col items-start justify-between gap-5 rounded-3xl border border-[#D8E4ED] bg-white/90 p-5 shadow-[0_14px_40px_rgba(2,81,153,0.07)] sm:flex-row sm:items-center sm:p-6"
        >
          <div>
            <p
              class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.16em] text-[#F27C29]"
            >
              Need a tailored recommendation?
            </p>
            <p class="mt-2 max-w-2xl font-nunito text-[14px] leading-6 text-[#64748B]">
              Tell us what you are seeing and where the activity occurs. APESCON can arrange an
              inspection before preparing the appropriate proposal.
            </p>
          </div>

          <!-- Replace with router-link to="/services" when the dedicated page is ready. -->
          <button
            type="button"
            class="primary-cta w-full shrink-0 sm:w-auto"
            @click="showContactModal = true"
          >
            Discuss Your Pest Concern
            <i class="fa-solid fa-arrow-right text-[12px]"></i>
          </button>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section class="relative overflow-hidden bg-[#062B4C] py-16 sm:py-20 lg:py-26">
      <div class="absolute -left-20 -top-24 h-80 w-80 rounded-full bg-white/8 blur-3xl"></div>
      <div
        class="absolute -bottom-28 -right-16 h-88 w-88 rounded-full bg-[#F27C29]/16 blur-3xl"
      ></div>
      <div class="brand-dot-pattern-light absolute inset-0 opacity-[0.1]"></div>

      <div class="relative z-10 mx-auto w-full max-w-295 px-6 sm:px-8 lg:px-10">
        <div class="grid grid-cols-1 items-end gap-7 lg:grid-cols-12">
          <div class="lg:col-span-7">
            <p class="section-eyebrow text-[#F7B267]">How APESCON Works</p>

            <h2
              class="mt-4 max-w-3xl font-montserrat text-[30px] font-bold leading-tight text-white sm:text-[40px] lg:text-[50px]"
            >
              A clear service process from first inspection to follow-up
            </h2>
          </div>

          <p class="font-nunito text-[14px] leading-7 text-white/68 sm:text-[15px] lg:col-span-5">
            Every property has different pest pressures, structural conditions, and operational
            requirements. Our process keeps the scope clear before treatment begins.
          </p>
        </div>

        <div class="relative mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div
            class="absolute left-[12.5%] right-[12.5%] top-8 hidden h-px bg-linear-to-r from-transparent via-white/24 to-transparent lg:block"
          ></div>

          <article
            v-for="(step, index) in processSteps"
            :key="step.title"
            class="relative rounded-[22px] border border-white/14 bg-white/8 p-5 backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#F27C29]/55 hover:bg-white/10"
          >
            <div class="relative z-10 flex items-center justify-between">
              <span
                class="flex h-14 w-14 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white shadow-[0_10px_26px_rgba(0,0,0,0.16)]"
              >
                <i :class="[step.icon, 'text-[18px]']"></i>
              </span>

              <span class="font-montserrat text-[12px] font-bold tracking-[0.15em] text-[#F7B267]">
                0{{ index + 1 }}
              </span>
            </div>

            <h3
              class="mt-6 font-montserrat text-[16px] font-semibold uppercase tracking-[0.09em] text-white"
            >
              {{ step.title }}
            </h3>

            <p class="mt-3 font-nunito text-[13px] leading-6 text-white/65">
              {{ step.text }}
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- Who we serve -->
    <section class="relative overflow-hidden bg-white py-16 sm:py-20 lg:py-26">
      <div class="absolute -right-24 top-0 h-72 w-72 rounded-full bg-[#025199]/5 blur-3xl"></div>

      <div
        class="relative z-10 mx-auto grid w-full max-w-295 grid-cols-1 items-start gap-12 px-6 sm:px-8 lg:grid-cols-12 lg:gap-14 lg:px-10"
      >
        <div class="lg:sticky lg:top-28 lg:col-span-5">
          <p class="section-eyebrow">Who We Serve</p>

          <h2 class="section-title mt-4">
            Pest-control programs adapted to the way your property operates.
          </h2>

          <p class="section-copy mt-5">
            APESCON can support different property types from individual residences to facilities
            with operational, documentation, scheduling, and compliance requirements.
          </p>

          <button type="button" class="primary-cta mt-7" @click="showContactModal = true">
            Talk to Our Team
            <i class="fa-solid fa-arrow-right text-[12px]"></i>
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:col-span-7">
          <article
            v-for="client in clientTypes"
            :key="client.title"
            class="group relative overflow-hidden rounded-3xl border border-[#D8E4ED] bg-[#F8FBFD] p-5 transition-all duration-300 hover:-translate-y-1 hover:border-[#025199]/28 hover:bg-white hover:shadow-[0_18px_45px_rgba(2,81,153,0.09)] sm:p-6"
          >
            <div
              class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-[#025199]/5 transition-all duration-500 group-hover:scale-125 group-hover:bg-[#F27C29]/8"
            ></div>

            <span
              class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#025199] text-white shadow-[0_10px_24px_rgba(2,81,153,0.22)]"
            >
              <i :class="[client.icon, 'text-[17px]']"></i>
            </span>

            <h3 class="relative mt-5 font-montserrat text-[18px] font-bold text-[#163A5F]">
              {{ client.title }}
            </h3>

            <p class="relative mt-3 font-nunito text-[14px] leading-7 text-[#64748B]">
              {{ client.text }}
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- Warranty and reasons -->
    <section
      class="relative overflow-hidden border-y border-[#D8E4ED] bg-linear-to-br from-[#F3F8FB] via-white to-[#F8FAFC] py-16 sm:py-20 lg:py-26"
    >
      <div class="brand-dot-pattern absolute inset-0 opacity-[0.045]"></div>

      <div
        class="relative z-10 mx-auto grid w-full max-w-295 grid-cols-1 gap-8 px-6 sm:px-8 lg:grid-cols-12 lg:px-10"
      >
        <div
          class="relative overflow-hidden rounded-[28px] bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68] p-6 shadow-[0_22px_60px_rgba(5,35,61,0.2)] sm:p-8 lg:col-span-5"
        >
          <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
          <div
            class="absolute -bottom-20 -left-12 h-60 w-60 rounded-full bg-[#F27C29]/20 blur-3xl"
          ></div>

          <div class="relative z-10">
            <p
              class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.22em] text-[#F7B267]"
            >
              Service Assurance
            </p>

            <div class="mt-6 flex items-end gap-4">
              <span
                class="font-montserrat text-[72px] font-bold leading-none text-white sm:text-[88px]"
              >
                1
              </span>

              <div class="pb-2">
                <p class="font-montserrat text-[28px] font-bold leading-none text-white">Year</p>
                <p
                  class="mt-2 font-montserrat text-[11px] font-semibold uppercase tracking-[0.14em] text-white/60"
                >
                  Service Warranty
                </p>
              </div>
            </div>

            <p class="mt-5 font-nunito text-[14px] leading-7 text-white/72">
              Qualified contracted services may include a one-year warranty and scheduled follow-up
              visits, subject to the approved scope, pest condition, client responsibilities, and
              service agreement
            </p>

            <div class="mt-6 h-px bg-white/15"></div>

            <p class="mt-5 font-nunito text-[12px] leading-5 text-white/58">
              Warranty coverage and exclusions should always be confirmed in the signed contract.
            </p>
          </div>
        </div>

        <div class="lg:col-span-7">
          <p class="section-eyebrow">Why Choose APESCON</p>

          <h2 class="section-title mt-4">
            Professional service with clear scope and continued support
          </h2>

          <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <article
              v-for="reason in reasons"
              :key="reason.title"
              class="rounded-[20px] border border-[#D8E4ED] bg-white p-5 shadow-[0_10px_30px_rgba(2,81,153,0.05)]"
            >
              <div class="flex items-start gap-4">
                <span
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#F27C29]/10 text-[#F27C29]"
                >
                  <i :class="[reason.icon, 'text-[14px]']"></i>
                </span>

                <div>
                  <h3
                    class="font-montserrat text-[13px] font-semibold uppercase tracking-widest text-[#163A5F]"
                  >
                    {{ reason.title }}
                  </h3>

                  <p class="mt-2 font-nunito text-[13px] leading-6 text-[#64748B]">
                    {{ reason.text }}
                  </p>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ teaser -->
    <section class="bg-white py-16 sm:py-20 lg:py-24">
      <div
        class="mx-auto grid w-full max-w-295 grid-cols-1 items-center gap-10 px-6 sm:px-8 lg:grid-cols-12 lg:px-10"
      >
        <div class="lg:col-span-5">
          <p class="section-eyebrow">Before You Book</p>

          <h2 class="section-title mt-4">
            Questions about inspections, preparation, or follow-up?
          </h2>

          <p class="section-copy mt-5">
            Review common questions about pest-control visits or contact APESCON directly for
            property-specific guidance.
          </p>

          <button
            type="button"
            class="secondary-cta mt-7"
            @click="
              openComingSoon(
                'Customer Service Dashboard',
                'Online service monitoring and customer account tools are currently being prepared.',
                'fa-solid fa-chart-line',
              )
            "
          >
            <i class="fa-regular fa-circle-question text-[14px]"></i>
            Open Help & FAQs
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:col-span-7">
          <article
            v-for="faq in faqPreview"
            :key="faq.question"
            class="rounded-[20px] border border-[#D8E4ED] bg-[#F8FBFD] p-5 sm:p-6"
          >
            <div class="flex items-start gap-4">
              <span
                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#025199] font-montserrat text-[11px] font-bold text-white"
              >
                Q
              </span>

              <div>
                <h3 class="font-montserrat text-[14px] font-semibold leading-6 text-[#163A5F]">
                  {{ faq.question }}
                </h3>

                <p class="mt-2 font-nunito text-[13px] leading-6 text-[#64748B]">
                  {{ faq.answer }}
                </p>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="relative overflow-hidden bg-[#062B4C] py-14 sm:py-16 lg:py-20">
      <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-white/8 blur-3xl"></div>
      <div
        class="absolute -right-20 bottom-0 h-72 w-72 rounded-full bg-[#F27C29]/18 blur-3xl"
      ></div>
      <div class="brand-dot-pattern-light absolute inset-0 opacity-[0.1]"></div>

      <div
        class="relative z-10 mx-auto flex w-full max-w-295 flex-col items-start justify-between gap-7 px-6 sm:px-8 lg:flex-row lg:items-center lg:px-10"
      >
        <div>
          <p
            class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.22em] text-[#F7B267]"
          >
            Protect Your Property
          </p>

          <h2
            class="mt-3 max-w-3xl font-montserrat text-[29px] font-bold leading-tight text-white sm:text-[38px] lg:text-[46px]"
          >
            Let’s identify the problem before it becomes more costly.
          </h2>

          <p class="mt-4 max-w-2xl font-nunito text-[14px] leading-7 text-white/68 sm:text-[15px]">
            Request an inspection or speak with APESCON about the pest activity you are currently
            experiencing.
          </p>
        </div>

        <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row">
          <button
            type="button"
            class="inline-flex min-h-12 items-center justify-center gap-3 rounded-xl bg-[#F27C29] px-6 font-montserrat text-[12px] font-semibold uppercase tracking-[0.12em] text-white shadow-[0_14px_30px_rgba(242,124,41,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#D9671B]"
            @click="showContactModal = true"
          >
            <i class="fa-regular fa-envelope text-[13px]"></i>
            Contact APESCON
          </button>

          <a
            href="tel:+639173104694"
            class="inline-flex min-h-12 items-center justify-center gap-3 rounded-xl border border-white/24 bg-white/8 px-6 font-montserrat text-[12px] font-semibold uppercase tracking-[0.12em] text-white backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-white/45 hover:bg-white/12"
          >
            <i class="fa-solid fa-phone text-[12px]"></i>
            +63 917 310 4694
          </a>
        </div>
      </div>
    </section>

    <myFooterShort
      @open-contact-modal="showContactModal = true"
      @open-coming-soon-modal="handleComingSoonRequest"
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
import ComingSoonModal from '@/components/ComingSoonModal.vue'

export default {
  name: 'LandingPage',

  components: {
    Navbar,
    myFooterShort,
    ContactUsModal,
    LoginModal,
    PrivacyTermsModal,
    HelpFaqsModal,
    ComingSoonModal,
  },

  data() {
    return {
      showContactModal: false,
      showLoginModal: false,
      showLegalModal: false,
      legalInitialTab: 'privacy',
      showFaqsModal: false,
      showComingSoonModal: false,
      comingSoonFeatureTitle: '',
      comingSoonDescription: '',
      comingSoonIcon: 'fa-solid fa-screwdriver-wrench',

      activeHeroSlide: 0,
      heroInterval: null,

      heroSlides: [
        {
          alt: 'APESCON banner 1',
          xs: '/assets/banners/banner1-xs.webp',
          sm: '/assets/banners/banner1-sm.webp',
          md: '/assets/banners/banner1-md.webp',
          lg: '/assets/banners/banner1-lg.webp',
          xl: '/assets/banners/banner1-xl.webp',
        },
        {
          alt: 'APESCON banner 2',
          xs: '/assets/banners/banner2-xs.webp',
          sm: '/assets/banners/banner2-sm.webp',
          md: '/assets/banners/banner2-md.webp',
          lg: '/assets/banners/banner2-lg.webp',
          xl: '/assets/banners/banner2-xl.webp',
        },
        {
          alt: 'APESCON banner 3',
          xs: '/assets/banners/banner3-xs.webp',
          sm: '/assets/banners/banner3-sm.webp',
          md: '/assets/banners/banner3-md.webp',
          lg: '/assets/banners/banner3-lg.webp',
          xl: '/assets/banners/banner3-xl.webp',
        },
      ],

      commitments: [
        {
          title: 'Site Assessment',
          text: 'Recommendations based on actual property conditions.',
          icon: 'fa-solid fa-magnifying-glass-location',
        },
        {
          title: 'Tailored Treatment',
          text: 'Service plans matched to the identified pest concern.',
          icon: 'fa-solid fa-bullseye',
        },
        {
          title: 'Documented Scope',
          text: 'Clear service coverage, schedules, and responsibilities.',
          icon: 'fa-regular fa-file-lines',
        },
        {
          title: 'Follow-Up Support',
          text: 'Monitoring and revisit schedules based on the agreement.',
          icon: 'fa-solid fa-shield-halved',
        },
      ],

      approach: [
        {
          title: 'Inspect',
          text: 'Assess activity, access points, and contributing conditions.',
          icon: 'fa-solid fa-magnifying-glass',
        },
        {
          title: 'Identify',
          text: 'Determine the pest concern and practical treatment options.',
          icon: 'fa-solid fa-crosshairs',
        },
        {
          title: 'Treat',
          text: 'Execute the approved treatment plan with clear precautions.',
          icon: 'fa-solid fa-spray-can-sparkles',
        },
        {
          title: 'Verify',
          text: 'Review results and recommend monitoring or follow-up action.',
          icon: 'fa-solid fa-clipboard-check',
        },
      ],

      services: [
        {
          category: 'Structural Protection',
          title: 'Termite Management',
          description:
            'Inspection-led treatment and preventive recommendations for properties showing termite activity or risk.',
          icon: 'fa-solid fa-house-crack',
          points: ['Termite inspection', 'Treatment recommendations', 'Preventive guidance'],
        },
        {
          category: 'General Pest Control',
          title: 'Crawling Insect Control',
          description:
            'Targeted management for cockroaches, ants, and other crawling pests commonly found in occupied spaces.',
          icon: 'fa-solid fa-bug',
          points: ['Activity assessment', 'Targeted application', 'Sanitation advice'],
        },
        {
          category: 'Property Protection',
          title: 'Rodent Management',
          description:
            'Control planning focused on rodent activity, entry points, harborage areas, and property conditions.',
          icon: 'fa-solid fa-shield-cat',
          points: ['Rodent activity check', 'Entry-point review', 'Monitoring strategy'],
        },
        {
          category: 'Indoor Treatment',
          title: 'Bed Bug Treatment',
          description:
            'Property-specific treatment planning for suspected or confirmed bed bug activity in sleeping areas.',
          icon: 'fa-solid fa-bed',
          points: ['Room assessment', 'Preparation guidance', 'Follow-up recommendation'],
        },
        {
          category: 'Flying Insects',
          title: 'Mosquito & Fly Control',
          description:
            'Control measures for flying-insect pressure around residential, commercial, and operational areas.',
          icon: 'fa-solid fa-mosquito',
          points: ['Breeding-site review', 'Area treatment', 'Prevention guidance'],
        },
        {
          category: 'Managed Programs',
          title: 'Commercial Pest Programs',
          description:
            'Scheduled pest-management support for businesses, facilities, institutions, and government properties.',
          icon: 'fa-solid fa-building-shield',
          points: ['Scheduled servicing', 'Site coordination', 'Service documentation'],
        },
      ],

      processSteps: [
        {
          title: 'Site Inspection',
          text: 'A technician reviews the property, pest activity, risk areas, and service requirements.',
          icon: 'fa-solid fa-magnifying-glass-location',
        },
        {
          title: 'Quotation & Plan',
          text: 'APESCON prepares the recommended scope, service cost, schedule, and contract terms.',
          icon: 'fa-regular fa-file-lines',
        },
        {
          title: 'Service Execution',
          text: 'The approved treatment is carried out with the necessary preparation and safety guidance.',
          icon: 'fa-solid fa-spray-can-sparkles',
        },
        {
          title: 'Follow-Up',
          text: 'Results are reviewed and revisit or warranty schedules are followed according to the agreement.',
          icon: 'fa-solid fa-clipboard-check',
        },
      ],

      clientTypes: [
        {
          title: 'Residential',
          text: 'Pest-control support for houses, condominiums, apartments, and residential communities.',
          icon: 'fa-solid fa-house',
        },
        {
          title: 'Commercial',
          text: 'Structured servicing for offices, retail spaces, restaurants, warehouses, and other businesses.',
          icon: 'fa-solid fa-store',
        },
        {
          title: 'Industrial',
          text: 'Coordinated pest-management programs for facilities with operational and scheduling requirements.',
          icon: 'fa-solid fa-industry',
        },
        {
          title: 'Government & Institutional',
          text: 'Service planning for LGUs, public offices, schools, facilities, and other institutional properties.',
          icon: 'fa-solid fa-landmark',
        },
      ],

      reasons: [
        {
          title: 'Inspection-Based Advice',
          text: 'Recommendations are prepared after reviewing the actual pest concern and property condition.',
          icon: 'fa-solid fa-magnifying-glass',
        },
        {
          title: 'Transparent Service Scope',
          text: 'Treatment coverage, pricing, schedules, and responsibilities are defined before execution.',
          icon: 'fa-regular fa-file-lines',
        },
        {
          title: 'Safety Guidance',
          text: 'Clients receive preparation and post-treatment instructions appropriate to the service.',
          icon: 'fa-solid fa-shield-halved',
        },
        {
          title: 'Continued Support',
          text: 'Follow-up visits and warranty conditions are managed according to the signed agreement.',
          icon: 'fa-solid fa-headset',
        },
      ],

      faqPreview: [
        {
          question: 'Do you need to inspect the property before providing a quotation?',
          answer:
            'An inspection is recommended so the quotation reflects the pest condition, affected area, and required treatment scope.',
        },
        {
          question: 'Can APESCON serve business and government properties?',
          answer:
            'Yes. Service planning can be adjusted for residential, commercial, industrial, institutional, and government clients.',
        },
        {
          question: 'Will the service include follow-up visits?',
          answer:
            'Follow-up schedules depend on the pest concern and the approved service agreement. Coverage is explained before the contract is signed.',
        },
      ],
    }
  },

  mounted() {
    this.startHeroSlider()
    document.addEventListener('visibilitychange', this.handleVisibilityChange)
  },

  beforeUnmount() {
    this.stopHeroSlider()
    document.removeEventListener('visibilitychange', this.handleVisibilityChange)
  },

  methods: {
    startHeroSlider() {
      this.stopHeroSlider()

      if (this.heroSlides.length < 2) return

      const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches
      if (reduceMotion) return

      this.heroInterval = window.setInterval(() => {
        this.nextHeroSlide()
      }, 5000)
    },

    stopHeroSlider() {
      if (!this.heroInterval) return

      window.clearInterval(this.heroInterval)
      this.heroInterval = null
    },

    nextHeroSlide() {
      this.activeHeroSlide = (this.activeHeroSlide + 1) % this.heroSlides.length
    },

    goToHeroSlide(index) {
      this.activeHeroSlide = index
      this.startHeroSlider()
    },

    handleComingSoonRequest(content = {}) {
      this.openComingSoon(
        content.featureTitle || 'Coming Soon',
        content.description ||
          'This feature is currently being prepared and will be available soon.',
        content.iconClass || 'fa-solid fa-screwdriver-wrench',
      )
    },

    handleVisibilityChange() {
      if (document.hidden) {
        this.stopHeroSlider()
        return
      }

      this.startHeroSlider()
    },

    scrollToServices() {
      document.getElementById('bestsellers')?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
      })
    },

    openPrivacyModal() {
      this.legalInitialTab = 'privacy'
      this.showLegalModal = true
    },

    openTermsModal() {
      this.legalInitialTab = 'terms'
      this.showLegalModal = true
    },

    openComingSoon(
      title,
      description = 'This feature is not available yet. We are currently working on it and will make it accessible in a future update.',
      icon = 'fa-solid fa-screwdriver-wrench',
    ) {
      this.comingSoonFeatureTitle = title
      this.comingSoonDescription = description
      this.comingSoonIcon = icon
      this.showComingSoonModal = true
    },
  },
}
</script>

<style scoped>
.brand-dot-pattern {
  pointer-events: none;
  background-image: radial-gradient(circle at 1px 1px, rgba(2, 81, 153, 0.55) 1px, transparent 0);
  background-size: 24px 24px;
}

.brand-dot-pattern-light {
  pointer-events: none;
  background-image: radial-gradient(
    circle at 1px 1px,
    rgba(255, 255, 255, 0.72) 1px,
    transparent 0
  );
  background-size: 24px 24px;
}

.section-eyebrow {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  line-height: 1.5;
  text-transform: uppercase;
  color: #f27c29;
}

.section-title {
  max-width: 48rem;
  font-family: 'Montserrat', sans-serif;
  font-size: clamp(1.875rem, 4vw, 3.25rem);
  font-weight: 700;
  line-height: 1.12;
  color: #163a5f;
}

.section-copy {
  max-width: 44rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.9375rem;
  line-height: 1.85;
  color: #64748b;
}

.primary-cta,
.secondary-cta {
  display: inline-flex;
  min-height: 3rem;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  border-radius: 0.75rem;
  padding: 0.75rem 1.5rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.11em;
  text-transform: uppercase;
  transition:
    transform 250ms ease,
    border-color 250ms ease,
    background-color 250ms ease,
    color 250ms ease,
    box-shadow 250ms ease;
}

.primary-cta {
  border: 1px solid #025199;
  background: #025199;
  color: white;
  box-shadow: 0 12px 28px rgba(2, 81, 153, 0.22);
}

.primary-cta:hover {
  transform: translateY(-2px);
  border-color: #063f73;
  background: #063f73;
  box-shadow: 0 16px 34px rgba(2, 81, 153, 0.28);
}

.secondary-cta {
  border: 1px solid #cbd8e2;
  background: white;
  color: #163a5f;
}

.secondary-cta:hover {
  transform: translateY(-2px);
  border-color: #f27c29;
  color: #025199;
  box-shadow: 0 12px 28px rgba(2, 81, 153, 0.08);
}

.service-card {
  position: relative;
  overflow: hidden;
  border: 1px solid #d8e4ed;
  border-radius: 1.5rem;
  background: rgba(255, 255, 255, 0.94);
  padding: 1.5rem;
  box-shadow: 0 14px 40px rgba(2, 81, 153, 0.06);
  transition:
    transform 300ms ease,
    border-color 300ms ease,
    box-shadow 300ms ease;
}

.service-card:hover {
  transform: translateY(-6px);
  border-color: rgba(2, 81, 153, 0.28);
  box-shadow: 0 24px 58px rgba(2, 81, 153, 0.12);
}

.service-icon {
  display: flex;
  width: 3.25rem;
  height: 3.25rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(2, 81, 153, 0.12);
  border-radius: 1rem;
  background: rgba(2, 81, 153, 0.08);
  color: #025199;
  transition:
    transform 300ms ease,
    background-color 300ms ease,
    color 300ms ease;
}

.service-card:hover .service-icon {
  transform: scale(1.05);
  background: #025199;
  color: white;
}

@media (min-width: 640px) {
  .section-copy {
    font-size: 1rem;
  }

  .service-card {
    padding: 1.75rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .primary-cta,
  .secondary-cta,
  .service-card,
  .service-icon {
    transition: none;
  }
}
</style>
