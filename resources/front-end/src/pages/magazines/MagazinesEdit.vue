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
  <div class="mx-auto max-w-[760px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <LoadingState v-if="loading" message="Loading magazine…" />
    <template v-else>
      <div class="mb-[22px]">
        <h1 class="mb-1 font-display text-[28px] font-bold tracking-[-0.02em]">Edit Magazine</h1>
        <p class="text-[15px] text-[#6b7077]">
          Update this magazine’s identifying details and compatibility.
        </p>
      </div>
      <MagazineForm
        :item="magazine"
        @complete="onComplete"
        @cancel="router.push({ name: 'MagazinesShow', params: { magazine_id: magazineId } })"
      />
    </template>
  </div>
</template>
