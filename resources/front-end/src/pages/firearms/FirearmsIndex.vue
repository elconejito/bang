<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="[{ label: 'Home', to: '/' }, { label: 'Firearms' }]" class="mb-4" />

    <PageHeader :title="'Firearms'" :count="countLabel" class="mb-5">
      <template #actions>
        <div class="flex overflow-hidden rounded border border-[#c2c6ca]">
          <button
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-[7px] text-[14px] font-medium transition-colors"
            :class="
              viewMode === 'grid'
                ? 'bg-ink-900 text-white'
                : 'bg-white text-ink-500 hover:bg-[#f5f6f7]'
            "
            :aria-pressed="viewMode === 'grid'"
            @click="viewMode = 'grid'"
          >
            <LayoutGrid class="h-[15px] w-[15px]" /> Grid
          </button>
          <button
            type="button"
            class="inline-flex items-center gap-1.5 border-l border-[#c2c6ca] px-3 py-[7px] text-[14px] font-medium transition-colors"
            :class="
              viewMode === 'table'
                ? 'bg-ink-900 text-white'
                : 'bg-white text-ink-500 hover:bg-[#f5f6f7]'
            "
            :aria-pressed="viewMode === 'table'"
            @click="viewMode = 'table'"
          >
            <Table2 class="h-[15px] w-[15px]" /> Table
          </button>
        </div>
        <router-link
          :to="{ name: 'FirearmsCreate' }"
          class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
        >
          <Plus class="h-4 w-4" />
          Add Firearm
        </router-link>
      </template>
    </PageHeader>

    <!-- Toolbar -->
    <div
      ref="toolbarRef"
      class="index-toolbar mb-6 flex flex-wrap items-center gap-2.5"
      @click.stop
    >
      <!-- Search -->
      <div class="index-toolbar-search bg-surface">
        <Search class="h-[17px] w-[17px] shrink-0 text-ink-400" />
        <input
          v-model="search"
          type="text"
          class="placeholder:text-ink-400"
          placeholder="Search by name, make, model, or customizer…"
        />
      </div>

      <!-- Caliber filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-surface px-3 py-2 text-[14px] transition-colors hover:bg-ink-50"
          :class="caliberFilter ? 'text-brass-800 font-medium' : 'text-ink-700'"
          @click.stop="openDropdown = openDropdown === 'caliber' ? null : 'caliber'"
        >
          Caliber <ChevronDown class="h-[15px] w-[15px] text-ink-400" />
        </button>
        <div
          v-if="openDropdown === 'caliber'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[160px] rounded border border-line bg-surface shadow-lg"
        >
          <div class="py-1">
            <button
              class="w-full px-3 py-2 text-left text-[14px] transition-colors hover:bg-ink-50"
              :class="caliberFilter === null ? 'font-medium text-brass-800' : 'text-ink-700'"
              @click="
                caliberFilter = null;
                openDropdown = null;
              "
            >
              All calibers
            </button>
            <button
              v-for="caliber in availableCalibers"
              :key="caliber.id"
              class="w-full px-3 py-2 text-left text-[14px] transition-colors hover:bg-ink-50"
              :class="caliberFilter === caliber.id ? 'font-medium text-brass-800' : 'text-ink-700'"
              @click="
                caliberFilter = caliber.id;
                openDropdown = null;
              "
            >
              {{ caliber.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Storage filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-surface px-3 py-2 text-[14px] transition-colors hover:bg-ink-50"
          :class="locationFilter ? 'text-brass-800 font-medium' : 'text-ink-700'"
          @click.stop="openDropdown = openDropdown === 'storage' ? null : 'storage'"
        >
          Storage <ChevronDown class="h-[15px] w-[15px] text-ink-400" />
        </button>
        <div
          v-if="openDropdown === 'storage'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[180px] rounded border border-line bg-surface shadow-lg"
        >
          <div class="py-1">
            <button
              class="w-full px-3 py-2 text-left text-[14px] transition-colors hover:bg-ink-50"
              :class="locationFilter === null ? 'font-medium text-brass-800' : 'text-ink-700'"
              @click="
                locationFilter = null;
                openDropdown = null;
              "
            >
              All locations
            </button>
            <button
              v-for="location in availableLocations"
              :key="location.id"
              class="w-full px-3 py-2 text-left text-[14px] transition-colors hover:bg-ink-50"
              :class="
                locationFilter === location.id ? 'font-medium text-brass-800' : 'text-ink-700'
              "
              @click="
                locationFilter = location.id;
                openDropdown = null;
              "
            >
              {{ location.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Status filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-surface px-3 py-2 text-[14px] transition-colors hover:bg-ink-50"
          :class="statusFilter !== 'active' ? 'font-medium text-brass-800' : 'text-ink-700'"
          @click.stop="openDropdown = openDropdown === 'status' ? null : 'status'"
        >
          {{ statusOptions.find((option) => option.value === statusFilter)?.label }}
          <ChevronDown class="h-[15px] w-[15px] text-ink-400" />
        </button>
        <div
          v-if="openDropdown === 'status'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[150px] rounded border border-line bg-surface shadow-lg"
        >
          <div class="py-1">
            <button
              v-for="option in statusOptions"
              :key="option.value"
              class="w-full px-3 py-2 text-left text-[14px] transition-colors hover:bg-ink-50"
              :class="statusFilter === option.value ? 'font-medium text-brass-800' : 'text-ink-700'"
              @click="
                statusFilter = option.value;
                openDropdown = null;
              "
            >
              {{ option.label }}
            </button>
          </div>
        </div>
      </div>

      <div class="h-6 w-px bg-[#d6d9dc]" />

      <!-- Sort -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-surface px-3 py-2 text-[14px] text-ink-900 transition-colors hover:bg-ink-50"
          @click.stop="openDropdown = openDropdown === 'sort' ? null : 'sort'"
        >
          <ArrowUpDown class="h-[15px] w-[15px] text-ink-500" />
          {{ currentSortOption.shortLabel }}
          <span class="text-ink-400">{{ currentSortOption.dir }}</span>
          <ChevronDown class="h-[15px] w-[15px] text-ink-400" />
        </button>
        <div
          v-if="openDropdown === 'sort'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[190px] rounded border border-line bg-surface shadow-lg"
        >
          <div class="py-1">
            <button
              v-for="option in sortOptions"
              :key="option.value"
              class="w-full px-3 py-2 text-left text-[14px] transition-colors hover:bg-ink-50"
              :class="sortBy === option.value ? 'font-medium text-brass-800' : 'text-ink-700'"
              @click="
                sortBy = option.value;
                openDropdown = null;
              "
            >
              {{ option.label }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <ErrorCard v-if="error" :error="error" />

    <FirearmList
      v-else-if="viewMode === 'grid'"
      :firearms="filteredFirearms"
      :is-loading="isLoading"
      :empty-title="allFirearms.length ? 'No firearms match your filters' : 'No firearms yet'"
      :empty-message="
        allFirearms.length
          ? 'Try adjusting your search, caliber, or storage filters.'
          : 'Add the static details for your first firearm, then attach photos and log activity from its detail page.'
      "
      :empty-action-label="allFirearms.length ? '' : 'Add Firearm'"
      :empty-action-to="allFirearms.length ? null : { name: 'FirearmsCreate' }"
    />
    <FirearmTable
      v-else
      :firearms="filteredFirearms"
      :is-loading="isLoading"
      :empty-title="allFirearms.length ? 'No firearms match your filters' : 'No firearms yet'"
      :empty-message="
        allFirearms.length
          ? 'Try adjusting your search, caliber, or storage filters.'
          : 'Add the static details for your first firearm, then attach photos and log activity from its detail page.'
      "
      :empty-action-label="allFirearms.length ? '' : 'Add Firearm'"
      :empty-action-to="allFirearms.length ? null : { name: 'FirearmsCreate' }"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { LayoutGrid, Table2, Plus, Search, ChevronDown, ArrowUpDown } from 'lucide-vue-next';
import { useFirearmsStore } from '@/stores/firearms';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import PageHeader from '@/components/PageHeader.vue';
import FirearmList from '@/components/firearms/FirearmList.vue';
import FirearmTable from '@/components/firearms/FirearmTable.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import { usePersistentViewMode } from '@/composables/usePersistentViewMode';

const firearmsStore = useFirearmsStore();

const allFirearms = ref([]);
const isLoading = ref(false);
const error = ref(null);
const search = ref('');
const caliberFilter = ref(null);
const locationFilter = ref(null);
const statusFilter = ref('active');
const sortBy = ref('label_asc');
const openDropdown = ref(null);
const toolbarRef = ref(null);
const viewMode = usePersistentViewMode('firearms');

const sortOptions = [
  { value: 'label_asc', label: 'Name A → Z', shortLabel: 'Name', dir: 'A→Z' },
  { value: 'label_desc', label: 'Name Z → A', shortLabel: 'Name', dir: 'Z→A' },
  { value: 'rounds_desc', label: 'Most rounds fired', shortLabel: 'Rounds', dir: '↓' },
  { value: 'rounds_asc', label: 'Fewest rounds fired', shortLabel: 'Rounds', dir: '↑' },
];

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'archived', label: 'Archived' },
  { value: 'all', label: 'All statuses' },
];

const currentSortOption = computed(
  () => sortOptions.find((o) => o.value === sortBy.value) ?? sortOptions[0]
);

const countLabel = computed(() => {
  if (isLoading.value) return undefined;
  const count = allFirearms.value.filter(
    (firearm) => statusFilter.value === 'all' || (firearm.status ?? 'active') === statusFilter.value
  ).length;
  const label = statusFilter.value === 'all' ? 'TOTAL' : statusFilter.value.toUpperCase();
  return `${count} ${label}`;
});

const availableCalibers = computed(() => {
  const seen = new Set();
  const result = [];
  for (const firearm of allFirearms.value) {
    for (const cal of firearm.calibers ?? []) {
      if (!seen.has(cal.id)) {
        seen.add(cal.id);
        result.push(cal);
      }
    }
  }
  return result.sort((a, b) => a.label.localeCompare(b.label));
});

const availableLocations = computed(() => {
  const seen = new Set();
  const result = [];
  for (const firearm of allFirearms.value) {
    if (firearm.location && !seen.has(firearm.location.id)) {
      seen.add(firearm.location.id);
      result.push(firearm.location);
    }
  }
  return result.sort((a, b) => a.label.localeCompare(b.label));
});

const filteredFirearms = computed(() => {
  let list = allFirearms.value;

  if (statusFilter.value !== 'all') {
    list = list.filter((firearm) => (firearm.status ?? 'active') === statusFilter.value);
  }

  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((firearm) =>
      [
        firearm.label,
        firearm.manufacturer,
        firearm.model,
        firearm.customizer,
        firearm.custom_package,
      ].some((value) => value?.toLowerCase().includes(q))
    );
  }

  if (caliberFilter.value !== null) {
    list = list.filter((f) => f.calibers?.some((c) => c.id === caliberFilter.value));
  }

  if (locationFilter.value !== null) {
    list = list.filter((f) => f.location?.id === locationFilter.value);
  }

  const sorted = [...list];
  if (sortBy.value === 'label_asc') sorted.sort((a, b) => a.label.localeCompare(b.label));
  if (sortBy.value === 'label_desc') sorted.sort((a, b) => b.label.localeCompare(a.label));
  if (sortBy.value === 'rounds_desc')
    sorted.sort((a, b) => (b.rounds_fired ?? 0) - (a.rounds_fired ?? 0));
  if (sortBy.value === 'rounds_asc')
    sorted.sort((a, b) => (a.rounds_fired ?? 0) - (b.rounds_fired ?? 0));

  return sorted;
});

function closeDropdowns() {
  openDropdown.value = null;
}

onMounted(async () => {
  document.addEventListener('click', closeDropdowns);
  isLoading.value = true;
  error.value = null;
  try {
    const { data } = await firearmsStore.fetchAll({ 'filter[status]': 'all' });
    allFirearms.value = data;
  } catch (exception) {
    error.value = exception;
  } finally {
    isLoading.value = false;
  }
});

onBeforeUnmount(() => {
  document.removeEventListener('click', closeDropdowns);
});
</script>
