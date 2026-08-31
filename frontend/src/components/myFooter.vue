<template>
  <footer
    class="relative overflow-hidden border-t border-[#D8E4ED] bg-linear-to-br from-white via-[#FAFCFE] to-[#F2F7FB]"
  >
    <!-- Subtle brand accent -->
    <div
      class="absolute inset-x-0 top-0 h-0.5 bg-linear-to-r from-transparent via-[#025199] to-transparent"
    ></div>

    <!-- Decorative background -->
    <div
      class="pointer-events-none absolute -left-20 top-10 h-64 w-64 rounded-full bg-[#025199]/5 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -right-20 bottom-10 h-64 w-64 rounded-full bg-[#F27C29]/5 blur-3xl"
    ></div>

    <div class="relative z-10 mx-auto w-full max-w-295 px-6 pb-8 pt-16 sm:px-8 lg:px-10">
      <!-- TOP -->
      <div class="grid grid-cols-1 items-start gap-12 md:grid-cols-12 md:gap-8">
        <!-- LEFT: Logo + Contact -->
        <div class="md:col-span-4">
          <div class="flex flex-col items-center text-center md:items-start md:text-left">
            <router-link
              to="/"
              class="inline-flex items-center justify-center"
              aria-label="APESCON Home"
            >
              <img
                src="/assets/apescon_logo_transparent.png"
                alt="APESCON Associated Pest Control"
                class="h-auto w-28 object-contain sm:w-32 md:w-36"
              />
            </router-link>

            <div class="mt-1 font-nunito text-[14px] leading-7 text-[#64748B] sm:text-[15px]">
              <p>
                39 N. Romualdez Street<br />
                BF Homes, Quezon City<br />
                Metro Manila, Philippines
              </p>

              <div class="mt-5 space-y-2">
                <p class="flex flex-col items-center gap-1 md:items-start">
                  <a
                    href="mailto:apesconservice@gmail.com"
                    class="break-all transition-colors duration-200 hover:text-[#025199]"
                  >
                    <b>Email : </b> apesconservice@gmail.com
                  </a>
                </p>

                <p class="flex flex-col items-center gap-1 md:items-start">
                  <a
                    href="tel:+639173104694"
                    class="transition-colors duration-200 hover:text-[#025199]"
                  >
                    <b>Mobile : </b>+63 917 310 4694
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="md:col-span-8 md:pt-5">
          <div class="grid grid-cols-1 items-start gap-10 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Quick Links -->
            <div>
              <h3
                class="font-montserrat text-[16px] font-semibold uppercase tracking-[0.13em] text-[#163A5F]"
              >
                Quick Links
              </h3>

              <div class="mt-3 h-0.5 w-10 rounded-full bg-[#F27C29]"></div>

              <ul class="mt-6 space-y-3 font-nunito text-[15px] text-[#64748B]">
                <li>
                  <router-link to="/" class="footer-link group inline-flex items-center gap-2">
                    <span
                      class="h-1.5 w-1.5 rounded-full bg-[#F27C29] opacity-0 transition group-hover:opacity-100"
                    ></span>
                    Home
                  </router-link>
                </li>

                <li>
                  <router-link
                    :to="{ path: '/', hash: '#bestsellers' }"
                    class="footer-link group inline-flex items-center gap-2"
                  >
                    <span
                      class="h-1.5 w-1.5 rounded-full bg-[#F27C29] opacity-0 transition group-hover:opacity-100"
                    ></span>
                    Our Services
                  </router-link>
                </li>

                <li>
                  <button
                    type="button"
                    class="footer-link group inline-flex items-center gap-2"
                    @click="
                      openComingSoon(
                        'Announcements',
                        'The APESCON announcements page is currently being prepared.',
                        'fa-solid fa-bullhorn',
                      )
                    "
                  >
                    <span
                      class="h-1.5 w-1.5 rounded-full bg-[#F27C29] opacity-0 transition group-hover:opacity-100"
                    ></span>

                    Announcement
                  </button>
                </li>
              </ul>
            </div>

            <!-- Useful Links -->
            <div>
              <h3
                class="font-montserrat text-[16px] font-semibold uppercase tracking-[0.13em] text-[#163A5F]"
              >
                Useful Links
              </h3>

              <div class="mt-3 h-0.5 w-10 rounded-full bg-[#F27C29]"></div>

              <ul class="mt-6 space-y-3 font-nunito text-[15px] text-[#64748B]">
                <li>
                  <button
                    type="button"
                    @click="$emit('open-contact-modal')"
                    class="footer-link group inline-flex items-center gap-2"
                  >
                    <span
                      class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#F27C29] opacity-0 transition-all duration-200 group-hover:opacity-100"
                    ></span>

                    Contact Us
                  </button>
                </li>

                <li>
                  <button
                    type="button"
                    @click="
                      openComingSoon(
                        'Help & FAQs',
                        'The APESCON help and frequently asked questions section is currently being prepared.',
                        'fa-regular fa-circle-question',
                      )
                    "
                    class="footer-link group inline-flex items-center gap-2"
                  >
                    <span
                      class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#F27C29] opacity-0 transition-all duration-200 group-hover:opacity-100"
                    ></span>

                    Help & FAQs
                  </button>
                </li>

                <li>
                  <button
                    type="button"
                    class="footer-link group inline-flex items-center gap-2"
                    @click="
                      openComingSoon(
                        'Privacy Policy',
                        'The APESCON privacy policy page is currently being finalized.',
                        'fa-solid fa-shield-halved',
                      )
                    "
                  >
                    <span
                      class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#F27C29] opacity-0 transition-all duration-200 group-hover:opacity-100"
                    ></span>

                    Privacy Policy
                  </button>
                </li>

                <li>
                  <button
                    type="button"
                    class="footer-link group inline-flex items-center gap-2"
                    @click="
                      openComingSoon(
                        'Terms & Conditions',
                        'The APESCON terms and conditions page is currently being finalized.',
                        'fa-regular fa-file-lines',
                      )
                    "
                  >
                    <span
                      class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#F27C29] opacity-0 transition-all duration-200 group-hover:opacity-100"
                    ></span>

                    Terms & Conditions
                  </button>
                </li>
              </ul>
            </div>

            <!-- Subscribe -->
            <div>
              <h3
                class="font-montserrat text-[16px] font-semibold uppercase tracking-[0.13em] text-[#163A5F]"
              >
                Stay Updated
              </h3>

              <div class="mt-3 h-0.5 w-10 rounded-full bg-[#F27C29]"></div>

              <p class="mt-6 max-w-70 font-nunito text-[15px] leading-7 text-[#64748B]">
                Receive APESCON service updates, pest-control advice, and company announcements.
              </p>

              <form class="mt-5 w-full max-w-80" @submit.prevent="submitSubscribe">
                <label for="footer-email" class="sr-only">Email Address</label>

                <div class="relative">
                  <input
                    id="footer-email"
                    v-model="email"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="Email Address"
                    class="h-12 w-full rounded-lg border border-[#CBD8E2] bg-white pl-4 pr-13 font-nunito text-[14px] text-[#334155] outline-none transition duration-200 placeholder:text-[#94A3B8] focus:border-[#025199] focus:ring-4 focus:ring-[#025199]/10"
                  />

                  <button
                    type="submit"
                    aria-label="Subscribe"
                    class="group absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-[#025199] text-white transition-all duration-200 hover:bg-[#063F73] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/20"
                  >
                    <i
                      class="fa-solid fa-arrow-right text-[12px] transition-transform duration-200 group-hover:translate-x-0.5"
                    ></i>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="mt-12 border-t border-[#D8E4ED] pt-7">
        <div
          class="flex flex-col items-center justify-between gap-5 text-center md:flex-row md:text-left"
        >
          <p class="font-nunito text-[14px] text-[#64748B]">
            © 2026 APESCON. All Rights Reserved.
          </p>

          <div
            class="flex flex-wrap items-center justify-center gap-3 font-montserrat text-[12px] font-medium uppercase tracking-[0.08em] text-[#64748B]"
          >
            <a
              href="https://www.facebook.com/associatedpest"
              target="_blank"
              rel="noopener noreferrer"
              class="social-link"
            >
              <i class="fab fa-facebook-f text-[13px]"></i>
              <span>Facebook</span>
            </a>

            <span class="text-[#CBD5E1]">|</span>

            <button
              type="button"
              class="social-link"
              @click="
                openComingSoon(
                  'APESCON on TikTok',
                  'Our official TikTok page will be available soon.',
                  'fab fa-tiktok',
                )
              "
            >
              <i class="fab fa-tiktok text-[13px]"></i>
              <span>TikTok</span>
            </button>

            <span class="text-[#CBD5E1]">|</span>

            <button
              type="button"
              class="social-link"
              @click="
                openComingSoon(
                  'APESCON on Instagram',
                  'Our official Instagram page will be available soon.',
                  'fab fa-instagram',
                )
              "
            >
              <i class="fab fa-instagram text-[14px]"></i>
              <span>Instagram</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Back to top -->
      <button
        type="button"
        @click="scrollToTop"
        aria-label="Back to top"
        class="group fixed bottom-5 right-5 z-40 flex h-12 w-12 items-center justify-center rounded-full border border-white/30 bg-[#025199] text-white shadow-[0_12px_30px_rgba(2,81,153,0.30)] transition-all duration-300 hover:-translate-y-1 hover:bg-[#063F73] hover:shadow-[0_16px_38px_rgba(2,81,153,0.38)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/20"
      >
        <i
          class="fa-solid fa-arrow-up text-[16px] transition-transform duration-300 group-hover:-translate-y-0.5"
        ></i>
      </button>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'myFooterShort',
  emits: ['open-contact-modal', 'open-coming-soon-modal'],
  data() {
    return {
      email: '',
    }
  },
  methods: {
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    submitSubscribe() {
      if (!this.email) return
      console.log('Subscribed email:', this.email)
      this.email = ''
    },
    openComingSoon(featureTitle, description, iconClass = 'fa-solid fa-screwdriver-wrench') {
      this.$emit('open-coming-soon-modal', {
        featureTitle,
        description,
        iconClass,
      })
    },
  },
}
</script>

<style scoped>
footer a,
footer button {
  cursor: pointer;
}

.footer-link {
  color: #64748b;
  transition:
    color 200ms ease,
    transform 200ms ease;
}

.footer-link:hover {
  color: #025199;
  transform: translateX(3px);
}

.social-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;

  /* Keep anchors and buttons identical */
  margin: 0;
  padding: 0;
  border: 0;
  background: transparent;
  appearance: none;

  font-family: 'Montserrat', sans-serif;
  font-size: 0.75rem;
  font-weight: 500;
  line-height: 1.25rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: inherit;

  cursor: pointer;

  transition:
    color 200ms ease,
    transform 200ms ease;
}

.social-link:hover {
  color: #025199;
  transform: translateY(-1px);
}
</style>
