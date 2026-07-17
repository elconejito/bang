<template>
  <div class="mx-auto max-w-[1080px] px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="[{ label: 'Account' }, { label: 'Manage Lists' }]" class="mb-4" />

    <div class="mb-6">
      <h1 class="font-display text-[30px] font-bold leading-none tracking-[-0.02em] text-ink-900">
        Manage Lists
      </h1>
      <p class="mt-2 max-w-[640px] text-[15px] text-ink-500">
        The shared lists that power the dropdowns across Bang. Set them once — editing a value
        updates it everywhere it's already used.
      </p>
    </div>

    <LoadingState
      v-if="loading"
      class="rounded border border-line bg-surface"
      message="Loading lists…"
    />

    <div v-else class="grid grid-cols-[232px_1fr] items-start gap-5">
      <!-- Left rail -->
      <div class="overflow-hidden rounded border border-line bg-surface">
        <template v-for="group in groups" :key="group.key">
          <div
            class="px-4 pb-1.5 pt-3.5 font-mono text-[10px] uppercase tracking-[0.09em] text-muted"
            :class="group.key !== groups[0].key ? 'border-t border-line' : ''"
          >
            {{ group.label }}
          </div>
          <button
            v-for="type in group.types"
            :key="type"
            class="flex w-full cursor-pointer items-center gap-2.5 border-l-[3px] px-4 py-[11px] text-left transition-colors"
            :class="
              activeType === type
                ? 'border-brass bg-[#faf6ea]'
                : 'border-transparent bg-white hover:bg-[#fafbfb]'
            "
            @click="selectType(type)"
          >
            <component
              :is="meta[type].icon"
              class="h-[17px] w-[17px] shrink-0"
              :class="activeType === type ? 'text-brass-800' : 'text-muted'"
            />
            <span
              class="flex-1 text-[14px]"
              :class="
                activeType === type ? 'font-semibold text-ink-900' : 'font-medium text-ink-700'
              "
              >{{ meta[type].title }}</span
            >
            <span class="font-mono text-[12px] text-muted">{{ listFor(type).length }}</span>
          </button>
        </template>
      </div>

      <ErrorCard v-if="error" :error="error" />

      <!-- Active list card -->
      <div v-else class="rounded border border-line bg-surface">
        <!-- List header -->
        <div class="border-b border-[#eef0f1] px-[18px] py-4">
          <div class="flex min-w-0 items-center gap-2.5">
            <h2 class="min-w-0 font-display text-[21px] font-bold text-ink-900">
              {{ active.title }}
            </h2>
            <span
              class="shrink-0 rounded border border-line px-2 py-0.5 font-mono text-[11px] text-muted"
              >{{ activeItems.length }}</span
            >
          </div>
          <p class="mt-1 text-[13px] text-muted">{{ active.sub }}</p>

          <div class="index-toolbar mt-3 flex flex-wrap items-center gap-2.5">
            <div class="shrink-0">
              <!-- View toggle -->
              <div class="flex h-10 overflow-hidden rounded border border-[#c2c6ca]">
                <button
                  class="inline-flex h-full items-center gap-1.5 px-3 text-[14px] font-medium transition-colors"
                  :class="
                    viewMode === 'table'
                      ? 'bg-ink-900 text-white'
                      : 'bg-white text-ink-500 hover:bg-[#f5f6f7]'
                  "
                  @click="viewMode = 'table'"
                >
                  <TableIcon class="h-[15px] w-[15px]" /> Table
                </button>
                <button
                  class="inline-flex h-full items-center gap-1.5 border-l border-[#c2c6ca] px-3 text-[14px] font-medium transition-colors"
                  :class="
                    viewMode === 'cards'
                      ? 'bg-ink-900 text-white'
                      : 'bg-white text-ink-500 hover:bg-[#f5f6f7]'
                  "
                  @click="viewMode = 'cards'"
                >
                  <LayoutGrid class="h-[15px] w-[15px]" /> Cards
                </button>
              </div>
            </div>

            <div class="index-toolbar-search min-w-[200px] gap-1.5 px-2.5">
              <Search class="h-[15px] w-[15px] shrink-0 text-muted" />
              <input
                v-model="search"
                type="text"
                :placeholder="`Search ${active.noun}…`"
                class="text-[14px] placeholder:text-muted"
              />
            </div>
            <button
              class="ml-auto inline-flex shrink-0 items-center gap-1.5 rounded border border-[#b08a2e] bg-brass px-3 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
              @click="openAdd"
            >
              <Plus class="h-4 w-4" /> {{ active.addLabel }}
            </button>
          </div>
        </div>

        <!-- Empty search -->
        <div
          v-if="filteredItems.length === 0"
          class="px-[18px] py-8 text-center text-[14px] text-muted"
        >
          <template v-if="search">No {{ active.noun }} match “{{ search }}”.</template>
          <template v-else>No {{ active.noun }} yet.</template>
        </div>

        <!-- Table view -->
        <template v-else-if="viewMode === 'table'">
          <div
            class="flex items-center gap-3 border-b border-[#f1f2f3] bg-[#fafbfb] px-[18px] py-[9px] font-mono text-[10px] uppercase tracking-[0.07em] text-muted"
          >
            <span class="flex-1">{{ active.colName }}</span>
            <span class="w-[210px]">Used by</span>
            <span class="w-16 text-right">Edit</span>
          </div>

          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="flex items-center gap-3 border-b border-[#f1f2f3] px-[18px] py-[13px] transition-colors hover:bg-[#fafbfb]"
          >
            <div class="flex flex-1 flex-wrap items-center gap-2">
              <component
                :is="active.linkable ? 'router-link' : 'span'"
                v-bind="active.linkable ? { to: routeFor(item) } : {}"
                :class="
                  active.linkable
                    ? 'text-[14px] font-semibold text-brass-800 hover:underline'
                    : 'rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[11px] py-[3px] text-[14px] font-semibold text-ink-900'
                "
                >{{ item.label }}</component
              >
              <span
                v-if="activeType === 'caliber' && item.caliber && item.caliber !== item.label"
                class="font-mono text-[14px] text-ink-500"
                >{{ item.caliber }}</span
              >
              <span
                v-if="usageOf(activeType, item) === 0"
                class="rounded border border-dashed border-[#c8ccd0] px-1.5 py-px font-mono text-[10px] uppercase tracking-[0.05em] text-muted"
                >Unused</span
              >
            </div>

            <div class="w-[210px] text-[13px] text-ink-500">
              {{ usageSummary(activeType, item) }}
            </div>

            <div class="flex w-16 items-center justify-end gap-1">
              <button
                class="rounded p-1.5 text-[#5b6066] transition-colors hover:bg-[#eceef0]"
                title="Edit"
                @click="openEdit(item)"
              >
                <Pencil class="h-[15px] w-[15px]" />
              </button>
              <button
                v-if="usageOf(activeType, item) === 0"
                class="rounded p-1.5 text-caution transition-colors hover:bg-caution-bg"
                title="Delete"
                @click="destroy(item)"
              >
                <Trash2 class="h-[15px] w-[15px]" />
              </button>
              <span
                v-else
                class="cursor-not-allowed p-1.5 text-[#c8ccd0]"
                title="In use — reassign before deleting"
              >
                <Trash2 class="h-[15px] w-[15px]" />
              </span>
            </div>
          </div>

          <button
            class="flex w-full items-center gap-1.5 px-[18px] py-[13px] text-[14px] font-semibold text-brass-800 transition-colors hover:bg-[#fafbfb]"
            @click="openAdd"
          >
            <Plus class="h-4 w-4" /> {{ active.addLabel }}
          </button>
        </template>

        <!-- Cards view -->
        <div v-else class="grid grid-cols-[repeat(auto-fill,minmax(216px,1fr))] gap-3 p-[18px]">
          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="flex flex-col gap-2 rounded border border-line p-[13px] transition-shadow hover:border-[#c2c6ca] hover:shadow-[0_2px_8px_rgba(20,22,26,0.06)]"
          >
            <div class="flex items-start justify-between gap-2">
              <component
                :is="active.linkable ? 'router-link' : 'span'"
                v-bind="active.linkable ? { to: routeFor(item) } : {}"
                class="text-[15px] font-semibold"
                :class="active.linkable ? 'text-brass-800 hover:underline' : 'text-ink-900'"
                >{{ item.label }}</component
              >
              <div class="flex shrink-0 items-center gap-0.5">
                <button
                  class="rounded p-1.5 text-[#5b6066] transition-colors hover:bg-[#eceef0]"
                  title="Edit"
                  @click="openEdit(item)"
                >
                  <Pencil class="h-[15px] w-[15px]" />
                </button>
                <button
                  v-if="usageOf(activeType, item) === 0"
                  class="rounded p-1.5 text-caution transition-colors hover:bg-caution-bg"
                  title="Delete"
                  @click="destroy(item)"
                >
                  <Trash2 class="h-[15px] w-[15px]" />
                </button>
                <span
                  v-else
                  class="cursor-not-allowed p-1.5 text-[#c8ccd0]"
                  title="In use — reassign before deleting"
                >
                  <Trash2 class="h-[15px] w-[15px]" />
                </span>
              </div>
            </div>
            <span
              v-if="activeType === 'caliber' && item.caliber && item.caliber !== item.label"
              class="-mt-1 font-mono text-[13px] text-ink-500"
              >{{ item.caliber }}</span
            >
            <div
              class="mt-auto flex items-center gap-2 border-t border-[#f1f2f3] pt-1.5 text-[13px] text-ink-500"
            >
              <span>{{ usageSummary(activeType, item) }}</span>
              <span
                v-if="usageOf(activeType, item) === 0"
                class="ml-auto rounded border border-dashed border-[#c8ccd0] px-1.5 py-px font-mono text-[9px] uppercase tracking-[0.05em] text-muted"
                >Unused</span
              >
            </div>
          </div>

          <button
            class="flex min-h-[88px] items-center justify-center gap-2 rounded border-[1.5px] border-dashed border-[#c8ccd0] text-[14px] font-semibold text-brass-800 transition-colors hover:bg-[#fafbfb]"
            @click="openAdd"
          >
            <Plus class="h-4 w-4" /> {{ active.addLabel }}
          </button>
        </div>
      </div>
    </div>

    <ReferenceItemModal
      v-if="modal"
      :type="activeType"
      :mode="modal.mode"
      :item="modal.item"
      @close="modal = null"
      @saved="onSaved"
      @deleted="onDeleted"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Search, Plus, Pencil, Trash2, Table as TableIcon, LayoutGrid } from 'lucide-vue-next';
