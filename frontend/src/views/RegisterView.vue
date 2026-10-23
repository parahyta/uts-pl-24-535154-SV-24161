<script setup>
import { reactive, ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Loader2 } from 'lucide-vue-next'

import AuthLayout from '@/components/AuthLayout.vue'
import PasswordInput from '@/components/PasswordInput.vue'
import { registerUser } from '@/services/authService'

const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  confirm: '',
})

const errors = reactive({
  name: '',
  email: '',
  password: '',
  confirm: '',
})

const loading = ref(false)
const serverError = ref('')

onMounted(() => {
  document.title = 'Create your account — Craftory'
})

function clearErrors() {
  Object.keys(errors).forEach((key) => {
    errors[key] = ''
  })

  serverError.value = ''
}

function validateForm() {
  clearErrors()

  if (!form.name.trim()) {
    errors.name = 'Tell us your name.'
  }

  if (!/^\S+@\S+\.\S+$/.test(form.email.trim())) {
    errors.email = 'Please enter a valid email.'
  }

  if (form.password.length < 8) {
    errors.password = 'Use at least 8 characters.'
  }

  if (form.confirm !== form.password) {
    errors.confirm = "Passwords don't match."
  }

  return !Object.values(errors).some(Boolean)
}

async function submit() {
  if (loading.value || !validateForm()) return

  loading.value = true

  try {
    await registerUser({
      name: form.name.trim(),
      email: form.email.trim(),
      password: form.password,
      password_confirmation: form.confirm,
    })

    await router.push('/dashboard')
  } catch (error) {
    if (error.response?.status === 422) {
      const backendErrors = error.response.data.errors ?? {}

      errors.name = backendErrors.name?.[0] ?? ''
      errors.email = backendErrors.email?.[0] ?? ''
      errors.password = backendErrors.password?.[0] ?? ''

      if (backendErrors.password_confirmation?.length) {
        errors.confirm = backendErrors.password_confirmation[0]
      }

      if (!Object.values(errors).some(Boolean)) {
        serverError.value =
          error.response.data.message ?? 'Please check your information.'
      }
    } else {
      serverError.value =
        'Unable to create your account. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Join Craftory"
    subtitle="Make a cozy home for all your handmade ideas."
  >
    <form
      class="flex flex-col gap-4"
      novalidate
      @submit.prevent="submit"
    >
      <!-- Name -->
      <div class="flex flex-col gap-1.5">
        <label for="name" class="text-sm font-semibold">
          Name
        </label>

        <input
          id="name"
          v-model="form.name"
          class="field"
          autocomplete="name"
          :aria-invalid="Boolean(errors.name)"
          placeholder="Your name"
        />

        <p v-if="errors.name" class="text-sm text-destructive">
          {{ errors.name }}
        </p>
      </div>

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

        <p v-if="errors.email" class="text-sm text-destructive">
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
          autocomplete="new-password"
          placeholder="At least 8 characters"
          :invalid="Boolean(errors.password)"
        />

        <p v-if="errors.password" class="text-sm text-destructive">
          {{ errors.password }}
        </p>
      </div>

      <!-- Confirm Password -->
      <div class="flex flex-col gap-1.5">
        <label for="confirm" class="text-sm font-semibold">
          Confirm Password
        </label>

        <PasswordInput
          id="confirm"
          v-model="form.confirm"
          autocomplete="new-password"
          placeholder="Type it again"
          :invalid="Boolean(errors.confirm)"
        />

        <p v-if="errors.confirm" class="text-sm text-destructive">
          {{ errors.confirm }}
        </p>
      </div>

      <!-- Server error -->
      <p
        v-if="serverError"
        role="alert"
        class="rounded-xl border border-cherry bg-cherry-soft p-3 text-sm text-cherry"
      >
        {{ serverError }}
      </p>

      <button
        type="submit"
        :disabled="loading"
        class="btn btn-primary mt-2 w-full text-base"
      >
        <Loader2
          v-if="loading"
          class="h-4 w-4 animate-spin"
        />

        {{ loading ? 'Creating…' : 'Create Account' }}
      </button>

      <p class="text-center text-sm">
        Already have an account?

        <RouterLink
          to="/login"
          class="font-semibold underline decoration-cherry decoration-2 underline-offset-4"
        >
          Log in
        </RouterLink>
      </p>
    </form>
  </AuthLayout>
</template>