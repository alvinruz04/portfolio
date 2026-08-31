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
      class="relative overflow-hidden bg-[#fff8fb] pt-22 pb-14 sm:pt-24 sm:pb-18 md:pt-28 md:pb-20"
    >
      <!-- Background image layer -->
      <div
        class="absolute inset-0 scale-[1.01] bg-cover bg-center bg-no-repeat opacity-100 blur-none"
        style="background-image: url('/assets/glow-stories-background.webp')"
      ></div>

      <!-- Soft overlay for readability -->
      <div
        class="absolute inset-0"
        style="
          background: linear-gradient(
            90deg,
            rgba(255, 248, 251, 0.96) 0%,
            rgba(255, 248, 251, 0.88) 28%,
            rgba(255, 248, 251, 0.55) 52%,
            rgba(255, 248, 251, 0.12) 100%
          );
        "
      ></div>

      <!-- Optional soft bottom fade -->
      <div class="absolute inset-x-0 bottom-0 h-28 bg-linear-to-b from-transparent to-white"></div>

      <div class="relative z-10 mx-auto w-full max-w-295 px-6 sm:px-8 lg:px-10">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12">
          <!-- Left -->
          <div class="text-center lg:col-span-6 lg:text-left">
            <div
              class="inline-flex items-center rounded-full border border-[#f1d8e2] bg-white/80 px-4 py-2 shadow-[0_8px_24px_rgba(214,112,151,0.08)]"
            >
              <span
                class="mr-2 flex h-7 w-7 items-center justify-center rounded-full bg-[#ffe8f1] text-[#e97aac]"
              >
                <i class="fa-solid fa-star text-[12px]"></i>
              </span>
              <span
                class="font-nunito text-[11px] font-bold uppercase tracking-[0.2em] text-[#e97aac]"
              >
                Real Results • Product Reels • Customer Stories
              </span>
            </div>

            <h1
              class="mt-2 font-playfair text-[42px] leading-tight text-[#4f5968] sm:text-[56px] md:text-[68px]"
            >
              Glow Stories
            </h1>

            <p
              class="mx-auto mt-1 max-w-2xl font-nunito text-[15px] leading-8 text-[#737b86] sm:text-[16px] lg:mx-0"
            >
              Real customer photos, before-and-after results, product reels, skincare tips, and
              honest glow stories.
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3 lg:justify-start">
              <span class="hero-pill">
                <i class="fa-solid fa-code-compare text-[11px]"></i>
                Before & After
              </span>

              <span class="hero-pill">
                <i class="fa-solid fa-video text-[11px]"></i>
                Reels
              </span>

              <span class="hero-pill">
                <i class="fa-solid fa-comments text-[11px]"></i>
                Testimonials
              </span>
            </div>
          </div>

          <!-- Right hero preview -->
          <div class="relative lg:col-span-6">
            <div class="relative mx-auto max-w-xl">
              <div
                class="absolute -left-4 top-8 h-28 w-28 rounded-full bg-[#e97aac]/15 blur-2xl"
              ></div>
              <div
                class="absolute -right-4 bottom-8 h-36 w-36 rounded-full bg-[#f8bfd3]/30 blur-2xl"
              ></div>

              <div class="relative grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Hero pair -->
                <div
                  v-if="heroPair"
                  class="animate-float-slow overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-[0_20px_60px_rgba(214,112,151,0.16)]"
                >
                  <button type="button" class="block w-full text-left" @click="openPair(heroPair)">
                    <div class="grid grid-cols-2">
                      <div class="relative">
                        <img
                          :src="heroPair.beforeImage"
                          :alt="heroPair.altBefore"
                          class="h-64 w-full object-cover sm:h-80"
                          loading="eager"
                          decoding="async"
                          draggable="false"
                        />
                        <span class="hero-badge before">Before</span>
                      </div>

                      <div class="relative">
                        <img
                          :src="heroPair.afterImage"
                          :alt="heroPair.altAfter"
                          class="h-64 w-full object-cover sm:h-80"
                          loading="eager"
                          decoding="async"
                          draggable="false"
                        />
                        <span class="hero-badge after">After</span>
                      </div>
                    </div>
                  </button>
                </div>

                <!-- Hero reel preview -->
                <div
                  v-if="heroReel"
                  class="animate-float overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-[0_20px_60px_rgba(214,112,151,0.16)]"
                >
                  <button type="button" class="block w-full text-left" @click="openVideo(heroReel)">
                    <div class="hero-reel-frame relative overflow-hidden bg-[#fff6fa]">
                      <video
                        :src="heroReel.video"
                        :poster="heroReel.poster || undefined"
                        class="h-full w-full object-cover"
                        muted
                        playsinline
                        preload="metadata"
                        controlsList="nodownload"
                        draggable="false"
                        @contextmenu.prevent
                      ></video>

                      <span class="hero-badge after">Product Reel</span>

                      <span class="reel-play-button" aria-hidden="true">
                        <i class="fa-solid fa-play text-[15px]"></i>
                      </span>
                    </div>
                  </button>
                </div>

                <!-- Fallback hero combined photo if no reel exists -->
                <div
                  v-else-if="heroCombined"
                  class="animate-float overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-[0_20px_60px_rgba(214,112,151,0.16)]"
                >
                  <button
                    type="button"
                    class="block w-full text-left"
                    @click="openSingle(heroCombined)"
                  >
                    <img
                      :src="heroCombined.image"
                      :alt="heroCombined.alt"
                      class="h-64 w-full object-cover sm:h-80"
                      loading="eager"
                      decoding="async"
                      draggable="false"
                    />
                  </button>
                </div>

                <!-- Stats -->
                <div
                  class="rounded-[26px] border border-[#f1d8e2] bg-white/86 p-4 text-center shadow-[0_16px_45px_rgba(214,112,151,0.10)] backdrop-blur sm:col-span-2"
                >
                  <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div>
                      <p class="font-playfair text-[28px] leading-none text-[#d96799]">
                        {{ beforeAfterPairs.length }}
                      </p>
                      <p
                        class="mt-1 font-nunito text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a8f99]"
                      >
                        Pairs
                      </p>
                    </div>

                    <div>
                      <p class="font-playfair text-[28px] leading-none text-[#d96799]">
                        {{ combinedBeforeAfter.length }}
                      </p>
                      <p
                        class="mt-1 font-nunito text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a8f99]"
                      >
                        Collages
                      </p>
                    </div>

                    <div>
                      <p class="font-playfair text-[28px] leading-none text-[#d96799]">
                        {{ productReels.length }}
                      </p>
                      <p
                        class="mt-1 font-nunito text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a8f99]"
                      >
                        Reels
                      </p>
                    </div>

                    <div>
                      <p class="font-playfair text-[28px] leading-none text-[#d96799]">
                        {{ testimonialPhotos.length }}
                      </p>
                      <p
                        class="mt-1 font-nunito text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a8f99]"
                      >
                        Reviews
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div
                v-if="!heroPair && !heroCombined && !heroReel"
                class="rounded-[28px] border border-[#f1d8e2] bg-white/80 p-8 text-center font-nunito text-[#8a8f99]"
              >
                No glow story media found. Please check your image and video paths.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ACCORDION SECTION -->
    <section class="relative bg-[#fffdfd] py-10 sm:py-12 md:py-16">
      <div class="mx-auto w-full max-w-295 px-6 sm:px-8 lg:px-10">
        <div class="mx-auto max-w-4xl text-center">
          <p
            class="font-nunito text-[11px] font-bold uppercase tracking-[0.26em] text-[#b38497] sm:text-[12px]"
          >
            Choose what you want to view
          </p>

          <h2 class="mt-3 font-playfair text-[30px] leading-tight text-[#5f6670] sm:text-[42px]">
            Browse Glow Evidence by Category
          </h2>
        </div>

        <div class="mt-10 space-y-5">
          <!-- PAIRED BEFORE AFTER -->
          <div class="accordion-card">
            <button type="button" class="accordion-button" @click="togglePanel('pairs')">
              <div class="flex items-start gap-4">
                <div class="accordion-icon">
                  <i class="fa-solid fa-code-compare"></i>
                </div>

                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="accordion-title">Paired Before & After Stories</h3>

                    <span class="accordion-count"> {{ beforeAfterPairs.length }} items </span>
                  </div>

                  <p class="accordion-subtitle">
                    Separated before and after photos shown side by side for easy comparison.
                  </p>
                </div>
              </div>

              <div class="accordion-chevron" :class="activePanel === 'pairs' ? 'is-open' : ''">
                <i class="fa-solid fa-chevron-down text-[13px]"></i>
              </div>
            </button>

            <transition name="accordion">
              <div v-if="activePanel === 'pairs'" class="accordion-content">
                <div
                  v-if="visiblePairs.length"
                  class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3"
                >
                  <article v-for="item in visiblePairs" :key="item.id" class="story-card">
                    <button class="block w-full text-left" type="button" @click="openPair(item)">
                      <div class="grid grid-cols-2">
                        <div class="relative overflow-hidden bg-[#fff6fa]">
                          <img
                            :src="item.beforeImage"
                            :alt="item.altBefore"
                            loading="lazy"
                            decoding="async"
                            class="h-60 w-full object-cover transition duration-500 group-hover:scale-[1.04] sm:h-72"
                            draggable="false"
                          />
                          <span class="image-badge before">Before</span>
                        </div>

                        <div class="relative overflow-hidden bg-[#fff6fa]">
                          <img
                            :src="item.afterImage"
                            :alt="item.altAfter"
                            loading="lazy"
                            decoding="async"
                            class="h-60 w-full object-cover transition duration-500 group-hover:scale-[1.04] sm:h-72"
                            draggable="false"
                          />
                          <span class="image-badge after">After</span>
                        </div>
                      </div>

                      <div class="p-5">
                        <p class="story-kicker">Customer Shared</p>
                        <h3 class="mt-2 font-playfair text-[23px] leading-tight text-[#4f5968]">
                          {{ item.title }}
                        </h3>
                        <p class="mt-2 font-nunito text-[13px] leading-6 text-[#8a8f99]">
                          {{ item.subtitle }}
                        </p>
                      </div>
                    </button>
                  </article>
                </div>

                <empty-state v-else text="No paired before-and-after photos found." />

                <div
                  v-if="visibleLimits.pairs < beforeAfterPairs.length"
                  class="mt-8 flex justify-center"
                >
                  <button type="button" class="load-more-btn" @click="visibleLimits.pairs += 6">
                    Show More
                    <i class="fa-solid fa-plus text-[11px]"></i>
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- COMBINED -->
          <div class="accordion-card">
            <button type="button" class="accordion-button" @click="togglePanel('combined')">
              <div class="flex items-start gap-4">
                <div class="accordion-icon">
                  <i class="fa-regular fa-images"></i>
                </div>

                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="accordion-title">Combined Before & After Photos</h3>

                    <span class="accordion-count"> {{ combinedBeforeAfter.length }} items </span>
                  </div>

                  <p class="accordion-subtitle">
                    Customer-shared collages and ready-made comparison photos.
                  </p>
                </div>
              </div>

              <div class="accordion-chevron" :class="activePanel === 'combined' ? 'is-open' : ''">
                <i class="fa-solid fa-chevron-down text-[13px]"></i>
              </div>
            </button>

            <transition name="accordion">
              <div v-if="activePanel === 'combined'" class="accordion-content">
                <div
                  v-if="visibleCombined.length"
                  class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
                >
                  <article v-for="item in visibleCombined" :key="item.id" class="story-card">
                    <button class="block w-full text-left" type="button" @click="openSingle(item)">
                      <div class="relative overflow-hidden bg-[#fff6fa]">
                        <img
                          :src="item.image"
                          :alt="item.alt"
                          loading="lazy"
                          decoding="async"
                          class="h-84 w-full object-cover transition duration-500 group-hover:scale-[1.03] sm:h-100"
                          draggable="false"
                        />
                        <span class="image-badge after">Before & After</span>
                      </div>

                      <div class="p-5">
                        <p class="story-kicker">Comparison Photo</p>
                        <h3 class="mt-2 font-playfair text-[23px] leading-tight text-[#4f5968]">
                          {{ item.title }}
                        </h3>
                        <p class="mt-2 font-nunito text-[13px] leading-6 text-[#8a8f99]">
                          {{ item.subtitle }}
                        </p>
                      </div>
                    </button>
                  </article>
                </div>

                <empty-state v-else text="No combined before-and-after photos found." />

                <div
                  v-if="visibleLimits.combined < combinedBeforeAfter.length"
                  class="mt-8 flex justify-center"
                >
                  <button type="button" class="load-more-btn" @click="visibleLimits.combined += 6">
                    Show More
                    <i class="fa-solid fa-plus text-[11px]"></i>
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- PRODUCT REELS / VIDEOS -->
          <div class="accordion-card">
            <button type="button" class="accordion-button" @click="togglePanel('reels')">
              <div class="flex items-start gap-4">
                <div class="accordion-icon">
                  <i class="fa-solid fa-video"></i>
                </div>

                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="accordion-title">Product Reels & Videos</h3>

                    <span class="accordion-count"> {{ productReels.length }} items </span>
                  </div>

                  <p class="accordion-subtitle">
                    Short videos showing before-and-after results, product usage, skincare tips,
                    ingredients, and benefits.
                  </p>
                </div>
              </div>

              <div class="accordion-chevron" :class="activePanel === 'reels' ? 'is-open' : ''">
                <i class="fa-solid fa-chevron-down text-[13px]"></i>
              </div>
            </button>

            <transition name="accordion">
              <div v-if="activePanel === 'reels'" class="accordion-content">
                <div
                  v-if="visibleReels.length"
                  class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                  <article v-for="item in visibleReels" :key="item.id" class="story-card">
                    <button class="block w-full text-left" type="button" @click="openVideo(item)">
                      <div class="reel-frame relative overflow-hidden bg-[#fff6fa]">
                        <video
                          :src="item.video"
                          :poster="item.poster || undefined"
                          class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                          muted
                          playsinline
                          preload="metadata"
                          controlsList="nodownload"
                          draggable="false"
                          @contextmenu.prevent
                        ></video>

                        <span class="image-badge after">{{ item.badge }}</span>

                        <span class="reel-play-button" aria-hidden="true">
                          <i class="fa-solid fa-play text-[15px]"></i>
                        </span>
                      </div>

                      <div class="p-5">
                        <p class="story-kicker">{{ item.kicker }}</p>
                        <h3 class="mt-2 font-playfair text-[23px] leading-tight text-[#4f5968]">
                          {{ item.title }}
                        </h3>
                        <p class="mt-2 font-nunito text-[13px] leading-6 text-[#8a8f99]">
                          {{ item.subtitle }}
                        </p>
                      </div>
                    </button>
                  </article>
                </div>

                <empty-state v-else text="No product reels or videos found." />

                <div
                  v-if="visibleLimits.reels < productReels.length"
                  class="mt-8 flex justify-center"
                >
                  <button type="button" class="load-more-btn" @click="visibleLimits.reels += 6">
                    Show More
                    <i class="fa-solid fa-plus text-[11px]"></i>
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- TESTIMONIALS -->
          <div class="accordion-card">
            <button type="button" class="accordion-button" @click="togglePanel('testimonials')">
              <div class="flex items-start gap-4">
                <div class="accordion-icon">
                  <i class="fa-solid fa-comments"></i>
                </div>

                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="accordion-title">Customer Testimonials</h3>

                    <span class="accordion-count"> {{ testimonialPhotos.length }} items </span>
                  </div>

                  <p class="accordion-subtitle">
                    Screenshots of customer messages and product feedback.
                  </p>
                </div>
              </div>

              <div
                class="accordion-chevron"
                :class="activePanel === 'testimonials' ? 'is-open' : ''"
              >
                <i class="fa-solid fa-chevron-down text-[13px]"></i>
              </div>
            </button>

            <transition name="accordion">
              <div v-if="activePanel === 'testimonials'" class="accordion-content">
                <div
                  v-if="visibleTestimonials.length"
                  class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
                >
                  <article v-for="item in visibleTestimonials" :key="item.id" class="story-card">
                    <button class="block w-full text-left" type="button" @click="openSingle(item)">
                      <div class="relative overflow-hidden bg-[#fff6fa]">
                        <img
                          :src="item.image"
                          :alt="item.alt"
                          loading="lazy"
                          decoding="async"
                          class="h-84 w-full object-cover object-top transition duration-500 group-hover:scale-[1.03] sm:h-100"
                          draggable="false"
                        />
                        <span class="image-badge after">Testimonial</span>
                      </div>

                      <div class="p-5">
                        <p class="story-kicker">Customer Feedback</p>
                        <h3 class="mt-2 font-playfair text-[23px] leading-tight text-[#4f5968]">
                          {{ item.title }}
                        </h3>
                      </div>
                    </button>
                  </article>
                </div>

                <empty-state v-else text="No testimonial screenshots found." />

                <div
                  v-if="visibleLimits.testimonials < testimonialPhotos.length"
                  class="mt-8 flex justify-center"
                >
                  <button
                    type="button"
                    class="load-more-btn"
                    @click="visibleLimits.testimonials += 6"
                  >
                    Show More
                    <i class="fa-solid fa-plus text-[11px]"></i>
                  </button>
                </div>
              </div>
            </transition>
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

    <!-- MODAL -->
    <transition name="modal-fade">
      <div
        v-if="modalOpen"
        class="fixed inset-0 z-220 flex items-center justify-center bg-[#2b1f25]/55 p-3 backdrop-blur-md sm:p-5"
        @click.self="closeModal"
      >
        <div
          class="relative max-h-[92vh] w-full max-w-6xl overflow-hidden rounded-[30px] border border-white/40 bg-white shadow-[0_24px_80px_rgba(0,0,0,0.24)]"
          :class="modalClosing ? 'animate-modal-out' : 'animate-modal-in'"
        >
          <div
            class="flex items-start justify-between gap-4 border-b border-[#f0e4e9] bg-[#fffafb] px-5 py-4 sm:px-6"
          >
            <div class="min-w-0">
              <p
                class="font-nunito text-[10px] font-bold uppercase tracking-[0.22em] text-[#e97aac]"
              >
                Glow Story Preview
              </p>
              <h3
                class="mt-1 font-playfair text-[22px] leading-tight text-[#4f5968] sm:text-[28px]"
              >
                {{ modalTitle }}
              </h3>
              <p
                v-if="modalSubtitle"
                class="mt-1 line-clamp-2 font-nunito text-[12px] leading-5 text-[#8a8f99] sm:text-[13px]"
              >
                {{ modalSubtitle }}
              </p>
            </div>

            <button
              type="button"
              @click="closeModal"
              aria-label="Close preview"
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#eadce2] bg-white text-[#6b7280] transition hover:border-[#e97aac] hover:text-[#e97aac]"
            >
              <i class="fa-solid fa-xmark text-[16px]"></i>
            </button>
          </div>

          <div class="max-h-[78vh] overflow-y-auto bg-[#fff8fb]">
            <div v-if="modalType === 'pair'" class="grid grid-cols-1 md:grid-cols-2">
              <div class="relative flex items-center justify-center bg-white p-3 sm:p-5">
                <img
                  :src="modalBefore"
                  alt="Before"
                  class="max-h-[72vh] w-full rounded-[20px] object-contain"
                  draggable="false"
                />
                <span class="image-badge before">Before</span>
              </div>

              <div class="relative flex items-center justify-center bg-white p-3 sm:p-5">
                <img
                  :src="modalAfter"
                  alt="After"
                  class="max-h-[72vh] w-full rounded-[20px] object-contain"
                  draggable="false"
                />
                <span class="image-badge after">After</span>
              </div>
            </div>

            <div
              v-else-if="modalType === 'video'"
              class="flex items-center justify-center bg-white p-3 sm:p-5"
            >
              <div class="modal-video-frame">
                <video
                  :src="modalVideo"
                  :poster="modalPoster || undefined"
                  class="h-full w-full rounded-[22px] bg-black object-contain"
                  controls
                  playsinline
                  preload="metadata"
                  controlsList="nodownload"
                  @contextmenu.prevent
                ></video>
              </div>
            </div>

            <div v-else class="flex items-center justify-center bg-white p-3 sm:p-5">
              <img
                :src="modalImage"
                alt="Glow story preview"
                class="max-h-[76vh] w-full rounded-[20px] object-contain"
                draggable="false"
              />
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, defineComponent, h, reactive, ref, watch } from 'vue'
import Navbar from '../components/myNavbar.vue'
import myFooterShort from '../components/myFooter.vue'
import LoginModal from '../components/LoginModal.vue'
import ContactUsModal from '@/components/ContactUsModal.vue'
import { beforeAfterPairs, combinedBeforeAfter, testimonialPhotos } from '@/data/glowstories'
import PrivacyTermsModal from '@/components/PrivacyTermsModal.vue'
import HelpFaqsModal from '@/components/HelpFaqsModal.vue'

