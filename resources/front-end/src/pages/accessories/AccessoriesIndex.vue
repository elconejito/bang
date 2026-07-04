<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { ChevronDown } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import SuppressorCard from '@/components/accessories/SuppressorCard.vue';
import OpticCard from '@/components/accessories/OpticCard.vue';
import LightCard from '@/components/accessories/LightCard.vue';
import MiscCard from '@/components/accessories/MiscCard.vue';
import MagGroupCard from '@/components/accessories/MagGroupCard.vue';
import { useAccessoriesStore } from '@/stores/accessories';

const router = useRouter();
const accessoriesStore = useAccessoriesStore();

const loading = ref(true);
const suppressors = ref([]);
const optics = ref([]);
const lights = ref([]);
const misc = ref([]);
const magazines = ref([]);

const search = ref('');
const filterMounted = ref('');
const filterCategory = ref('');
const filterCaliberId = ref(null);
const openDropdown = ref(null);
const addMenuOpen = ref(false);

const addOptions = [
  { label: 'Suppressor', to: { name: 'SuppressorCreate' } },
  { label: 'Optic', to: { name: 'OpticCreate' } },
  { label: 'Light', to: { name: 'LightCreate' } },
  { label: 'Magazine', to: { name: 'MagazinesCreate' } },
  { label: 'Misc', to: { name: 'MiscCreate' } },
];

const categoryOptions = [
  { label: 'Suppressors', value: 'suppressors' },
  { label: 'Magazines', value: 'magazines' },
  { label: 'Optics', value: 'optics' },
  { label: 'Lights', value: 'lights' },
  { label: 'Misc', value: 'misc' },
];

function handleOutsideClick(e) {
  if (!e.target.closest('[data-add-menu]')) addMenuOpen.value = false;
  openDropdown.value = null;
}

onMounted(async () => {
  document.addEventListener('click', handleOutsideClick);
  const { data } = await accessoriesStore.fetchAll();
  suppressors.value = data.suppressors;
  optics.value = data.optics;
  lights.value = data.lights;
  misc.value = data.misc;
  magazines.value = data.magazines;
  loading.value = false;
});

onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick));

const crumbs = [{ label: 'Home', to: '/' }, { label: 'Accessories' }];

