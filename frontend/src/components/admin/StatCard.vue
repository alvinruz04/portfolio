<template>
  <div
    class="rounded-2xl border border-white/10 p-4 bg-neutral-900/60"
    :class="highlight ? 'ring-1 ring-white/20' : ''"
  >
    <div class="flex items-start justify-between">
      <div>
        <div class="text-[11px] uppercase tracking-widest text-white/40">
          {{ title }}
        </div>

        <div class="text-2xl font-semibold mt-2 text-white">
          {{ value }}
        </div>

        <div class="text-[11px] text-white/40 mt-1">
          {{ subtitle }}
        </div>
      </div>

      <div
        class="h-10 w-10 rounded-2xl border border-white/10 bg-neutral-950/40 grid place-items-center text-white/70"
      >
        <i :class="icon"></i>
      </div>
    </div>

    <div class="mt-4 flex items-center gap-2 text-[11px] text-white/50">
      <span
        class="inline-flex items-center px-2 py-1 rounded-full bg-white/5 border border-white/10"
      >
        {{ trend }}
      </span>
      <span>vs previous period</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatCard',
  props: {
    title: { type: String, default: '' },
    value: { type: [String, Number], default: '' },
    subtitle: { type: String, default: '' },
    icon: { type: String, default: '' },
    trend: { type: String, default: '' },
    highlight: { type: Boolean, default: false },
  },
}
</script>
