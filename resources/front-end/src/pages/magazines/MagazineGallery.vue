<script setup>
import { ref, computed, onMounted } from 'vue'
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue'
import { useMagazinesStore } from '@/stores/magazines'

const props = defineProps({
  magazineId: { type: Number, required: true },
})

const store = useMagazinesStore()
const magazine = ref(null)

onMounted(async () => {
  const { data } = await store.fetchOne(props.magazineId)
  magazine.value = data
})

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Magazines', to: { name: 'MagazinesIndex' } },
  {
    label: magazine.value?.label ?? magazine.value?.model_name ?? '…',
    to: { name: 'MagazinesShow', params: { magazine_id: props.magazineId } },
  },
  { label: 'Photos' },
])
</script>

<template>
  <GalleryPageContent
    entity-type="magazines"
    :entity-id="magazineId"
    :crumbs="crumbs"
    :entity-title="magazine?.label ?? magazine?.model_name"
  />
</template>
