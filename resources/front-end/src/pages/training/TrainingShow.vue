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
          <router-link :to="{ name: 'TrainingIndex' }">All Training</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ training.label }}</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          <small>Training</small><br />
          {{ training.label }}
          <button type="button" class="btn btn-outline-info">
            <font-awesome-icon icon="edit" />
          </button>
        </h1>
      </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item" v-for="(tab, i) in tabNames" :key="i">
        <span
          class="btn btn-link nav-link"
          :class="{ active: tabNameSlug(tab) === currentTab }"
          @click="setCurrentTab(tabNameSlug(tab))"
        >
          {{ tab }}
        </span>
      </li>
    </ul>

    <div class="tab-content py-3">
      <component :is="currentTabComponent" :training="training" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTrainingStore } from '@/stores/training'
import { useNavTabs } from '@/composables/useNavTabs'
import TrainingDetails from '@/components/training/TrainingDetails.vue'
import TrainingInventory from '@/components/training/TrainingInventory.vue'

const props = defineProps({
  trainingId: {
    type: Number,
    required: true,
  },
})

const trainingStore = useTrainingStore()
const { currentTab, currentTabComponent, tabNames, initTabs, setCurrentTab, tabNameSlug } = useNavTabs()

const training = ref({})

onMounted(() => {
  initTabs({
    details:   { active: true,  label: 'Details',   component: TrainingDetails },
    inventory: { active: false, label: 'Inventory', component: TrainingInventory },
  })
  fetchTraining()
})

async function fetchTraining() {
  const data = await trainingStore.fetchOne(props.trainingId)
  training.value = data
}
</script>
