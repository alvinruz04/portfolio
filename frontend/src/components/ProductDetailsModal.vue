<template>
  <transition name="product-premium-fade">
    <div
      v-if="show && product"
      class="fixed inset-0 z-200 flex items-center justify-center p-3 sm:p-4 md:p-6"
      @click.self="handleClose"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-[#2b1f25]/40 backdrop-blur-md"></div>

      <!-- Modal -->
      <div
        class="relative z-10 w-[95vw] max-w-230 max-h-[92vh] overflow-y-auto rounded-[30px] border border-white/50 bg-white shadow-[0_28px_80px_rgba(82,45,61,0.22)]"
        :class="isClosing ? 'animate-product-premium-out' : 'animate-product-premium-in'"
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
          aria-label="Close product details"
          class="absolute right-4 top-4 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-[#efdde5] bg-white/90 text-[#7b8190] shadow-sm transition hover:border-[#e97aac] hover:text-[#e97aac] hover:shadow-md sm:right-5 sm:top-5 sm:h-11 sm:w-11"
        >
          <i class="fa-solid fa-xmark text-[16px]"></i>
        </button>

        <div class="relative z-10 px-5 pb-6 pt-8 sm:px-7 sm:pb-8 sm:pt-9 md:px-9 md:pb-9">
          <!-- Header -->
          <div class="pr-12">
            <div class="flex flex-wrap items-center gap-2">
              <span
                class="inline-flex items-center rounded-full border border-[#f0d8e2] bg-[#fff7fa] px-3.5 py-1.5 font-nunito text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#d96799]"
              >
                {{ product.category }}
              </span>

              <span
                v-if="product.badge"
                class="inline-flex items-center rounded-full bg-[#e97aac] px-3.5 py-1.5 font-nunito text-[10px] font-extrabold uppercase tracking-[0.18em] text-white shadow-[0_8px_18px_rgba(233,122,172,0.22)]"
              >
                {{ product.badge }}
              </span>
            </div>

            <p
              class="mt-5 font-nunito text-[11px] font-extrabold uppercase tracking-[0.28em] text-[#b86a8a]"
            >
              Product Details
            </p>

            <h3
              class="mt-2 wrap-break-word font-playfair text-[31px] leading-tight text-[#4f5968] sm:text-[40px] md:text-[46px]"
            >
              {{ product.name }}
            </h3>

            <p
              class="mt-4 max-w-3xl font-nunito text-[15px] leading-8 text-[#737b86] sm:text-[16px]"
            >
              {{ product.longDescription }}
            </p>
          </div>

          <!-- Quick facts -->
          <div v-if="product.quickFacts?.length" class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div
              v-for="fact in product.quickFacts"
              :key="fact.label"
              class="rounded-[20px] border border-[#f1dce5] bg-[#fffafb] px-4 py-4 shadow-[0_10px_24px_rgba(233,122,172,0.06)]"
            >
              <p
                class="font-nunito text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#e97aac]"
              >
                {{ fact.label }}
              </p>
              <p class="mt-1 font-playfair text-[21px] leading-tight text-[#4f5968]">
                {{ fact.value }}
              </p>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="product.tags?.length" class="mt-6 flex flex-wrap gap-2">
            <span
              v-for="tag in product.tags"
              :key="tag"
              class="rounded-full wrap-break-word border border-[#f0d8e2] bg-white px-3.5 py-2 font-nunito text-[11px] font-bold uppercase tracking-[0.11em] text-[#d96799] shadow-sm"
            >
              {{ tag }}
            </span>
          </div>

          <!-- Main info -->
          <div class="mt-7 grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Benefits -->
            <section
              class="rounded-3xl border border-[#f1dce5] bg-linear-to-br from-[#fffafb] to-white p-5 shadow-[0_14px_34px_rgba(233,122,172,0.07)]"
            >
              <div class="flex items-center gap-3">
                <div
                  class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e97aac]/12 text-[#e97aac]"
                >
                  <i class="fa-solid fa-heart text-[15px]"></i>
                </div>

                <div>
                  <p class="font-playfair text-[24px] text-[#4f5968]">Key Benefits</p>
                  <p class="font-nunito text-[12px] text-[#9aa0aa]">What this product supports</p>
                </div>
              </div>

              <ul class="mt-5 space-y-3">
                <li
                  v-for="benefit in product.benefits"
                  :key="benefit"
                  class="flex gap-3 font-nunito text-[14px] leading-6 text-[#747b86]"
                >
                  <span
                    class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#e97aac]/12 text-[#e97aac]"
                  >
                    <i class="fa-solid fa-check text-[10px]"></i>
                  </span>
                  <span>{{ benefit }}</span>
                </li>
              </ul>
            </section>

            <!-- How to use -->
            <section
              class="rounded-3xl border border-[#f1dce5] bg-linear-to-br from-[#fffafb] to-white p-5 shadow-[0_14px_34px_rgba(233,122,172,0.07)]"
            >
              <div class="flex items-center gap-3">
                <div
                  class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e97aac]/12 text-[#e97aac]"
                >
                  <i class="fa-solid fa-wand-magic-sparkles text-[15px]"></i>
                </div>

                <div>
                  <p class="font-playfair text-[24px] text-[#4f5968]">How to Use</p>
                  <p class="font-nunito text-[12px] text-[#9aa0aa]">Simple usage guide</p>
                </div>
              </div>

              <ol class="mt-5 space-y-3">
                <li
                  v-for="(step, index) in product.howToUse"
                  :key="step"
                  class="flex gap-3 font-nunito text-[14px] leading-6 text-[#747b86]"
                >
                  <span
                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#fff1f6] font-nunito text-[11px] font-extrabold text-[#e97aac]"
                  >
                    {{ index + 1 }}
                  </span>
                  <span>{{ step }}</span>
                </li>
              </ol>
            </section>
          </div>

          <!-- Precautions -->
          <section
            v-if="product.precautions?.length"
            class="mt-4 rounded-3xl border border-[#f1dce5] bg-[#fffdfd] p-5 shadow-[0_12px_28px_rgba(0,0,0,0.04)]"
          >
            <div class="flex items-start gap-3">
              <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#fff1f6] text-[#e97aac]"
              >
                <i class="fa-solid fa-circle-info text-[15px]"></i>
              </div>

              <div>
                <p class="font-playfair text-[23px] text-[#4f5968]">Friendly Reminder</p>
                <p class="mt-1 font-nunito text-[13px] leading-6 text-[#8a8f99]">
                  Please read before using this product.
                </p>
              </div>
            </div>

            <ul class="mt-4 space-y-2.5">
              <li
                v-for="item in product.precautions"
                :key="item"
                class="flex gap-3 font-nunito text-[13px] leading-6 text-[#7b8190]"
              >
                <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#e97aac]"></span>
                <span>{{ item }}</span>
              </li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'ProductDetailsModal',

  props: {
    show: {
      type: Boolean,
      default: false,
    },
    product: {
      type: Object,
      default: null,
    },
  },

  emits: ['close'],

  data() {
    return {
      isClosing: false,
    }
  },

  watch: {
    show(value) {
      if (value) this.isClosing = false
    },
  },

  methods: {
    handleClose() {
      this.isClosing = true

      setTimeout(() => {
        this.$emit('close')
        this.isClosing = false
      }, 220)
    },
  },
}
</script>

<style scoped>
.product-premium-fade-enter-active,
.product-premium-fade-leave-active {
  transition: opacity 0.22s ease;
}

.product-premium-fade-enter-from,
.product-premium-fade-leave-to {
  opacity: 0;
}

@keyframes productPremiumIn {
  from {
    opacity: 0;
    transform: translateY(18px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes productPremiumOut {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateY(18px) scale(0.97);
  }
}

.animate-product-premium-in {
  animation: productPremiumIn 0.26s ease-out both;
}

.animate-product-premium-out {
  animation: productPremiumOut 0.2s ease-in both;
}
</style>
