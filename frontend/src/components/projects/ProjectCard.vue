
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { CalendarHeart, ArrowUpRight } from 'lucide-vue-next'

import CraftImage from './CraftImage.vue'
import ProgressBar from './ProgressBar.vue'
import StatusBadge from './StatusBadge.vue'

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
  index: {
    type: Number,
    default: 0,
  },
})

const progress = computed(() =>
  Math.min(100, Math.max(0, Number(props.project.progress) || 0)),
)

function formatDate(date) {
  if (!date) return ''

  const parsed = new Date(`${String(date).slice(0, 10)}T00:00:00`)

  if (Number.isNaN(parsed.getTime())) {
    return String(date)
  }

  return parsed.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}
</script>

<template>
  <article
    class="card-craft group flex flex-col overflow-hidden transition-all hover:-translate-y-1 hover:shadow-pop animate-rise"
    :style="{ animationDelay: `${index * 60}ms` }"
  >
    <div class="relative">
      <CraftImage
        :src="project.reference_image_url || ''"
        :alt="project.name"
        class="aspect-[4/3] w-full border-b-2 border-cocoa"
      />

      <span
        class="label-sticker absolute left-3 top-3 -rotate-3 bg-paper"
      >
        {{ project.category }}
      </span>
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5">
      <div class="flex items-start justify-between gap-2">
        <h3 class="text-xl font-semibold leading-tight">
          {{ project.name }}
        </h3>
      </div>

      <div>
        <StatusBadge :status="project.status" />
      </div>

      <div>
        <div class="mb-1 flex justify-between text-sm">
          <span class="text-muted-foreground">Progress</span>

          <span class="font-bold">
            {{ progress }}%
          </span>
        </div>

        <ProgressBar :value="progress" />
      </div>

      <div class="mt-auto flex items-center justify-between pt-2">
        <span
          class="inline-flex items-center gap-1 text-sm text-muted-foreground"
        >
          <template v-if="project.target_date">
            <CalendarHeart class="h-4 w-4" />
            {{ formatDate(project.target_date) }}
          </template>
        </span>

        <RouterLink
          :to="`/projects/${project.id}`"
          class="inline-flex items-center gap-1 font-semibold underline decoration-cherry decoration-2 underline-offset-4 hover:text-sky-deep"
        >
          View Details
          <ArrowUpRight class="h-4 w-4" />
        </RouterLink>
      </div>
    </div>
  </article>
</template>
