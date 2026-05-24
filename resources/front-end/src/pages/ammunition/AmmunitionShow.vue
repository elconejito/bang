<template>
  <div v-if="isLoading" class="flex h-64 items-center justify-center">
    <Loading class="text-3xl text-gray-400" />
  </div>

  <div v-else class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'CalibersIndex' }" class="hover:text-gray-700">All Calibers</router-link>
      <span>›</span>
      <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }" class="hover:text-gray-700">
        {{ caliber.label }}
      </router-link>
      <span>›</span>
      <span class="text-gray-700">{{ ammunition.manufacturer }} {{ ammunition.label }}</span>
    </nav>

    <div class="mb-6 flex items-center gap-3">
      <div>
        <p class="text-sm text-gray-500">{{ ammunition.manufacturer }}</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ ammunition.label }}</h1>
      </div>
      <router-link
        :to="{ name: 'AmmunitionEdit', params: { caliber_id: caliberId, ammunition_id: ammunitionId } }"
        class="inline-flex items-center gap-1 rounded border border-gray-400 px-2 py-1 text-sm text-gray-600 transition-colors hover:bg-gray-100"
      >
        <font-awesome-icon icon="edit" />
      </router-link>
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

    <component :is="currentTabComponent" :ammunition="ammunition" :caliber="caliber" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useLoading } from '@/composables/useLoading'
import { useNavTabs } from '@/composables/useNavTabs'
import Loading from '@/components/Loading.vue'
import AmmunitionDetails from '@/components/ammunition/AmmunitionDetails.vue'
import AmmunitionInventory from '@/components/ammunition/AmmunitionInventory.vue'
import AmmunitionTraining from '@/components/ammunition/AmmunitionTraining.vue'
import AmmunitionFirearms from '@/components/ammunition/AmmunitionFirearms.vue'
import AmmunitionImages from '@/components/ammunition/AmmunitionImages.vue'

const props = defineProps({
  ammunitionId: { type: Number, required: true },
  caliberId: { type: Number, required: true },
})

const calibersStore = useCalibersStore()
const ammunitionStore = useAmmunitionStore()
const { isLoading, loadingQueue } = useLoading()
const { currentTab, currentTabComponent, tabNames, initTabs, setCurrentTab, tabNameSlug } = useNavTabs()

const caliber = ref({})
const ammunition = ref({})

onMounted(() => {
  initTabs({
    details:   { active: true,  label: 'Details',   component: AmmunitionDetails },
    inventory: { active: false, label: 'Inventory', component: AmmunitionInventory },
    training:  { active: false, label: 'Training',  component: AmmunitionTraining },
    firearms:  { active: false, label: 'Firearms',  component: AmmunitionFirearms },
    images:    { active: false, label: 'Images',    component: AmmunitionImages },
  })
  fetchData()
})

function fetchData() {
  isLoading.value = true
  loadingQueue.caliber = false
  loadingQueue.ammunition = false
  fetchCaliber()
  fetchAmmunition()
}

async function fetchCaliber() {
  const { data } = await calibersStore.fetchOne(props.caliberId)
  caliber.value = data
  loadingQueue.caliber = true
}

async function fetchAmmunition() {
  const { data } = await ammunitionStore.fetchOne(props.caliberId, props.ammunitionId)
  ammunition.value = data
  loadingQueue.ammunition = true
}
</script>
