<template>
  <div v-if="isLoading">
    <Loading />
  </div>
  <div class="container" v-else>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'FirearmsIndex' }">All Firearms</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ firearm.label }}</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          <small>{{ firearm.manufacturer }}</small><br />
          {{ firearm.model }}
          <router-link
            :to="{ name: 'FirearmsEdit', params: { firearm_id: firearmId } }"
            class="btn btn-outline-info"
          >
            <font-awesome-icon icon="edit" />
          </router-link>
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
      <component :is="currentTabComponent" :firearm="firearm" />
    </div>
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