const showContactModal = ref(false)
const showLoginModal = ref(false)

// ADD THESE
const showFaqsModal = ref(false)
const showLegalModal = ref(false)
const legalInitialTab = ref('privacy')

function openPrivacyModal() {
  legalInitialTab.value = 'privacy'
  showLegalModal.value = true
}

function openTermsModal() {
  legalInitialTab.value = 'terms'
  showLegalModal.value = true
}

const activePanel = ref(null)

const productReels = [
  {
    id: 'reel-1',
    title: 'Charlie & Chino',
    subtitle: 'Shepu Appu Juice Glow Result.',
    kicker: 'Before and After',
    badge: 'Shepu Appu',
    video: '/assets/reels-video/reels1.mp4',
    poster: '',
  },
  {
    id: 'reel-2',
    title: 'Justine Aglamma',
    subtitle: 'Beauty White Capsule product info.',
    kicker: 'Product Info',
    badge: 'Capsule',
    video: '/assets/reels-video/reels2.mp4',
    poster: '',
  },
  {
    id: 'reel-3',
    title: 'Justine Aglamma',
    subtitle: 'Beauty white soap routine, application, and daily glow care.',
    kicker: 'Skincare Routine',
    badge: 'Soap',
    video: '/assets/reels-video/reels3.mp4',
    poster: '',
  },
  {
    id: 'reel-4',
    title: 'Mommy Che & Koya Ferson',
    subtitle: 'Beauty white sunscreen application.',
    kicker: 'Product Info',
    badge: 'SunScreen',
    video: '/assets/reels-video/reels4.mp4',
    poster: '',
  },
  {
    id: 'reel-5',
    title: 'Charlie & Chino',
    subtitle: 'Skin care routine with beauty white rejuv set.',
    kicker: 'Tutorial',
    badge: 'Rejuv Set',
    video: '/assets/reels-video/reels5.mp4',
    poster: '',
  },
]

