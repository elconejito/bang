<template>
  <div v-if="isLoading" class="mx-auto max-w-[640px] px-8 py-6">
    <div class="h-8 w-48 animate-pulse rounded bg-ink-100" />
    <div class="mt-4 h-10 w-64 animate-pulse rounded bg-ink-100" />
  </div>

  <div v-else class="mx-auto max-w-[640px] px-8 py-6 pb-16">
    <AppBreadcrumb
      :crumbs="[
        { label: 'Home', to: '/' },
        { label: 'Firearms', to: { name: 'FirearmsIndex' } },
        { label: firearm.label, to: { name: 'FirearmsShow', params: { firearm_id: firearmId } } },
        { label: 'Edit' },
      ]"
      class="mb-4"
    />

    <h1 class="mb-[22px] font-display text-[28px] font-bold tracking-[-0.02em]">Edit {{ firearm.label }}</h1>

    <FirearmFormCard
      :firearm="firearm"
      @complete="onComplete"
      @cancel="router.push({ name: 'FirearmsShow', params: { firearm_id: firearmId } })"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFirearmsStore } from '@/stores/firearms'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import FirearmFormCard from '@/components/firearms/FirearmFormCard.vue'

const props = defineProps({
  firearmId: { type: Number, required: true },
})

const router = useRouter()
const firearmsStore = useFirearmsStore()

const firearm = ref({})
const isLoading = ref(true)

onMounted(async () => {
  try {
    const { data } = await firearmsStore.fetchOne(props.firearmId)
    firearm.value = data
  } finally {
    isLoading.value = false
  }
})

function onComplete() {
  router.push({ name: 'FirearmsShow', params: { firearm_id: props.firearmId } })
}
</script>
