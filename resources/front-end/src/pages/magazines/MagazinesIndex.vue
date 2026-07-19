<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import LoadingCard from '@/components/status/LoadingCard.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import MagazineGroupCard from '@/components/magazines/MagazineGroupCard.vue';
import { useMagazineGroupsStore } from '@/stores/magazineGroups';
import { useFirearmsStore } from '@/stores/firearms';

const route = useRoute();
const router = useRouter();
const store = useMagazineGroupsStore();
const firearmsStore = useFirearmsStore();
const groups = ref([]);
const meta = ref({ groups: 0, magazines: 0 });
const loading = ref(true);
const error = ref(null);
const compatibleFirearm = ref(null);

const compatibleFirearmId = computed(() => route.params.firearm_id ?? null);
const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  ...(compatibleFirearmId.value
    ? [
        { label: 'Magazines', to: { name: 'MagazinesIndex' } },
        {
          label: compatibleFirearm.value
            ? `Compatible with ${[
                compatibleFirearm.value.manufacturer,
                compatibleFirearm.value.label,
              ]
                .filter(Boolean)
                .join(' ')}`
            : 'Compatible magazines',
        },
      ]
    : [{ label: 'Magazines' }]),
]);
const contextLabel = computed(() => {
  if (!compatibleFirearmId.value) return null;
  const firearm = compatibleFirearm.value;
  return firearm
    ? `Compatible with ${[firearm.manufacturer, firearm.label].filter(Boolean).join(' ')}`
    : 'Compatible magazines';
});

async function loadGroups() {
  loading.value = true;
  error.value = null;
  try {
    const params = compatibleFirearmId.value
      ? { 'filter[compatible_firearm_id]': compatibleFirearmId.value }
      : {};
    params['filter[lifecycle_status]'] = route.query.lifecycle_status ?? 'active';
    const response = await store.fetchAll(params);
    groups.value = response.data;
    meta.value = response.meta;
    if (compatibleFirearmId.value) {
      const { data } = await firearmsStore.fetchOne(compatibleFirearmId.value);
      compatibleFirearm.value = data;
    } else {
      compatibleFirearm.value = null;
    }
  } catch (exception) {
    error.value = exception;
  } finally {
    loading.value = false;
  }
}

onMounted(loadGroups);
watch([compatibleFirearmId, () => route.query.lifecycle_status], loadGroups);
</script>

<template>
  <div class="mx-auto max-w-[1280px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="font-display text-[28px] font-bold tracking-[-0.02em] text-ink-900">
          Magazine Groups
        </h1>
        <p v-if="contextLabel" class="mt-1 text-sm text-muted">{{ contextLabel }}</p>
        <p v-else class="mt-1 text-sm text-muted">
          {{ meta.groups }} groups · {{ meta.magazines }} magazines
        </p>
      </div>
      <div class="flex flex-wrap gap-2">
        <select
          :value="route.query.lifecycle_status ?? 'active'"
          aria-label="Filter by lifecycle status"
          class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm text-ink-700 outline-none focus:border-brass-700"
          @change="
            router.push({
              name: 'MagazinesIndex',
              params: route.params,
              query: {
                ...route.query,
                lifecycle_status:
                  $event.target.value === 'active' ? undefined : $event.target.value,
              },
            })
          "
        >
          <option value="active">Active</option>
          <option value="archived">Archived</option>
          <option value="all">All statuses</option>
        </select>
        <router-link
          :to="{ name: 'MagazineBatchCreate' }"
          class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50"
        >
          Add several
        </router-link>
        <router-link
          :to="{ name: 'MagazinesCreate' }"
          class="rounded border border-brass-700 px-3 py-2 text-sm font-semibold text-brass-800 hover:bg-brass-50"
        >
          Add Magazine
        </router-link>
      </div>
    </div>

    <LoadingCard v-if="loading" message="Loading magazine groups..." />
    <ErrorCard v-else-if="error" :error="error" />
    <EmptyState
      v-else-if="!groups.length"
      title="No magazine groups found"
      message="Add a magazine or clear the compatibility filter."
      action-label="Add Magazine"
      :action-to="{ name: 'MagazinesCreate' }"
    />
    <div v-else class="grid grid-cols-3 gap-4">
      <MagazineGroupCard v-for="group in groups" :key="group.key" :group="group" />
    </div>
  </div>
</template>