const heroReel = computed(() => productReels[0] || null)

const visibleLimits = reactive({
  pairs: 6,
  combined: 6,
  reels: 6,
  testimonials: 6,
})

const modalOpen = ref(false)
const modalClosing = ref(false)
const modalType = ref('single')
const modalTitle = ref('')
const modalSubtitle = ref('')
const modalImage = ref('')
const modalBefore = ref('')
const modalAfter = ref('')
const modalVideo = ref('')
const modalPoster = ref('')

const heroPair = computed(
  () => beforeAfterPairs.find((item) => item.featured) || beforeAfterPairs[0] || null,
)

const heroCombined = computed(
  () => combinedBeforeAfter.find((item) => item.featured) || combinedBeforeAfter[0] || null,
)

const visiblePairs = computed(() => beforeAfterPairs.slice(0, visibleLimits.pairs))
const visibleCombined = computed(() => combinedBeforeAfter.slice(0, visibleLimits.combined))
const visibleReels = computed(() => productReels.slice(0, visibleLimits.reels))
const visibleTestimonials = computed(() => testimonialPhotos.slice(0, visibleLimits.testimonials))

function togglePanel(panel) {
  activePanel.value = activePanel.value === panel ? null : panel
}

function openPair(item) {
  modalType.value = 'pair'
  modalTitle.value = item.title
  modalSubtitle.value = item.subtitle || ''
  modalBefore.value = item.beforeImage
  modalAfter.value = item.afterImage
  modalImage.value = ''
  modalVideo.value = ''
  modalPoster.value = ''
  modalOpen.value = true
}

