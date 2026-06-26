<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import {
  ArrowLeftRight,
  ArrowUpDown,
  BatteryCharging,
  ChevronDown,
  Circle,
  ListFilter,
  Lock,
  MapPin,
  PackagePlus,
  Plus,
  Sparkles,
  Target,
  Wrench,
} from 'lucide-vue-next'
import LogEventModal from '@/components/history/LogEventModal.vue'
import { useAccessoryEventsStore } from '@/stores/accessoryEvents'

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  manualEventTypes: { type: Array, required: true }, // [{ value, label }]
  historyLabel: { type: String, default: 'HISTORY' },
})

const store = useAccessoryEventsStore()

const entries = ref([])
const meta = ref({ total: 0, last_page: 1, range_count: 0, mount_count: 0 })
const loading = ref(true)
const showModal = ref(false)

const groupFilter = ref('') // '', 'range', 'mount', 'maintenance'
const reversed = ref(false) // false = newest first
const page = ref(1)
const perPage = ref(8)
const filterOpen = ref(false)

const filterOptions = [
  { label: 'All', value: '' },
  { label: 'Range', value: 'range' },
  { label: 'Mounts', value: 'mount' },
  { label: 'Maintenance', value: 'maintenance' },
]

const activeFilterLabel = computed(
  () => filterOptions.find((o) => o.value === groupFilter.value)?.label ?? 'All',
)

const showControls = computed(() => entries.value.length > 0 || groupFilter.value !== '' || page.value > 1)

async function load() {
  loading.value = true
  try {
    const params = {
      page: page.value,
      per_page: perPage.value,
      sort: reversed.value ? 'date' : '-date',
    }
    if (groupFilter.value) {
      params['filter[group]'] = groupFilter.value
    }
    const res = await store.fetchForEntity(props.entityType, props.entityId, params)
    entries.value = res.data ?? []
    meta.value = res.meta ?? { total: 0, last_page: 1, range_count: 0, mount_count: 0 }
  } finally {
    loading.value = false
  }
}

function setGroupFilter(value) {
  groupFilter.value = value
  filterOpen.value = false
  page.value = 1
  load()
}

function toggleSort() {
  reversed.value = !reversed.value
  page.value = 1
  load()
}

function goToPage(next) {
  page.value = next
  load()
}

function setPerPage(value) {
  perPage.value = value
  page.value = 1
  load()
}

function onEventCreated() {
  showModal.value = false
  page.value = 1
  reversed.value = false
  load()
}

function closeFilter() {
  filterOpen.value = false
}

const ICONS = {
  RANGE: Target,
  MOUNT: ArrowLeftRight,
  UNMOUNT: ArrowLeftRight,
  CLEAN: Sparkles,
  REPAIR: Wrench,
  BATTERY: BatteryCharging,
  ADDED: PackagePlus,
  LOCATION: MapPin,
  NFA: Lock,
}

function iconFor(type) {
  return ICONS[type] ?? Circle
}

function nodeClass(group) {
  switch (group) {
    case 'range':
      return 'bg-[#f4ecd6] border-[#e3d3a3] text-[#7d6320]'
    case 'mount':
      return 'bg-[#eee9f3] border-[#ddd4ea] text-[#6b5a8c]'
    case 'added':
      return 'bg-[#e7f1eb] border-[#9ccbb1] text-[#2f7d57]'
    case 'location':
      return 'bg-[#e4eef7] border-[#a8c6e2] text-[#2563a8]'
    default:
      return 'bg-[#f5f6f7] border-[#e2e4e6] text-[#5b6066]'
  }
}

function badgeClass(group) {
  switch (group) {
    case 'range':
      return 'bg-[#f4ecd6] border-[#e3d3a3] text-[#7d6320]'
    case 'mount':
      return 'bg-[#eee9f3] border-[#c3b6d6] text-[#6b5a8c]'
    case 'added':
      return 'bg-[#e7f1eb] border-[#9ccbb1] text-[#2f7d57]'
    case 'location':
      return 'bg-[#e4eef7] border-[#a8c6e2] text-[#2563a8]'
    default:
      return 'bg-[#f5f6f7] border-[#c2c6ca] text-[#5b6066]'
  }
}

