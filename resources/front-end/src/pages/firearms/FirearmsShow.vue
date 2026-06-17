<template>
  <div v-if="isLoading" class="mx-auto max-w-[1280px] px-8 py-6">
    <div class="h-8 w-48 animate-pulse rounded bg-ink-100" />
    <div class="mt-5 h-10 w-64 animate-pulse rounded bg-ink-100" />
  </div>

  <div v-else class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <AppBreadcrumb
      :crumbs="[{ label: 'Home', to: '/' }, { label: 'Firearms', to: { name: 'FirearmsIndex' } }, { label: firearm.label }]"
      class="mb-[18px]"
    />

    <!-- Header -->
    <div class="mb-[22px] flex flex-wrap items-center gap-4">
      <div>
        <h1 class="font-display text-[30px] font-bold leading-none tracking-[-0.02em]">{{ firearm.label }}</h1>
        <p class="mt-[3px] text-[15px] text-[#6b7077]">{{ subtitle }}</p>
      </div>
      <div class="ml-auto flex items-center gap-2.5">
        <router-link
          :to="{ name: 'FirearmsEdit', params: { firearm_id: firearmId } }"
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-surface px-[14px] py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-ink-50"
        >
          <Pencil class="h-[15px] w-[15px]" />
          Edit
        </router-link>
        <button
          class="inline-flex cursor-not-allowed items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 opacity-60"
          title="Training log coming soon"
        >
          <Plus class="h-4 w-4" />
          Log
          <ChevronDown class="h-[14px] w-[14px]" />
        </button>
      </div>
    </div>

    <!-- Two-column layout -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[344px_1fr] lg:items-start">

      <!-- ── Left rail ── -->
      <div class="flex flex-col gap-4">

        <!-- Photo card -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="relative h-[208px] w-full bg-ink-100">
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
              v-if="firearm.pictures?.length"
              class="absolute bottom-2.5 right-2.5 inline-flex cursor-pointer items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
            >
              <Camera class="h-[13px] w-[13px]" />
              {{ firearm.pictures.length }} photos · manage
            </span>
          </div>
          <!-- Thumbnail strip -->
          <div class="grid grid-cols-4 gap-1.5 p-1.5">
            <div
              v-for="n in 3"
              :key="n"
              class="h-[54px] rounded border border-line bg-ink-50"
            />
            <div class="flex h-[54px] cursor-pointer items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50">
              <Plus class="h-4 w-4" />
            </div>
          </div>
        </div>

        <!-- Spec card -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-baseline justify-between border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]">
            <span class="text-[14px] text-[#6b7077]">Rounds fired</span>
            <span class="font-mono text-[30px] font-medium leading-none">{{ formatQuantity(firearm.rounds_fired ?? 0) }}</span>
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
                >{{ cal.label }}</span>
                <span v-if="!firearm.calibers?.length" class="text-[14px] text-muted">—</span>
              </div>
            </div>
            <!-- Serial -->
            <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
              <span class="text-[14px] text-[#6b7077]">Serial #</span>
              <span class="font-mono text-[14px]">{{ firearm.serial ?? '—' }}</span>
            </div>
            <!-- Purchased -->
            <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
              <span class="text-[14px] text-[#6b7077]">Purchased</span>
              <span class="text-[14px]">{{ purchaseDisplay ?? '—' }}</span>
            </div>
            <!-- Purchase store -->
            <div v-if="firearm.purchase_store" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
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

        <!-- Accessories stub -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-center justify-between border-b border-[#eef0f1] px-4 py-[13px]">
            <span class="font-display text-[16px] font-semibold">Accessories</span>
            <button class="inline-flex cursor-not-allowed items-center gap-[5px] text-[13px] font-semibold text-brass-800 opacity-50" title="Coming soon">
              <Plus class="h-[14px] w-[14px]" />
              Mount
            </button>
          </div>
          <div class="flex flex-col items-center justify-center px-4 py-8 text-center">
            <p class="text-[14px] font-medium text-ink-700">No accessories mounted</p>
            <p class="mt-1 text-[13px] text-muted">Accessories will be available in a future update</p>
          </div>
        </div>
      </div>

      <!-- ── Activity stub ── -->
      <div class="overflow-hidden rounded border border-line bg-surface">
        <div class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] px-[18px] py-4">
          <span class="font-display text-[18px] font-semibold">Activity</span>
          <span class="font-mono text-[11px] tracking-[0.04em] text-muted">NO SESSIONS YET</span>
        </div>
        <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
          <p class="text-[15px] font-medium text-ink-700">No sessions logged yet</p>
          <p class="mt-1.5 max-w-[280px] text-[14px] text-muted">Range sessions, cleaning, and accessory mounts will appear here once you start logging activity.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import dayjs from 'dayjs'
import numeral from 'numeral'
import { Camera, ChevronDown, MapPin, Pencil, Plus } from 'lucide-vue-next'
import { useFirearmsStore } from '@/stores/firearms'
import { useNumbers } from '@/composables/useNumbers'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'

const props = defineProps({
  firearmId: { type: Number, required: true },
})

const firearmsStore = useFirearmsStore()
const { formatQuantity } = useNumbers()

const firearm = ref({})
const isLoading = ref(true)

const primaryPhoto = computed(() => firearm.value.primary_photo_url ?? null)

const subtitle = computed(() => {
  const parts = [firearm.value.manufacturer, firearm.value.model].filter(Boolean).join(' ')
  const cals = (firearm.value.calibers ?? []).map(c => c.label).join(', ')
  return cals ? `${parts} · ${cals}` : parts
})

const purchaseDisplay = computed(() => {
  const { purchase_date, purchase_price } = firearm.value
  if (!purchase_date && !purchase_price) return null
  const date = purchase_date ? dayjs(purchase_date).format('MMM YYYY') : null
  const price = purchase_price ? numeral(purchase_price).format('$0,0[.]00') : null
  return [date, price].filter(Boolean).join(' · ')
})

onMounted(async () => {
  try {
    const { data } = await firearmsStore.fetchOne(props.firearmId)
    firearm.value = data
  } finally {
    isLoading.value = false
  }
})
</script>
