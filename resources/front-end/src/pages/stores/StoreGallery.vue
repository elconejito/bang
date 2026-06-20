<script setup>
import { ref, computed, onMounted } from 'vue'
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue'
import { useGunStoresStore } from '@/stores/gunStores'

const props = defineProps({
  storeId: { type: Number, required: true },
})

const store = useGunStoresStore()
const gunStore = ref(null)

onMounted(async () => {
  const { data } = await store.fetchOne(props.storeId)
  gunStore.value = data
})

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Stores', to: { name: 'StoreIndex' } },
  {
    label: gunStore.value?.label ?? '…',
    to: { name: 'StoreShow', params: { store_id: props.storeId } },
  },
  { label: 'Photos' },
])
</script>

<template>
  <GalleryPageContent
    entity-type="stores"
    :entity-id="storeId"
    :crumbs="crumbs"
    :entity-title="gunStore?.label"
  />
</template>
