<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <!-- Loading -->
    <template v-if="loading">
      <div class="mb-4 h-4 w-56 animate-pulse rounded bg-ink-100" />
      <div class="mb-6 h-8 w-80 animate-pulse rounded bg-ink-100" />
      <div class="grid grid-cols-[344px_1fr] gap-6">
        <div class="flex flex-col gap-4">
          <div v-for="n in 3" :key="n" class="h-40 animate-pulse rounded border border-line bg-white" />
        </div>
        <div class="flex flex-col gap-4">
          <div class="grid grid-cols-2 gap-4">
            <div v-for="n in 2" :key="n" class="h-40 animate-pulse rounded border border-line bg-white" />
          </div>
          <div class="h-64 animate-pulse rounded border border-line bg-white" />
        </div>
      </div>
    </template>

    <template v-else-if="ammo">
      <AppBreadcrumb
        :crumbs="[
          { label: 'Home', to: '/' },
          { label: 'Ammo', to: { name: 'AmmoIndex' } },
          { label: ammo.caliber?.label ?? '', to: { name: 'AmmoIndex' } },
          { label: ammo.label },
        ]"
        class="mb-[18px]"
      />

      <!-- Header row -->
      <div class="mb-[22px] flex flex-wrap items-center gap-4">
        <div class="min-w-0 flex-1">
          <h1 class="font-display text-[26px] font-bold leading-[1.15] tracking-[-0.02em]">
            {{ ammo.label }}
          </h1>
          <div class="mt-1 text-[15px] text-muted">
            {{ ammo.manufacturer }} · {{ ammo.caliber?.label }} · {{ ammo.purpose?.label }}
          </div>
        </div>
        <div class="ml-auto flex items-center gap-2.5">
          <router-link
            :to="{ name: 'AmmoEdit', params: { ammunition_id: ammo.id } }"
            class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-[14px] py-2 text-[14px] font-semibold text-ink-900 hover:bg-[#f5f6f7]"
          >
            <Pencil class="h-[15px] w-[15px]" />Edit
          </router-link>
          <button
            class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 hover:bg-brass-600"
            @click="stockOpen = true"
          >
            <Plus class="h-4 w-4" />Inventory
          </button>
        </div>
      </div>

      <!-- Two-column layout -->
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <!-- ===== LEFT RAIL ===== -->
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link :to="{ name: 'AmmoGallery', params: { ammunition_id: ammunitionId } }" class="block">
              <div class="relative h-[208px] w-full bg-ink-100">
                <img
                  v-if="ammo.primary_photo_url"
                  :src="ammo.primary_photo_url"
                  :alt="ammo.label"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white">
                  <Camera class="h-[13px] w-[13px]" />
                  {{ ammo.pictures_count ? `${ammo.pictures_count} photos` : 'Add photos' }}
                </span>
              </div>
            </router-link>
            <div v-if="ammo.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in ammo.thumbnail_urls"
                :key="i"
                :to="{ name: 'AmmoGallery', params: { ammunition_id: ammunitionId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - ammo.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'AmmoGallery', params: { ammunition_id: ammunitionId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'AmmoGallery', params: { ammunition_id: ammunitionId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>

          <!-- On hand card -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex items-baseline justify-between border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]">
              <span class="text-[14px] text-muted">On hand</span>
              <span class="font-mono text-[30px] font-medium leading-none">{{ ammo.on_hand.toLocaleString() }}</span>
            </div>
            <div class="flex flex-col gap-[13px] px-4 py-[14px]">
              <!-- Reorder progress bar (shown only when reorder_target is set) -->
              <div v-if="ammo.reorder_target">
                <div class="mb-[6px] flex items-center justify-between">
                  <span class="font-mono text-[11px] text-[#b4452f]">REORDER {{ ammo.reorder_min?.toLocaleString() ?? '—' }}</span>
                  <span class="font-mono text-[11px] text-muted">TARGET {{ ammo.reorder_target.toLocaleString() }}</span>
                </div>
                <div class="relative h-[10px] overflow-visible rounded-full border border-[#e2e4e6] bg-[#eceef0]">
                  <div
                    class="absolute bottom-0 left-0 top-0 rounded-full bg-[#c2a14d]"
                    :style="{ width: reorderFillPct + '%' }"
                  />
                  <div
                    v-if="ammo.reorder_min"
                    class="absolute bottom-[-3px] top-[-3px] w-[2px] bg-[#b4452f]"
                    :style="{ left: reorderMinPct + '%' }"
                  />
                </div>
                <div class="mt-[6px] text-[12px] text-muted">
                  {{ reorderFillPct }}% of target ·
                  <span :class="reorderStatusColor" class="font-semibold">{{ reorderStatus }}</span>
                </div>
              </div>
              <div class="flex items-center justify-between border-t border-[#f1f2f3] pt-[11px]">
                <span class="text-[14px] text-muted">Avg cost / rd</span>
                <span class="font-mono text-[15px]" :class="avgCostPerRound ? '' : 'text-muted'">{{ avgCostPerRound ?? '—' }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-[14px] text-muted">Est. value</span>
                <span class="font-mono text-[15px]" :class="estimatedValue ? '' : 'text-muted'">{{ estimatedValue ?? '—' }}</span>
              </div>
            </div>
          </div>

          <!-- Specs card -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="border-b border-[#eef0f1] px-4 py-3 font-display text-[16px] font-semibold">Specs</div>
            <div class="px-4 py-1.5">
              <div v-if="ammo.purpose" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-muted">Purpose</span>
                <span class="rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[9px] py-px text-[12px] text-ink-700">
                  {{ ammo.purpose.label }}
                </span>
              </div>
              <div v-if="ammo.weight" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-muted">Bullet</span>
                <span class="text-[14px]">
                  {{ ammo.weight }} gr{{ ammo.bullet_type ? ' · ' + ammo.bullet_type.label : '' }}
                </span>
              </div>
              <div v-if="ammo.ammunition_casing" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-muted">Case</span>
                <span class="text-[14px]">{{ ammo.ammunition_casing.label }}</span>
              </div>
              <div v-if="ammo.primer_type" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-muted">Primer</span>
                <span class="text-[14px]">{{ ammo.primer_type.label }}</span>
              </div>
              <div v-if="ammo.ammunition_condition" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-muted">Condition</span>
                <span class="text-[14px]">{{ ammo.ammunition_condition.label }}</span>
              </div>
              <p v-if="!hasAnySpec" class="py-3 text-[14px] text-muted">No specs recorded.</p>
            </div>
          </div>

          <!-- Used by -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="border-b border-[#eef0f1] px-4 py-3 font-display text-[16px] font-semibold">Used by</div>
            <div v-if="!ammo.used_by_firearms?.length" class="px-4 py-3 text-[14px] text-muted italic">
              No firearms chambered for {{ ammo.caliber?.label }}.
            </div>
            <div v-else class="divide-y divide-[#f1f2f3]">
              <router-link
                v-for="firearm in ammo.used_by_firearms"
                :key="firearm.id"
                :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
                class="flex items-center justify-between px-4 py-[10px] text-[14px] hover:bg-[#f5f6f7] transition-colors"
              >
                <div>
                  <span class="font-medium">{{ firearm.label }}</span>
                  <span class="ml-2 text-muted text-[13px]">{{ firearm.manufacturer }}</span>
                </div>
                <svg class="h-[13px] w-[13px] text-[#c2c6ca]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              </router-link>
            </div>
          </div>
        </div>

        <!-- ===== RIGHT ===== -->
        <div class="flex flex-col gap-4">
          <!-- Trend charts -->
          <div class="grid grid-cols-2 gap-4">
            <!-- On hand 12 mo -->
            <div class="rounded border border-line bg-white p-[18px]">
              <div class="mb-[14px] flex items-baseline justify-between">
                <span class="font-display text-[15px] font-semibold">On hand · 12 mo</span>
              </div>
              <div class="h-[80px]"><Bar :data="onHandChartData" :options="barOptions" /></div>
              <div class="mt-1.5 flex justify-between font-mono text-[9px] text-muted">
                <span v-for="m in monthLabels" :key="m">{{ m }}</span>
              </div>
            </div>
            <!-- Cost/rd 12 mo -->
            <div class="rounded border border-line bg-white p-[18px]">
              <div class="mb-[14px] flex items-baseline justify-between">
                <span class="font-display text-[15px] font-semibold">Cost / rd · 12 mo</span>
              </div>
              <div class="h-[80px]"><Bar :data="costChartData" :options="barOptions" /></div>
              <div class="mt-1.5 flex justify-between font-mono text-[9px] text-muted">
                <span v-for="m in monthLabels" :key="m">{{ m }}</span>
              </div>
            </div>
          </div>

          <!-- Inventory & usage ledger -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] px-[18px] py-[15px]">
              <span class="font-display text-[18px] font-semibold">Inventory &amp; usage</span>
              <!-- Type filter -->
              <div class="ml-auto flex items-center gap-1">
                <button
                  v-for="opt in ['ALL', 'BUY', 'FIRED', 'ADJUST']"
                  :key="opt"
                  class="rounded px-2 py-0.5 font-mono text-[11px] tracking-[0.04em] transition-colors"
                  :class="ledgerTypeFilter === opt
                    ? 'bg-ink-900 text-white'
                    : 'text-muted hover:text-ink-700'"
                  @click="ledgerTypeFilter = opt"
                >{{ opt }}</button>
              </div>
              <button
                class="p-1 text-muted transition-colors hover:text-ink-700"
                title="Reverse sort"
                @click="ledgerReversed = !ledgerReversed"
              >
                <svg class="h-[14px] w-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h13M3 12h9M3 16h5M21 8l-4-4-4 4M17 4v16"/></svg>
              </button>
            </div>

            <!-- Column headers -->
            <div class="grid grid-cols-[100px_1fr_90px_90px] border-b border-[#e2e4e6] bg-[#f5f6f7] font-mono text-[10px] uppercase tracking-[0.05em] text-muted">
              <div class="px-4 py-[10px]">Date</div>
              <div class="px-3 py-[10px]">Activity</div>
              <div class="px-3 py-[10px] text-right">Change</div>
              <div class="px-4 py-[10px] text-right">Balance</div>
            </div>

            <!-- Loading -->
            <div v-if="ledgerLoading" class="px-[18px] py-8 text-center text-[14px] text-muted">Loading…</div>

            <!-- Empty -->
            <div v-else-if="!filteredLedger.length" class="px-[18px] py-8 text-center text-[14px] text-muted">
              No inventory history yet.
            </div>

            <!-- Rows -->
            <template v-else>
              <div
                v-for="entry in filteredLedger"
                :key="entry.id"
                class="grid grid-cols-[100px_1fr_90px_90px] items-center border-b border-[#f1f2f3] last:border-b-0"
              >
                <!-- Date -->
                <div class="px-4 py-[11px] font-mono text-[12px] text-muted">
                  {{ dayjs(entry.inventory_date).format('MMM D, YYYY') }}
                </div>

                <!-- Activity -->
                <div class="flex items-center gap-2 px-3 py-[11px]">
                  <!-- Type badge -->
                  <span
                    class="rounded px-[7px] py-[2px] font-mono text-[10px] font-medium tracking-[0.04em]"
                    :class="{
                      'bg-[#f4ecd6] text-[#7d6320] border border-[#e3d3a3]': entry.type === 'BUY',
                      'bg-[#f5f6f7] text-ink-500 border border-[#d6d9dc]': entry.type === 'FIRED',
                      'bg-[#eee9f3] text-[#4a3d6b] border border-[#c3b6d6]': entry.type === 'ADJUST',
                    }"
                  >{{ entry.type }}</span>
                  <!-- Description -->
                  <span class="text-[13px] text-ink-700">
                    <template v-if="entry.type === 'FIRED' && entry.training_session_label">
                      <router-link
                        :to="{ name: 'TrainingShow', params: { training_id: entry.training_session_id } }"
                        class="text-[#7d6320] underline-offset-2 hover:underline"
                      >{{ entry.training_session_label }}</router-link>
                    </template>
                    <template v-else-if="entry.type === 'FIRED'">Range session</template>
                    <template v-else-if="entry.type === 'BUY'">Purchase</template>
                    <template v-else>Adjustment</template>
                  </span>
                  <!-- Cost hint for purchases -->
                  <span v-if="entry.type === 'BUY' && entry.cost > 0" class="ml-auto font-mono text-[12px] text-muted">
                    ${{ entry.cost.toFixed(2) }}
                  </span>
                </div>

                <!-- Change -->
                <div
                  class="px-3 py-[11px] text-right font-mono text-[13px] font-medium"
                  :class="entry.rounds >= 0 ? 'text-[#2f7d57]' : 'text-[#b4452f]'"
                >
                  {{ entry.rounds >= 0 ? '+' : '' }}{{ entry.rounds.toLocaleString() }}
                </div>

                <!-- Balance -->
                <div class="px-4 py-[11px] text-right font-mono text-[13px] text-ink-700">
                  {{ entry.balance.toLocaleString() }}
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <p class="text-muted">Load not found.</p>
    </template>

    <AddStockModal
      v-if="stockOpen && ammo"
      :ammo="ammo"
      @close="stockOpen = false"
      @stocked="onStocked"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Camera, Plus, Pencil, ImageIcon } from 'lucide-vue-next'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
} from 'chart.js'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useInventoriesStore } from '@/stores/inventories'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import PageHeader from '@/components/PageHeader.vue'
import AddStockModal from '@/components/ammunition/AddStockModal.vue'
import dayjs from 'dayjs'

ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip)

