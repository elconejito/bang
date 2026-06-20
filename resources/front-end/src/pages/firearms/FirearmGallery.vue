<script setup>
import { ref, computed, onMounted } from 'vue'
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue'
import { useFirearmsStore } from '@/stores/firearms'

const props = defineProps({
  firearmId: { type: Number, required: true },
})

const firearmsStore = useFirearmsStore()
const firearm = ref(null)

onMounted(async () => {
  const { data } = await firearmsStore.fetchOne(props.firearmId)
  firearm.value = data
})

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Firearms', to: { name: 'FirearmsIndex' } },
  {
    label: firearm.value?.label ?? firearm.value?.manufacturer ?? '…',
    to: { name: 'FirearmsShow', params: { firearm_id: props.firearmId } },
  },
  { label: 'Photos' },
])
</script>

<template>
  <GalleryPageContent
    entity-type="firearms"
    :entity-id="firearmId"
    :crumbs="crumbs"
    :entity-title="firearm?.label ?? firearm?.manufacturer"
  />
</template>
