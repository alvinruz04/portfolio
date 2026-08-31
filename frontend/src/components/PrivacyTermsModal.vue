<template>
  <transition name="legal-policy-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-200 flex items-start justify-center overflow-y-auto p-3 py-6 sm:p-4 sm:py-6 md:items-center md:p-6"
      @click.self="handleClose"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-[#2b1f25]/40 backdrop-blur-md"></div>

      <!-- Modal -->
      <div
        class="relative z-10 w-[95vw] max-w-260 overflow-visible rounded-[30px] border border-white/50 bg-white shadow-[0_28px_80px_rgba(82,45,61,0.22)] md:max-h-[92vh] md:overflow-hidden"
        :class="isClosing ? 'animate-legal-policy-out' : 'animate-legal-policy-in'"
      >
        <!-- Decorative glow -->
        <div
          class="pointer-events-none absolute -top-20 -left-20 h-64 w-64 rounded-full bg-[#ffd7e6]/55 blur-3xl"
        ></div>
        <div
          class="pointer-events-none absolute -bottom-24 right-0 h-72 w-72 rounded-full bg-[#f4bfd3]/35 blur-3xl"
        ></div>

        <!-- Top accent -->
        <div
          class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-[#f7c8d9] via-[#e97aac] to-[#f7c8d9]"
        ></div>

        <!-- Close -->
        <button
          @click="handleClose"
          aria-label="Close legal policy modal"
          class="absolute right-4 top-4 z-30 flex h-10 w-10 items-center justify-center rounded-full border border-[#efdde5] bg-white/90 text-[#7b8190] shadow-sm transition hover:border-[#e97aac] hover:text-[#e97aac] hover:shadow-md sm:right-5 sm:top-5 sm:h-11 sm:w-11"
        >
          <i class="fa-solid fa-xmark text-[16px]"></i>
        </button>

        <div
          class="relative z-10 grid max-h-none grid-cols-1 overflow-visible md:max-h-[92vh] md:grid-cols-[0.85fr_1.15fr] md:overflow-hidden"
        >
          <!-- Left panel -->
          <aside
            class="relative overflow-hidden bg-linear-to-br from-[#fff7fa] via-[#fdf8fa] to-[#f3dbe6] px-5 py-7 sm:px-7 md:px-8 md:py-9"
          >
            <div
              class="absolute -top-10 -left-10 h-40 w-40 rounded-full bg-[#f3c8d9]/50 blur-3xl"
            ></div>
            <div
              class="absolute bottom-0 right-0 h-52 w-52 rounded-full bg-[#edd6df]/60 blur-3xl"
            ></div>

            <div
              class="absolute inset-0 opacity-[0.12]"
              style="
                background-image: radial-gradient(
                  circle at 1px 1px,
                  rgba(120, 120, 120, 0.16) 1px,
                  transparent 0
                );
                background-size: 24px 24px;
              "
            ></div>

            <div class="relative z-10">
              <span
                class="inline-flex items-center rounded-full border border-[#e9c4d3] bg-white/70 px-3 py-1 font-nunito text-[10px] font-semibold uppercase tracking-[0.24em] text-[#9c6b80]"
              >
                You Glow Babe
              </span>

              <h2
                class="mt-5 font-playfair text-[32px] leading-tight text-[#5f6670] sm:text-[40px] md:text-[44px]"
              >
                Website Policies
              </h2>

              <p class="mt-4 max-w-md font-nunito text-[14px] leading-7 text-[#7d838d]">
                Please read these policies carefully before using the You Glow Babe website,
                creating an account, submitting proof of purchase, or browsing seller information.
              </p>

              <!-- Tabs -->
              <div class="mt-7 space-y-3">
                <button
                  type="button"
                  @click="setTab('privacy')"
                  class="group flex w-full items-center justify-between rounded-2xl border px-4 py-4 text-left transition"
                  :class="
                    activeTab === 'privacy'
                      ? 'border-[#e97aac] bg-white text-[#d96799] shadow-[0_12px_26px_rgba(233,122,172,0.13)]'
                      : 'border-white/70 bg-white/60 text-[#6f7681] hover:border-[#e97aac]/50 hover:bg-white'
                  "
                >
                  <span>
                    <span
                      class="block font-nunito text-[11px] font-extrabold uppercase tracking-[0.18em]"
                    >
                      Privacy Policy
                    </span>
                    <span class="mt-1 block font-nunito text-[13px] leading-5">
                      How we collect, use, protect, and manage personal data.
                    </span>
                  </span>

                  <i
                    class="fa-solid fa-chevron-right text-[12px] transition group-hover:translate-x-1"
                  ></i>
                </button>

                <button
                  type="button"
                  @click="setTab('terms')"
                  class="group flex w-full items-center justify-between rounded-2xl border px-4 py-4 text-left transition"
                  :class="
                    activeTab === 'terms'
                      ? 'border-[#e97aac] bg-white text-[#d96799] shadow-[0_12px_26px_rgba(233,122,172,0.13)]'
                      : 'border-white/70 bg-white/60 text-[#6f7681] hover:border-[#e97aac]/50 hover:bg-white'
                  "
                >
                  <span>
                    <span
                      class="block font-nunito text-[11px] font-extrabold uppercase tracking-[0.18em]"
                    >
                      Terms & Conditions
                    </span>
                    <span class="mt-1 block font-nunito text-[13px] leading-5">
                      Website usage, accounts, points, products, and seller pages.
                    </span>
                  </span>

                  <i
                    class="fa-solid fa-chevron-right text-[12px] transition group-hover:translate-x-1"
                  ></i>
                </button>
              </div>

              <div
                class="mt-7 rounded-[22px] border border-white/70 bg-white/70 p-4 shadow-[0_10px_24px_rgba(0,0,0,0.04)]"
              >
                <div class="flex gap-3">
                  <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#e97aac]/12 text-[#e97aac]"
                  >
                    <i class="fa-solid fa-shield-heart text-[15px]"></i>
                  </div>

                  <p class="font-nunito text-[13px] leading-6 text-[#7d838d]">
                    We use reasonable security measures to protect personal information submitted
                    through this website.
                  </p>
                </div>
              </div>
            </div>
          </aside>

          <!-- Right panel -->
          <main
            class="relative border-t border-[#f1dce5] bg-white/90 px-5 py-7 sm:px-7 md:border-t-0 md:px-8 md:py-9"
          >
            <div class="pr-12">
              <p
                class="font-nunito text-[11px] font-extrabold uppercase tracking-[0.26em] text-[#b86a8a]"
              >
                {{ activeTab === 'privacy' ? 'Privacy Policy' : 'Terms & Conditions' }}
              </p>

              <h3
                class="mt-2 font-playfair text-[30px] leading-tight text-[#4f5968] sm:text-[38px]"
              >
                {{ activeTab === 'privacy' ? 'Your Privacy Matters' : 'Website Terms of Use' }}
              </h3>

              <p class="mt-3 font-nunito text-[13px] leading-6 text-[#8a8f99]">
                Last updated: May 12, 2026
              </p>
            </div>

            <div
              class="legal-scroll mt-6 max-h-none overflow-visible pr-1 sm:pr-2 md:max-h-[68vh] md:overflow-y-auto"
            >
              <!-- Privacy Policy -->
              <section v-if="activeTab === 'privacy'" class="space-y-5">
                <PolicyBlock title="1. Introduction">
                  <p>
                    This Privacy Policy explains how You Glow Babe collects, uses, stores, protects,
                    and manages personal information submitted through this website. By using this
                    website, creating an account, submitting proof of purchase, subscribing to
                    updates, contacting us, or using our seller-related features, you agree to this
                    Privacy Policy.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="2. Information We May Collect">
                  <p>We may collect the following information when you use the website:</p>

                  <ul>
                    <li>Name, email address, phone number, and account login details.</li>
                    <li>Proof-of-purchase images or details submitted for points validation.</li>
                    <li>Messages, inquiries, seller concerns, and customer support requests.</li>
                    <li>
                      Product interests, submitted forms, newsletter subscriptions, and activity on
                      the website.
                    </li>
                    <li>
                      Basic technical information such as browser type, device information, pages
                      visited, and security logs.
                    </li>
                  </ul>
                </PolicyBlock>

                <PolicyBlock title="3. How We Use Your Information">
                  <p>
                    Personal information collected through the website will be used for You Glow
                    Babe customer engagement, official marketing, account support, points
                    validation, product updates, seller authenticity guidance, and website-related
                    communications.
                  </p>

                  <p>
                    We may use your information to manage user accounts, credit points after proof
                    of purchase review, respond to inquiries, send announcements or product updates,
                    improve website content, monitor security, and protect customers from fake or
                    unauthorized sellers.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="4. Proof of Purchase and Points">
                  <p>
                    Users may submit proof of purchase to earn points or rewards, subject to review
                    and approval. Submitted proof may be checked to confirm product authenticity,
                    purchase validity, and eligibility for points.
                  </p>

                  <p>
                    Proof-of-purchase files should only contain information necessary for
                    validation. Users should avoid submitting unrelated sensitive personal
                    information.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="5. Seller Locator and Fake Seller Locator">
                  <p>
                    The Seller Locator helps users find authentic and authorized You Glow Babe
                    sellers. The Fake Seller Locator provides reported or suspicious shop names,
                    platforms, and links for customer awareness.
                  </p>

                  <p>
                    Links to third-party shops or platforms are provided for reference only. You
                    Glow Babe does not control third-party websites, marketplace pages, or seller
                    accounts outside the official website.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="6. Marketing Communications">
                  <p>
                    If you subscribe, create an account, submit forms, or interact with our website,
                    we may send official You Glow Babe updates, product information, promotions,
                    announcements, and customer engagement messages, where allowed by applicable law
                    and your communication preferences.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="7. Data Sharing">
                  <p>
                    We do not sell personal information. We may share information only when needed
                    for website operation, customer support, marketing communication, technical
                    maintenance, legal compliance, fraud prevention, or protection of You Glow Babe,
                    its customers, and authorized sellers.
                  </p>

                  <p>
                    Service providers who help operate the website should only process information
                    for authorized website-related purposes.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="8. Data Security">
                  <p>
                    We apply reasonable administrative, technical, and organizational safeguards to
                    help protect personal information against unauthorized access, misuse, loss,
                    alteration, or disclosure.
                  </p>

                  <p>
                    These measures may include access control, secure authentication, server-side
                    validation, protected admin access, file restrictions, and regular review of
                    website security practices. However, no online system can guarantee absolute
                    security.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="9. Data Retention">
                  <p>
                    We retain personal information only for as long as necessary for the purpose it
                    was collected, including account management, points validation, customer
                    support, marketing records, legal compliance, fraud prevention, and website
                    security.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="10. User Rights">
                  <p>
                    Users may request access, correction, updating, or deletion of their personal
                    information, subject to verification, legitimate business needs, and applicable
                    legal requirements.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="11. Contact Us">
                  <p>
                    For privacy-related concerns, account questions, seller concerns, or requests
                    involving your personal information, you may contact You Glow Babe through the
                    official contact details provided on this website.
                  </p>
                </PolicyBlock>
              </section>

              <!-- Terms and Conditions -->
              <section v-else class="space-y-5">
                <PolicyBlock title="1. Acceptance of Terms">
                  <p>
                    By accessing or using the You Glow Babe website, you agree to follow these Terms
                    & Conditions. If you do not agree, please do not use the website, create an
                    account, submit proof of purchase, or rely on the website features.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="2. Website Purpose">
                  <p>
                    This website provides information about You Glow Babe products, announcements,
                    Glow Stories, customer engagement features, seller locator tools, fake seller
                    awareness information, and account features such as points earned from approved
                    proof-of-purchase submissions.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="3. User Accounts">
                  <p>
                    Users may create or use an account to access website features such as points,
                    submissions, and customer engagement tools. Users are responsible for keeping
                    login credentials confidential and for all activities under their account.
                  </p>

                  <p>
                    You Glow Babe may suspend or restrict accounts that submit false information,
                    suspicious proof of purchase, abusive content, or activities that may harm the
                    website, customers, sellers, or brand.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="4. Points and Proof-of-Purchase Submissions">
                  <p>
                    Points, rewards, or similar benefits are subject to validation, approval, and
                    You Glow Babe’s internal rules. Submitting proof of purchase does not guarantee
                    automatic approval or crediting of points.
                  </p>

                  <p>
                    You Glow Babe reserves the right to reject submissions that are incomplete,
                    unreadable, duplicated, suspicious, edited, invalid, unrelated to You Glow Babe
                    products, or not compliant with the current program guidelines.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="5. Product Information">
                  <p>
                    Product descriptions, benefits, usage reminders, images, and content on the
                    website are provided for general information and customer awareness. Results may
                    vary per person depending on usage, skin type, lifestyle, consistency, and other
                    factors.
                  </p>

                  <p>
                    Users should read product labels, follow usage instructions, perform patch tests
                    when applicable, and consult a qualified professional for health, skin, allergy,
                    pregnancy, or medical concerns.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="6. Seller Locator">
                  <p>
                    The Seller Locator is provided to help users find authentic and authorized You
                    Glow Babe sellers. Seller details may change over time, and users should still
                    verify seller information before completing any transaction.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="7. Fake Seller Locator">
                  <p>
                    The Fake Seller Locator lists reported or suspicious shops, platforms, or links
                    for customer awareness. The list may not include every fake or unauthorized
                    seller, and the absence of a shop from the list does not automatically mean that
                    the shop is authorized.
                  </p>

                  <p>
                    Users are encouraged to buy only from official or authorized sellers and to use
                    the Fake Seller Locator together with the Seller Locator before purchasing.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="8. Third-Party Links">
                  <p>
                    The website may contain links to third-party websites, marketplaces, seller
                    pages, social media pages, or external platforms. You Glow Babe does not control
                    third-party websites and is not responsible for their content, availability,
                    privacy practices, products, services, or transactions.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="9. User Conduct">
                  <p>Users must not:</p>

                  <ul>
                    <li>Submit false, misleading, stolen, or fraudulent information.</li>
                    <li>Upload harmful files, malicious code, or unrelated content.</li>
                    <li>
                      Attempt to access admin areas, user accounts, or protected systems without
                      permission.
                    </li>
                    <li>Use the website to harass, impersonate, defame, or harm others.</li>
                    <li>
                      Copy, misuse, or exploit website content, brand assets, or customer content
                      without permission.
                    </li>
                  </ul>
                </PolicyBlock>

                <PolicyBlock title="10. Intellectual Property">
                  <p>
                    Website design, text, product images, logos, graphics, brand names, and other
                    content are owned by or licensed to You Glow Babe unless otherwise stated. Users
                    may not copy, reproduce, modify, sell, or distribute website content without
                    permission.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="11. Limitation of Liability">
                  <p>
                    You Glow Babe aims to keep website information accurate and updated, but we do
                    not guarantee that all information will always be complete, current, or
                    error-free. Use of the website is at the user’s own discretion.
                  </p>

                  <p>
                    To the extent allowed by law, You Glow Babe will not be liable for losses caused
                    by reliance on third-party links, unauthorized sellers, invalid submissions,
                    account misuse, website interruptions, or user failure to verify seller details.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="12. Changes to These Terms">
                  <p>
                    You Glow Babe may update these Terms & Conditions from time to time. Updates
                    will be posted on the website with the latest revision date. Continued use of
                    the website means you accept the updated terms.
                  </p>
                </PolicyBlock>

                <PolicyBlock title="13. Contact Us">
                  <p>
                    For questions about these Terms & Conditions, product information, account
                    concerns, seller concerns, or website support, please contact You Glow Babe
                    through the official contact details provided on this website.
                  </p>
                </PolicyBlock>
              </section>
            </div>
          </main>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { h } from 'vue'

const PolicyBlock = {
  name: 'PolicyBlock',

  props: {
    title: {
      type: String,
      required: true,
    },
  },

  render() {
    return h(
      'article',
      {
        class:
          'rounded-[22px] border border-[#f1dce5] bg-[#fffafb] p-5 shadow-[0_10px_24px_rgba(233,122,172,0.05)]',
      },
      [
        h(
          'h4',
          {
            class: 'font-playfair text-[23px] leading-tight text-[#4f5968]',
          },
          this.title,
        ),

        h(
          'div',
          {
            class: 'policy-content mt-3 space-y-3 font-nunito text-[14px] leading-7 text-[#747b86]',
          },
          this.$slots.default ? this.$slots.default() : [],
        ),
      ],
    )
  },
}

export default {
  name: 'PrivacyTermsModal',

  components: {
    PolicyBlock,
  },

  props: {
    show: {
      type: Boolean,
      default: false,
    },

    initialTab: {
      type: String,
      default: 'privacy',
      validator: (value) => ['privacy', 'terms'].includes(value),
    },
  },

  emits: ['close'],

  data() {
    return {
      activeTab: 'privacy',
      isClosing: false,
      previousBodyOverflow: '',
    }
  },

  watch: {
    show(value) {
      if (value) {
        this.activeTab = this.initialTab
        this.isClosing = false
        this.lockBody()
        document.addEventListener('keydown', this.handleEscape)
      } else {
        this.unlockBody()
        document.removeEventListener('keydown', this.handleEscape)
      }
    },

    initialTab(value) {
      if (this.show) {
        this.activeTab = value
      }
    },
  },

  methods: {
    setTab(tab) {
      this.activeTab = tab
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
.legal-policy-fade-enter-active,
.legal-policy-fade-leave-active {
  transition: opacity 0.22s ease;
}

.legal-policy-fade-enter-from,
.legal-policy-fade-leave-to {
  opacity: 0;
}

@keyframes legalPolicyIn {
  from {
    opacity: 0;
    transform: translateY(18px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes legalPolicyOut {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateY(18px) scale(0.97);
  }
}

.animate-legal-policy-in {
  animation: legalPolicyIn 0.26s ease-out both;
}

.animate-legal-policy-out {
  animation: legalPolicyOut 0.2s ease-in both;
}

.legal-scroll {
  scrollbar-width: thin;
  scrollbar-color: #e97aac #fff1f6;
}

.legal-scroll::-webkit-scrollbar {
  width: 8px;
}

.legal-scroll::-webkit-scrollbar-track {
  background: #fff1f6;
  border-radius: 999px;
}

.legal-scroll::-webkit-scrollbar-thumb {
  background: #e97aac;
  border-radius: 999px;
}

.policy-content :deep(ul) {
  list-style-type: disc;
  padding-left: 1.25rem;
}

.policy-content :deep(li) {
  margin-top: 0.35rem;
}
</style>