import { useCalibersStore } from '@/stores/calibers';
import { usePurposesStore } from '@/stores/purposes';
import { useLocationsStore } from '@/stores/locations';
import { useGunStoresStore } from '@/stores/gunStores';
import { useRangesStore } from '@/stores/ranges';
import {
  REFERENCE_TYPES as meta,
  REFERENCE_GROUPS as groups,
  usageOf,
  usageSummary,
} from '@/components/reference/referenceMeta';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';

const props = defineProps({
  list: { type: String, default: null },
});

const calibersStore = useCalibersStore();
const purposesStore = usePurposesStore();
const locationsStore = useLocationsStore();
const gunStoresStore = useGunStoresStore();
const rangesStore = useRangesStore();
const loading = ref(true);

const VALID_TYPES = Object.keys(meta);
const activeType = ref(props.list && VALID_TYPES.includes(props.list) ? props.list : 'caliber');
const viewMode = ref('table');
const search = ref('');
const modal = ref(null);
const error = ref(null);

const calibers = ref([]);
const purposes = ref([]);
const locations = ref([]);
const stores = ref([]);
const ranges = ref([]);

const lists = {
  caliber: calibers,
  purpose: purposes,
  location: locations,
  store: stores,
  range: ranges,
};
const listStores = {
  caliber: calibersStore,
  purpose: purposesStore,
  location: locationsStore,
  store: gunStoresStore,
  range: rangesStore,
};