function formatDate(dateStr) {
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

onMounted(() => {
  document.addEventListener('click', closeFilter)
  load()
})

onBeforeUnmount(() => document.removeEventListener('click', closeFilter))
</script>

<template>
  <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
    <!-- Header -->
    <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1] flex-wrap">
      <span class="font-display font-semibold text-[18px]">History</span>
      <span class="font-mono text-[11px] text-muted tracking-[0.04em] whitespace-nowrap">{{ historyLabel }}</span>
      <div class="ml-auto flex items-center gap-2">
        <template v-if="showControls">
          <!-- Filter -->
          <div class="relative">
            <button
              class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-[11px] py-[6px] text-[13px] text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
              @click.stop="filterOpen = !filterOpen"
            >
              <ListFilter class="h-[14px] w-[14px] text-muted" />
              {{ activeFilterLabel }}
              <ChevronDown class="h-[13px] w-[13px] text-muted" />
            </button>
            <div
              v-if="filterOpen"
              class="absolute right-0 top-full z-20 mt-1 min-w-[140px] rounded border border-line bg-white shadow-lg"
            >
              <button
                v-for="opt in filterOptions"
                :key="opt.value"
                class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
                :class="groupFilter === opt.value ? 'font-medium text-ink-900' : 'text-ink-700'"
                @click.stop="setGroupFilter(opt.value)"
              >{{ opt.label }}</button>
            </div>
          </div>
          <!-- Sort -->
          <button
            class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-[11px] py-[6px] text-[13px] text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
            @click="toggleSort"
          >
            <ArrowUpDown class="h-[14px] w-[14px] text-muted" />
            {{ reversed ? 'Oldest' : 'Newest' }}
          </button>
        </template>
        <!-- Log event -->
        <button
          class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#3a3e44] bg-white border border-[#c2c6ca] rounded px-3 py-1.5 hover:bg-[#f5f6f7] transition-colors"
          @click="showModal = true"
        >
          <Plus class="w-[13px] h-[13px]" />
          Log
        </button>
      </div>
    </div>

    <!-- Timeline -->
    <div v-if="loading" class="px-[18px] py-12 text-center text-muted text-[14px]">Loading…</div>

    <div v-else-if="!entries.length" class="px-[18px] py-12 text-center text-muted text-[14px]">
      {{ groupFilter ? 'No matching history.' : 'No history yet. Add this item or log an event to start the timeline.' }}
    </div>

    <div v-else class="px-[18px] pt-5 pb-2">
      <div v-for="(entry, i) in entries" :key="entry.id" class="flex gap-[14px]">
        <!-- Node + connector -->
        <div class="flex flex-none flex-col items-center">
          <div
            class="flex h-[30px] w-[30px] flex-none items-center justify-center rounded-full border"
            :class="nodeClass(entry.group)"
          >
            <component :is="iconFor(entry.type)" class="h-[15px] w-[15px]" :stroke-width="2" />
          </div>
          <div v-if="i < entries.length - 1" class="my-1 w-0.5 flex-1 bg-[#eef0f1]" style="min-height: 16px" />
        </div>

        <!-- Content -->
        <div class="flex-1" :class="i < entries.length - 1 ? 'pb-5' : 'pb-0'">
          <div class="flex items-center gap-[9px]">
            <span
              class="shrink-0 rounded border font-mono text-[10px] tracking-[0.05em]"
              style="padding: 1px 6px"
              :class="badgeClass(entry.group)"
            >{{ entry.type }}</span>
            <span class="min-w-0 flex-1 text-[16px] font-medium">{{ entry.title }}</span>
            <span class="ml-auto shrink-0 font-mono text-[12px] text-muted">{{ formatDate(entry.date) }}</span>
          </div>
          <div v-if="entry.subtitle" class="mt-1 text-[14px] text-[#6b7077]">{{ entry.subtitle }}</div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div
      v-if="(meta.last_page ?? 1) > 1"
      class="flex items-center justify-between border-t border-[#eef0f1] px-[18px] py-3"
    >
      <div class="flex items-center gap-2">
        <span class="text-[13px] text-muted">Per page</span>
        <select
          :value="perPage"
          class="rounded border border-line bg-white px-2 py-1 text-[13px] text-ink-700 focus:outline-none"
          @change="setPerPage(Number($event.target.value))"
        >
          <option v-for="opt in [8, 25, 50]" :key="opt" :value="opt">{{ opt }}</option>
        </select>
        <span class="text-[13px] text-muted">{{ meta.total }} total</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          class="rounded border border-line bg-white px-3 py-1 text-[13px] text-ink-700 hover:bg-ink-50 disabled:opacity-40"
          :disabled="page === 1"
          @click="goToPage(page - 1)"
        >Prev</button>
        <span class="px-3 text-[13px] text-muted">{{ page }} / {{ meta.last_page }}</span>
        <button
          class="rounded border border-line bg-white px-3 py-1 text-[13px] text-ink-700 hover:bg-ink-50 disabled:opacity-40"
          :disabled="page === (meta.last_page ?? 1)"
          @click="goToPage(page + 1)"
        >Next</button>
      </div>
    </div>
  </div>

  <LogEventModal
    v-if="showModal"
    :entity-type="entityType"
    :entity-id="entityId"
    :available-types="manualEventTypes"
    @created="onEventCreated"
    @close="showModal = false"
  />
</template>
