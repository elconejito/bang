<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }"><font-awesome-icon icon="home" /></router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'FirearmsIndex' }">All Firearms</router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'FirearmsShow', params: { firearm_id: firearmId } }">{{ firearm.label }}</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Edit Firearm</h1>
      </div>
    </div>

    <EditFirearmForm :original="firearm" @complete="onComplete" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFirearmsStore } from '@/stores/firearms'
import { useLoading } from '@/composables/useLoading'
import Loading from '@/components/Loading.vue'
import EditFirearmForm from '@/components/firearms/EditFirearmForm.vue'

const props = defineProps({
  firearmId: { type: Number, required: true },
})

const router = useRouter()
const firearmsStore = useFirearmsStore()
const { isLoading, loadingQueue } = useLoading()

const firearm = ref({})

onMounted(async () => {
  isLoading.value = true
  loadingQueue.firearm = false
  const { data } = await firearmsStore.fetchOne(props.firearmId)
  firearm.value = data
  loadingQueue.firearm = true
})

function onComplete() {
  router.push({ name: 'FirearmsShow', params: { firearm_id: props.firearmId } })
}
</script>
