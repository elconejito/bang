<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import MagazineForm from '@/components/magazines/MagazineForm.vue';
import { useMagazinesStore } from '@/stores/magazines';

const props = defineProps({
  magazineId: { type: Number, required: true },
});

const router = useRouter();
const magazinesStore = useMagazinesStore();

const magazine = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await magazinesStore.fetchOne(props.magazineId);
  magazine.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Magazines', to: { name: 'MagazinesIndex' } },
  {
    label: magazine.value?.label ?? magazine.value?.model_name ?? '…',
    to: { name: 'MagazinesShow', params: { magazine_id: props.magazineId } },
  },
  { label: 'Edit' },
]);

function onComplete() {
  router.push({ name: 'MagazinesShow', params: { magazine_id: props.magazineId } });
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Edit Magazine</h1>

    <LoadingState v-if="loading" message="Loading magazine…" />
    <MagazineForm
      v-else
      :item="magazine"
      @complete="onComplete"
      @cancel="router.push({ name: 'MagazinesShow', params: { magazine_id: magazineId } })"
    />
  </div>
</template>
