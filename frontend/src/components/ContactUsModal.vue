<template>
  <transition name="contact-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-200 flex items-center justify-center overflow-y-auto p-2 sm:p-4 lg:p-6"
      role="dialog"
      aria-modal="true"
      aria-labelledby="contact-modal-title"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-[#071A2B]/65 backdrop-blur-sm" @click="handleClose"></div>

      <!-- Modal -->
      <div
        class="relative z-10 my-auto max-h-[calc(100dvh-1rem)] w-full max-w-6xl overflow-y-auto rounded-[26px] border border-[#D8E4ED] bg-white shadow-[0_30px_90px_rgba(5,35,61,0.28)] sm:max-h-[calc(100dvh-2rem)] sm:rounded-[30px] lg:max-h-[calc(100dvh-3rem)]"
        :class="isClosing ? 'animate-modal-out' : 'animate-modal-in'"
      >
        <!-- Top brand accent -->
        <div
          class="absolute inset-x-0 top-0 z-30 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
        ></div>

        <div class="grid grid-cols-1 lg:grid-cols-[0.92fr_1.08fr]">
          <!-- LEFT PANEL -->
          <section
            class="relative overflow-hidden bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68]"
          >
            <!-- Mobile close -->
            <button
              type="button"
              @click="handleClose"
              aria-label="Close contact modal"
              class="absolute right-4 top-4 z-30 flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-md transition-all duration-200 hover:border-[#F27C29] hover:bg-white/18 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-white/20 lg:hidden"
            >
              <svg
                viewBox="0 0 24 24"
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.4"
                stroke-linecap="round"
                aria-hidden="true"
              >
                <path d="M6 6L18 18M18 6L6 18" />
              </svg>
            </button>

            <!-- Decorative atmosphere -->
            <div
              class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"
            ></div>

            <div
              class="pointer-events-none absolute -bottom-24 -right-20 h-80 w-80 rounded-full bg-[#F27C29]/18 blur-3xl"
            ></div>

            <!-- Dot pattern -->
            <div
              class="pointer-events-none absolute inset-0 opacity-[0.12]"
              style="
                background-image: radial-gradient(
                  circle at 1px 1px,
                  rgba(255, 255, 255, 0.75) 1px,
                  transparent 0
                );
                background-size: 24px 24px;
              "
            ></div>

            <div class="relative z-10 flex h-full flex-col p-5 sm:p-7 lg:p-8">
              <!-- Brand -->
              <div class="pr-12 lg:pr-0">
                <div class="flex items-center gap-4">
                  <div
                    class="flex h-18 w-18 shrink-0 items-center justify-center rounded-[20px] border border-white/20 bg-white shadow-[0_14px_30px_rgba(0,0,0,0.16)]"
                  >
                    <img
                      src="/assets/apescon_logo_transparent.png"
                      alt="APESCON Associated Pest Control"
                      class="h-15 w-15 object-contain"
                    />
                  </div>

                  <div>
                    <p
                      class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.22em] text-[#F7B267]"
                    >
                      Associated Pest Control
                    </p>

                    <p class="mt-1 font-montserrat text-[18px] font-bold text-white">APESCON</p>
                  </div>
                </div>

                <h2
                  class="mt-7 max-w-md font-montserrat text-[28px] font-bold leading-[1.15] text-white sm:text-[34px] lg:text-[40px]"
                >
                  Professional support for a safer, pest free property
                </h2>

                <p
                  class="mt-4 max-w-md font-nunito text-[14px] leading-7 text-white/72 sm:text-[15px]"
                >
                  Contact APESCON for service inquiries, property inspections, quotations, scheduled
                  treatments, and follow-up assistance.
                </p>
              </div>

              <!-- MAP -->
              <div
                class="mt-7 overflow-hidden rounded-[20px] border border-white/20 bg-white/10 shadow-[0_18px_40px_rgba(0,0,0,0.16)]"
              >
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1147.3996785033105!2d121.07589857811956!3d14.691253792065055!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b0a649702f7d%3A0x168a1c7443ea2c5c!2s39%20N%20Romualdez%2C%20Quezon%20City%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1785401504556!5m2!1sen!2sph"
                  title="APESCON office location"
                  class="h-52 w-full sm:h-60 md:h-58 lg:h-64"
                  style="border: 0"
                  allowfullscreen
                  loading="lazy"
                  referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>
              </div>

              <!-- Location -->
              <div
                class="mt-5 rounded-[20px] border border-white/15 bg-white/10 p-4 backdrop-blur-md sm:p-5"
              >
                <div class="flex items-start gap-3">
                  <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#F27C29] text-white shadow-[0_10px_24px_rgba(242,124,41,0.28)]"
                  >
                    <i class="fa-solid fa-location-dot text-[17px]"></i>
                  </div>

                  <div>
                    <p
                      class="font-montserrat text-[12px] font-semibold uppercase tracking-[0.16em] text-white"
                    >
                      APESCON Office
                    </p>

                    <p class="mt-2 font-nunito text-[14px] leading-6 text-white/72">
                      39 N. Romualdez Street<br />
                      BF Homes, Quezon City<br />
                      Metro Manila, Philippines
                    </p>

                    <a
                      href="https://www.google.com/maps/search/?api=1&query=39+N+Romualdez+Street+BF+Homes+Quezon+City+Metro+Manila"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="group mt-3 inline-flex items-center gap-2 font-montserrat text-[11px] font-semibold uppercase tracking-[0.12em] text-[#F7B267] transition hover:text-white"
                    >
                      Open in Google Maps

                      <i
                        class="fa-solid fa-arrow-right text-[10px] transition-transform duration-200 group-hover:translate-x-1"
                      ></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- RIGHT PANEL -->
          <section class="relative bg-linear-to-br from-white via-[#FCFDFE] to-[#F3F7FA]">
            <!-- Desktop close -->
            <button
              type="button"
              @click="handleClose"
              aria-label="Close contact modal"
              class="absolute right-5 top-5 z-30 hidden h-11 w-11 items-center justify-center rounded-full border border-[#D8E4ED] bg-white text-[#475569] shadow-sm transition-all duration-200 hover:border-[#025199] hover:text-[#025199] hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#025199]/12 lg:flex"
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

            <div class="px-5 py-7 sm:px-7 sm:py-8 lg:px-9 lg:py-10">
              <!-- Heading -->
              <div class="pr-0 lg:pr-14">
                <p
                  class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.22em] text-[#F27C29]"
                >
                  Contact APESCON
                </p>

                <h3
                  id="contact-modal-title"
                  class="mt-3 max-w-xl font-montserrat text-[26px] font-bold leading-tight text-[#163A5F] sm:text-[32px] lg:text-[36px]"
                >
                  Let’s discuss your pest control requirements
                </h3>

                <p
                  class="mt-4 max-w-xl font-nunito text-[14px] leading-7 text-[#64748B] sm:text-[15px]"
                >
                  Reach our team through the official contact details below. We can assist with
                  residential, commercial, industrial, institutional, and government service
                  inquiries.
                </p>
              </div>

              <!-- Contact cards -->
              <div class="mt-7 space-y-4">
                <!-- Address -->
                <article class="contact-card group border-l-4 border-l-[#F27C29]">
                  <div class="flex items-start gap-4">
                    <div class="contact-icon">
                      <i class="fa-solid fa-location-dot text-[17px]"></i>
                    </div>

                    <div>
                      <p class="contact-label">Office Address</p>

                      <p class="mt-2 font-nunito text-[14px] leading-7 text-[#64748B]">
                        39 N. Romualdez Street<br />
                        BF Homes, Quezon City<br />
                        Metro Manila, Philippines
                      </p>
                    </div>
                  </div>
                </article>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                  <!-- Email -->
                  <article class="contact-card group">
                    <div class="flex items-start gap-4">
                      <div class="contact-icon">
                        <i class="fa-regular fa-envelope text-[17px]"></i>
                      </div>

                      <div class="min-w-0">
                        <p class="contact-label">Email</p>

                        <a
                          href="mailto:apesconservice@gmail.com"
                          class="mt-2 inline-block break-all font-nunito text-[14px] leading-6 text-[#64748B] transition-colors duration-200 hover:text-[#025199]"
                        >
                          apesconservice@gmail.com
                        </a>
                      </div>
                    </div>
                  </article>

                  <!-- Mobile -->
                  <article class="contact-card group">
                    <div class="flex items-start gap-4">
                      <div class="contact-icon">
                        <i class="fa-solid fa-mobile-screen-button text-[16px]"></i>
                      </div>

                      <div>
                        <p class="contact-label">Mobile</p>

                        <a
                          href="tel:+639173104694"
                          class="mt-2 inline-block font-nunito text-[14px] leading-6 text-[#64748B] transition-colors duration-200 hover:text-[#025199]"
                        >
                          +63 917 310 4694
                        </a>
                      </div>
                    </div>
                  </article>
                </div>

                <!-- Facebook -->
                <article class="contact-card group">
                  <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                      <div class="contact-icon">
                        <i class="fab fa-facebook-f text-[17px]"></i>
                      </div>

                      <div>
                        <p class="contact-label">Official Facebook Page</p>

                        <p class="mt-1 font-nunito text-[13px] text-[#64748B]">
                          Follow APESCON for company and service updates.
                        </p>
                      </div>
                    </div>

                    <a
                      href="https://www.facebook.com/associatedpest"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#025199] text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#063F73]"
                      aria-label="Visit APESCON Facebook page"
                    >
                      <i class="fa-solid fa-arrow-up-right-from-square text-[12px]"></i>
                    </a>
                  </div>
                </article>
              </div>

              <!-- Actions -->
              <div class="mt-7 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                  href="tel:+639173104694"
                  class="group inline-flex min-h-12 items-center justify-center gap-3 rounded-xl bg-[#025199] px-5 font-montserrat text-[12px] font-semibold uppercase tracking-[0.12em] text-white shadow-[0_12px_28px_rgba(2,81,153,0.24)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#063F73] hover:shadow-[0_16px_34px_rgba(2,81,153,0.30)]"
                >
                  <i class="fa-solid fa-phone text-[12px]"></i>
                  Call APESCON
                </a>

                <a
                  href="mailto:apesconservice@gmail.com"
                  class="group inline-flex min-h-12 items-center justify-center gap-3 rounded-xl border border-[#CBD8E2] bg-white px-5 font-montserrat text-[12px] font-semibold uppercase tracking-[0.12em] text-[#163A5F] transition-all duration-300 hover:-translate-y-0.5 hover:border-[#F27C29] hover:text-[#025199] hover:shadow-md"
                >
                  <i class="fa-regular fa-envelope text-[13px]"></i>
                  Send an Email
                </a>
              </div>

              <div
                class="mt-6 flex items-start gap-3 rounded-xl border border-[#D8E4ED] bg-[#F7FAFC] px-4 py-3"
              >
                <i class="fa-solid fa-shield-halved mt-1 text-[14px] text-[#F27C29]"></i>

                <p class="font-nunito text-[12px] leading-5 text-[#64748B]">
                  Use only the official APESCON contact details shown here for quotations,
                  appointments, and service coordination.
                </p>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'ContactUsModal',

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
    }
  },

  watch: {
    show(value) {
      if (value) {
        this.lockBody()
        document.addEventListener('keydown', this.handleEscape)
      } else {
        this.unlockBody()
        document.removeEventListener('keydown', this.handleEscape)
      }
    },
  },

  methods: {
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
.contact-card {
  border-top: 1px solid #d8e4ed;
  border-right: 1px solid #d8e4ed;
  border-bottom: 1px solid #d8e4ed;
  border-left: 1px solid #d8e4ed;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  padding: 1rem;
  transition:
    border-color 220ms ease,
    box-shadow 220ms ease,
    transform 220ms ease;
}

.contact-card:hover {
  border-color: rgba(2, 81, 153, 0.28);
  box-shadow: 0 14px 34px rgba(2, 81, 153, 0.08);
  transform: translateY(-2px);
}

.contact-icon {
  display: flex;
  width: 2.75rem;
  height: 2.75rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(2, 81, 153, 0.12);
  border-radius: 9999px;
  background: rgba(2, 81, 153, 0.08);
  color: #025199;
  transition:
    background-color 220ms ease,
    color 220ms ease,
    transform 220ms ease;
}

.contact-card:hover .contact-icon {
  background: #025199;
  color: white;
  transform: scale(1.04);
}

.contact-label {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.13em;
  text-transform: uppercase;
  color: #163a5f;
}

.contact-fade-enter-active,
.contact-fade-leave-active {
  transition: opacity 0.22s ease;
}

.contact-fade-enter-from,
.contact-fade-leave-to {
  opacity: 0;
}

@keyframes modalIn {
  0% {
    opacity: 0;
    transform: translateY(16px) scale(0.98);
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
    transform: translateY(12px) scale(0.985);
  }
}

.animate-modal-in {
  animation: modalIn 0.24s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.animate-modal-out {
  animation: modalOut 0.2s ease-in forwards;
}

@media (min-width: 640px) {
  .contact-card {
    padding: 1.125rem;
  }
}
</style>
