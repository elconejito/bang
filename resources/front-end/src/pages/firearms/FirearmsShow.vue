<template>
  <div v-if="isLoading" class="mx-auto max-w-[1280px] px-8 py-6">
    <div class="h-8 w-48 animate-pulse rounded bg-ink-100" />
    <div class="mt-5 h-10 w-64 animate-pulse rounded bg-ink-100" />
  </div>

  <div v-else class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <AppBreadcrumb
      :crumbs="[
        { label: 'Home', to: '/' },
        { label: 'Firearms', to: { name: 'FirearmsIndex' } },
        { label: firearm.label },
      ]"
      class="mb-[18px]"
    />

    <!-- Header -->
    <div class="mb-[22px] flex flex-wrap items-center gap-4">
      <div>
        <h1 class="font-display text-[30px] font-bold leading-none tracking-[-0.02em]">
          {{ firearm.label }}
        </h1>
        <p class="mt-[3px] text-[15px] text-[#6b7077]">{{ subtitle }}</p>
        <p v-if="firearm.customizer" class="mt-1 text-[13px] text-[#6b7077]">
          Customized by {{ firearm.customizer
          }}<template v-if="firearm.custom_package"> · {{ firearm.custom_package }}</template>
        </p>
      </div>
      <div class="ml-auto flex items-center gap-2.5">
        <router-link
          :to="{ name: 'FirearmsEdit', params: { firearm_id: firearmId } }"
          class="detail-action"
        >
          <Pencil class="h-[15px] w-[15px]" />
          Edit
        </router-link>
        <router-link :to="{ name: 'TrainingCreate' }" class="detail-action detail-action-primary">
          <Plus class="h-4 w-4" />
          Log session
        </router-link>
      </div>
    </div>

    <!-- Two-column layout -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[344px_1fr] lg:items-start">
      <!-- ── Left rail ── -->
      <div class="flex flex-col gap-4">
        <!-- Photo card -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <router-link
            :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }"
            class="block"
          >
            <div class="relative aspect-[5/3] w-full bg-ink-100">
              <img
                v-if="primaryPhoto"
                :src="primaryPhoto"
                :alt="firearm.label"
                class="h-full w-full object-cover"
              />
              <div v-else class="flex h-full w-full items-center justify-center">
                <Camera class="h-10 w-10 text-ink-300" />
              </div>
              <span
                class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
              >
                <Camera class="h-[13px] w-[13px]" />
                {{ firearm.pictures_count ? `${firearm.pictures_count} photos` : 'Add photos' }}
              </span>
            </div>
          </router-link>
          <!-- Thumbnail strip — only shown when 2+ photos -->
          <div v-if="firearm.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
            <router-link
              v-for="(url, i) in firearm.thumbnail_urls"
              :key="i"
              :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }"
              class="aspect-[4/3] rounded border border-line bg-ink-50 block overflow-hidden"
            >
              <img :src="url" class="h-full w-full object-cover" alt="" />
            </router-link>
            <!-- Fill remaining slots with placeholders up to 3 -->
            <router-link
              v-for="n in Math.max(0, 3 - firearm.thumbnail_urls.length)"
              :key="`ph-${n}`"
              :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }"
              class="aspect-[4/3] rounded border border-line bg-ink-50 block"
            />
            <router-link
              :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }"
              class="flex aspect-[4/3] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
            >
              <Plus class="h-4 w-4" />
            </router-link>
          </div>
        </div>

        <!-- Spec card -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div
            class="flex items-baseline justify-between border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]"
          >
            <span class="text-[14px] text-[#6b7077]">Rounds fired</span>
            <span class="font-mono text-[30px] font-medium leading-none">{{
              formatQuantity(firearm.rounds_fired ?? 0)
            }}</span>
          </div>
          <div class="px-4">
            <!-- Calibers -->
            <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
              <span class="text-[14px] text-[#6b7077]">Caliber</span>
              <div class="flex flex-wrap justify-end gap-1">
                <span
                  v-for="cal in firearm.calibers"
                  :key="cal.id"
                  class="rounded border border-[#c2c6ca] bg-ink-50 px-[9px] py-[1px] text-[12px] text-ink-700"
                  >{{ cal.label }}</span
                >
                <span v-if="!firearm.calibers?.length" class="text-[14px] text-muted">—</span>
              </div>
            </div>
            <!-- Serial -->
            <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
              <span class="text-[14px] text-[#6b7077]">Serial #</span>
              <span class="font-mono text-[14px]">{{ firearm.serial ?? '—' }}</span>
            </div>
            <!-- Customization -->
            <div
              v-if="firearm.customizer"
              class="flex items-start justify-between gap-4 border-b border-[#f1f2f3] py-[9px]"
            >
              <span class="text-[14px] text-[#6b7077]">Customized by</span>
              <span class="text-right text-[14px]">
                {{ firearm.customizer
                }}<template v-if="firearm.custom_package"> · {{ firearm.custom_package }}</template>
              </span>
            </div>
            <!-- Purchased -->
            <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
              <span class="text-[14px] text-[#6b7077]">Purchased</span>
              <span class="text-[14px]">{{ purchaseDisplay ?? '—' }}</span>
            </div>
            <!-- Purchase store -->
            <div
              v-if="firearm.purchase_store"
              class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]"
            >
              <span class="text-[14px] text-[#6b7077]">Purchased from</span>
              <span class="text-[14px]">{{ firearm.purchase_store.label }}</span>
            </div>
            <!-- Storage -->
            <div class="flex items-center justify-between py-[9px]">
              <span class="text-[14px] text-[#6b7077]">Storage</span>
              <span class="inline-flex items-center gap-1.5 text-[14px]">
                <MapPin class="h-[14px] w-[14px] text-ink-400" />
                {{ firearm.location?.label ?? '—' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Accessories -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-center justify-between border-b border-[#eef0f1] px-4 py-[13px]">
            <span class="font-display text-[16px] font-semibold">Accessories</span>
            <router-link
              :to="{ name: 'AccessoriesIndex' }"
              class="inline-flex items-center gap-[5px] text-[13px] font-semibold text-[#7d6320] transition-colors hover:text-[#5f4b18]"
            >
              <Plus class="h-[14px] w-[14px]" />
              Mount
            </router-link>
          </div>

          <!-- Magazine currently inserted -->
          <div v-if="firearm.current_magazines?.length" class="border-b border-[#eef0f1] px-4 py-3">
            <div class="mb-2.5 font-mono text-[10px] tracking-[0.06em] text-muted">IN FIREARM</div>
            <router-link
              v-for="magazine in firearm.current_magazines"
              :key="magazine.id"
              :to="{ name: 'MagazinesShow', params: { magazine_id: magazine.id } }"
              class="-mx-1 flex items-center gap-2.5 rounded px-1 py-1 transition-colors hover:bg-[#fafbfb]"
            >
              <div
                class="flex h-8 w-8 flex-none items-center justify-center rounded border border-[#d9c787] bg-[#faf6e8] text-[#7d6320]"
              >
                <Cylinder class="h-[17px] w-[17px]" :stroke-width="1.9" />
              </div>
              <div class="min-w-0 flex-1">
                <div class="truncate text-[14px] font-medium text-ink-900">
                  {{ magazine.id_marking || magazine.label || magazine.model_name || 'Magazine' }}
                </div>
                <div class="truncate text-[12px] text-muted">
                  {{ magazine.loaded_rounds }} / {{ magazine.capacity }} rounds
                  <template v-if="magazine.loaded_ammunition">
                    &middot; {{ magazine.loaded_ammunition.manufacturer }}
                    {{ magazine.loaded_ammunition.label }}
                  </template>
                </div>
              </div>
              <ChevronRight class="h-[15px] w-[15px] flex-none text-[#b6bcc1]" />
            </router-link>
          </div>

          <!-- Mounted now -->
          <div v-if="firearm.mounted_accessories?.length" class="px-4 pb-1.5 pt-3">
            <div class="mb-2.5 font-mono text-[10px] tracking-[0.06em] text-muted">MOUNTED NOW</div>
            <div class="flex flex-col gap-[11px]">
              <router-link
                v-for="acc in firearm.mounted_accessories"
                :key="`${acc.type}-${acc.id}`"
                :to="accessoryRoute(acc)"
                class="-mx-1 flex items-center gap-2.5 rounded px-1 py-0.5 transition-colors hover:bg-[#fafbfb]"
              >
                <div
                  class="flex h-8 w-8 flex-none items-center justify-center rounded border"
                  :class="accessoryIconClass(acc.type)"
                >
                  <component
                    :is="accessoryIcon(acc.type)"
                    class="h-[17px] w-[17px]"
                    :stroke-width="1.9"
                  />
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-[7px]">
                    <span class="truncate text-[14px] font-medium text-ink-900">{{
                      acc.label
                    }}</span>
                    <span
                      v-if="acc.is_nfa"
                      class="flex-none rounded-[3px] bg-ink-900 px-[5px] py-px font-mono text-[9px] text-white"
                      >NFA</span
                    >
                  </div>
                  <div class="text-[12px] text-muted">{{ acc.subtitle }}</div>
                </div>
                <ChevronRight class="h-[15px] w-[15px] flex-none text-[#b6bcc1]" />
              </router-link>
            </div>
          </div>

          <!-- Empty state -->
          <div v-else class="flex flex-col items-center justify-center px-4 py-8 text-center">
            <p class="text-[14px] font-medium text-ink-700">No accessories mounted</p>
            <p class="mt-1 text-[13px] text-muted">
              Mount an accessory to this firearm to see it here.
            </p>
          </div>

          <!-- Compatible links -->
          <div class="mt-2.5 border-t border-[#eef0f1]">
            <router-link
              :to="{
                name: 'CompatibleMagazines',
                params: { firearm_id: firearm.id },
              }"
              class="flex items-center justify-between px-4 py-[11px] transition-colors hover:bg-[#fafbfb]"
            >
              <span class="text-[14px]">Compatible magazines</span>
              <span
                class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#7d6320]"
              >
                {{ firearm.compatible_magazines_count ?? 0 }}
                <ChevronRight class="h-[14px] w-[14px]" />
              </span>
            </router-link>
          </div>
        </div>

        <NotesPanel entity-type="firearms" :entity-id="firearmId" />
      </div>

      <!-- ── Activity feed ── -->
      <div class="overflow-hidden rounded border border-line bg-surface">
        <!-- Header -->
        <div class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] px-[18px] py-4">
          <span class="font-display text-[18px] font-semibold">Activity</span>
          <span
            v-if="activityMeta.range_count"
            class="font-mono text-[11px] tracking-[0.04em] text-muted"
          >
            {{ activityMeta.range_count }}
            {{ activityMeta.range_count === 1 ? 'SESSION' : 'SESSIONS'
            }}{{ lastShotLabel ? ' · ' + lastShotLabel : '' }}
          </span>
          <div v-if="showActivityControls" class="ml-auto flex items-center gap-2">
            <div class="relative">
              <button
                class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-[11px] py-[6px] text-[13px] text-ink-700 transition-colors hover:bg-[#f5f6f7]"
                @click.stop="filterDropdownOpen = !filterDropdownOpen"
              >
                <ListFilter class="h-[14px] w-[14px] text-muted" />
                {{ activityTypeFilter === 'ALL' ? 'All' : activityTypeFilter }}
                <ChevronDown class="h-[13px] w-[13px] text-muted" />
              </button>
              <div
                v-if="filterDropdownOpen"
                class="absolute right-0 top-full z-20 mt-1 min-w-[120px] rounded border border-line bg-white shadow-lg"
              >
                <button
                  v-for="opt in activityFilterOptions"
                  :key="opt.value"
                  class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
                  :class="
                    activityTypeFilter === opt.value ? 'font-medium text-ink-900' : 'text-ink-700'
                  "
                  @click.stop="setActivityTypeFilter(opt.value)"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>
            <button
              class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-[11px] py-[6px] text-[13px] text-ink-700 transition-colors hover:bg-[#f5f6f7]"
              @click="toggleActivitySort"
            >
              <ArrowUpDown class="h-[14px] w-[14px] text-muted" />
              {{ activityReversed ? 'Oldest' : 'Newest' }}
            </button>
          </div>
        </div>

        <!-- Timeline -->
        <div v-if="activity.length" class="px-[18px] pb-2 pt-5">
          <div
            v-for="(entry, i) in activity"
            :key="`${entry.type}-${entry.session_id ?? entry.event_id}`"
            class="flex gap-[14px]"
          >
            <!-- Circle + connector -->
            <div class="flex flex-none flex-col items-center">
              <div
                class="flex h-[30px] w-[30px] flex-none items-center justify-center rounded-full border"
                :class="typeIconClass(entry.type)"
              >
                <Target v-if="entry.type === 'RANGE'" class="h-[15px] w-[15px]" />
                <ArrowLeftRight v-else-if="entry.type === 'MOUNT'" class="h-[14px] w-[14px]" />
              </div>
              <div
                v-if="i < activity.length - 1"
                class="my-1 w-0.5 flex-1 bg-[#eef0f1]"
                style="min-height: 16px"
              />
            </div>

            <!-- Content -->
            <div class="flex-1" :class="i < activity.length - 1 ? 'pb-5' : 'pb-0'">
              <div class="flex items-center gap-[9px]">
                <span
                  class="shrink-0 rounded border font-mono text-[10px] tracking-[0.05em]"
                  style="padding: 1px 6px"
                  :class="typeBadgeClass(entry.type)"
                  >{{ entry.type }}</span
                >
                <router-link
                  v-if="entry.session_id"
                  :to="{ name: 'TrainingShow', params: { training_id: entry.session_id } }"
                  class="min-w-0 flex-1 text-[16px] font-medium hover:text-brass-800"
                  >{{ entry.title }}</router-link
                >
                <span v-else class="min-w-0 flex-1 text-[16px] font-medium">{{ entry.title }}</span>
                <span class="ml-auto shrink-0 font-mono text-[12px] text-muted">{{
                  formatActivityDate(entry.date)
                }}</span>
              </div>
              <div v-if="entry.subtitle" class="mt-1 text-[14px] text-[#6b7077]">
                {{ entry.subtitle }}
              </div>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div
          v-else-if="!isLoadingActivity"
          class="flex flex-col items-center justify-center px-6 py-16 text-center"
        >
          <p class="text-[15px] font-medium text-ink-700">
            {{ activityTypeFilter === 'ALL' ? 'No sessions logged yet' : 'No matching activity' }}
          </p>
          <p
            v-if="activityTypeFilter === 'ALL'"
            class="mt-1.5 max-w-[280px] text-[14px] text-muted"
          >
            Range sessions, cleaning, and accessory mounts will appear here once you start logging
            activity.
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="(activityMeta.last_page ?? 1) > 1"
          class="flex items-center justify-between border-t border-[#eef0f1] px-[18px] py-3"
        >
          <div class="flex items-center gap-2">
            <span class="text-[13px] text-muted">Per page</span>
            <select
              :value="activityPerPage"
              class="rounded border border-line bg-white px-2 py-1 text-[13px] text-ink-700 focus:outline-none"
              @change="setActivityPerPage(Number($event.target.value))"
            >
              <option v-for="opt in [10, 25, 50]" :key="opt" :value="opt">{{ opt }}</option>
            </select>
            <span class="text-[13px] text-muted">{{ activityMeta.total }} total</span>
          </div>
          <div class="flex items-center gap-1">
            <button
              class="rounded border border-line bg-white px-3 py-1 text-[13px] text-ink-700 hover:bg-ink-50 disabled:opacity-40"
              :disabled="activityPage === 1"
              @click="goToActivityPage(activityPage - 1)"
            >
              Prev
            </button>
            <span class="px-3 text-[13px] text-muted"
              >{{ activityPage }} / {{ activityMeta.last_page }}</span
            >
            <button
              class="rounded border border-line bg-white px-3 py-1 text-[13px] text-ink-700 hover:bg-ink-50 disabled:opacity-40"
              :disabled="activityPage === (activityMeta.last_page ?? 1)"
              @click="goToActivityPage(activityPage + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import dayjs from 'dayjs';
import numeral from 'numeral';
import {
  ArrowLeftRight,
  ArrowUpDown,
  Camera,
  ChevronDown,
  ChevronRight,
  Cylinder,
  Lightbulb,
  ListFilter,
  MapPin,
  Pencil,
  Plus,
  Target,
  Wrench,
} from 'lucide-vue-next';
import { useFirearmsStore } from '@/stores/firearms';
import { useNumbers } from '@/composables/useNumbers';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';

const props = defineProps({
  firearmId: { type: Number, required: true },
});

const firearmsStore = useFirearmsStore();
const { formatQuantity } = useNumbers();

const firearm = ref({});
const isLoading = ref(true);

const activity = ref([]);
const activityMeta = ref({ total: 0, last_page: 1, range_count: 0, last_session_date: null });
const isLoadingActivity = ref(true);
const activityTypeFilter = ref('ALL');
const activityReversed = ref(false);
const activityPage = ref(1);
const activityPerPage = ref(10);
const filterDropdownOpen = ref(false);

const activityFilterOptions = [
  { label: 'All', value: 'ALL' },
  { label: 'RANGE', value: 'RANGE' },
  { label: 'MOUNT', value: 'MOUNT' },
];

function closeFilterDropdown() {
  filterDropdownOpen.value = false;
}

const primaryPhoto = computed(() => firearm.value.primary_photo_url ?? null);

const subtitle = computed(() => {
  const parts = [firearm.value.manufacturer, firearm.value.model].filter(Boolean).join(' ');
  const cals = (firearm.value.calibers ?? []).map((c) => c.label).join(', ');
  return cals ? `${parts} · ${cals}` : parts;
});

const purchaseDisplay = computed(() => {
  const { purchase_date, purchase_price } = firearm.value;
  if (!purchase_date && !purchase_price) return null;
  const date = purchase_date ? dayjs(purchase_date).format('MMM YYYY') : null;
  const price = purchase_price ? numeral(purchase_price).format('$0,0[.]00') : null;
  return [date, price].filter(Boolean).join(' · ');
});

const lastShotLabel = computed(() => {
  if (!activityMeta.value.last_session_date) return null;
  return 'LAST SHOT ' + dayjs(activityMeta.value.last_session_date).format('MMM D').toUpperCase();
});

const showActivityControls = computed(
  () => activity.value.length > 0 || activityTypeFilter.value !== 'ALL' || activityPage.value > 1
);

async function loadActivity() {
  isLoadingActivity.value = true;
  try {
    const params = {
      page: activityPage.value,
      per_page: activityPerPage.value,
      sort: activityReversed.value ? 'date' : '-date',
    };
    if (activityTypeFilter.value !== 'ALL') {
      params['filter[type]'] = activityTypeFilter.value;
    }
    const res = await firearmsStore.fetchActivity(props.firearmId, params);
    activity.value = res.data ?? [];
    activityMeta.value = res.meta ?? {
      total: 0,
      last_page: 1,
      range_count: 0,
      last_session_date: null,
    };
  } finally {
    isLoadingActivity.value = false;
  }
}

function setActivityTypeFilter(value) {
  activityTypeFilter.value = value;
  filterDropdownOpen.value = false;
  activityPage.value = 1;
  loadActivity();
}

function toggleActivitySort() {
  activityReversed.value = !activityReversed.value;
  activityPage.value = 1;
  loadActivity();
}

function goToActivityPage(page) {
  activityPage.value = page;
  loadActivity();
}

function setActivityPerPage(value) {
  activityPerPage.value = value;
  activityPage.value = 1;
  loadActivity();
}

function typeIconClass(type) {
  if (type === 'RANGE') return 'bg-[#f4ecd6] border-[#e3d3a3] text-[#7d6320]';
  if (type === 'MOUNT') return 'bg-[#eee9f3] border-[#ddd4ea] text-[#6b5a8c]';
  return 'bg-[#f5f6f7] border-[#e2e4e6] text-[#5b6066]';
}

function typeBadgeClass(type) {
  if (type === 'RANGE') return 'bg-[#f4ecd6] border-[#e3d3a3] text-[#7d6320]';
  if (type === 'MOUNT') return 'bg-[#eee9f3] border-[#c3b6d6] text-[#6b5a8c]';
  return 'bg-[#f5f6f7] border-[#c2c6ca] text-[#5b6066]';
}

function formatActivityDate(dateStr) {
  return dayjs(dateStr).format('MMM D');
}

const ACCESSORY_ROUTES = {
  Suppressor: { name: 'SuppressorShow', param: 'suppressor_id' },
  Optic: { name: 'OpticShow', param: 'optic_id' },
  Light: { name: 'LightShow', param: 'light_id' },
  Misc: { name: 'MiscShow', param: 'misc_id' },
};

function accessoryRoute(acc) {
  const r = ACCESSORY_ROUTES[acc.type];
  return r ? { name: r.name, params: { [r.param]: acc.id } } : '/';
}

const ACCESSORY_ICONS = {
  Suppressor: Cylinder,
  Optic: Target,
  Light: Lightbulb,
  Misc: Wrench,
};

function accessoryIcon(type) {
  return ACCESSORY_ICONS[type] ?? Wrench;
}

function accessoryIconClass(type) {
  return type === 'Suppressor'
    ? 'border-[#ddd4ea] bg-[#eee9f3] text-[#6b5a8c]'
    : 'border-[#e2e4e6] bg-[#f5f6f7] text-[#6b7077]';
}

onMounted(async () => {
  document.addEventListener('click', closeFilterDropdown);

  const [firearmRes] = await Promise.allSettled([
    firearmsStore.fetchOne(props.firearmId),
    loadActivity(),
  ]);

  if (firearmRes.status === 'fulfilled') {
    firearm.value = firearmRes.value.data;
  }

  isLoading.value = false;
});

onBeforeUnmount(() => document.removeEventListener('click', closeFilterDropdown));
</script>
