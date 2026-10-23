
<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  getAuthenticatedUser,
  logoutUser,
  clearToken,
} from '@/services/authService'

const router = useRouter()

const user = ref(null)
const loading = ref(true)
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
    console.error('Unable to revoke token on server:', error)
  } finally {
    loggingOut.value = false
    await router.replace('/login')
  }
}
</script>

<template>
  <main class="paper-dots min-h-screen px-6 py-16">
    <div class="card-craft mx-auto max-w-2xl p-8 shadow-pop">
      <div class="flex items-center justify-between gap-4">
        <h1 class="text-4xl font-bold">
          My Craft Space
        </h1>

        <button
          type="button"
          class="btn btn-danger"
          :disabled="loggingOut"
          @click="handleLogout"
        >
          {{ loggingOut ? 'Logging out…' : 'Logout' }}
        </button>
      </div>

      <p v-if="loading" class="mt-5">
        Loading your account...
      </p>

      <p
        v-else-if="errorMessage"
        class="mt-5 text-cherry"
      >
        {{ errorMessage }}
      </p>

      <div v-else-if="user" class="mt-5">
        <p>Welcome, {{ user.name }}! ✿</p>

        <p class="mt-2 text-sm">
          Email: {{ user.email }}
        </p>

        <p class="mt-4 text-sm text-muted-foreground">
          Your account is connected to Laravel.
        </p>
      </div>
    </div>
  </main>
</template>