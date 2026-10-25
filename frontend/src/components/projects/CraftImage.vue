
<script setup>
import { ref, watch } from 'vue'
import { ImageOff } from 'lucide-vue-next'
import YarnBall from '@/components/doodles/YarnBall.vue'

const props = defineProps({
  src: {
    type: String,
    default: '',
  },
  alt: {
    type: String,
    required: true,
  },
})

const failed = ref(false)

watch(
  () => props.src,
  () => {
    failed.value = false
  },
)
</script>

<template>
  <div
    v-if="!src || failed"
    class="paper-dots flex flex-col items-center justify-center gap-2 bg-butter"
  >
    <ImageOff
      v-if="src"
      class="h-7 w-7 opacity-60"
    />

    <YarnBall
      v-else
      class="h-14 w-14 text-sky"
    />

    <span class="font-fun text-xs opacity-70">
      {{ src ? 'Image unavailable' : 'No inspiration image yet' }}
    </span>
  </div>

  <img
    v-else
    :src="src"
    :alt="alt"
    loading="lazy"
    class="object-cover"
    @error="failed = true"
  />
</template>
