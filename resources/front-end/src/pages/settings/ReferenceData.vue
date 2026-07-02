<template>
  <div class="mx-auto max-w-[1080px] px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="[{ label: 'Settings' }, { label: 'Reference Data' }]" class="mb-4" />

    <div class="mb-6">
      <h1 class="font-display text-[30px] font-bold leading-none tracking-[-0.02em] text-ink-900">
        Reference Data
      </h1>
      <p class="mt-2 max-w-[620px] text-[15px] text-ink-500">
        The shared lists that power the dropdowns across Bang. Set them once — editing a value
        updates it everywhere it's already used.
      </p>
    </div>

    <div class="grid grid-cols-[232px_1fr] items-start gap-5">
      <!-- Left rail -->
      <div class="rounded border border-line bg-surface">
        <div
          class="px-4 pb-1.5 pt-3.5 font-mono text-[10px] uppercase tracking-[0.09em] text-muted"
        >
          Lists you manage
        </div>
        <button
          v-for="list in lists"
          :key="list.type"
          class="flex w-full items-center gap-2.5 border-l-[3px] px-4 py-[11px] text-left transition-colors"
          :class="
            activeType === list.type
              ? 'border-brass bg-[#faf6ea]'
              : 'border-transparent bg-white hover:bg-[#fafbfb]'
          "
          @click="activeType = list.type"
        >
          <component
            :is="list.icon"
            class="h-[17px] w-[17px] shrink-0"
            :class="activeType === list.type ? 'text-brass-800' : 'text-muted'"
          />
          <span
            class="flex-1 text-[14px]"
            :class="
              activeType === list.type ? 'font-semibold text-ink-900' : 'font-medium text-ink-700'
            "
            >{{ list.label }}</span
          >
          <span class="font-mono text-[12px] text-muted">{{ list.items.length }}</span>
        </button>

        <div class="mx-4 my-1.5 border-t border-line" />

        <div class="px-4 pb-1.5 pt-2 font-mono text-[10px] uppercase tracking-[0.09em] text-faint">
          Managed elsewhere
        </div>
        <div
          v-for="other in managedElsewhere"
          :key="other.label"
          class="flex items-center gap-2.5 px-4 py-2 text-[13px] text-[#9aa0a6]"
        >
          <component :is="other.icon" class="h-[15px] w-[15px] shrink-0 text-faint" />
          <span>{{ other.label }}</span>
        </div>
      </div>

      <!-- Active list card -->
      <div class="rounded border border-line bg-surface">
        <!-- List header -->
        <div class="border-b border-[#eef0f1] px-[18px] py-4">
          <div class="flex flex-wrap items-center gap-x-2.5 gap-y-2">
            <h2 class="font-display text-[21px] font-bold text-ink-900">{{ active.label }}</h2>
            <span class="rounded border border-line px-2 py-0.5 font-mono text-[11px] text-muted">{{
              active.items.length
            }}</span>
            <div class="ml-auto flex items-center gap-2.5">
              <div
                class="flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-2.5 py-[7px]"
              >
                <Search class="h-[15px] w-[15px] text-muted" />
                <input
                  v-model="search"
                  type="text"
                  :placeholder="`Search ${active.label.toLowerCase()}…`"
                  class="w-[150px] bg-transparent text-[13px] placeholder:text-muted focus:outline-none"
                />
              </div>
              <button
                class="inline-flex items-center gap-1.5 rounded border border-[#b08a2e] bg-brass px-3 py-[7px] text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
                @click="openAdd"
              >
                <Plus class="h-4 w-4" /> {{ active.addLabel }}
              </button>
            </div>
          </div>
          <p class="mt-1 text-[13px] text-muted">{{ active.subhead }}</p>
        </div>

        <!-- Column header -->
        <div
          class="flex items-center gap-3 border-b border-[#f1f2f3] bg-[#fafbfb] px-[18px] py-[9px] font-mono text-[10px] uppercase tracking-[0.07em] text-muted"
        >
          <span class="flex-1">{{ active.nameColumn }}</span>
          <span class="w-[210px]">Used by</span>
          <span class="w-16 text-right">Edit</span>
        </div>

        <!-- Rows -->
        <div
          v-if="filteredItems.length === 0"
          class="px-[18px] py-8 text-center text-[14px] text-muted"
        >
          No {{ active.label.toLowerCase() }} match “{{ search }}”.
        </div>
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="flex items-center gap-3 border-b border-[#f1f2f3] px-[18px] py-[13px] transition-colors hover:bg-[#fafbfb]"
        >
          <!-- Name cell -->
          <div class="flex flex-1 flex-wrap items-center gap-2">
            <span
              class="rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[11px] py-[3px] text-[14px] font-semibold text-ink-900"
              >{{ item.label }}</span
            >
            <span
              v-if="isCaliber && item.caliber && item.caliber !== item.label"
              class="font-mono text-[14px] text-ink-500"
              >{{ item.caliber }}</span
            >
            <span
              v-if="usageOf(item) === 0"
              class="rounded border border-dashed border-[#c8ccd0] px-1.5 py-px font-mono text-[10px] uppercase tracking-[0.05em] text-muted"
              >Unused</span
            >
          </div>

          <!-- Used by cell -->
          <div class="w-[210px] text-[13px]">
            <template v-if="isCaliber">
              <span
                class="font-mono"
                :class="item.firearms_count ? 'text-ink-700' : 'text-faint'"
                >{{ item.firearms_count ?? 0 }}</span
              >
              <span class="text-muted"> firearms</span>
              <span class="text-[#d6d9dc]"> · </span>
              <span class="font-mono" :class="item.loads_count ? 'text-ink-700' : 'text-faint'">{{
                item.loads_count ?? 0
              }}</span>
              <span class="text-muted"> loads</span>
            </template>
            <template v-else>
              <span class="font-mono" :class="item.loads_count ? 'text-ink-700' : 'text-faint'">{{
                item.loads_count ?? 0
              }}</span>
              <span class="text-muted"> loads</span>
            </template>
          </div>

          <!-- Edit cell -->
          <div class="flex w-16 items-center justify-end gap-1">
            <button
              class="rounded p-1.5 text-[#5b6066] transition-colors hover:bg-[#eceef0]"
              title="Edit"
              @click="openEdit(item)"
            >
              <Pencil class="h-[15px] w-[15px]" />
            </button>
            <button
              v-if="usageOf(item) === 0"
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

        <!-- Footer add row -->
        <button
          class="flex w-full items-center gap-1.5 px-[18px] py-[13px] text-[14px] font-semibold text-brass-800 transition-colors hover:bg-[#fafbfb]"
          @click="openAdd"
        >
          <Plus class="h-4 w-4" /> {{ active.addLabel }}
        </button>
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
import { Crosshair, Target, MapPin, Factory, Search, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { useCalibersStore } from '@/stores/calibers';
import { usePurposesStore } from '@/stores/purposes';
import { useLoading } from '@/composables/useLoading';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const calibersStore = useCalibersStore();
const purposesStore = usePurposesStore();
const { isLoading, loadingQueue } = useLoading();

const activeType = ref('caliber');
const search = ref('');
const modal = ref(null);

const calibers = ref([]);
const purposes = ref([]);

const isCaliber = computed(() => activeType.value === 'caliber');

const lists = computed(() => [
  { type: 'caliber', label: 'Calibers', icon: Crosshair, items: calibers.value },
  { type: 'purpose', label: 'Purposes', icon: Target, items: purposes.value },
]);

const managedElsewhere = [
  { label: 'Locations & FFLs', icon: MapPin },
  { label: 'Manufacturers', icon: Factory },
];

const active = computed(() =>
  isCaliber.value
    ? {
        label: 'Calibers',
        items: calibers.value,
        addLabel: 'Add caliber',
        nameColumn: 'Label · Official name',
        subhead: 'The cartridge chamberings used across firearms and ammo loads.',
      }
    : {
        label: 'Purposes',
        items: purposes.value,
        addLabel: 'Add purpose',
        nameColumn: 'Purpose',
        subhead: 'The tags applied to ammo loads to describe what they are for.',
      }
);

const filteredItems = computed(() => {
  const term = search.value.trim().toLowerCase();
  if (!term) return active.value.items;
  return active.value.items.filter(
    (item) => item.label?.toLowerCase().includes(term) || item.caliber?.toLowerCase().includes(term)
  );
});

function usageOf(item) {
  return isCaliber.value
    ? (item.firearms_count ?? 0) + (item.loads_count ?? 0)
    : (item.loads_count ?? 0);
}

onMounted(() => fetchData());

async function fetchData() {
  isLoading.value = true;
  loadingQueue.referenceData = false;
  try {
    const [caliberData, purposeData] = await Promise.all([
      calibersStore.fetchAll(),
      purposesStore.fetchAll(),
    ]);
    calibers.value = caliberData.data ?? [];
    purposes.value = purposeData.data ?? [];
  } finally {
    loadingQueue.referenceData = true;
  }
}

function openAdd() {
  search.value = '';
  modal.value = { mode: 'add', item: null };
}

function openEdit(item) {
  modal.value = { mode: 'edit', item };
}

async function refreshActive() {
  if (isCaliber.value) {
    const { data } = await calibersStore.fetchAll();
    calibers.value = data ?? [];
  } else {
    const { data } = await purposesStore.fetchAll();
    purposes.value = data ?? [];
  }
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
  if (usageOf(item) > 0) return;
  try {
    if (isCaliber.value) {
      await calibersStore.remove(item.id);
    } else {
      await purposesStore.remove(item.id);
    }
    await refreshActive();
  } catch (error) {
    console.error('ReferenceData: failed to delete item', error);
  }
}
</script>
