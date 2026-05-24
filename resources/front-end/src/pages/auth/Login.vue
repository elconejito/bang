<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <h1>Login</h1>
        <FormError v-if="error" :error="error" />
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" v-model="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" v-model="password">
        </div>
        <button class="btn btn-primary" :disabled="isLoading" @click="login">
          Log in
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useLoading } from '@/composables/useLoading'
import FormError from '@/components/FormError.vue'

const router = useRouter()
const authStore = useAuthStore()
const { isLoading, loadingQueue } = useLoading()

const email = ref('')
const password = ref('')
const error = ref(null)

async function login() {
  isLoading.value = true
  loadingQueue.login = false
  error.value = null
  try {
    await authStore.login({ email: email.value, password: password.value })
    router.push({ name: 'dashboard' })
  } catch (err) {
    error.value = err
  } finally {
    loadingQueue.login = true
  }
}
</script>
