<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }"><font-awesome-icon icon="home" /></router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'FirearmsIndex' }">All Firearms</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Add Firearm</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Add Firearm</h1>
      </div>
    </div>

    <FirearmForm @complete="onComplete" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import FirearmForm from '@/components/firearms/FirearmForm.vue'

const router = useRouter()

function onComplete(created) {
  router.push({ name: 'FirearmsShow', params: { firearm_id: created.id } })
}
</script>
