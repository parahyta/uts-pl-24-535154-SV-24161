
<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  ExternalLink,
  Pencil,
  Trash2,
  CalendarDays,
  CalendarHeart,
  Loader2,
} from 'lucide-vue-next'

import AppShell from '@/components/AppShell.vue'
import CraftImage from '@/components/projects/CraftImage.vue'
import ProgressBar from '@/components/projects/ProgressBar.vue'
import StatusBadge from '@/components/projects/StatusBadge.vue'
import Flower from '@/components/doodles/Flower.vue'
import Star from '@/components/doodles/Star.vue'

import { getProject, deleteProject } from '@/services/projectService'
import { clearToken } from '@/services/authService'

const route = useRoute()
const router = useRouter()

const project = ref(null)
const loading = ref(true)
const errorMessage = ref('')
const notFound = ref(false)

const confirmDelete = ref(false)
const deleting = ref(false)
const deleteError = ref('')

const categoryLabels = {
  crochet: 'Crochet',
  knitting: 'Knitting',
  scrapbooking: 'Scrapbooking',
  painting: 'Painting',
  embroidery: 'Embroidery',
  beading: 'Beading',
  clay_craft: 'Clay Craft',
  sewing: 'Sewing',
  other: 'Other',
}

const difficultyLabels = {
  beginner: 'Beginner',
  intermediate: 'Intermediate',
  advanced: 'Advanced',
}

const categoryLabel = computed(() =>
  categoryLabels[project.value?.category] ??
  project.value?.category ??
  '',
)

const difficultyLabel = computed(() =>
  difficultyLabels[project.value?.difficulty] ??
  project.value?.difficulty ??
  '',
)

const progress = computed(() =>
  Math.min(100, Math.max(0, Number(project.value?.progress) || 0)),
)

const progressMessage = computed(() => {
  if (progress.value === 100) {
    return 'Finished! Time to celebrate ✿'
  }

  if (progress.value === 0) {
    return 'Ready when you are.'
  }

  return `${100 - progress.value}% to go — you've got this!`
})

const materialsList = computed(() =>
  (project.value?.materials ?? '')
    .split('\n')
    .map((item) => item.trim())
    .filter(Boolean),
)