// All calibers from accessories that have a caliber
const availableCalibers = computed(() => {
  const seen = new Map();
  const allItems = [...suppressors.value, ...optics.value, ...lights.value, ...misc.value];
  allItems.forEach((item) => {
    const caliber = item.caliber ?? item.calibers?.[0];
    if (caliber && !seen.has(caliber.id)) seen.set(caliber.id, caliber);
  });
  magazines.value.forEach((m) => {
    const caliber = m.calibers?.[0];
    if (caliber && !seen.has(caliber.id)) seen.set(caliber.id, caliber);
  });
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

const magazineGroups = computed(() => {
  const groups = {};
  magazines.value.forEach((mag) => {
    const key = `${mag.model_name || mag.label || 'Unknown'}|${mag.manufacturer}|${mag.capacity}`;
    if (!groups[key]) {
      groups[key] = {
        key,
        model_name: mag.model_name || mag.label || 'Magazine',
        manufacturer: mag.manufacturer,
        capacity: mag.capacity,
        caliber_label: mag.calibers?.[0]?.label ?? null,
        caliber_id: mag.calibers?.[0]?.id ?? null,
        magazines: [],
      };
    }
    groups[key].magazines.push(mag);
  });
  return Object.values(groups);
});

const filteredMagazineGroups = computed(() => {
  return magazineGroups.value.filter((g) => {
    if (search.value) {
      const q = search.value.toLowerCase();
      if (
        !g.model_name?.toLowerCase().includes(q) &&
        !g.manufacturer?.toLowerCase().includes(q) &&
        !g.caliber_label?.toLowerCase().includes(q)
      )
        return false;
    }
    if (filterCaliberId.value && g.caliber_id !== filterCaliberId.value) return false;
    return true;
  });
});

// Design order: Suppressors → Magazines → Optics → Lights → Misc
const showSuppressors = computed(
  () =>
    (!filterCategory.value || filterCategory.value === 'suppressors') &&
    filteredSuppressors.value.length > 0
);
const showMagazines = computed(
  () =>
    (!filterCategory.value || filterCategory.value === 'magazines') &&
    filteredMagazineGroups.value.length > 0
);
const showOptics = computed(
  () =>
    (!filterCategory.value || filterCategory.value === 'optics') && filteredOptics.value.length > 0
);
const showLights = computed(
  () =>
    (!filterCategory.value || filterCategory.value === 'lights') && filteredLights.value.length > 0
);
const showMisc = computed(
  () => (!filterCategory.value || filterCategory.value === 'misc') && filteredMisc.value.length > 0
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

const hasAnyAccessories = computed(() => totalCount.value > 0);
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
      <PageHeader
        title="Accessories"
        :count="loading ? undefined : `${totalCount} items in ${categoryCount} categories`"
      >
        <template #actions>
          <div class="relative" data-add-menu>
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
    <div class="flex items-center gap-2.5 mb-7 flex-wrap">
      <div
        class="flex-1 min-w-[220px] flex items-center gap-2 border border-[#c2c6ca] rounded bg-white px-3 py-2"
      >
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
          class="flex-1 text-[15px] bg-transparent outline-none placeholder:text-muted"
        />
      </div>

      <!-- Category filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-700 hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'category' ? null : 'category'"
        >
          {{
            filterCategory
              ? categoryOptions.find((c) => c.value === filterCategory)?.label
              : 'Category'
          }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'category'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[140px] rounded border border-line bg-white shadow-lg"
        >
          <button
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="!filterCategory ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="
              filterCategory = '';
              openDropdown = null;
            "
          >
            All
          </button>
          <button
            v-for="opt in categoryOptions"
            :key="opt.value"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="filterCategory === opt.value ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="
              filterCategory = opt.value;
              openDropdown = null;
            "
          >
            {{ opt.label }}
          </button>
        </div>
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
    </div>

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else>
      <EmptyState
        v-if="!hasAnyAccessories"
        title="No accessories yet"
        message="Add suppressors, optics, lights, magazines, or misc gear to track mount status and history."
        action-label="Add Suppressor"
        :action-to="{ name: 'SuppressorCreate' }"
      />

      <EmptyState
        v-else-if="!hasVisibleAccessories"
        title="No accessories match your filters"
        message="Try adjusting your search, category, caliber, or mount status filters."
      />

      <!-- Suppressors -->
      <template v-if="showSuppressors">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Suppressors</span>
          <span class="font-mono text-[12px] text-muted">
            {{ suppressors.length }} ITEM{{ suppressors.length !== 1 ? 'S' : '' }}
            <template v-if="suppressors.some((s) => s.is_nfa)"> · NFA</template>
          </span>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
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
      </template>

      <!-- Magazines (design order: after Suppressors) -->
      <template v-if="showMagazines">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Magazines</span>
          <span class="font-mono text-[12px] text-muted">
            {{ magazineGroups.length }} TYPE{{ magazineGroups.length !== 1 ? 'S' : '' }} ·
            {{ magazines.length }} MAG{{ magazines.length !== 1 ? 'S' : '' }}
          </span>
          <router-link
            :to="{ name: 'MagazinesIndex' }"
            class="ml-auto text-[13px] font-semibold text-brass-800 hover:underline"
            >Manage individually</router-link
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
        <div class="flex flex-col gap-[14px] mb-8">
          <MagGroupCard v-for="g in filteredMagazineGroups" :key="g.key" :group="g" />
        </div>
      </template>

      <!-- Optics -->
      <template v-if="showOptics">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Optics</span>
          <span class="font-mono text-[12px] text-muted"
            >{{ optics.length }} ITEM{{ optics.length !== 1 ? 'S' : '' }}</span
          >
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
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
      </template>

      <!-- Lights -->
      <template v-if="showLights">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Lights</span>
          <span class="font-mono text-[12px] text-muted"
            >{{ lights.length }} ITEM{{ lights.length !== 1 ? 'S' : '' }}</span
          >
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
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
      </template>

      <!-- Misc -->
      <template v-if="showMisc">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Misc</span>
          <span class="font-mono text-[12px] text-muted"
            >{{ misc.length }} ITEM{{ misc.length !== 1 ? 'S' : '' }}</span
          >
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
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
      </template>
    </template>
  </div>
</template>