function openSingle(item) {
  modalType.value = 'single'
  modalTitle.value = item.title
  modalSubtitle.value = item.subtitle || ''
  modalImage.value = item.image
  modalBefore.value = ''
  modalAfter.value = ''
  modalVideo.value = ''
  modalPoster.value = ''
  modalOpen.value = true
}

function openVideo(item) {
  modalType.value = 'video'
  modalTitle.value = item.title
  modalSubtitle.value = item.subtitle || ''
  modalVideo.value = item.video
  modalPoster.value = item.poster || ''
  modalImage.value = ''
  modalBefore.value = ''
  modalAfter.value = ''
  modalOpen.value = true
}

function closeModal() {
  modalClosing.value = true

  setTimeout(() => {
    modalOpen.value = false
    modalClosing.value = false
  }, 180)
}

watch(modalOpen, (isOpen) => {
  document.body.style.overflow = isOpen ? 'hidden' : ''
})

const EmptyState = defineComponent({
  name: 'EmptyState',
  props: {
    text: {
      type: String,
      default: 'No items found.',
    },
  },
  setup(props) {
    return () =>
      h(
        'div',
        {
          class:
            'rounded-[24px] border border-[#f0e4e9] bg-[#fff8fb] p-8 text-center font-nunito text-[14px] text-[#8a8f99]',
        },
        props.text,
      )
  },
})
</script>