function listFor(type) {
  return lists[type].value;
}

const active = computed(() => meta[activeType.value]);
const activeItems = computed(() => listFor(activeType.value));

const filteredItems = computed(() => {
  const term = search.value.trim().toLowerCase();
  if (!term) {
    return activeItems.value;
  }
  return activeItems.value.filter(
    (item) => item.label?.toLowerCase().includes(term) || item.caliber?.toLowerCase().includes(term)
  );
});

function routeFor(item) {
  const { name, param } = active.value.showRoute;
  return { name, params: { [param]: item.id } };
}

onMounted(() => fetchData());

async function fetchData() {
  loading.value = true;
  error.value = null;
  try {
    const [caliberData, purposeData, locationData, storeData, rangeData] = await Promise.all([
      calibersStore.fetchAll(),
      purposesStore.fetchAll(),
      locationsStore.fetchAll(),
      gunStoresStore.fetchAll(),
      rangesStore.fetchAll(),
    ]);
    calibers.value = caliberData.data ?? [];
    purposes.value = purposeData.data ?? [];
    locations.value = locationData.data ?? [];
    stores.value = storeData.data ?? [];
    ranges.value = rangeData.data ?? [];
  } catch (exception) {
    error.value = exception;
  } finally {
    loading.value = false;
  }
}

async function refreshActive() {
  const type = activeType.value;
  const { data } = await listStores[type].fetchAll();
  lists[type].value = data ?? [];
}

function selectType(type) {
  activeType.value = type;
  search.value = '';
}

function openAdd() {
  search.value = '';
  modal.value = { mode: 'add', item: null };
}

function openEdit(item) {
  modal.value = { mode: 'edit', item };
}

async function onSaved() {
  modal.value = null;
  await refreshActive();
}

async function onDeleted() {
  modal.value = null;
  await refreshActive();
}

async function destroy(item) {
  if (usageOf(activeType.value, item) > 0) {
    return;
  }
  try {
    const store = listStores[activeType.value];
    await (store.remove ? store.remove(item.id) : store.destroy(item.id));
    await refreshActive();
  } catch (error) {
    console.error('ManageLists: failed to delete item', error);
  }
}
</script>
