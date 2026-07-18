<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Search } from 'lucide-vue-next';
import { useRoute, useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import MagazineGroupTable from '@/components/magazines/MagazineGroupTable.vue';
import MagazineStateModal from '@/components/magazines/MagazineStateModal.vue';
import { useLocationsStore } from '@/stores/locations';
import { useMagazineGroupsStore } from '@/stores/magazineGroups';

const props = defineProps({ groupKey: { type: String, required: true } });
const route = useRoute();
const router = useRouter();
const store = useMagazineGroupsStore();
const locationsStore = useLocationsStore();

const magazines = ref([]);
const group = ref(null);
const meta = ref({ current_page: 1, last_page: 1, per_page: 25, from: null, to: null, total: 0 });
const locations = ref([]);
const selectedMagazine = ref(null);
const loading = ref(true);
const failed = ref(false);
const search = ref(String(route.query.search ?? ''));
let searchTimer;
let requestNumber = 0;

const stateOptions = [
  { value: '', label: 'All states' },
  { value: 'in_gun', label: 'In firearm' },
  { value: 'loaded', label: 'Loaded spare' },
  { value: 'empty', label: 'Empty' },
];
const sortOptions = [
  { value: 'id_marking', label: 'Marking (A-Z)' },
  { value: '-id_marking', label: 'Marking (Z-A)' },
  { value: 'state', label: 'State' },
  { value: '-state', label: 'State (reverse)' },
  { value: 'loaded_ammunition', label: 'Loaded with (A-Z)' },
  { value: '-loaded_ammunition', label: 'Loaded with (Z-A)' },
  { value: 'location', label: 'Location (A-Z)' },
  { value: '-location', label: 'Location (Z-A)' },
];
const perPageOptions = [10, 25, 50, 100];

const groupTitle = computed(() => group.value?.model_name || 'Magazine group');
const groupSubtitle = computed(() =>
  group.value
    ? [
        group.value.manufacturer,
        group.value.calibers?.map((caliber) => caliber.label).join(' / '),
        `${group.value.capacity} rd`,
      ]
        .filter(Boolean)
        .join(' / ')
    : ''
);
const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Magazines', to: { name: 'MagazinesIndex' } },
  { label: groupTitle.value },
]);

function requestParams() {
  return {
    ...(route.query.state ? { 'filter[state]': route.query.state } : {}),
    ...(route.query.location_id ? { 'filter[location_id]': route.query.location_id } : {}),
    ...(route.query.search ? { 'filter[search]': route.query.search } : {}),
    'filter[lifecycle_status]': route.query.lifecycle_status ?? 'active',
    sort: route.query.sort ?? 'id_marking',
    page: route.query.page ?? 1,
    per_page: route.query.per_page ?? 25,
  };
}

async function loadMagazines() {
  const currentRequest = ++requestNumber;
  loading.value = true;
  failed.value = false;
  try {
    const response = await store.fetchGroupMagazines(props.groupKey, requestParams());
    if (currentRequest !== requestNumber) return;
    magazines.value = response.data ?? [];
    group.value = response.group;
    meta.value = response.meta;
  } catch {
    if (currentRequest === requestNumber) failed.value = true;
  } finally {
    if (currentRequest === requestNumber) loading.value = false;
  }
}

function updateQuery(changes, resetPage = true) {
  router.push({
    name: 'MagazineGroupShow',
    params: { group: props.groupKey },
    query: { ...route.query, ...changes, ...(resetPage ? { page: undefined } : {}) },
  });
}

function updateSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    updateQuery({ search: search.value.trim() || undefined });
  }, 300);
}

function goToPage(page) {
  if (page >= 1 && page <= meta.value.last_page) updateQuery({ page: String(page) }, false);
}

onMounted(async () => {
  loadMagazines();
  try {
    const response = await locationsStore.fetchAll();
    locations.value = response.data ?? [];
  } catch {
    locations.value = [];
  }
});
onBeforeUnmount(() => clearTimeout(searchTimer));
watch(() => route.fullPath, loadMagazines);
watch(
  () => route.query.search,
  (value) => {
    search.value = String(value ?? '');
  }
);
</script>

