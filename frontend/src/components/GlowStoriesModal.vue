<template>
  <transition name="story-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-210 bg-[#2b1f25]/45 backdrop-blur-md"
      @click.self="closeModal"
      @contextmenu.prevent
    >
      <!-- MOBILE / DESKTOP WRAPPER -->
      <div
        class="h-full w-full overflow-y-auto overscroll-contain p-3 sm:p-4 md:p-6 flex items-center justify-center"
      >
        <div
          class="relative w-full max-w-6xl overflow-hidden rounded-[28px] border border-[#f0e4e9] bg-white shadow-[0_24px_70px_rgba(0,0,0,0.20)]"
          :class="closing ? 'animate-story-out' : 'animate-story-in'"
        >
          <!-- Close -->
          <button
            @click="closeModal"
            aria-label="Close Glow Stories"
            class="absolute right-3 top-3 z-40 flex h-9 w-9 items-center justify-center rounded-full border border-[#eadce2] bg-white/95 text-[#6b7280] shadow-sm transition duration-200 hover:border-[#e97aac] hover:text-[#e97aac] sm:right-4 sm:top-4 sm:h-10 sm:w-10"
          >
            <i class="fa-solid fa-xmark text-[15px] sm:text-[17px]"></i>
          </button>

          <div class="grid grid-cols-1 lg:grid-cols-12">
            <!-- LEFT: IMAGE / VIEWER -->
            <div class="relative lg:col-span-8 bg-[#f7f2f4] flex items-center justify-center">
              <div
                class="relative w-full"
                @touchstart.passive="handleTouchStart"
                @touchend.passive="handleTouchEnd"
              >
                <img
                  v-if="currentStory"
                  :src="currentStory.src"
                  :alt="currentStory.title"
                  class="block w-full object-contain bg-[#f7f2f4] h-[42vh] sm:h-[58vh] lg:h-[78vh] protected-image"
                  draggable="false"
                  @contextmenu.prevent
                  @dragstart.prevent
                  @mousedown.prevent
                />

                <!-- Prev -->
                <button
                  v-if="stories.length > 1 && !isMobile"
                  @click="prevStory"
                  aria-label="Previous story"
                  class="absolute left-3 top-1/2 z-30 -translate-y-1/2 flex h-12 w-12 items-center justify-center rounded-full bg-white/92 text-[#5f6670] shadow-md transition hover:bg-white hover:text-[#e97aac] sm:left-5"
                >
                  <i class="fa-solid fa-chevron-left"></i>
                </button>

                <!-- Next -->
                <button
                  v-if="stories.length > 1 && !isMobile"
                  @click="nextStory"
                  aria-label="Next story"
                  class="absolute right-3 top-1/2 z-30 -translate-y-1/2 flex h-12 w-12 items-center justify-center rounded-full bg-white/92 text-[#5f6670] shadow-md transition hover:bg-white hover:text-[#e97aac] sm:right-5"
                >
                  <i class="fa-solid fa-chevron-right"></i>
                </button>

                <!-- Counter -->
                <div
                  v-if="stories.length > 0"
                  class="absolute bottom-4 left-1/2 z-30 -translate-x-1/2 rounded-full bg-[#2b1f25]/65 px-4 py-2 font-nunito text-[12px] font-semibold uppercase tracking-[0.12em] text-white"
                >
                  {{ activeIndex + 1 }} / {{ stories.length }}
                </div>
              </div>
            </div>

            <!-- RIGHT: DETAILS + THUMBNAILS -->
            <div
              class="lg:col-span-4 border-t lg:border-t-0 lg:border-l border-[#f1e3e9] bg-linear-to-br from-[#fffafb] via-[#fffefe] to-[#fff5f8] flex flex-col"
            >
              <!-- details -->
              <div class="px-5 pt-5 pb-5 sm:px-6 sm:pt-6">
                <p
                  class="font-nunito text-[11px] font-semibold uppercase tracking-[0.22em] text-[#b38497]"
                >
                  Glow Stories
                </p>

                <h3
                  v-if="currentStory"
                  class="mt-2 font-playfair text-[24px] leading-tight text-[#5f6670] sm:text-[28px]"
                >
                  {{ currentStory.title }}
                </h3>

                <p
                  v-if="currentStory"
                  class="mt-3 font-nunito text-[15px] leading-7 text-[#7d838d]"
                >
                  {{ currentStory.description }}
                </p>
              </div>

              <!-- desktop thumbnails only -->
              <div class="hidden lg:block px-5 sm:px-6 pb-6">
                <div class="story-thumb-scroll pr-1">
                  <div class="grid grid-cols-3 gap-3">
                    <button
                      v-for="(story, index) in stories"
                      :key="story.src"
                      @click="setActiveStory(index)"
                      class="group relative overflow-hidden rounded-[18px] border transition"
                      :class="
                        activeIndex === index
                          ? 'border-[#e97aac] ring-2 ring-[#f7c4d8]'
                          : 'border-[#f0e4e9] hover:border-[#e6b5c9]'
                      "
                    >
                      <img
                        :src="story.src"
                        :alt="story.title"
                        class="h-24 w-full object-cover transition duration-300 group-hover:scale-[1.03] protected-image"
                        draggable="false"
                        @contextmenu.prevent
                        @dragstart.prevent
                        @mousedown.prevent
                      />
                    </button>
                  </div>
                </div>
              </div>

              <!-- mobile helper -->
              <div class="lg:hidden px-5 pb-5 sm:px-6 sm:pb-6">
                <p class="font-nunito text-[13px] text-[#9aa0a9]">
                  Swipe left or right to view more glow stories.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'GlowStoriesModal',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    stories: {
      type: Array,
      default: () => [],
    },
    startIndex: {
      type: Number,
      default: 0,
    },
  },
  emits: ['close'],

  data() {
    return {
      activeIndex: 0,
      closing: false,
      touchStartX: 0,
      touchEndX: 0,
      isMobile: false,
    }
  },

  computed: {
    currentStory() {
      return this.stories[this.activeIndex] || null
    },
  },

  watch: {
    show(val) {
      if (val) {
        this.activeIndex = this.normalizeIndex(this.startIndex)
        this.closing = false
        this.checkMobile()
        document.body.style.overflow = 'hidden'
        window.addEventListener('keydown', this.handleKeydown)
        window.addEventListener('resize', this.checkMobile)
      } else {
        document.body.style.overflow = ''
        window.removeEventListener('keydown', this.handleKeydown)
        window.removeEventListener('resize', this.checkMobile)
      }
    },

    startIndex(val) {
      this.activeIndex = this.normalizeIndex(val)
    },
  },

  mounted() {
    this.checkMobile()

    if (this.show) {
      this.activeIndex = this.normalizeIndex(this.startIndex)
      document.body.style.overflow = 'hidden'
      window.addEventListener('keydown', this.handleKeydown)
      window.addEventListener('resize', this.checkMobile)
    }
  },

  beforeUnmount() {
    document.body.style.overflow = ''
    window.removeEventListener('keydown', this.handleKeydown)
    window.removeEventListener('resize', this.checkMobile)
  },

  methods: {
    checkMobile() {
      this.isMobile = window.innerWidth < 1024
    },

    normalizeIndex(index) {
      if (!this.stories.length) return 0
      if (index < 0) return 0
      if (index >= this.stories.length) return this.stories.length - 1
      return index
    },

    setActiveStory(index) {
      this.activeIndex = index
    },

    nextStory() {
      if (!this.stories.length) return
      this.activeIndex = (this.activeIndex + 1) % this.stories.length
    },

    prevStory() {
      if (!this.stories.length) return
      this.activeIndex = (this.activeIndex - 1 + this.stories.length) % this.stories.length
    },

    async closeModal() {
      if (this.closing) return
      this.closing = true
      await new Promise((resolve) => setTimeout(resolve, 220))
      this.$emit('close')
      this.closing = false
    },

    handleKeydown(e) {
      if (!this.show) return

      const key = e.key.toLowerCase()

      if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(key)) ||
        (e.ctrlKey && ['s', 'u', 'p'].includes(key))
      ) {
        e.preventDefault()
        return
      }

      if (e.key === 'Escape') {
        this.closeModal()
      }

      if (!this.isMobile && e.key === 'ArrowRight') {
        this.nextStory()
      }

      if (!this.isMobile && e.key === 'ArrowLeft') {
        this.prevStory()
      }
    },

    handleTouchStart(e) {
      this.touchStartX = e.changedTouches[0].clientX
    },

    handleTouchEnd(e) {
      this.touchEndX = e.changedTouches[0].clientX
      const distance = this.touchEndX - this.touchStartX

      if (Math.abs(distance) < 40) return

      if (distance < 0) {
        this.nextStory()
      } else {
        this.prevStory()
      }
    },
  },
}
</script>

