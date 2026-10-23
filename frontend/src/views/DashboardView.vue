
<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  getAuthenticatedUser,
  clearToken,
} from '@/services/authService'

const router = useRouter()
const user = ref(null)
const loading = ref(true)
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
</script>

<template>
  <main class="paper-dots min-h-screen px-6 py-16">
    <div class="card-craft mx-auto max-w-2xl p-8 shadow-pop">
      <h1 class="text-4xl font-bold">
        My Craft Space
      </h1>

      <p v-if="loading" class="mt-5">
        Loading your account...
      </p>

      <p v-else-if="errorMessage" class="mt-5 text-cherry">
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
