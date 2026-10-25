
<script setup>
import { onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { ArrowLeft } from 'lucide-vue-next'

import AppShell from '@/components/AppShell.vue'
import ProjectForm from '@/components/projects/ProjectForm.vue'
import { createProject } from '@/services/projectService'

const router = useRouter()

onMounted(() => {
  document.title = 'New Project — Craftory'
})

async function handleCreate(payload, { resolve, reject }) {
  try {
    const project = await createProject(payload)

    // Halaman detail belum dibuat.
    // Setelah berhasil, kembali ke Dashboard agar
    // proyek yang baru disimpan langsung terlihat.
    await router.push('/dashboard')

    resolve(project)
  } catch (error) {
    reject(error)
  }
}
</script>

<template>
  <AppShell>
    <div>
      <RouterLink
        to="/dashboard"
        class="inline-flex items-center gap-1 text-sm font-medium hover:underline"
      >
        <ArrowLeft class="h-4 w-4" />
        Back to My Projects
      </RouterLink>

      <h1 class="mt-4 text-5xl font-bold">
        A new
        <em class="font-medium text-sky-deep">little project</em>
      </h1>

      <p class="mb-10 mt-2 text-lg text-muted-foreground">
        Jot down the details — you can always tweak them later.
      </p>

      <ProjectForm
        submit-label="Save Project"
        @submit="handleCreate"
      />
    </div>
  </AppShell>
</template>
