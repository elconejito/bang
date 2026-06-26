<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="[{ label: 'Home', to: '/' }, { label: 'Ammo' }]" class="mb-4" />

    <!-- Page header -->
    <PageHeader
      title="Ammo"
      :count="loading ? undefined : `${totalLoads} LOADS · ${totalRounds.toLocaleString()} RNDS`"
      class="mb-5"
    >
      <template v-if="!loading && lowCount > 0" #count-suffix>
        <span class="inline-flex items-center gap-1 rounded border border-[#e0a999] bg-[#f7e9e4] px-2 py-px font-mono text-[11px] text-[#b4452f]">
          <svg class="h-[11px] w-[11px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
          {{ lowCount }} LOW
        </span>
      </template>
      <template #actions>
        <router-link
          :to="{ name: 'AmmoCreate' }"
          class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
        >
          <Plus class="h-4 w-4" />Add Ammo
        </router-link>
      </template>
    </PageHeader>

    <!-- Toolbar -->
    <div class="mb-7 flex flex-wrap items-center gap-2.5">
      <div class="flex min-w-[220px] flex-1 items-center gap-2 rounded border border-[#c2c6ca] bg-white px-3 py-2">
        <Search class="h-[17px] w-[17px] shrink-0 text-muted" />
        <input
          v-model="search"
          type="text"
          placeholder="Search by brand or load…"
          class="flex-1 bg-transparent text-[15px] placeholder:text-muted focus:outline-none"
        />
      </div>

      <!-- Caliber filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-700 hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'caliber' ? null : 'caliber'"
        >
          {{ activeCaliber ? activeCaliber.label : 'Caliber' }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'caliber'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[160px] rounded border border-line bg-white shadow-lg"
        >
          <button
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-[#f5f6f7]"
            :class="!activeCaliberId ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="setCaliberFilter(null)"
          >All calibers</button>
          <button
            v-for="c in availableCalibers"
            :key="c.id"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-[#f5f6f7]"
            :class="activeCaliberId === c.id ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="setCaliberFilter(c.id)"
          >{{ c.label }}</button>
        </div>
      </div>

      <!-- Purpose filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-700 hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'purpose' ? null : 'purpose'"
        >
          {{ activePurpose ? activePurpose.label : 'Purpose' }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'purpose'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[160px] rounded border border-line bg-white shadow-lg"
        >
          <button
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-[#f5f6f7]"
            :class="!activePurposeId ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="activePurposeId = null; openDropdown = null"
          >All purposes</button>
          <button
            v-for="p in availablePurposes"
            :key="p.id"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-[#f5f6f7]"
            :class="activePurposeId === p.id ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="activePurposeId = p.id; openDropdown = null"
          >{{ p.label }}</button>
        </div>
      </div>

      <!-- Low stock toggle -->
      <button
        class="inline-flex items-center gap-[7px] rounded border px-3 py-2 text-[14px] font-medium transition-colors"
        :class="lowStockOnly
          ? 'border-[#e0a999] bg-[#f7e9e4] text-[#b4452f]'
          : 'border-[#c2c6ca] bg-white text-ink-700 hover:bg-[#f5f6f7]'"
        @click="lowStockOnly = !lowStockOnly"
      >
        <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
        Low stock
      </button>

      <!-- Zero stock toggle -->
      <button
        class="inline-flex items-center gap-[7px] rounded border px-3 py-2 text-[14px] font-medium transition-colors"
        :class="!hideZeroStock
          ? 'border-[#b08a2e] bg-[#f7f0db] text-[#7d6320]'
          : 'border-[#c2c6ca] bg-white text-ink-700 hover:bg-[#f5f6f7]'"
        @click="toggleZeroStock"
      >
        <component :is="hideZeroStock ? Eye : EyeOff" class="h-[15px] w-[15px]" />
        {{ hideZeroStock ? 'Show zero stock' : 'Hide zero stock' }}
      </button>

      <div class="h-6 w-px bg-[#d6d9dc]" />

      <!-- Sort -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-900 hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'sort' ? null : 'sort'"
        >
          <ArrowUpDown class="h-[15px] w-[15px] text-[#5b6066]" />
          {{ sortOptions.find((s) => s.value === sortBy)?.label }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'sort'"
          class="absolute right-0 top-full z-20 mt-1 min-w-[160px] rounded border border-line bg-white shadow-lg"
        >
          <button
            v-for="opt in sortOptions"
            :key="opt.value"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-[#f5f6f7]"
            :class="sortBy === opt.value ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="sortBy = opt.value; openDropdown = null"
          >{{ opt.label }}</button>
        </div>
      </div>
    </div>

    <!-- Loading skeletons -->
    <template v-if="loading">
      <div v-for="n in 2" :key="n" class="mb-8">
        <div class="mb-4 h-7 w-32 animate-pulse rounded bg-ink-100" />
        <div class="grid grid-cols-3 gap-4">
          <div v-for="i in 3" :key="i" class="h-[148px] animate-pulse rounded border border-line bg-white" />
        </div>
      </div>
    </template>

    <!-- Empty state -->
    <EmptyState
      v-else-if="!loading && allAmmo.length === 0"
      title="No ammo loads yet"
      message="Add a load to start tracking on-hand rounds, reorder thresholds, and cost history."
      action-label="Add Ammo"
      :action-to="{ name: 'AmmoCreate' }"
    />

    <EmptyState
      v-else-if="sortedGroups.length === 0"
      title="No ammo loads match your filters"
      message="Try adjusting your search, caliber, purpose, low-stock, or zero-stock filter."
    />

    <!-- Caliber groups -->
    <template v-else>
      <div v-for="group in sortedGroups" :key="group.caliberLabel" class="mb-8">
        <!-- Group header -->
        <div class="mb-4 flex flex-wrap items-baseline gap-3 border-b border-[#d6d9dc] pb-2">
          <span class="font-display text-[22px] font-bold tracking-[-0.01em]">{{ group.caliberLabel }}</span>
          <span
            v-if="group.isLow"
            class="rounded border border-caution-border bg-caution-bg px-1.5 py-0.5 font-mono text-[10px] tracking-[0.04em] text-caution"
          >LOW</span>
          <span class="font-mono text-[12px] tracking-[0.03em] text-muted">
            {{ group.totalRounds.toLocaleString() }} ON HAND · {{ group.items.length }} LOADS
          </span>
        </div>

        <!-- Cards grid -->
        <div class="grid grid-cols-3 gap-4">
          <AmmoCard
            v-for="ammo in group.items"
            :key="ammo.id"
            :ammo="ammo"
            @add-stock="openStock(ammo)"
          />

          <!-- "Add load" dashed tile -->
          <router-link
            :to="{ name: 'AmmoCreate', query: { caliber_id: group.caliberId } }"
            class="flex min-h-[150px] flex-col items-center justify-center gap-[7px] rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-[14px] text-muted transition-colors hover:border-[#a9aeb3] hover:bg-[#f3f4f5]"
          >
            <Plus class="h-[22px] w-[22px] text-[#7d6320]" />
            Add a {{ group.caliberLabel }} load
          </router-link>
        </div>
      </div>
    </template>

    <AddStockModal
      v-if="stockAmmo"
      :ammo="stockAmmo"
      @close="stockAmmo = null"
      @stocked="onStocked"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, ChevronDown, ArrowUpDown, Eye, EyeOff } from 'lucide-vue-next'
import { useAmmunitionStore } from '@/stores/ammunition'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import PageHeader from '@/components/PageHeader.vue'
import EmptyState from '@/components/EmptyState.vue'
import AmmoCard from '@/components/ammunition/AmmoCard.vue'
import AddStockModal from '@/components/ammunition/AddStockModal.vue'

const route = useRoute()
const router = useRouter()
const ammunitionStore = useAmmunitionStore()

const allAmmo = ref([])
const loading = ref(true)
const search = ref('')
const activePurposeId = ref(null)
const activeCaliberId = ref(route.query.caliber_id ? Number(route.query.caliber_id) : null)
const sortBy = ref('on_hand')
const openDropdown = ref(null)
const stockAmmo = ref(null)
const lowStockOnly = ref(false)
const hideZeroStock = ref(true)

const sortOptions = [
  { label: 'On hand', value: 'on_hand' },
  { label: 'Manufacturer', value: 'manufacturer' },
  { label: 'Load name', value: 'label' },
]

const availablePurposes = computed(() => {
  const seen = new Map()
  allAmmo.value.forEach((a) => {
    if (a.purpose && !seen.has(a.purpose.id)) seen.set(a.purpose.id, a.purpose)
  })
  return [...seen.values()]
})

const availableCalibers = computed(() => {
  const seen = new Map()
  allAmmo.value.forEach((a) => {
    if (a.caliber && !seen.has(a.caliber.id)) seen.set(a.caliber.id, a.caliber)
  })
  return [...seen.values()].sort((a, b) => a.label.localeCompare(b.label))
})

const activePurpose = computed(
  () => availablePurposes.value.find((p) => p.id === activePurposeId.value) ?? null,
)

const activeCaliber = computed(
  () => availableCalibers.value.find((c) => c.id === activeCaliberId.value) ?? null,
)

const totalLoads = computed(() => allAmmo.value.length)
const totalRounds = computed(() => allAmmo.value.reduce((sum, a) => sum + a.on_hand, 0))

function isLow(ammo) {
  return ammo.reorder_min != null ? ammo.on_hand <= ammo.reorder_min : ammo.on_hand === 0
}

const lowCount = computed(() => allAmmo.value.filter(isLow).length)

const filteredAmmo = computed(() => {
  let list = allAmmo.value
  if (search.value.trim()) {
    const q = search.value.trim().toLowerCase()
    list = list.filter(
      (a) => a.manufacturer.toLowerCase().includes(q) || a.label.toLowerCase().includes(q),
    )
  }
  if (activePurposeId.value) {
    list = list.filter((a) => a.purpose?.id === activePurposeId.value)
  }
  if (activeCaliberId.value) {
    list = list.filter((a) => a.caliber?.id === activeCaliberId.value)
  }
  if (lowStockOnly.value) {
    list = list.filter(isLow)
  }
  return list
})

const sortedGroups = computed(() => {
  const groups = new Map()
  filteredAmmo.value.forEach((a) => {
    const key = a.caliber?.id ?? 0
    if (!groups.has(key)) {
      groups.set(key, {
        caliberId: key,
        caliberLabel: a.caliber?.label ?? 'Unknown',
        items: [],
        totalRounds: 0,
        isLow: false,
      })
    }
    const g = groups.get(key)
    g.items.push(a)
    g.totalRounds += a.on_hand
    if (isLow(a)) g.isLow = true
  })

  groups.forEach((group) => {
    group.items.sort((a, b) => {
      if (sortBy.value === 'on_hand') return b.on_hand - a.on_hand
      return (a[sortBy.value] ?? '').localeCompare(b[sortBy.value] ?? '')
    })
  })

  return [...groups.values()].sort((a, b) => {
    const aEmpty = a.totalRounds === 0
    const bEmpty = b.totalRounds === 0
    if (aEmpty !== bEmpty) return aEmpty ? 1 : -1
    return a.caliberLabel.localeCompare(b.caliberLabel)
  })
})

function setCaliberFilter(id) {
  activeCaliberId.value = id
  openDropdown.value = null
  router.replace({ query: { ...route.query, caliber_id: id ?? undefined } })
}

function openStock(ammo) {
  stockAmmo.value = ammo
}

function onStocked({ rounds }) {
  if (stockAmmo.value) {
    stockAmmo.value.on_hand += rounds
    const match = allAmmo.value.find((a) => a.id === stockAmmo.value.id)
    if (match) match.on_hand += rounds
  }
  stockAmmo.value = null
}

function handleOutsideClick() {
  openDropdown.value = null
}

async function fetchAmmo() {
  loading.value = true
  try {
    const params = hideZeroStock.value ? { 'filter[in_stock]': 1 } : {}
    const response = await ammunitionStore.fetchAll(params)
    allAmmo.value = response.data ?? []
  } finally {
    loading.value = false
  }
}

function toggleZeroStock() {
  hideZeroStock.value = !hideZeroStock.value
  fetchAmmo()
}

onMounted(async () => {
  document.addEventListener('click', handleOutsideClick)
  await fetchAmmo()
})

onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick))
</script>