<style scoped>
.accordion-card {
  overflow: hidden;
  border-radius: 30px;
  border: 1px solid #f0e4e9;
  background: white;
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.05);
  transition:
    box-shadow 0.3s ease,
    border-color 0.3s ease;
}

.accordion-card:hover {
  border-color: #efbfd2;
  box-shadow: 0 24px 70px rgba(214, 112, 151, 0.13);
}

.accordion-button {
  display: flex;
  width: 100%;
  flex-direction: column;
  gap: 1rem;
  padding: 1.25rem;
  text-align: left;
  transition: background 0.25s ease;
}

.accordion-button:hover {
  background: #fffafb;
}

.accordion-icon {
  display: flex;
  height: 3rem;
  width: 3rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  background: #ffe8f1;
  color: #e97aac;
  transition: transform 0.25s ease;
}

.accordion-button:hover .accordion-icon {
  transform: scale(1.05);
}

.accordion-title {
  font-family:
    Playfair Display,
    serif;
  font-size: 25px;
  line-height: 1.15;
  color: #4f5968;
}

.accordion-count {
  border-radius: 999px;
  border: 1px solid #f1d8e2;
  background: #fff6fa;
  padding: 0.25rem 0.75rem;
  font-family: Nunito, sans-serif;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #d96799;
}

