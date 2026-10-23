<script setup>
import { ref } from 'vue'
import { Eye, EyeOff } from 'lucide-vue-next'

defineProps({
  id: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
    default: '',
  },
  invalid: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: '',
  },
  autocomplete: {
    type: String,
    default: 'current-password',
  },
})

const emit = defineEmits(['update:modelValue'])

const show = ref(false)
</script>

<template>
  <div class="relative">
    <input
      :id="id"
      :type="show ? 'text' : 'password'"
      class="field !pr-12"
      :value="modelValue"
      :aria-invalid="invalid"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      @input="emit('update:modelValue', $event.target.value)"
    />

    <button
      type="button"
      class="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full hover:bg-butter"
      :aria-label="show ? 'Hide password' : 'Show password'"
      @click="show = !show"
    >
      <EyeOff v-if="show" class="h-4 w-4" />
      <Eye v-else class="h-4 w-4" />
    </button>
  </div>
</template>