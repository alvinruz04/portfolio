<template>
  <div>
    <router-view />
    <Toast :toasts="toasts" />
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'
import Toast from '@/components/myToast.vue'

const toasts = ref([])
let id = 0

function showToast(message, type = 'success', duration = 3000) {
  const toastId = ++id
  toasts.value.push({ id: toastId, message, type })

  setTimeout(() => {
    const idx = toasts.value.findIndex((t) => t.id === toastId)
    if (idx !== -1) toasts.value.splice(idx, 1)
  }, duration)
}

provide('showToast', showToast)
</script>
