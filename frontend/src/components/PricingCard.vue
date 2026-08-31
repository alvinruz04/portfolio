<template>
  <div :class="[
    'flex-shrink-0 w-[320px] md:w-[385px] bg-white rounded-xl shadow-md snap-center mx-2',
    hoverEffect ? 'transform transition-transform duration-300 hover:scale-105' : ''
  ]">
    <!-- Header -->
    <div :class="`text-white text-center rounded-t-xl py-6 ${plan.bgColor}`">
      <div class="flex justify-center mb-2">
        <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center"
          :style="{ color: plan.textColor }">
          <i :class="plan.icon"></i>
        </div>
      </div>
      <h2 class="text-2xl font-normal font-oswald">{{ plan.title }}</h2>
    </div>

    <!-- Pricing Options -->
    <div class="p-6 text-center space-y-6">
      <div v-for="(item, i) in plan.options" :key="i">
        <div v-if="item.highlight" class="bg-yellow-100 border border-yellow-400 rounded-lg p-4 shadow-inner">
          <p class="text-sm font-medium font-quicksand  text-yellow-800">{{ item.label }}</p>
          <p class="text-2xl font-bold font-oswald text-yellow-900">{{ item.price }}</p>
          <span class="inline-block mt-2 text-xs bg-yellow-300 text-yellow-900 font-semibold px-2 py-1 rounded-full">
            {{ item.note }}
          </span>
        </div>
        <div v-else>
          <p class="text-sm font-quicksand">{{ item.label }}</p>
          <p class="text-2xl font-oswald font-bold" :style="{ color: plan.textColor }">{{ item.price }}</p>
          <p v-if="item.note" class="text-green-600 text-sm">{{ item.note }}</p>
        </div>
      </div>

      <!-- General Note (e.g. per person) -->
      <div v-if="plan.note" class="pt-2">
        <p class="text-xs italic font-sans text-red-500">{{ plan.note }}</p>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  name: 'PricingCard',
  props: {
    plan: Object,
    hoverEffect: {
      type: Boolean,
      default: false,
    },
  }
}
</script>