const props = defineProps({
  ammunitionId: { type: Number, required: true },
})

const ammunitionStore = useAmmunitionStore()
const inventoriesStore = useInventoriesStore()

const ammo = ref(null)
const loading = ref(true)
const stockOpen = ref(false)
const ledgerEntries = ref([])
const ledgerLoading = ref(true)
const ledgerTypeFilter = ref('ALL')
const ledgerReversed = ref(false)

const monthLabels = Array.from({ length: 12 }, (_, i) =>
  dayjs().subtract(11 - i, 'month').format('MMM').charAt(0)
)

const onHandByMonth = computed(() =>
  Array.from({ length: 12 }, (_, i) => {
    const endOfMonth = dayjs().subtract(11 - i, 'month').endOf('month')
    const balance = ledgerEntries.value
      .filter((e) => dayjs(e.inventory_date).isBefore(endOfMonth) || dayjs(e.inventory_date).isSame(endOfMonth, 'day'))
      .reduce((sum, e) => sum + e.rounds, 0)
    return Math.max(0, balance)
  })
)

const costPerRoundByMonth = computed(() =>
  Array.from({ length: 12 }, (_, i) => {
    const monthStr = dayjs().subtract(11 - i, 'month').format('YYYY-MM')
    const buys = ledgerEntries.value.filter(
      (e) => e.type === 'BUY' && e.cost > 0 && dayjs(e.inventory_date).format('YYYY-MM') === monthStr,
    )
    if (!buys.length) return 0
    const totalCost = buys.reduce((sum, e) => sum + e.cost, 0)
    const totalRounds = buys.reduce((sum, e) => sum + e.rounds, 0)
    return totalRounds > 0 ? totalCost / totalRounds : 0
  })
)

