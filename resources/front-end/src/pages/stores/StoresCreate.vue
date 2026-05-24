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
          <router-link :to="{ name: 'StoreIndex' }">All Stores</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">New Store</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Add a Store</h1>
      </div>
    </div>

    <StoreForm @complete="onComplete" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import StoreForm from '@/components/stores/StoreForm.vue'

const router = useRouter()

function onComplete() {
  router.push({ name: 'StoreIndex' })
}
</script>
