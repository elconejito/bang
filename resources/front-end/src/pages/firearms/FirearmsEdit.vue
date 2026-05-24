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
      <router-link
        :to="{ name: 'FirearmsShow', params: { firearm_id: firearmId } }"
        class="hover:text-gray-700"
      >{{ firearm.label }}</router-link>
      <span>›</span>
      <span class="text-gray-700">Edit</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Firearm</h1>

    <div class="max-w-lg">
      <EditFirearmForm :original="firearm" @complete="onComplete" />
    </div>
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
