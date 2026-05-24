<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'TrainingIndex' }" class="hover:text-gray-700">All Training</router-link>
      <span>›</span>
      <span class="text-gray-700">{{ training.label }}</span>
    </nav>

    <div class="mb-6 flex items-center gap-3">
      <div>
        <p class="text-sm text-gray-500">Training</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ training.label }}</h1>
      </div>
      <button
        type="button"
        class="inline-flex items-center gap-1 rounded border border-gray-400 px-2 py-1 text-sm text-gray-600 transition-colors hover:bg-gray-100"
      >
        <font-awesome-icon icon="edit" />
      </button>
    </div>

    <div class="mb-6 border-b border-gray-200">
      <nav class="flex gap-1">
        <button
          v-for="(tab, i) in tabNames"
          :key="i"
          class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
          :class="tabNameSlug(tab) === currentTab
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
          @click="setCurrentTab(tabNameSlug(tab))"
        >{{ tab }}</button>
      </nav>
    </div>

    <component :is="currentTabComponent" :training="training" />
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
