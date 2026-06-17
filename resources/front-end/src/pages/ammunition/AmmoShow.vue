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
          <!-- Photo placeholder -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex h-[150px] w-full items-center justify-center bg-ink-100">
              <ImageIcon class="h-8 w-8 text-ink-300" />
            </div>
          </div>

          <!-- On hand card -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex items-baseline justify-between border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]">
              <span class="text-[14px] text-muted">On hand</span>
              <span class="font-mono text-[30px] font-medium leading-none">{{ ammo.on_hand.toLocaleString() }}</span>
            </div>
            <div class="flex flex-col gap-[13px] px-4 py-[14px]">
              <div class="flex items-center justify-between border-t border-[#f1f2f3] pt-[11px]">
                <span class="text-[14px] text-muted">Avg cost / rd</span>
                <span class="font-mono text-[15px]">{{ avgCostPerRound ?? '—' }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-[14px] text-muted">Est. value</span>
                <span class="font-mono text-[15px]">{{ estimatedValue ?? '—' }}</span>
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

          <!-- Used by stub -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="border-b border-[#eef0f1] px-4 py-3 font-display text-[16px] font-semibold">Used by</div>
            <div class="px-4 py-3 text-[14px] text-muted">
              <!-- TODO: load firearms sharing this caliber -->
              <span class="italic">Firearm links coming soon.</span>
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
                <span class="font-mono text-[11px] text-muted">stub data</span>
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
                <span class="font-mono text-[11px] text-muted">stub data</span>
              </div>
              <div class="h-[80px]"><Bar :data="costChartData" :options="barOptions" /></div>
              <div class="mt-1.5 flex justify-between font-mono text-[9px] text-muted">
                <span v-for="m in monthLabels" :key="m">{{ m }}</span>
              </div>
            </div>
          </div>

          <!-- Ledger stub -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] px-[18px] py-[15px]">
              <span class="font-display text-[18px] font-semibold">Inventory &amp; usage</span>
            </div>
            <!-- header row -->
            <div class="grid grid-cols-[96px_1fr_96px_96px] border-b border-[#e2e4e6] bg-[#f5f6f7] font-mono text-[10px] uppercase tracking-[0.05em] text-muted">
              <div class="px-4 py-[10px]">Date</div>
              <div class="px-3 py-[10px]">Activity</div>
              <div class="px-3 py-[10px] text-right">Change</div>
              <div class="px-4 py-[10px] text-right">Balance</div>
            </div>
            <!-- Empty / stub -->
            <div class="px-[18px] py-8 text-center text-[14px] text-muted italic">
              Inventory history coming soon.
            </div>
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
import { Plus, Pencil, ImageIcon } from 'lucide-vue-next'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
} from 'chart.js'
import { useAmmunitionStore } from '@/stores/ammunition'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import PageHeader from '@/components/PageHeader.vue'
import AddStockModal from '@/components/ammunition/AddStockModal.vue'
import dayjs from 'dayjs'

ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip)

const props = defineProps({
  ammunitionId: { type: Number, required: true },
})

const ammunitionStore = useAmmunitionStore()

const ammo = ref(null)
const loading = ref(true)
const stockOpen = ref(false)

const monthLabels = Array.from({ length: 12 }, (_, i) =>
  dayjs().subtract(11 - i, 'month').format('MMM').charAt(0)
)

const onHandChartData = {
  labels: monthLabels,
  datasets: [
    {
      data: [240, 320, 280, 460, 400, 580, 500, 300, 180, 440, 620, 850],
      backgroundColor: monthLabels.map((_, i) => (i === 11 ? '#c2a14d' : '#dcdee0')),
      borderRadius: 2,
    },
  ],
}

const costChartData = {
  labels: monthLabels,
  datasets: [
    {
      data: [0.34, 0.33, 0.31, 0.29, 0.3, 0.31, 0.31, 0.32, 0.31, 0.3, 0.3, 0.3],
      backgroundColor: monthLabels.map((_, i) => (i === 11 ? '#c2a14d' : '#d3d6d9')),
      borderRadius: 2,
    },
  ],
}

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

const avgCostPerRound = computed(() => null) // TODO from inventory ledger
const estimatedValue = computed(() => null)  // TODO from inventory ledger

onMounted(async () => {
  try {
    const response = await ammunitionStore.fetchOne(props.ammunitionId)
    ammo.value = response.data
  } finally {
    loading.value = false
  }
})

function onStocked({ rounds }) {
  if (ammo.value) ammo.value.on_hand += rounds
  stockOpen.value = false
}
</script>
