<script setup>
import { reactive, ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Loader2 } from 'lucide-vue-next'

import AuthLayout from '@/components/authentication/AuthLayout.vue'
import PasswordInput from '@/components/authentication/PasswordInput.vue'
import { loginUser } from '@/services/authService'

const router = useRouter()

const form = reactive({
  email: '',
  password: '',
})

const errors = reactive({
  email: '',
  password: '',
  form: '',
})

const loading = ref(false)

onMounted(() => {
  document.title = 'Log in — Craftory'
})

function validateForm() {
  errors.email = ''
  errors.password = ''
  errors.form = ''

  if (!/^\S+@\S+\.\S+$/.test(form.email.trim())) {
    errors.email = 'Please enter a valid email.'
  }

  if (!form.password) {
    errors.password = 'Password is required.'
  }

  return !errors.email && !errors.password
}

async function submit() {
  if (loading.value || !validateForm()) return

  loading.value = true

  try {
    await loginUser(form.email.trim(), form.password)

    await router.replace('/dashboard')
  } catch (error) {
    const status = error.response?.status
    const backendErrors = error.response?.data?.errors ?? {}

    if (status === 422) {
      errors.email = backendErrors.email?.[0] ?? ''
      errors.password = backendErrors.password?.[0] ?? ''

      if (!errors.email && !errors.password) {
        errors.form =
          error.response?.data?.message ??
          'Invalid email or password.'
      }
    } else if (status === 401) {
      errors.form = 'Invalid email or password.'
    } else {
      errors.form =
        'Unable to log in. Please check your connection and try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Welcome back!"
    subtitle="Your yarn missed you. Log in to continue crafting."
  >
    <form
      class="flex flex-col gap-5"
      novalidate
      @submit.prevent="submit"
    >
      <!-- Form error -->
      <p
        v-if="errors.form"
        role="alert"
        class="rounded-xl border-2 border-destructive bg-destructive/10 px-4 py-3 text-sm font-medium"
      >
        {{ errors.form }}
      </p>

      <!-- Email -->
      <div class="flex flex-col gap-1.5">
        <label for="email" class="text-sm font-semibold">
          Email
        </label>

        <input
          id="email"
          v-model="form.email"
          type="email"
          autocomplete="email"
          class="field"
          :aria-invalid="Boolean(errors.email)"
          placeholder="you@example.com"
        />

        <p
          v-if="errors.email"
          class="text-sm text-destructive"
        >
          {{ errors.email }}
        </p>
      </div>

      <!-- Password -->
      <div class="flex flex-col gap-1.5">
        <label for="password" class="text-sm font-semibold">
          Password
        </label>

        <PasswordInput
          id="password"
          v-model="form.password"
          :invalid="Boolean(errors.password)"
          autocomplete="current-password"
          placeholder="••••••••"
        />

        <p
          v-if="errors.password"
          class="text-sm text-destructive"
        >
          {{ errors.password }}
        </p>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="loading"
        class="btn btn-primary mt-2 w-full text-base"
      >
        <Loader2
          v-if="loading"
          class="h-4 w-4 animate-spin"
        />

        {{ loading ? 'Logging in…' : 'Log In' }}
      </button>

      <!-- Register link -->
      <p class="text-center text-sm">
        New here?

        <RouterLink
          to="/register"
          class="font-semibold underline decoration-cherry decoration-2 underline-offset-4"
        >
          Create an account
        </RouterLink>
      </p>
    </form>
  </AuthLayout>
</template>