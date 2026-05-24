<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }"><font-awesome-icon icon="home" /></router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'MagazinesIndex' }">All Magazines</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Add Magazine</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Add Magazine</h1>
      </div>
    </div>

    <MagazineForm @complete="onComplete" />
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
