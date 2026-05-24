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
      <router-link :to="{ name: 'FirearmsIndex' }" class="hover:text-gray-700">All Firearms</router-link>
      <span>›</span>
      <span class="text-gray-700">{{ firearm.label }}</span>
    </nav>

    <div class="mb-6 flex items-center gap-3">
      <div>
        <p class="text-sm text-gray-500">{{ firearm.manufacturer }}</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ firearm.model }}</h1>
      </div>
      <router-link
        :to="{ name: 'FirearmsEdit', params: { firearm_id: firearmId } }"
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

    <component :is="currentTabComponent" :firearm="firearm" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFirearmsStore } from '@/stores/firearms'
import { useLoading } from '@/composables/useLoading'
import { useNavTabs } from '@/composables/useNavTabs'
import FirearmAmmunition from '@/components/firearms/FirearmAmmunition.vue'
import FirearmDetails from '@/components/firearms/FirearmDetails.vue'
import FirearmImages from '@/components/firearms/FirearmImages.vue'
import FirearmMagazines from '@/components/firearms/FirearmMagazines.vue'
import Loading from '@/components/Loading.vue'

const props = defineProps({
  firearmId: {
    type: Number,
    required: true,
  },
})

const firearmsStore = useFirearmsStore()
const { isLoading, loadingQueue } = useLoading()
const { currentTab, currentTabComponent, tabNames, initTabs, setCurrentTab, tabNameSlug } = useNavTabs()

const firearm = ref({})

onMounted(() => {
  initTabs({
    details:    { active: true,  label: 'Details',    component: FirearmDetails },
    ammunition: { active: false, label: 'Ammunition', component: FirearmAmmunition },
    magazines:  { active: false, label: 'Magazines',  component: FirearmMagazines },
    images:     { active: false, label: 'Images',     component: FirearmImages },
  })
  fetchFirearm()
})

async function fetchFirearm() {
  isLoading.value = true
  loadingQueue.firearm = false
  const { data } = await firearmsStore.fetchOne(props.firearmId)
  firearm.value = data
  loadingQueue.firearm = true
}
</script>
