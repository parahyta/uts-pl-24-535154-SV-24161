
<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: Number,
    default: 0,
  },
  big: {
    type: Boolean,
    default: false,
  },
})

const progress = computed(() =>
  Math.min(100, Math.max(0, Number(props.value) || 0)),
)
</script>

<template>
  <div
    :class="[
      'w-full overflow-hidden rounded-full border-2 border-cocoa bg-paper',
      big ? 'h-6' : 'h-3',
    ]"
    role="progressbar"
    :aria-valuenow="progress"
    aria-valuemin="0"
    aria-valuemax="100"
  >
    <div
      :class="[
        'h-full rounded-full transition-all duration-700',
        progress === 100 ? 'bg-cherry' : 'bg-sky',
      ]"
      :style="{ width: `${progress}%` }"
    ></div>
  </div>
</template>
