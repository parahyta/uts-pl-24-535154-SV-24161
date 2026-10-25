<script setup>
const rotations = [0, 72, 144, 216, 288]
</script>

<template>
  <svg viewBox="0 0 48 48" aria-hidden="true">
    <ellipse
      v-for="rotation in rotations"
      :key="rotation"
      cx="24"
      cy="12"
      rx="7"
      ry="10"
      fill="currentColor"
      stroke="var(--cocoa)"
      stroke-width="2"
      :transform="`rotate(${rotation} 24 24)`"
    />

    <circle
      cx="24"
      cy="24"
      r="6"
      fill="var(--butter)"
      stroke="var(--cocoa)"
      stroke-width="2"
    />
  </svg>
</template>