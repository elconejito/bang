<template>
  <div class="container">
    <div class="row">
      <div class="col" v-if="success">
        <p>You have successfully registered. Please login now.</p>
        <router-link :to="{ name: 'login' }">Login</router-link>
      </div>
      <div class="col" v-else>
        <h1>Register</h1>
        <FormError v-if="error" :error="error" />
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" id="name" v-model="name">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" v-model="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" v-model="password">
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" v-model="password_confirmation">
        </div>
        <button class="btn btn-primary" @click="register" :disabled="isLoading">
          Register
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useLoading } from '@/composables/useLoading'
import FormError from '@/components/FormError.vue'

const authStore = useAuthStore()
const { isLoading, loadingQueue } = useLoading()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref(null)
const success = ref(false)

async function register() {
  isLoading.value = true
  loadingQueue.register = false
  error.value = null
  try {
    await authStore.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    })
    loadingQueue.register = true
    success.value = true
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
    loadingQueue.register = true
  }
}
</script>
