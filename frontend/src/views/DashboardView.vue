
<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import {
  Plus,
  Search,
  Layers,
  Hourglass,
  CheckCircle2,
  Circle,
} from 'lucide-vue-next'

import AppShell from '@/components/AppShell.vue'
import ProjectCard from '@/components/projects/ProjectCard.vue'

import Flower from '@/components/doodles/Flower.vue'
import Star from '@/components/doodles/Star.vue'
import YarnBall from '@/components/doodles/YarnBall.vue'

import { getProjects } from '@/services/projectService'
import { clearToken } from '@/services/authService'
import { useRouter } from 'vue-router'

const router = useRouter()

const projects = ref([])
const loading = ref(true)
const errorMessage = ref('')

const query = ref('')
const category = ref('All')
const status = ref('All')

// Kategori diambil dari data proyek pengguna.
// Tidak menggunakan data dummy dari Lovable.
const categories = computed(() =>
  [...new Set(projects.value.map((p) => p.category).filter(Boolean))]
    .sort(),
)

const statuses = [
  { value: 'not_started', label: 'Not Started' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'completed', label: 'Completed' },
]

const stats = computed(() => [
  {
    label: 'Total Projects',
    value: projects.value.length,
    icon: Layers,
    bg: 'bg-paper',
  },
  {
    label: 'In Progress',
    value: projects.value.filter(
      (p) => p.status === 'in_progress',
    ).length,
    icon: Hourglass,
    bg: 'bg-sky',
  },
  {
    label: 'Completed',
    value: projects.value.filter(
      (p) => p.status === 'completed',
    ).length,
    icon: CheckCircle2,
    bg: 'bg-butter',
  },
  {
    label: 'Not Started',
    value: projects.value.filter(
      (p) => p.status === 'not_started',
    ).length,
    icon: Circle,
    bg: 'bg-cherry-soft',
  },
])

const filteredProjects = computed(() => {
  const keyword = query.value.toLowerCase().trim()

  return [...projects.value]
    .sort((a, b) => {
      return (
        new Date(b.created_at ?? 0).getTime() -
        new Date(a.created_at ?? 0).getTime()
      )
    })
    .filter((project) => {
      const matchesCategory =
        category.value === 'All' ||
        project.category === category.value

      const matchesStatus =
        status.value === 'All' ||
        project.status === status.value

      const matchesQuery = (project.name ?? '')
        .toLowerCase()
        .includes(keyword)

      return matchesCategory && matchesStatus && matchesQuery
    })
})

async function loadProjects() {
  loading.value = true
  errorMessage.value = ''

  try {
    projects.value = await getProjects()
  } catch (error) {
    if (error.response?.status === 401) {
      clearToken()
      await router.replace('/login')
      return
    }

    errorMessage.value =
      'Unable to load your projects. Please try again.'
  } finally {
    loading.value = false
  }
}

function clearFilters() {
  query.value = ''
  category.value = 'All'
  status.value = 'All'
}

onMounted(() => {
  document.title = 'My Craft Space — Craftory'
  loadProjects()
})
</script>