<template>
  <div class="mx-auto max-w-[1280px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
      <div>
        <h1 class="font-display text-[28px] font-bold tracking-[-0.02em] text-ink-900">
          {{ groupTitle }}
        </h1>
        <p class="mt-1 text-sm text-muted">{{ groupSubtitle }}</p>
      </div>
      <div class="flex flex-wrap items-center justify-end gap-2">
        <select
          :value="route.query.lifecycle_status ?? 'active'"
          aria-label="Filter by lifecycle status"
          class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm text-ink-700 outline-none focus:border-brass-700"
          @change="
            updateQuery({
              lifecycle_status: $event.target.value === 'active' ? undefined : $event.target.value,
            })
          "
        >
          <option value="active">Active</option>
          <option value="archived">Archived</option>
          <option value="all">All statuses</option>
        </select>
        <span v-if="!loading" class="mr-1 font-mono text-xs text-muted"
          >{{ meta.total }} MAGAZINES</span
        >
        <router-link
          :to="{ name: 'MagazineBatchCreate', query: { group: groupKey } }"
          class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50"
        >
          Add several
        </router-link>
        <router-link
          :to="{ name: 'MagazinesCreate', query: { group: groupKey } }"
          class="rounded border border-brass-700 px-3 py-2 text-sm font-semibold text-brass-800 hover:bg-brass-50"
        >
          Add magazine
        </router-link>
      </div>
    </div>

    <div
      class="index-toolbar mb-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-[minmax(220px,1fr)_170px_200px_210px]"
    >
      <label class="index-toolbar-search gap-2">
        <Search class="h-4 w-4 shrink-0 text-muted" />
        <input
          v-model="search"
          type="search"
          placeholder="Search marking..."
          class="text-sm text-ink-900 placeholder:text-muted"
          @input="updateSearch"
        />
      </label>
      <select
        :value="route.query.state ?? ''"
        aria-label="Filter by state"
        class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm text-ink-700 outline-none focus:border-brass-700"
        @change="updateQuery({ state: $event.target.value || undefined })"
      >
        <option v-for="option in stateOptions" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
      <select
        :value="route.query.location_id ?? ''"
        aria-label="Filter by location"
        class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm text-ink-700 outline-none focus:border-brass-700"
        @change="updateQuery({ location_id: $event.target.value || undefined })"
      >
        <option value="">All locations</option>
        <option value="in_firearm">In a firearm</option>
        <option value="unassigned">Unassigned</option>
        <option v-for="location in locations" :key="location.id" :value="String(location.id)">
          {{ location.label }}
        </option>
      </select>
      <select
        :value="route.query.sort ?? 'id_marking'"
        aria-label="Sort magazines"
        class="rounded border border-[#c2c6ca] bg-white px-3 py-2 text-sm text-ink-700 outline-none focus:border-brass-700"
        @change="
          updateQuery({
            sort: $event.target.value === 'id_marking' ? undefined : $event.target.value,
          })
        "
      >
        <option v-for="option in sortOptions" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </div>

    <p
      v-if="failed"
      class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
    >
      The magazines could not be loaded. Please try again.
    </p>
    <MagazineGroupTable
      :magazines="magazines"
      :loading="loading"
      @change-state="selectedMagazine = $event"
    />

    <MagazineStateModal
      v-if="selectedMagazine"
      :magazine="selectedMagazine"
      @close="selectedMagazine = null"
      @saved="
        selectedMagazine = null;
        loadMagazines();
      "
    />

    <div
      v-if="!loading && !failed && meta.total"
      class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4"
    >
      <div class="flex items-center gap-2 text-sm text-muted">
        <span>Showing {{ meta.from }}-{{ meta.to }} of {{ meta.total }}</span>
        <select
          :value="meta.per_page"
          aria-label="Magazines per page"
          class="rounded border border-line bg-white px-2 py-1 text-sm text-ink-700 outline-none"
          @change="
            updateQuery({
              per_page: $event.target.value === '25' ? undefined : $event.target.value,
            })
          "
        >
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }} / page
          </option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <button
          class="rounded border border-line bg-white px-3 py-1.5 text-sm text-ink-700 hover:bg-ink-50 disabled:cursor-not-allowed disabled:opacity-40"
          :disabled="meta.current_page <= 1"
          @click="goToPage(meta.current_page - 1)"
        >
          Previous
        </button>
        <span class="font-mono text-xs text-muted"
          >{{ meta.current_page }} / {{ meta.last_page }}</span
        >
        <button
          class="rounded border border-line bg-white px-3 py-1.5 text-sm text-ink-700 hover:bg-ink-50 disabled:cursor-not-allowed disabled:opacity-40"
          :disabled="meta.current_page >= meta.last_page"
          @click="goToPage(meta.current_page + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>
