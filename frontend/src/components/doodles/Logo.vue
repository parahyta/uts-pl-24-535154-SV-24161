
<script setup>
import { RouterLink } from 'vue-router'
import Flower from './Flower.vue'

defineProps({
  to: {
    type: String,
    default: '/',
  },
})
</script>

<template>
  <RouterLink
    :to="to"
    class="group inline-flex items-center gap-2"
    aria-label="Craftory home"
  >
    <span
      class="grid h-9 w-9 place-items-center rounded-full border-2 border-cocoa bg-butter transition-transform group-hover:rotate-12"
    >
      <Flower class="h-6 w-6 text-cherry" />
    </span>

    <span class="font-display text-2xl font-bold italic tracking-tight">
      Craftory
    </span>
  </RouterLink>
</template>
