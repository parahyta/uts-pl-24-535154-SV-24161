
<script setup>
import { onMounted, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import {
  LayoutGrid,
  Shapes,
  LogOut,
  Menu,
  X,
} from 'lucide-vue-next'

import Logo from '@/components/doodles/Logo.vue'
import YarnBall from '@/components/doodles/YarnBall.vue'
import {
  getAuthenticatedUser,
  logoutUser,
  clearToken,
} from '@/services/authService'

const router = useRouter()

const user = ref(null)
const loading = ref(true)
const menuOpen = ref(false)
const loggingOut = ref(false)
const errorMessage = ref('')

onMounted(async () => {
  try {
    user.value = await getAuthenticatedUser()
  } catch (error) {
    if (error.response?.status === 401) {
      clearToken()
      await router.replace('/login')
    } else {
      errorMessage.value = 'Unable to load your account.'
    }
  } finally {
    loading.value = false
  }
})

async function handleLogout() {
  if (loggingOut.value) return

  loggingOut.value = true

  try {
    await logoutUser()
  } catch (error) {
    console.error('Logout request failed:', error)
  } finally {
    loggingOut.value = false
    menuOpen.value = false
    await router.replace('/login')
  }
}
</script>

<template>
  <!-- Loading state -->
  <div
    v-if="loading"
    class="grid min-h-screen place-items-center"
  >
    <YarnBall class="h-14 w-14 animate-spin-slow text-sky" />
  </div>

  <!-- Error state -->
  <div
    v-else-if="errorMessage"
    class="grid min-h-screen place-items-center px-4"
  >
    <div class="card-craft max-w-md p-8 text-center shadow-pop">
      <h1 class="text-2xl font-bold">
        Something went wrong
      </h1>

      <p class="mt-3 text-muted-foreground">
        {{ errorMessage }}
      </p>

      <button
        class="btn btn-butter mt-6"
        @click="router.go(0)"
      >
        Try Again
      </button>
    </div>
  </div>

  <!-- Authenticated application -->
  <div v-else-if="user" class="min-h-screen">
    <header
      class="sticky top-0 z-30 border-b-2 border-cocoa bg-cream/95 backdrop-blur"
    >
      <div
        class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6"
      >
        <Logo to="/dashboard" />

        <!-- Desktop navigation -->
        <nav class="hidden items-center gap-1 md:flex">
          <RouterLink
            to="/dashboard"
            class="flex items-center gap-2 rounded-full px-4 py-2 font-medium hover:bg-butter"
            active-class="bg-butter"
          >
            <LayoutGrid class="h-4 w-4" />
            Dashboard
          </RouterLink>

          <RouterLink
            to="/dashboard#projects"
            class="flex items-center gap-2 rounded-full px-4 py-2 font-medium hover:bg-butter"
          >
            <Shapes class="h-4 w-4" />
            My Projects
          </RouterLink>

          <button
            type="button"
            class="flex items-center gap-2 rounded-full px-4 py-2 font-medium hover:bg-butter"
            :disabled="loggingOut"
            @click="handleLogout"
          >
            <LogOut class="h-4 w-4" />
            {{ loggingOut ? 'Logging out…' : 'Logout' }}
          </button>
        </nav>

        <!-- User avatar -->
        <div class="hidden items-center gap-2 md:flex">
          <span
            class="grid h-9 w-9 place-items-center rounded-full border-2 border-cocoa bg-cherry font-fun font-semibold uppercase text-cream"
            :title="user.email"
          >
            {{ user.name?.charAt(0) || '?' }}
          </span>
        </div>

        <!-- Mobile menu button -->
        <button
          type="button"
          class="btn btn-ghost !min-h-11 !px-3 md:hidden"
          aria-label="Menu"
          :aria-expanded="menuOpen"
          @click="menuOpen = !menuOpen"
        >
          <X v-if="menuOpen" />
          <Menu v-else />
        </button>
      </div>

      <!-- Mobile navigation -->
      <nav
        v-if="menuOpen"
        class="flex flex-col gap-1 border-t-2 border-cocoa p-3 md:hidden"
      >
        <RouterLink
          to="/dashboard"
          class="flex items-center gap-2 rounded-full px-4 py-2 font-medium hover:bg-butter"
          @click="menuOpen = false"
        >
          <LayoutGrid class="h-4 w-4" />
          Dashboard
        </RouterLink>

        <RouterLink
          to="/dashboard#projects"
          class="flex items-center gap-2 rounded-full px-4 py-2 font-medium hover:bg-butter"
          @click="menuOpen = false"
        >
          <Shapes class="h-4 w-4" />
          My Projects
        </RouterLink>

        <button
          type="button"
          class="flex items-center gap-2 rounded-full px-4 py-2 font-medium hover:bg-butter"
          :disabled="loggingOut"
          @click="handleLogout"
        >
          <LogOut class="h-4 w-4" />
          {{ loggingOut ? 'Logging out…' : 'Logout' }}
        </button>
      </nav>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12">
      <slot :user="user" />
    </main>
  </div>
</template>
