
<script setup>
import { reactive, ref, computed, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { Loader2, Link2 } from 'lucide-vue-next'
import CraftImage from './CraftImage.vue'

const props = defineProps({
  initial: {
    type: Object,
    default: null,
  },
  submitLabel: {
    type: String,
    default: 'Save Project',
  },
})

const emit = defineEmits(['submit'])

function today() {
  const date = new Date()
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

// Kategori sementara mengikuti jenis craft yang muncul
// di Landing Page. Bisa disamakan dengan store Lovable.
const categories = [
  { value: 'crochet', label: 'Crochet' },
  { value: 'knitting', label: 'Knitting' },
  { value: 'scrapbooking', label: 'Scrapbooking' },
  { value: 'painting', label: 'Painting' },
  { value: 'embroidery', label: 'Embroidery' },
  { value: 'beading', label: 'Beading' },
  { value: 'clay_craft', label: 'Clay Craft' },
  { value: 'sewing', label: 'Sewing' },
  { value: 'other', label: 'Other' },
]

const difficulties = [
  { value: 'beginner', label: 'Beginner' },
  { value: 'intermediate', label: 'Intermediate' },
  { value: 'advanced', label: 'Advanced' },
]

const statuses = [
  { value: 'not_started', label: 'Not Started' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'completed', label: 'Completed' },
]

const form = reactive({
  name: '',
  category: '',
  status: 'not_started',
  progress: 0,
  start_date: today(),
  target_date: '',
  reference_image_url: '',
  tutorial_url: '',
  difficulty: '',
  materials: '',
  notes: '',
})

const errors = reactive({})
const saving = ref(false)
const serverError = ref('')

function populateForm(project) {
  form.name = project?.name ?? ''
  form.category = project?.category ?? ''
  form.status = project?.status ?? 'not_started'
  form.progress = Number(project?.progress ?? 0)
  form.start_date = String(project?.start_date ?? today()).slice(0, 10)
  form.target_date = String(project?.target_date ?? '').slice(0, 10)
  form.reference_image_url = project?.reference_image_url ?? ''
  form.tutorial_url = project?.tutorial_url ?? ''
  form.difficulty = project?.difficulty ?? ''
  form.materials = project?.materials ?? ''
  form.notes = project?.notes ?? ''
}

watch(
  () => props.initial,
  populateForm,
  { immediate: true },
)

const imagePreview = computed(() =>
  isHttpUrl(form.reference_image_url)
    ? form.reference_image_url
    : '',
)

function isHttpUrl(value) {
  if (!value) return false

  try {
    const url = new URL(value)
    return ['http:', 'https:'].includes(url.protocol)
  } catch {
    return false
  }
}

function clearErrors() {
  for (const key of Object.keys(errors)) {
    delete errors[key]
  }
  serverError.value = ''
}

function validate() {
  clearErrors()

  if (!form.name.trim()) {
    errors.name = 'Give your project a name.'
  }

  if (!form.category) {
    errors.category = 'Pick a craft category.'
  }

  if (!form.start_date) {
    errors.start_date = 'When did (or will) you start?'
  }

  if (
    form.target_date &&
    form.start_date &&
    form.target_date < form.start_date
  ) {
    errors.target_date = 'Target date should be after the start date.'
  }

  if (
    form.reference_image_url &&
    !isHttpUrl(form.reference_image_url)
  ) {
    errors.reference_image_url =
      'Use a link starting with http:// or https://'
  }

  if (form.tutorial_url && !isHttpUrl(form.tutorial_url)) {
    errors.tutorial_url =
      'Use a link starting with http:// or https://'
  }

  return Object.keys(errors).length === 0
}

function selectStatus(value) {
  form.status = value

  if (value === 'not_started') {
    form.progress = 0
  } else if (value === 'completed') {
    form.progress = 100
  } else if (form.progress === 0 || form.progress === 100) {
    form.progress = 1
  }
}

function updateProgress(event) {
  const value = Number(event.target.value)
  form.progress = value

  if (value === 0) {
    form.status = 'not_started'
  } else if (value === 100) {
    form.status = 'completed'
  } else {
    form.status = 'in_progress'
  }
}

async function submit() {
  if (saving.value || !validate()) return

  saving.value = true

  const payload = {
    name: form.name.trim(),
    category: form.category,
    status: form.status,
    progress: Number(form.progress),
    start_date: form.start_date,
    target_date: form.target_date || null,
    reference_image_url: form.reference_image_url.trim() || null,
    tutorial_url: form.tutorial_url.trim() || null,
    difficulty: form.difficulty || null,
    materials: form.materials,
    notes: form.notes,
  }

  try {
    // Parent menangani request Axios.
    // Promise digunakan supaya loading tetap aktif.
    await new Promise((resolve, reject) => {
      emit('submit', payload, { resolve, reject })
    })
  } catch (error) {
    if (error.response?.status === 422) {
      const backendErrors = error.response.data?.errors ?? {}

      for (const [field, messages] of Object.entries(backendErrors)) {
        errors[field] = Array.isArray(messages)
          ? messages[0]
          : String(messages)
      }

      if (!Object.keys(backendErrors).length) {
        serverError.value =
          error.response.data?.message || 'Please check your project details.'
      }
    } else {
      serverError.value =
        'Unable to save your project. Please try again.'
    }
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <form
    novalidate
    class="grid gap-8 lg:grid-cols-[1fr_340px]"
    @submit.prevent="submit"
  >
    <div class="flex flex-col gap-6">
      <!-- The basics -->
      <section class="card-craft flex flex-col gap-5 p-6 sm:p-8">
        <h2 class="text-2xl font-semibold">
          The basics
        </h2>

        <div class="flex flex-col gap-1.5">
          <label for="name" class="text-sm font-semibold">
            Project Name <span class="text-destructive">*</span>
          </label>

          <input
            id="name"
            v-model="form.name"
            class="field"
            maxlength="100"
            placeholder="e.g. Cozy cardigan for autumn"
            :aria-invalid="Boolean(errors.name)"
          />

          <p v-if="errors.name" role="alert" class="text-sm text-destructive">
            {{ errors.name }}
          </p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
          <!-- Category -->
          <div class="flex flex-col gap-1.5">
            <label for="category" class="text-sm font-semibold">
              Craft Category <span class="text-destructive">*</span>
            </label>

            <select
              id="category"
              v-model="form.category"
              class="field"
              :aria-invalid="Boolean(errors.category)"
            >
              <option value="" disabled>Choose one…</option>

              <option
                v-for="category in categories"
                :key="category.value"
                :value="category.value"
              >
                {{ category.label }}
              </option>
            </select>

            <p v-if="errors.category" role="alert" class="text-sm text-destructive">
              {{ errors.category }}
            </p>
          </div>

          <!-- Difficulty -->
          <div class="flex flex-col gap-1.5">
            <label for="difficulty" class="text-sm font-semibold">
              Difficulty Level
              <span class="font-normal text-muted-foreground">(optional)</span>
            </label>

            <select
              id="difficulty"
              v-model="form.difficulty"
              class="field"
              :aria-invalid="Boolean(errors.difficulty)"
            >
              <option value="">Not set</option>

              <option
                v-for="difficulty in difficulties"
                :key="difficulty.value"
                :value="difficulty.value"
              >
                {{ difficulty.label }}
              </option>
            </select>

            <p v-if="errors.difficulty" role="alert" class="text-sm text-destructive">
              {{ errors.difficulty }}
            </p>
          </div>

          <!-- Start Date -->
          <div class="flex flex-col gap-1.5">
            <label for="start" class="text-sm font-semibold">
              Start Date <span class="text-destructive">*</span>
            </label>

            <input
              id="start"
              v-model="form.start_date"
              type="date"
              class="field"
              :aria-invalid="Boolean(errors.start_date)"
            />

            <p v-if="errors.start_date" role="alert" class="text-sm text-destructive">
              {{ errors.start_date }}
            </p>
          </div>

          <!-- Target Date -->
          <div class="flex flex-col gap-1.5">
            <label for="target" class="text-sm font-semibold">
              Target Date
              <span class="font-normal text-muted-foreground">(optional)</span>
            </label>

            <input
              id="target"
              v-model="form.target_date"
              type="date"
              class="field"
              :min="form.start_date || undefined"
              :aria-invalid="Boolean(errors.target_date)"
            />

            <p v-if="errors.target_date" role="alert" class="text-sm text-destructive">
              {{ errors.target_date }}
            </p>
          </div>
        </div>
      </section>

      <!-- Progress -->
      <section class="card-craft flex flex-col gap-5 bg-butter p-6 sm:p-8">
        <h2 class="text-2xl font-semibold">
          Progress
        </h2>

        <div
          class="flex flex-wrap gap-2"
          role="group"
          aria-label="Project status"
        >
          <button
            v-for="item in statuses"
            :key="item.value"
            type="button"
            :aria-pressed="form.status === item.value"
            :class="[
              'btn !min-h-10 !py-1.5 text-sm',
              form.status === item.value
                ? 'btn-primary shadow-pop-sm'
                : 'btn-ghost bg-paper',
            ]"
            @click="selectStatus(item.value)"
          >
            {{ item.label }}
          </button>
        </div>

        <div class="flex items-center gap-5">
          <input
            type="range"
            min="0"
            max="100"
            :value="form.progress"
            aria-label="Progress percentage"
            class="craft-range"
            :style="{ '--val': `${form.progress}%` }"
            @input="updateProgress"
          />

          <span class="min-w-[4.5rem] text-right font-display text-4xl font-bold">
            {{ form.progress }}%
          </span>
        </div>

        <p class="text-sm text-muted-foreground">
          Status follows progress: 0% Not Started ·
          1–99% In Progress · 100% Completed.
        </p>

        <p v-if="errors.progress || errors.status" role="alert" class="text-sm text-destructive">
          {{ errors.progress || errors.status }}
        </p>
      </section>

      <!-- Supplies & notes -->
      <section class="card-craft flex flex-col gap-5 p-6 sm:p-8">
        <h2 class="text-2xl font-semibold">
          Supplies & notes
        </h2>

        <div class="flex flex-col gap-1.5">
          <label for="tut" class="text-sm font-semibold">
            Tutorial URL
            <span class="font-normal text-muted-foreground">(optional)</span>
          </label>

          <div class="relative">
            <Link2
              class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 opacity-60"
            />

            <input
              id="tut"
              v-model="form.tutorial_url"
              class="field !pl-9"
              placeholder="https://youtube.com/…"
              :aria-invalid="Boolean(errors.tutorial_url)"
            />
          </div>

          <p v-if="errors.tutorial_url" role="alert" class="text-sm text-destructive">
            {{ errors.tutorial_url }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="mat" class="text-sm font-semibold">
            Materials & Tools
            <span class="font-normal text-muted-foreground">(optional)</span>
          </label>

          <textarea
            id="mat"
            v-model="form.materials"
            rows="4"
            class="field"
            placeholder="One per line, e.g.&#10;4mm hook&#10;Cotton yarn"
            :aria-invalid="Boolean(errors.materials)"
          ></textarea>

          <p v-if="errors.materials" role="alert" class="text-sm text-destructive">
            {{ errors.materials }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="notes" class="text-sm font-semibold">
            Notes
            <span class="font-normal text-muted-foreground">(optional)</span>
          </label>

          <textarea
            id="notes"
            v-model="form.notes"
            rows="4"
            class="field"
            placeholder="Ideas, row counts, little reminders…"
            :aria-invalid="Boolean(errors.notes)"
          ></textarea>

          <p v-if="errors.notes" role="alert" class="text-sm text-destructive">
            {{ errors.notes }}
          </p>
        </div>
      </section>
    </div>

    <!-- Inspiration image and buttons -->
    <aside class="flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
      <section class="card-craft flex flex-col gap-4 p-6">
        <h2 class="text-xl font-semibold">
          Inspiration image
        </h2>

        <div
          class="relative -rotate-1 overflow-hidden rounded-2xl border-2 border-cocoa"
        >
          <CraftImage
            :src="imagePreview"
            alt="Reference preview"
            class="aspect-square w-full"
          />
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="img" class="text-sm font-semibold">
            Reference Image URL
            <span class="font-normal text-muted-foreground">(optional)</span>
          </label>

          <input
            id="img"
            v-model="form.reference_image_url"
            class="field"
            placeholder="Paste a direct image link"
            :aria-invalid="Boolean(errors.reference_image_url)"
          />

          <p
            v-if="errors.reference_image_url"
            role="alert"
            class="text-sm text-destructive"
          >
            {{ errors.reference_image_url }}
          </p>
        </div>
      </section>

      <p
        v-if="serverError"
        role="alert"
        class="rounded-xl border-2 border-cherry bg-cherry-soft p-3 text-sm text-cherry"
      >
        {{ serverError }}
      </p>

      <div class="flex flex-col gap-3">
        <button
          type="submit"
          :disabled="saving"
          class="btn btn-primary w-full text-base"
        >
          <Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Saving…' : submitLabel }}
        </button>

        <RouterLink
          :to="initial?.id ? `/projects/${initial.id}` : '/dashboard'"
          class="btn btn-ghost w-full"
        >
          Cancel
        </RouterLink>
      </div>
    </aside>
  </form>
</template>