<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import PageHeader from '@/components/PageHeader.vue';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
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
const addMenuOpen = ref(false);

const addOptions = [
  { label: 'Suppressor', to: { name: 'SuppressorCreate' } },
  { label: 'Optic', to: { name: 'OpticCreate' } },
  { label: 'Light', to: { name: 'LightCreate' } },
  { label: 'Magazine', to: { name: 'MagazinesCreate' } },
  { label: 'Misc', to: { name: 'MiscCreate' } },
];

function closeAddMenu(e) {
  if (!e.target.closest('[data-add-menu]')) {
    addMenuOpen.value = false;
  }
}

onMounted(() => document.addEventListener('click', closeAddMenu));
onBeforeUnmount(() => document.removeEventListener('click', closeAddMenu));

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories' },
];

onMounted(async () => {
  const { data } = await accessoriesStore.fetchAll();
  suppressors.value = data.suppressors;
  optics.value = data.optics;
  lights.value = data.lights;
  misc.value = data.misc;
  magazines.value = data.magazines;
  loading.value = false;
});

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

function filtered(items) {
  return items.filter((i) => matchesSearch(i) && matchesMounted(i));
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
        magazines: [],
      };
    }
    groups[key].magazines.push(mag);
  });
  return Object.values(groups);
});

const filteredMagazineGroups = computed(() => {
  if (!search.value) return magazineGroups.value;
  const q = search.value.toLowerCase();
  return magazineGroups.value.filter(
    (g) =>
      g.model_name?.toLowerCase().includes(q) ||
      g.manufacturer?.toLowerCase().includes(q) ||
      g.caliber_label?.toLowerCase().includes(q),
  );
});

const totalCount = computed(
  () =>
    suppressors.value.length +
    optics.value.length +
    lights.value.length +
    misc.value.length +
    magazines.value.length,
);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <div class="mb-5">
      <PageHeader title="Accessories" :count="loading ? undefined : totalCount">
        <template #actions>
          <div class="relative" data-add-menu>
            <button
              class="inline-flex items-center gap-1.5 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-4 py-2 rounded border border-[#b08a2e] hover:bg-[#b8902f] transition-colors"
              @click.stop="addMenuOpen = !addMenuOpen"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
              Add Accessory
              <svg class="w-3.5 h-3.5 ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
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
      <div class="flex-1 min-w-[220px] flex items-center gap-2 border border-[#c2c6ca] rounded-sm bg-white px-3 py-2">
        <svg class="w-[17px] h-[17px] text-muted flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input v-model="search" type="text" placeholder="Search by make, model, serial…" class="flex-1 text-[15px] bg-transparent outline-none placeholder:text-muted" />
      </div>
      <select v-model="filterMounted" class="border border-[#c2c6ca] rounded-sm bg-white px-3 py-2 text-[14px] text-[#3a3e44] focus:outline-none">
        <option value="">All</option>
        <option value="mounted">Mounted</option>
        <option value="unmounted">Unmounted</option>
      </select>
    </div>

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else>
      <!-- Suppressors -->
      <template v-if="suppressors.length">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Suppressors</span>
          <span class="font-mono text-[12px] text-muted">
            {{ suppressors.length }} ITEM{{ suppressors.length !== 1 ? 'S' : '' }}
            <template v-if="suppressors.some(s => s.is_nfa)"> · NFA</template>
          </span>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
          <SuppressorCard v-for="s in filteredSuppressors" :key="s.id" :suppressor="s" />
          <router-link
            :to="{ name: 'SuppressorCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg class="w-[22px] h-[22px] text-brass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            <span class="text-[14px]">Add suppressor</span>
          </router-link>
        </div>
      </template>

      <!-- Optics -->
      <template v-if="optics.length">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Optics</span>
          <span class="font-mono text-[12px] text-muted">{{ optics.length }} ITEM{{ optics.length !== 1 ? 'S' : '' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
          <OpticCard v-for="o in filteredOptics" :key="o.id" :optic="o" />
          <router-link
            :to="{ name: 'OpticCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg class="w-[22px] h-[22px] text-brass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            <span class="text-[14px]">Add optic</span>
          </router-link>
        </div>
      </template>

      <!-- Lights -->
      <template v-if="lights.length">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Lights</span>
          <span class="font-mono text-[12px] text-muted">{{ lights.length }} ITEM{{ lights.length !== 1 ? 'S' : '' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
          <LightCard v-for="l in filteredLights" :key="l.id" :light="l" />
          <router-link
            :to="{ name: 'LightCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg class="w-[22px] h-[22px] text-brass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            <span class="text-[14px]">Add light</span>
          </router-link>
        </div>
      </template>

      <!-- Magazines -->
      <template v-if="magazines.length">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Magazines</span>
          <span class="font-mono text-[12px] text-muted">
            {{ magazineGroups.length }} TYPE{{ magazineGroups.length !== 1 ? 'S' : '' }} ·
            {{ magazines.length }} MAG{{ magazines.length !== 1 ? 'S' : '' }}
          </span>
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
          <MagGroupCard
            v-for="g in filteredMagazineGroups"
            :key="g.key"
            :group="g"
          />
        </div>
      </template>

      <!-- Misc -->
      <template v-if="misc.length">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[22px] tracking-[-0.01em]">Misc</span>
          <span class="font-mono text-[12px] text-muted">{{ misc.length }} ITEM{{ misc.length !== 1 ? 'S' : '' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-8">
          <MiscCard v-for="m in filteredMisc" :key="m.id" :misc="m" />
          <router-link
            :to="{ name: 'MiscCreate' }"
            class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[150px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors cursor-pointer"
          >
            <svg class="w-[22px] h-[22px] text-brass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            <span class="text-[14px]">Add misc item</span>
          </router-link>
        </div>
      </template>

      <!-- Empty state -->
      <div
        v-if="!suppressors.length && !optics.length && !lights.length && !magazines.length && !misc.length"
        class="flex flex-col items-center justify-center gap-3 py-24 text-muted"
      >
        <p class="text-[15px]">No accessories yet.</p>
        <router-link :to="{ name: 'SuppressorCreate' }" class="text-[14px] text-brass font-semibold hover:underline">Add suppressor</router-link>
        <span class="text-muted text-[13px]">or use the Add Accessory button above to choose a type</span>
      </div>
    </template>
  </div>
</template>
