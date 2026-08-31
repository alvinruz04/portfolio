<template>
  <span
    class="inline-flex items-center rounded-full border px-2.5 py-1 font-montserrat text-[9px] font-semibold uppercase tracking-[0.12em]"
    :class="badgeClass"
  >
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: 'unpaid',
  },
})

const label = computed(() => {
  const labels = {
    unpaid: 'Unpaid',
    partial: 'Partial',
    paid: 'Paid',
  }

  return labels[props.status] || props.status || 'Unpaid'
})

const badgeClass = computed(() => {
  const classes = {
    unpaid: 'border-[#F0D2D5] bg-[#FFF5F5] text-[#B4232D]',
    partial: 'border-[#F1DDC9] bg-[#FFF8F1] text-[#9A5525]',
    paid: 'border-[#B9DFC9] bg-[#F0FAF4] text-[#267149]',
  }

  return classes[props.status] || classes.unpaid
})
</script>
