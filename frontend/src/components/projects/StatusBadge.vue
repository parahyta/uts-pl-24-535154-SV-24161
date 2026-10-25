
<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
})

const statusConfig = {
  not_started: {
    label: 'Not Started',
    className: 'bg-muted',
  },
  in_progress: {
    label: 'In Progress',
    className: 'bg-sky',
  },
  completed: {
    label: 'Completed',
    className: 'bg-butter',
  },
}

const current = computed(
  () => statusConfig[props.status] ?? {
    label: props.status,
    className: 'bg-muted',
  },
)
</script>

<template>
  <span :class="['label-sticker', current.className]">
    {{ status === 'completed' ? '✿ ' : '' }}{{ current.label }}
  </span>
</template>