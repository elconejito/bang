<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';
import { useMiscAccessoriesStore } from '@/stores/miscAccessories';

const props = defineProps({
  miscId: { type: Number, required: true },
});

const router = useRouter();
const miscStore = useMiscAccessoriesStore();
const misc = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await miscStore.fetchOne(props.miscId);
  misc.value = data;
  loading.value = false;
});

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Misc', to: { name: 'AccessoriesMisc' } },
  { label: 'Edit Misc Item' },
];

function onComplete(updated) {
  router.push({ name: 'MiscShow', params: { misc_id: updated.id } });
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Edit Misc Accessory</h1>
    <LoadingState v-if="loading" message="Loading accessory…" />
    <AccessoryFormCard
      v-else
      type="misc"
      :item="misc"
      @complete="onComplete"
      @cancel="router.back()"
    />
  </div>
</template>