.accordion-subtitle {
  margin-top: 0.5rem;
  max-width: 42rem;
  font-family: Nunito, sans-serif;
  font-size: 13px;
  line-height: 1.6;
  color: #8a8f99;
}

.accordion-chevron {
  display: flex;
  height: 2.75rem;
  width: 2.75rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  border: 1px solid #eadce2;
  background: white;
  color: #d96799;
  transition:
    transform 0.25s ease,
    background 0.25s ease,
    color 0.25s ease,
    border-color 0.25s ease;
}

.accordion-chevron.is-open {
  transform: rotate(180deg);
  border-color: #e97aac;
  background: #e97aac;
  color: white;
}

.accordion-content {
  border-top: 1px solid #f0e4e9;
  background: #fffdfd;
  padding: 1.25rem;
}

.story-card {
  overflow: hidden;
  border-radius: 28px;
  border: 1px solid #f0e4e9;
  background: white;
  box-shadow: 0 14px 42px rgba(0, 0, 0, 0.055);
  transition:
    transform 0.28s ease,
    box-shadow 0.28s ease,
    border-color 0.28s ease;
}

.story-card:hover {
  transform: translateY(-4px);
  border-color: #efbfd2;
  box-shadow: 0 24px 60px rgba(214, 112, 151, 0.16);
}

