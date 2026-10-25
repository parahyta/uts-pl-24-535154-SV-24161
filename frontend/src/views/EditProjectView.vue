
<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ArrowLeft, Loader2 } from 'lucide-vue-next'

import AppShell from '@/components/AppShell.vue'
import ProjectForm from '@/components/projects/ProjectForm.vue'
import Flower from '@/components/doodles/Flower.vue'

import {
  getProject,
  updateProject,
} from '@/services/projectService'

import { clearToken } from '@/services/authService'

const route = useRoute()
const router = useRouter()

const project = ref(null)
const loading = ref(true)
const notFound = ref(false)
const errorMessage = ref('')

async function loadProject(id) {
  loading.value = true
  project.value = null
  notFound.value = false
  errorMessage.value = ''

  try {
    const data = await getProject(id)

    // Abaikan respons lama jika pengguna berpindah ID
    // sebelum request sebelumnya selesai.
    if (String(route.params.id) !== String(id)) return

    project.value = data
    document.title = `Edit ${data.name} — Craftory`
  } catch (error) {
    if (String(route.params.id) !== String(id)) return

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
    if (String(route.params.id) === String(id)) {
      loading.value = false
    }
  }
}

async function handleUpdate(payload, { resolve, reject }) {
  if (!project.value) {
    reject(new Error('Project data is not available.'))
    return
  }

  try {
    const updatedProject = await updateProject(
      project.value.id,
      payload,
    )

    resolve(updatedProject)

    await router.replace(`/projects/${project.value.id}`)
  } catch (error) {
    if (error.response?.status === 401) {
      clearToken()
      reject(error)
      await router.replace('/login')
    } else if (error.response?.status === 404) {
      reject(error)
      project.value = null
      notFound.value = true
    } else {
      // ProjectForm menangani error 422 dan menampilkan
      // pesan validasi dari Laravel.
      reject(error)
    }
  }
}

watch(
  () => route.params.id,
  (id) => {
    if (id) {
      loadProject(id)
    }
  },
  { immediate: true },
)
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
        Loading your project…
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

    <!-- API error -->
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
    </div>

    <!-- Edit project form -->
    <div v-else-if="project">
      <RouterLink
        :to="`/projects/${project.id}`"
        class="inline-flex items-center gap-1 text-sm font-medium hover:underline"
      >
        <ArrowLeft class="h-4 w-4" />
        Back to project
      </RouterLink>

      <h1 class="mb-10 mt-4 text-5xl font-bold">
        Edit
        <em class="font-medium text-sky-deep">
          {{ project.name }}
        </em>
      </h1>

      <ProjectForm
        :key="project.id"
        :initial="project"
        submit-label="Save Changes"
        @submit="handleUpdate"
      />
    </div>
  </AppShell>
</template>