const onHandChartData = computed(() => ({
  labels: monthLabels,
  datasets: [
    {
      data: onHandByMonth.value,
      backgroundColor: monthLabels.map((_, i) => (i === 11 ? '#c2a14d' : '#dcdee0')),
      borderRadius: 2,
    },
  ],
}))

const costChartData = computed(() => ({
  labels: monthLabels,
  datasets: [
    {
      data: costPerRoundByMonth.value,
      backgroundColor: monthLabels.map((_, i) => (i === 11 ? '#c2a14d' : '#d3d6d9')),
      borderRadius: 2,
    },
  ],
}))

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { enabled: false } },
  scales: {
    x: { display: false },
    y: { display: false },
  },
}

const hasAnySpec = computed(
  () =>
    ammo.value?.purpose ||
    ammo.value?.weight ||
    ammo.value?.bullet_type ||
    ammo.value?.ammunition_casing ||
    ammo.value?.primer_type ||
    ammo.value?.ammunition_condition
)

// Ledger with running balance — entries come sorted desc from API; we compute balance ascending then re-reverse
const ledgerWithBalance = computed(() => {
  const chronological = [...ledgerEntries.value].reverse()
  let balance = 0
  const withBalance = chronological.map((entry) => {
    balance += entry.rounds
    return { ...entry, balance }
  })
  return withBalance.reverse()
})