.story-kicker {
  font-family: Nunito, sans-serif;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: #e97aac;
}

.image-badge,
.hero-badge {
  position: absolute;
  left: 12px;
  top: 12px;
  z-index: 2;
  border-radius: 999px;
  padding: 5px 11px;
  font-family: Nunito, sans-serif;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: white;
}

.image-badge.before,
.hero-badge.before {
  background: rgba(17, 24, 39, 0.72);
}

.image-badge.after,
.hero-badge.after {
  background: #e97aac;
}

.reel-frame {
  aspect-ratio: 9 / 16;
  width: 100%;
  max-height: 560px;
}

.reel-play-button {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 3;
  display: flex;
  height: 3.5rem;
  width: 3.5rem;
  transform: translate(-50%, -50%);
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.72);
  background: rgba(233, 122, 172, 0.9);
  color: white;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.22);
  transition:
    transform 0.25s ease,
    background 0.25s ease;
}

.story-card:hover .reel-play-button {
  transform: translate(-50%, -50%) scale(1.08);
  background: #e97aac;
}

.modal-video-frame {
  aspect-ratio: 9 / 16;
  width: min(100%, 430px);
  max-height: 76vh;
}

.hero-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  border-radius: 999px;
  border: 1px solid #f1d8e2;
  background: rgba(255, 255, 255, 0.78);
  padding: 0.55rem 0.9rem;
  font-family: Nunito, sans-serif;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #d96799;
  box-shadow: 0 10px 26px rgba(214, 112, 151, 0.08);
}

