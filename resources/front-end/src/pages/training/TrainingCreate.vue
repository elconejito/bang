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
          <router-link :to="{ name: 'TrainingIndex' }">
            All Training
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Add Training</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Add Training</h1>
      </div>
    </div>

    <div class="row">
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