<template>
  <AppShell v-slot="{ user }">
    <div class="flex flex-col gap-12">
      <!-- Welcome section -->
      <section
        class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
      >
        <div>
          <p class="font-fun text-sm text-muted-foreground">
            hi {{ user?.name }} ·
          </p>

          <h1 class="text-5xl font-bold sm:text-6xl">
            Hello, creative soul!
            <span class="text-cherry">✿</span>
          </h1>

          <p class="mt-3 text-lg text-muted-foreground">
            Let's see what you've been making lately.
          </p>
        </div>

        <RouterLink
          to="/projects/new"
          class="btn btn-primary text-base shadow-pop-sm"
        >
          <Plus class="h-5 w-5" />
          Add New Project
        </RouterLink>
      </section>

      <!-- Statistics -->
      <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div
          v-for="(stat, index) in stats"
          :key="stat.label"
          :class="[
            'card-craft relative p-5 sm:p-6 animate-rise',
            stat.bg,
          ]"
          :style="{ animationDelay: `${index * 70}ms` }"
        >
          <component :is="stat.icon" class="h-5 w-5" />

          <p class="mt-4 font-display text-5xl font-bold">
            {{ stat.value }}
          </p>

          <p class="mt-1 text-sm font-medium">
            {{ stat.label }}
          </p>

          <Star
            v-if="index === 2"
            class="absolute right-4 top-4 h-7 w-7 text-paper"
          />
        </div>
      </section>

      <!-- Projects -->
      <section id="projects" class="scroll-mt-24">
        <div
          class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
          <h2 class="text-3xl font-bold">
            My Projects
          </h2>

          <div class="flex flex-col gap-3 sm:flex-row">
            <!-- Search -->
            <div class="relative">
              <Search
                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 opacity-60"
              />

              <input
                v-model="query"
                class="field !pl-9 sm:w-60"
                placeholder="Search projects…"
                aria-label="Search by project name"
              />
            </div>

            <!-- Category filter -->
            <select
              v-model="category"
              class="field sm:w-44"
              aria-label="Filter by category"
            >
              <option value="All">
                All categories
              </option>

              <option
                v-for="item in categories"
                :key="item"
                :value="item"
              >
                {{ item }}
              </option>
            </select>

            <!-- Status filter -->
            <select
              v-model="status"
              class="field sm:w-40"
              aria-label="Filter by status"
            >
              <option value="All">
                All statuses
              </option>

              <option
                v-for="item in statuses"
                :key="item.value"
                :value="item.value"
              >
                {{ item.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Loading -->
        <div
          v-if="loading"
          class="card-craft mt-8 flex flex-col items-center gap-4 px-6 py-16"
        >
          <YarnBall
            class="h-16 w-16 animate-spin-slow text-sky"
          />

          <p class="font-fun text-muted-foreground">
            Loading your craft projects…
          </p>
        </div>

        <!-- API error -->
        <div
          v-else-if="errorMessage"
          class="card-craft mt-8 flex flex-col items-center gap-4 px-6 py-14 text-center"
        >
          <p role="alert" class="text-cherry">
            {{ errorMessage }}
          </p>

          <button
            type="button"
            class="btn btn-butter"
            @click="loadProjects"
          >
            Try Again
          </button>
        </div>

        <!-- Empty projects -->
        <div
          v-else-if="projects.length === 0"
          class="card-craft paper-dots mt-8 flex flex-col items-center gap-5 px-6 py-16 text-center"
        >
          <div class="relative">
            <YarnBall
              class="h-28 w-28 text-sky animate-floaty"
            />

            <Flower
              class="absolute -right-6 -top-2 h-10 w-10 text-cherry"
            />

            <Star
              class="absolute -left-6 bottom-0 h-8 w-8 text-butter"
            />
          </div>

          <h3 class="max-w-md text-3xl font-semibold">
            No projects yet. Let's make something wonderful!
          </h3>

          <RouterLink
            to="/projects/new"
            class="btn btn-primary"
          >
            <Plus class="h-4 w-4" />
            Create Your First Project
          </RouterLink>
        </div>

        <!-- No matching results -->
        <div
          v-else-if="filteredProjects.length === 0"
          class="card-craft mt-8 flex flex-col items-center gap-3 px-6 py-14 text-center"
        >
          <Search class="h-8 w-8 opacity-60" />

          <h3 class="text-2xl font-semibold">
            Nothing matches those filters
          </h3>

          <button
            type="button"
            class="btn btn-butter"
            @click="clearFilters"
          >
            Clear filters
          </button>
        </div>

        <!-- Project cards -->
        <div
          v-else
          class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
          <ProjectCard
            v-for="(project, index) in filteredProjects"
            :key="project.id"
            :project="project"
            :index="index"
          />
        </div>
      </section>
    </div>
  </AppShell>
</template>