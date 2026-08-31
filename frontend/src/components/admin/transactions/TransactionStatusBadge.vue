<template>
  <span
    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 font-montserrat text-[9px] font-semibold uppercase tracking-[0.12em]"
    :class="badgeClass"
  >
    <span class="h-1.5 w-1.5 rounded-full" :class="dotClass"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: 'draft',
  },
})

const label = computed(() => {
  const labels = {
    draft: 'Draft',
    active: 'Active',
    completed: 'Completed',
    cancelled: 'Cancelled',
  }

  return labels[props.status] || props.status || 'Unknown'
})

const badgeClass = computed(() => {
  const classes = {
    draft: 'border-[#D8E4ED] bg-[#F8FBFD] text-[#64748B]',
    active: 'border-[#B9DFC9] bg-[#F0FAF4] text-[#267149]',
    completed: 'border-[#BDD5EB] bg-[#F1F7FC] text-[#025199]',
    cancelled: 'border-[#F0D2D5] bg-[#FFF5F5] text-[#B4232D]',
  }

  return classes[props.status] || classes.draft
})

const dotClass = computed(() => {
  const classes = {
    draft: 'bg-[#94A3B8]',
    active: 'bg-[#2F8A5B]',
    completed: 'bg-[#025199]',
    cancelled: 'bg-[#D51C26]',
  }

  return classes[props.status] || classes.draft
})
</script>
