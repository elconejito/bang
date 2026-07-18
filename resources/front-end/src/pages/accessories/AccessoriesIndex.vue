<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { ChevronDown, LayoutGrid, Table2 } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import SuppressorCard from '@/components/accessories/SuppressorCard.vue';
import OpticCard from '@/components/accessories/OpticCard.vue';
import LightCard from '@/components/accessories/LightCard.vue';
import MiscCard from '@/components/accessories/MiscCard.vue';
import MagazineGroupCard from '@/components/magazines/MagazineGroupCard.vue';
import AccessoriesTable from '@/components/accessories/AccessoriesTable.vue';
import { useAccessoriesStore } from '@/stores/accessories';
import { usePersistentViewMode } from '@/composables/usePersistentViewMode';

const props = defineProps({
  category: {
    type: String,
    default: null,
    validator: (value) => ['suppressors', 'optics', 'lights', 'misc'].includes(value),
  },
});

const accessoriesStore = useAccessoriesStore();
const viewMode = usePersistentViewMode('accessories', 'grid');

const loading = ref(true);
const error = ref(null);
const suppressors = ref([]);
const optics = ref([]);
const lights = ref([]);
const misc = ref([]);
const magazines = ref([]);

const search = ref('');
const filterMounted = ref('');
const filterCaliberId = ref(null);
const lifecycleStatus = ref('active');
const openDropdown = ref(null);
const addMenuOpen = ref(false);

const addOptions = [
  { label: 'Suppressor', to: { name: 'SuppressorCreate' } },
  { label: 'Optic', to: { name: 'OpticCreate' } },
  { label: 'Light', to: { name: 'LightCreate' } },
  { label: 'Magazine', to: { name: 'MagazinesCreate' } },
  { label: 'Misc', to: { name: 'MiscCreate' } },
];

const categoryDefinitions = {
  suppressors: {
    label: 'Suppressors',
    route: 'AccessoriesSuppressors',
    addRoute: 'SuppressorCreate',
    addLabel: 'Add Suppressor',
  },
  magazines: { label: 'Magazines', route: 'MagazinesIndex' },
  optics: {
    label: 'Optics',
    route: 'AccessoriesOptics',
    addRoute: 'OpticCreate',
    addLabel: 'Add Optic',
  },
  lights: {
    label: 'Lights',
    route: 'AccessoriesLights',
    addRoute: 'LightCreate',
    addLabel: 'Add Light',
  },
  misc: {
    label: 'Misc',
    route: 'AccessoriesMisc',
    addRoute: 'MiscCreate',
    addLabel: 'Add Misc Item',
  },
};

function handleOutsideClick(e) {
  if (!e.target.closest('[data-add-menu]')) addMenuOpen.value = false;
  openDropdown.value = null;
}

async function loadAccessories() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await accessoriesStore.fetchAll({
      'filter[lifecycle_status]': lifecycleStatus.value,
    });
    suppressors.value = data.suppressors;
    optics.value = data.optics;
    lights.value = data.lights;
    misc.value = data.misc;
    magazines.value = data.magazines;
  } catch (exception) {
    error.value = exception;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleOutsideClick);
  loadAccessories();
});

onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick));
watch(lifecycleStatus, loadAccessories);

const activeCategory = computed(() =>
  props.category ? categoryDefinitions[props.category] : null
);
const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  ...(activeCategory.value
    ? [
        { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
        { label: activeCategory.value.label },
      ]
    : [{ label: 'Accessories' }]),
]);