.hero-reel-frame {
  aspect-ratio: 9 / 16;
  width: 100%;
  max-height: 320px;
}

@media (min-width: 640px) {
  .hero-reel-frame {
    max-height: 320px;
  }
}

.load-more-btn {
  display: inline-flex;
  min-height: 3rem;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 999px;
  border: 1px solid #eadce2;
  background: white;
  padding: 0 1.75rem;
  font-family: Nunito, sans-serif;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #d96799;
  box-shadow: 0 12px 28px rgba(214, 112, 151, 0.08);
  transition:
    transform 0.25s ease,
    border-color 0.25s ease,
    background 0.25s ease,
    color 0.25s ease;
}

.load-more-btn:hover {
  transform: translateY(-2px);
  border-color: #e97aac;
  background: #e97aac;
  color: white;
}

.accordion-enter-active,
.accordion-leave-active {
  overflow: hidden;
  transition:
    opacity 0.26s ease,
    transform 0.26s ease,
    max-height 0.3s ease;
  max-height: 1800px;
}

.accordion-enter-from,
.accordion-leave-to {
  opacity: 0;
  transform: translateY(-8px);
  max-height: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.animate-modal-in {
  animation: modalIn 0.22s ease both;
}

.animate-modal-out {
  animation: modalOut 0.18s ease both;
}

.animate-float {
  animation: float 5.2s ease-in-out infinite;
}

.animate-float-slow {
  animation: float 6.8s ease-in-out infinite;
}

@keyframes modalIn {
  from {
    opacity: 0;
    transform: translateY(14px) scale(0.98);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes modalOut {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  to {
    opacity: 0;
    transform: translateY(10px) scale(0.98);
  }
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}

@media (min-width: 640px) {
  .accordion-button {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
  }

  .accordion-content {
    padding: 1.5rem;
  }

  .accordion-subtitle {
    font-size: 14px;
  }

  .accordion-title {
    font-size: 30px;
  }
}

@media (min-width: 768px) {
  .accordion-button {
    padding: 1.75rem;
  }

  .accordion-content {
    padding: 1.75rem;
  }
}

@media (max-width: 640px) {
  .image-badge,
  .hero-badge {
    left: 8px;
    top: 8px;
    padding: 4px 9px;
    font-size: 9px;
  }

  .accordion-chevron {
    height: 2.5rem;
    width: 2.5rem;
  }

  .modal-video-frame {
    width: min(100%, 360px);
    max-height: 72vh;
  }
}
</style>
