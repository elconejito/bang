<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import MagazineForm from '@/components/magazines/MagazineForm.vue';
import { useMagazineGroupsStore } from '@/stores/magazineGroups';

const route = useRoute();
const router = useRouter();
const groupsStore = useMagazineGroupsStore();
const defaults = ref(null);
const loadingDefaults = ref(Boolean(route.query.group));

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Magazines', to: { name: 'MagazinesIndex' } },
  { label: 'Add Magazine' },
];

function onComplete(created) {
  router.push({ name: 'MagazinesShow', params: { magazine_id: created.id } });
}

onMounted(async () => {
  if (!route.query.group) return;

  try {
    const response = await groupsStore.fetchGroupMagazines(String(route.query.group), {
      per_page: 1,
    });
    defaults.value = response.group ?? null;
  } catch {
    defaults.value = null;
  } finally {
    loadingDefaults.value = false;
  }
});
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Add Magazine</h1>
    <div v-if="loadingDefaults" class="py-12 text-center text-sm text-muted">
      Loading group details…
    </div>
    <MagazineForm v-else :defaults="defaults" @complete="onComplete" @cancel="router.back()" />
  </div>
</template>