const filteredLedger = computed(() => {
  let rows = ledgerWithBalance.value
  if (ledgerTypeFilter.value !== 'ALL') {
    rows = rows.filter((r) => r.type === ledgerTypeFilter.value)
  }
  return ledgerReversed.value ? [...rows].reverse() : rows
})

const avgCostPerRound = computed(() => {
  const purchases = ledgerEntries.value.filter((e) => e.type === 'BUY' && e.cost > 0)
  if (!purchases.length) return null
  const totalCost = purchases.reduce((sum, e) => sum + e.cost, 0)
  const totalRounds = purchases.reduce((sum, e) => sum + e.rounds, 0)
  if (totalRounds <= 0) return null
  return '$' + (totalCost / totalRounds).toFixed(4)
})

const estimatedValue = computed(() => {
  const purchases = ledgerEntries.value.filter((e) => e.type === 'BUY' && e.cost > 0)
  if (!purchases.length || !ammo.value) return null
  const totalCost = purchases.reduce((sum, e) => sum + e.cost, 0)
  const totalRounds = purchases.reduce((sum, e) => sum + e.rounds, 0)
  if (totalRounds <= 0) return null
  const cpr = totalCost / totalRounds
  return '$' + (cpr * ammo.value.on_hand).toFixed(2)
})

