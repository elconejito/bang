<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'TrainingIndex' }" class="hover:text-gray-700">All Training</router-link>
      <span>›</span>
      <span class="text-gray-700">Add Training</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Add Training</h1>

    <div class="max-w-lg">
      <TrainingForm @complete="onComplete" />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import TrainingForm from '@/components/training/TrainingForm.vue'

const router = useRouter()

function onComplete(created) {
  router.push({ name: 'TrainingShow', params: { training_id: created?.id } })
}
</script>