// All calibers from accessories that have a caliber
const availableCalibers = computed(() => {
  const seen = new Map();
  const allItems = props.category
    ? {
        suppressors: suppressors.value,
        optics: optics.value,
        lights: lights.value,
        misc: misc.value,
      }[props.category]
    : [...suppressors.value, ...optics.value, ...lights.value, ...misc.value];
  allItems.forEach((item) => {
    const caliber = item.caliber ?? item.calibers?.[0];
    if (caliber && !seen.has(caliber.id)) seen.set(caliber.id, caliber);
  });
  if (!props.category) {
    magazines.value.forEach((magazine) => {
      const caliber = magazine.calibers?.[0];
      if (caliber && !seen.has(caliber.id)) seen.set(caliber.id, caliber);
    });
  }
  return [...seen.values()].sort((a, b) => a.label.localeCompare(b.label));
});

const activeCaliberLabel = computed(
  () => availableCalibers.value.find((c) => c.id === filterCaliberId.value)?.label ?? null
);

function matchesSearch(item) {
  if (!search.value) return true;
  const q = search.value.toLowerCase();
  return (
    item.manufacturer?.toLowerCase().includes(q) ||
    item.label?.toLowerCase().includes(q) ||
    item.serial?.toLowerCase().includes(q)
  );
}

function matchesMounted(item) {
  if (!filterMounted.value) return true;
  if (filterMounted.value === 'mounted') return !!item.firearm_id;
  return !item.firearm_id;
}

function matchesCaliber(item) {
  if (!filterCaliberId.value) return true;
  const caliber = item.caliber ?? item.calibers?.[0];
  return caliber?.id === filterCaliberId.value;
}

function filtered(items) {
  return items.filter((i) => matchesSearch(i) && matchesMounted(i) && matchesCaliber(i));
}

const filteredSuppressors = computed(() => filtered(suppressors.value));
const filteredOptics = computed(() => filtered(optics.value));
const filteredLights = computed(() => filtered(lights.value));
const filteredMisc = computed(() => filtered(misc.value));

const filteredMagazineGroups = computed(() => {
  return magazines.value.filter((g) => {
    if (search.value) {
      const q = search.value.toLowerCase();
      if (
        !g.model_name?.toLowerCase().includes(q) &&
        !g.manufacturer?.toLowerCase().includes(q) &&
        !g.calibers?.some((caliber) => caliber.label.toLowerCase().includes(q))
      )
        return false;
    }
    if (
      filterCaliberId.value &&
      !g.calibers?.some((caliber) => caliber.id === filterCaliberId.value)
    )
      return false;
    return true;
  });
});

// Design order: Suppressors → Magazines → Optics → Lights → Misc
const showSuppressors = computed(
  () =>
    (!props.category || props.category === 'suppressors') && filteredSuppressors.value.length > 0
);
const showMagazines = computed(() => !props.category && filteredMagazineGroups.value.length > 0);
const showOptics = computed(
  () => (!props.category || props.category === 'optics') && filteredOptics.value.length > 0
);
const showLights = computed(
  () => (!props.category || props.category === 'lights') && filteredLights.value.length > 0
);
const showMisc = computed(
  () => (!props.category || props.category === 'misc') && filteredMisc.value.length > 0
);

const populatedCategories = computed(() => {
  const cats = [];
  if (suppressors.value.length) cats.push('suppressors');
  if (magazines.value.length) cats.push('magazines');
  if (optics.value.length) cats.push('optics');
  if (lights.value.length) cats.push('lights');
  if (misc.value.length) cats.push('misc');
  return cats;
});

const totalCount = computed(
  () =>
    suppressors.value.length +
    optics.value.length +
    lights.value.length +
    misc.value.length +
    magazines.value.length
);