const reorderFillPct = computed(() => {
  if (!ammo.value?.reorder_target) return 0
  return Math.min(100, Math.round((ammo.value.on_hand / ammo.value.reorder_target) * 100))
})

const reorderMinPct = computed(() => {
  if (!ammo.value?.reorder_target || !ammo.value?.reorder_min) return 0
  return Math.min(100, Math.round((ammo.value.reorder_min / ammo.value.reorder_target) * 100))
})

const reorderStatus = computed(() => {
  if (!ammo.value) return ''
  const { on_hand, reorder_min } = ammo.value
  if (reorder_min != null && on_hand <= reorder_min) return 'needs reorder'
  return 'comfortably stocked'
})

const reorderStatusColor = computed(() => {
  if (reorderStatus.value === 'needs reorder') return 'text-[#b4452f]'
  return 'text-[#2f7d57]'
})

onMounted(async () => {
  try {
    const [ammoResp, ledgerResp] = await Promise.all([
      ammunitionStore.fetchOne(props.ammunitionId),
      inventoriesStore.fetchForAmmo(props.ammunitionId),
    ])
    ammo.value = ammoResp.data
    ledgerEntries.value = ledgerResp.data ?? []
  } finally {
    loading.value = false
    ledgerLoading.value = false
  }
})

function onStocked({ rounds }) {
  if (ammo.value) ammo.value.on_hand += rounds
  stockOpen.value = false
  // Reload ledger to pick up new entry
  inventoriesStore.fetchForAmmo(props.ammunitionId).then((resp) => {
    ledgerEntries.value = resp.data ?? []
  })
}
</script>
