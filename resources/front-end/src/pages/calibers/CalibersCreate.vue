<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersIndex' }">All Calibers</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Create Caliber</h1>
      </div>
    </div>

    <CaliberForm @complete="completeAddCaliber" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import CaliberForm from '@/components/caliber/CaliberForm.vue'

const router = useRouter()

function completeAddCaliber() {
  router.push({ name: 'CalibersIndex' })
}
</script>
