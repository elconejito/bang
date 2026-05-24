<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'MagazinesIndex' }" class="hover:text-gray-700">All Magazines</router-link>
      <span>›</span>
      <span class="text-gray-700">Add Magazine</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Add Magazine</h1>

    <div class="max-w-lg">
      <MagazineForm @complete="onComplete" />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import MagazineForm from '@/components/magazines/MagazineForm.vue'

const router = useRouter()

function onComplete(created) {
  router.push({ name: 'MagazinesShow', params: { magazine_id: created.id } })
}
</script>