const categoryCount = computed(() => populatedCategories.value.length);
const scopedItemCount = computed(() => {
  if (!props.category) return totalCount.value;

  return {
    suppressors: suppressors.value.length,
    optics: optics.value.length,
    lights: lights.value.length,
    misc: misc.value.length,
  }[props.category];
});
const pageTitle = computed(() => activeCategory.value?.label ?? 'Accessories');
const pageCount = computed(() =>
  props.category
    ? `${scopedItemCount.value} item${scopedItemCount.value === 1 ? '' : 's'}`
    : `${totalCount.value} items in ${categoryCount.value} categories`
);
const emptyStateTitle = computed(() =>
  activeCategory.value ? `No ${activeCategory.value.label.toLowerCase()} yet` : 'No accessories yet'
);
const emptyStateMessage = computed(() =>
  activeCategory.value
    ? `Add ${activeCategory.value.label.toLowerCase()} to track mount status and history.`
    : 'Add suppressors, optics, lights, magazines, or misc gear to track mount status and history.'
);
const emptyStateAction = computed(() => ({
  label: activeCategory.value?.addLabel ?? 'Add Suppressor',
  to: { name: activeCategory.value?.addRoute ?? 'SuppressorCreate' },
}));

const hasAnyAccessories = computed(() => scopedItemCount.value > 0);
const hasVisibleAccessories = computed(
  () =>
    showSuppressors.value ||
    showMagazines.value ||
    showOptics.value ||
    showLights.value ||
    showMisc.value
);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <div class="mb-5">
      <PageHeader :title="pageTitle" :count="loading ? undefined : pageCount">
        <template #actions>
          <div class="flex overflow-hidden rounded border border-[#c2c6ca]">
            <button
              type="button"
              class="inline-flex items-center gap-1.5 px-3 py-[7px] text-[14px] font-medium transition-colors"
              :class="
                viewMode === 'grid'
                  ? 'bg-ink-900 text-white'
                  : 'bg-surface text-muted hover:bg-ink-50'
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
                  : 'bg-surface text-muted hover:bg-ink-50'
              "
              :aria-pressed="viewMode === 'table'"
              @click="viewMode = 'table'"
            >
              <Table2 class="h-[15px] w-[15px]" /> Table
            </button>
          </div>
          <router-link
            v-if="activeCategory"
            :to="{ name: activeCategory.addRoute }"
            class="inline-flex items-center gap-1.5 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-4 py-2 rounded border border-[#b08a2e] hover:bg-[#b8902f] transition-colors"
          >
            <svg
              class="w-4 h-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            {{ activeCategory.addLabel }}
          </router-link>
          <div v-else class="relative" data-add-menu>
            <button
              class="inline-flex items-center gap-1.5 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-4 py-2 rounded border border-[#b08a2e] hover:bg-[#b8902f] transition-colors"
              @click.stop="addMenuOpen = !addMenuOpen"
            >
              <svg
                class="w-4 h-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M5 12h14" />
                <path d="M12 5v14" />
              </svg>
              Add Accessory
              <svg
                class="w-3.5 h-3.5 ml-0.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>
            <div
              v-if="addMenuOpen"
              class="absolute right-0 top-full mt-1.5 w-44 bg-white border border-[#d6d9dc] rounded shadow-[0_4px_16px_rgba(20,22,26,0.14)] z-20 overflow-hidden"
            >
              <router-link
                v-for="opt in addOptions"
                :key="opt.label"
                :to="opt.to"
                class="flex items-center gap-2 px-4 py-2.5 text-[14px] text-[#1a1c1f] hover:bg-[#f5f6f7] transition-colors"
                @click="addMenuOpen = false"
              >
                {{ opt.label }}
              </router-link>
            </div>
          </div>
        </template>
      </PageHeader>
    </div>

    <!-- Toolbar -->
    <div class="index-toolbar flex items-center gap-2.5 mb-7 flex-wrap">
      <div class="index-toolbar-search gap-2">
        <svg
          class="w-[17px] h-[17px] text-muted flex-none"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.3-4.3" />
        </svg>
        <input
          v-model="search"
          type="text"
          placeholder="Search by make, model, serial…"
          class="placeholder:text-muted"
        />
      </div>

      <!-- Caliber filter -->
      <div v-if="availableCalibers.length" class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-700 hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'caliber' ? null : 'caliber'"
        >
          {{ activeCaliberLabel ?? 'Caliber' }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'caliber'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[140px] rounded border border-line bg-white shadow-lg"
        >
          <button
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="!filterCaliberId ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="
              filterCaliberId = null;
              openDropdown = null;
            "
          >
            All calibers
          </button>
          <button
            v-for="c in availableCalibers"
            :key="c.id"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="filterCaliberId === c.id ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="
              filterCaliberId = c.id;
              openDropdown = null;
            "
          >
            {{ c.label }}
          </button>
        </div>
      </div>

      <!-- Mounted filter -->
      <select
        v-model="filterMounted"
        class="border border-[#c2c6ca] rounded bg-white px-3 py-2 text-[14px] text-[#3a3e44] focus:outline-none"
      >
        <option value="">All</option>
        <option value="mounted">Mounted</option>
        <option value="unmounted">Unmounted</option>
      </select>

      <select
        v-model="lifecycleStatus"
        aria-label="Filter by lifecycle status"
        class="border border-[#c2c6ca] rounded bg-white px-3 py-2 text-[14px] text-[#3a3e44] focus:outline-none"
      >
        <option value="active">Active</option>
        <option value="archived">Archived</option>
        <option value="all">All statuses</option>
      </select>
    </div>

    <LoadingState v-if="loading" message="Loading accessories…" />

    <ErrorCard v-else-if="error" :error="error" />

    <template v-else>
      <EmptyState
        v-if="!hasAnyAccessories"
        :title="emptyStateTitle"
        :message="emptyStateMessage"
        :action-label="emptyStateAction.label"
        :action-to="emptyStateAction.to"
      />

      <EmptyState
        v-else-if="!hasVisibleAccessories"
        title="No accessories match your filters"
        message="Try adjusting your search, caliber, or mount status filters."
      />

      <!-- Suppressors -->
      <template v-if="showSuppressors">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Suppressors</span>
          <span class="font-mono text-[12px] text-muted">
            {{ suppressors.length }} ITEM{{ suppressors.length !== 1 ? 'S' : '' }}
            <template v-if="suppressors.some((s) => s.is_nfa)"> · NFA</template>
          </span>
          <router-link
            v-if="category !== 'suppressors'"
            :to="{ name: categoryDefinitions.suppressors.route }"
            class="ml-auto text-[13px] font-semibold text-brass-800 hover:underline"
          >
            View all
          </router-link>
        </div>
        <div v-if="viewMode === 'grid'" class="grid grid-cols-3 gap-4 mb-8">
          <SuppressorCard v-for="s in filteredSuppressors" :key="s.id" :suppressor="s" />
          <router-link
            :to="{ name: 'SuppressorCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg
              class="w-[22px] h-[22px] text-brass"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            <span class="text-[14px]">Add suppressor</span>
          </router-link>
        </div>
        <AccessoriesTable
          v-else
          type="suppressors"
          :items="filteredSuppressors"
          add-route="SuppressorCreate"
          add-label="Add suppressor"
        />
      </template>

      <!-- Magazines (design order: after Suppressors) -->
      <template v-if="showMagazines">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Magazines</span>
          <span class="font-mono text-[12px] text-muted">
            {{ magazines.length }} TYPE{{ magazines.length !== 1 ? 'S' : '' }} ·
            {{ magazines.reduce((total, group) => total + group.summary.total, 0) }} MAGS
          </span>
          <router-link
            :to="{ name: 'MagazinesIndex' }"
            class="ml-auto text-[13px] font-semibold text-brass-800 hover:underline"
            >View all</router-link
          >
        </div>
        <!-- Page-level legend -->
        <div class="flex gap-[18px] mb-5 text-[14px] text-[#5b6066] flex-wrap">
          <span class="inline-flex items-center gap-[7px] whitespace-nowrap">
            <span class="w-[11px] h-[11px] rounded-full bg-[#2f7d57]" />In a gun
          </span>
          <span class="inline-flex items-center gap-[7px] whitespace-nowrap">
            <span class="w-[11px] h-[11px] rounded-full bg-[#c2a14d]" />Loaded spare
          </span>
          <span class="inline-flex items-center gap-[7px] whitespace-nowrap">
            <span class="w-[11px] h-[11px] rounded-full border-[1.5px] border-[#b6bcc1]" />Empty
          </span>
        </div>
        <div v-if="viewMode === 'grid'" class="grid grid-cols-3 gap-4 mb-8">
          <MagazineGroupCard v-for="g in filteredMagazineGroups" :key="g.key" :group="g" />
        </div>
        <AccessoriesTable v-else type="magazines" :items="filteredMagazineGroups" />
      </template>

      <!-- Optics -->
      <template v-if="showOptics">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Optics</span>
          <span class="font-mono text-[12px] text-muted"
            >{{ optics.length }} ITEM{{ optics.length !== 1 ? 'S' : '' }}</span
          >
          <router-link
            v-if="category !== 'optics'"
            :to="{ name: categoryDefinitions.optics.route }"
            class="ml-auto text-[13px] font-semibold text-brass-800 hover:underline"
          >
            View all
          </router-link>
        </div>
        <div v-if="viewMode === 'grid'" class="grid grid-cols-3 gap-4 mb-8">
          <OpticCard v-for="o in filteredOptics" :key="o.id" :optic="o" />
          <router-link
            :to="{ name: 'OpticCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg
              class="w-[22px] h-[22px] text-brass"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            <span class="text-[14px]">Add optic</span>
          </router-link>
        </div>
        <AccessoriesTable
          v-else
          type="optics"
          :items="filteredOptics"
          add-route="OpticCreate"
          add-label="Add optic"
        />
      </template>

      <!-- Lights -->
      <template v-if="showLights">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Lights</span>
          <span class="font-mono text-[12px] text-muted"
            >{{ lights.length }} ITEM{{ lights.length !== 1 ? 'S' : '' }}</span
          >
          <router-link
            v-if="category !== 'lights'"
            :to="{ name: categoryDefinitions.lights.route }"
            class="ml-auto text-[13px] font-semibold text-brass-800 hover:underline"
          >
            View all
          </router-link>
        </div>
        <div v-if="viewMode === 'grid'" class="grid grid-cols-3 gap-4 mb-8">
          <LightCard v-for="l in filteredLights" :key="l.id" :light="l" />
          <router-link
            :to="{ name: 'LightCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg
              class="w-[22px] h-[22px] text-brass"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            <span class="text-[14px]">Add light</span>
          </router-link>
        </div>
        <AccessoriesTable
          v-else
          type="lights"
          :items="filteredLights"
          add-route="LightCreate"
          add-label="Add light"
        />
      </template>

      <!-- Misc -->
      <template v-if="showMisc">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Misc</span>
          <span class="font-mono text-[12px] text-muted"
            >{{ misc.length }} ITEM{{ misc.length !== 1 ? 'S' : '' }}</span
          >
          <router-link
            v-if="category !== 'misc'"
            :to="{ name: categoryDefinitions.misc.route }"
            class="ml-auto text-[13px] font-semibold text-brass-800 hover:underline"
          >
            View all
          </router-link>
        </div>
        <div v-if="viewMode === 'grid'" class="grid grid-cols-3 gap-4 mb-8">
          <MiscCard v-for="m in filteredMisc" :key="m.id" :misc="m" />
          <router-link
            :to="{ name: 'MiscCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg
              class="w-[22px] h-[22px] text-brass"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            <span class="text-[14px]">Add misc item</span>
          </router-link>
        </div>
        <AccessoriesTable
          v-else
          type="misc"
          :items="filteredMisc"
          add-route="MiscCreate"
          add-label="Add misc item"
        />
      </template>
    </template>
  </div>
</template>
