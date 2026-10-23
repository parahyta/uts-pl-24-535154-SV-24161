<script setup>
import hero from '@/assets/hero-crafts.jpg'

import Logo from '@/components/doodles/Logo.vue'
import Star from '@/components/doodles/Star.vue'
import Flower from '@/components/doodles/Flower.vue'
import Squiggle from '@/components/doodles/Squiggle.vue'

defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="grid min-h-screen lg:grid-cols-2">
    <!-- Left illustration panel -->
    <div
      class="relative hidden overflow-hidden border-r-2 border-cocoa bg-butter p-12 lg:flex lg:flex-col lg:justify-between"
    >
      <Logo />

      <div class="relative mx-auto w-full max-w-md">
        <div
          class="tape -top-3 left-1/2 z-10 -translate-x-1/2 -rotate-3"
        ></div>

        <img
          :src="hero"
          alt="Crochet squares, a scrapbook and embroidery hoop"
          class="aspect-[4/5] w-full rotate-2 rounded-3xl border-2 border-cocoa object-cover shadow-pop"
        />

        <Star
          class="absolute -left-8 top-10 h-14 w-14 text-sky animate-floaty"
        />

        <Flower
          class="absolute -right-6 bottom-12 h-16 w-16 text-cherry animate-floaty"
          style="animation-delay: 1s"
        />
      </div>

      <p class="font-display text-2xl italic">
        "Every stitch is a little story."

        <Squiggle class="mt-2 h-4 w-28 text-cherry" />
      </p>
    </div>

    <!-- Right form panel -->
    <div
      class="paper-dots flex flex-col items-center justify-center px-5 py-10"
    >
      <div class="mb-8 lg:hidden">
        <Logo />
      </div>

      <div
        class="card-craft w-full max-w-md animate-rise p-7 shadow-pop sm:p-10"
      >
        <h1 class="text-4xl font-bold">
          {{ title }}
        </h1>

        <p class="mt-2 text-muted-foreground">
          {{ subtitle }}
        </p>

        <div class="mt-8">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>