function formatDate(value) {
  if (!value) return 'Not set'

  // Laravel may return "2026-10-23" or an ISO timestamp.
  // Use the date portion to avoid timezone shifts.
  const dateString = String(value).slice(0, 10)
  const date = new Date(`${dateString}T00:00:00`)

  if (Number.isNaN(date.getTime())) {
    return String(value)
  }

  return date.toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

async function loadProject(id) {
  project.value = null
  loading.value = true
  notFound.value = false
  errorMessage.value = ''
  confirmDelete.value = false
  deleteError.value = ''

  try {
    project.value = await getProject(id)

    document.title = `${project.value.name} — Craftory`
  } catch (error) {
    const status = error.response?.status

    if (status === 401) {
      clearToken()
      await router.replace('/login')
    } else if (status === 404) {
      notFound.value = true
      document.title = 'Project Not Found — Craftory'
    } else {
      errorMessage.value =
        'Unable to load this project. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

function openDeleteDialog() {
  deleteError.value = ''
  confirmDelete.value = true
}

function closeDeleteDialog() {
  if (deleting.value) return
  confirmDelete.value = false
  deleteError.value = ''
}

function handleEscape(event) {
  if (event.key === 'Escape') {
    closeDeleteDialog()
  }
}

async function handleDelete() {
  if (!project.value || deleting.value) return

  deleting.value = true
  deleteError.value = ''

  try {
    await deleteProject(project.value.id)

    confirmDelete.value = false
    await router.replace('/dashboard')
  } catch (error) {
    const status = error.response?.status

    if (status === 401) {
      confirmDelete.value = false
      clearToken()
      await router.replace('/login')
    } else if (status === 404) {
      confirmDelete.value = false
      notFound.value = true
      project.value = null
    } else {
      deleteError.value =
        'Unable to delete this project. Please try again.'
    }
  } finally {
    deleting.value = false
  }
}

watch(
  () => route.params.id,
  (id) => {
    if (id) loadProject(id)
  },
  { immediate: true },
)

onMounted(() => {
  window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscape)
})
</script>

<template>
  <AppShell>
    <!-- Loading -->
    <div
      v-if="loading"
      class="card-craft flex flex-col items-center gap-4 px-6 py-20"
    >
      <Loader2 class="h-10 w-10 animate-spin text-sky-deep" />
      <p class="font-fun text-muted-foreground">
        Loading your little project…
      </p>
    </div>

    <!-- Project not found -->
    <div
      v-else-if="notFound"
      class="card-craft mx-auto flex max-w-lg flex-col items-center gap-4 p-10 text-center"
    >
      <Flower class="h-14 w-14 text-cherry" />

      <h1 class="text-3xl font-bold">
        We couldn't find that project
      </h1>

      <p class="text-muted-foreground">
        It may have been deleted from your craft space.
      </p>

      <RouterLink to="/dashboard" class="btn btn-primary">
        Back to My Projects
      </RouterLink>
    </div>

    <!-- Request error -->
    <div
      v-else-if="errorMessage"
      class="card-craft mx-auto flex max-w-lg flex-col items-center gap-4 p-10 text-center"
    >
      <Flower class="h-14 w-14 text-cherry" />

      <h1 class="text-3xl font-bold">
        Something went wrong
      </h1>

      <p role="alert" class="text-muted-foreground">
        {{ errorMessage }}
      </p>

      <button
        type="button"
        class="btn btn-butter"
        @click="loadProject(route.params.id)"
      >
        Try Again
      </button>

      <RouterLink to="/dashboard" class="text-sm underline">
        Back to My Projects
      </RouterLink>
    </div>

    <!-- Project detail -->
    <div v-else-if="project" class="flex flex-col gap-8">
      <RouterLink
        to="/dashboard"
        class="inline-flex w-fit items-center gap-1 text-sm font-medium hover:underline"
      >
        <ArrowLeft class="h-4 w-4" />
        Back to My Projects
      </RouterLink>

      <div class="grid gap-10 lg:grid-cols-[1fr_1.1fr]">
        <!-- Reference image -->
        <div class="relative">
          <div
            class="tape -top-3 left-1/2 z-10 -translate-x-1/2 -rotate-2"
          ></div>

          <CraftImage
            :src="project.reference_image_url || ''"
            :alt="project.name"
            class="aspect-[4/5] w-full -rotate-1 overflow-hidden rounded-[2rem] border-2 border-cocoa shadow-pop"
          />

          <Star
            class="absolute -right-4 -bottom-4 h-12 w-12 text-butter animate-floaty"
          />
        </div>

        <!-- Project information -->
        <div class="flex flex-col gap-6">
          <div class="flex flex-wrap gap-2">
            <span class="label-sticker bg-paper">
              {{ categoryLabel }}
            </span>

            <span
              v-if="project.difficulty"
              class="label-sticker bg-cherry text-cream"
            >
              {{ difficultyLabel }}
            </span>

            <StatusBadge :status="project.status" />
          </div>

          <h1 class="text-5xl font-bold leading-tight sm:text-6xl">
            {{ project.name }}
          </h1>

          <!-- Progress -->
          <section class="card-craft bg-butter p-6 shadow-pop sm:p-8">
            <div class="flex items-end justify-between gap-4">
              <p class="font-fun text-sm">
                progress
              </p>

              <p class="font-display text-6xl font-bold leading-none sm:text-7xl">
                {{ progress }}<span class="text-3xl">%</span>
              </p>
            </div>

            <div class="mt-4">
              <ProgressBar :value="progress" big />
            </div>

            <p class="mt-3 text-sm">
              {{ progressMessage }}
            </p>
          </section>

          <!-- Dates -->
          <div class="grid grid-cols-2 gap-4">
            <div class="card-craft p-4">
              <p class="flex items-center gap-1 text-sm text-muted-foreground">
                <CalendarDays class="h-4 w-4" />
                Start date
              </p>

              <p class="mt-1 text-lg font-semibold">
                {{ formatDate(project.start_date) }}
              </p>
            </div>

            <div class="card-craft p-4">
              <p class="flex items-center gap-1 text-sm text-muted-foreground">
                <CalendarHeart class="h-4 w-4" />
                Target date
              </p>

              <p class="mt-1 text-lg font-semibold">
                {{ formatDate(project.target_date) }}
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-wrap gap-3">
            <a
              v-if="project.tutorial_url"
              :href="project.tutorial_url"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-primary"
            >
              <ExternalLink class="h-4 w-4" />
              Open Tutorial
            </a>

            <span
              v-else
              class="btn btn-ghost pointer-events-none opacity-60"
            >
              No tutorial saved
            </span>

            <RouterLink
              :to="`/projects/${project.id}/edit`"
              class="btn btn-butter"
            >
              <Pencil class="h-4 w-4" />
              Edit Project
            </RouterLink>

            <button
              type="button"
              class="btn btn-ghost"
              @click="openDeleteDialog"
            >
              <Trash2 class="h-4 w-4" />
              Delete Project
            </button>
          </div>
        </div>
      </div>

      <!-- Materials & Notes -->
      <div class="grid gap-6 md:grid-cols-2">
        <section class="card-craft p-6 sm:p-8">
          <h2 class="text-2xl font-semibold">
            Materials &amp; Tools
          </h2>

          <ul
            v-if="materialsList.length"
            class="mt-4 flex flex-col gap-2"
          >
            <li
              v-for="(material, index) in materialsList"
              :key="index"
              class="flex gap-2"
            >
              <span class="text-cherry">✿</span>
              <span>{{ material }}</span>
            </li>
          </ul>

          <p v-else class="mt-4 text-muted-foreground">
            No materials listed yet.
          </p>
        </section>

        <section class="card-craft paper-dots p-6 sm:p-8">
          <h2 class="text-2xl font-semibold">
            Notes
          </h2>

          <p
            v-if="project.notes"
            class="mt-4 whitespace-pre-line leading-relaxed"
          >
            {{ project.notes }}
          </p>

          <p v-else class="mt-4 text-muted-foreground">
            No notes yet.
          </p>
        </section>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div
      v-if="confirmDelete && project"
      class="fixed inset-0 z-50 grid place-items-center bg-cocoa/40 p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="delete-title"
      @click.self="closeDeleteDialog"
    >
      <div
        class="card-craft relative w-full max-w-md animate-rise p-8 text-center shadow-pop"
      >
        <Flower class="mx-auto h-14 w-14 text-cherry" />

        <h2
          id="delete-title"
          class="mt-4 text-3xl font-bold"
        >
          Delete this project?
        </h2>

        <p class="mt-2 text-muted-foreground">
          Are you sure? This little creation will be removed
          from your craft space.
        </p>

        <p
          v-if="deleteError"
          role="alert"
          class="mt-4 rounded-xl border-2 border-cherry bg-cherry-soft p-3 text-sm text-cherry"
        >
          {{ deleteError }}
        </p>

        <div
          class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center"
        >
          <button
            type="button"
            class="btn btn-ghost"
            :disabled="deleting"
            @click="closeDeleteDialog"
          >
            Cancel
          </button>

          <button
            type="button"
            class="btn btn-danger"
            :disabled="deleting"
            @click="handleDelete"
          >
            <Loader2
              v-if="deleting"
              class="h-4 w-4 animate-spin"
            />

            <Trash2 v-else class="h-4 w-4" />

            {{ deleting ? 'Deleting…' : 'Delete Project' }}
          </button>
        </div>
      </div>
    </div>
  </AppShell>
</template>