<style scoped>
.story-fade-enter-active,
.story-fade-leave-active {
  transition: opacity 0.22s ease;
}

.story-fade-enter-from,
.story-fade-leave-to {
  opacity: 0;
}

@keyframes storyModalIn {
  0% {
    opacity: 0;
    transform: translateY(14px) scale(0.985);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes storyModalOut {
  0% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  100% {
    opacity: 0;
    transform: translateY(14px) scale(0.985);
  }
}

.animate-story-in {
  animation: storyModalIn 0.24s ease-out forwards;
}

.animate-story-out {
  animation: storyModalOut 0.2s ease-in forwards;
}

.story-thumb-scroll {
  max-height: 460px;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(179, 132, 151, 0.45) transparent;
}

.story-thumb-scroll::-webkit-scrollbar {
  width: 8px;
}

.story-thumb-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.story-thumb-scroll::-webkit-scrollbar-thumb {
  background: rgba(179, 132, 151, 0.35);
  border-radius: 9999px;
}

.story-thumb-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(179, 132, 151, 0.55);
}

.protected-image {
  -webkit-user-drag: none;
  user-select: none;
  -webkit-touch-callout: none;
  pointer-events: auto;
}

@media (max-width: 1023px) {
  .animate-story-in,
  .animate-story-out {
    animation-duration: 0.2s;
  }
}
</style>
