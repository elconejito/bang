<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import { useMagazinesStore } from '@/stores/magazines'
import dayjs from 'dayjs'

const props = defineProps({
  magazineId: { type: Number, required: true },
})

const router = useRouter()
const magazinesStore = useMagazinesStore()

const magazine = ref(null)
const loading = ref(true)

onMounted(async () => {
  const { data } = await magazinesStore.fetchOne(props.magazineId)
  magazine.value = data
  loading.value = false
})

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: magazine.value?.model_name ?? magazine.value?.label ?? '…' },
])

const statusConfig = computed(() => {
  const s = magazine.value?.status
  if (s === 'in_gun') return { label: 'In gun', mono: 'IN GUN', green: true }
  if (s === 'loaded') return { label: 'Loaded', mono: 'LOADED', green: false, brass: true }
  return { label: 'Empty', mono: 'EMPTY', green: false, brass: false }
})

const caliberLabel = computed(() =>
  magazine.value?.calibers?.map((c) => c.label).join(' / ') ?? null,
)
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div v-if="loading" class="py-12 text-center text-sm text-muted">Loading…</div>

    <template v-else-if="magazine">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2.5 mb-1">
            <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">
              {{ magazine.model_name ?? magazine.label }}
            </h1>
            <span class="font-mono text-[11px] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px]">
              MAGAZINE
            </span>
          </div>
          <div class="text-[15px] text-[#6b7077]">
            {{ magazine.manufacturer }}
            <template v-if="caliberLabel"> · {{ caliberLabel }}</template>
            · {{ magazine.capacity }} rd
          </div>
        </div>
        <div class="flex items-center gap-2.5 ml-auto">
          <router-link
            :to="{ name: 'MagazinesEdit', params: { magazine_id: magazine.id } }"
            class="inline-flex items-center gap-1.5 bg-white text-[#1a1c1f] font-semibold text-[14px] px-[14px] py-2 rounded border border-[#c2c6ca] hover:bg-[#f5f6f7] transition-colors"
          >
            <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            Edit
          </router-link>
        </div>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">

        <!-- Left rail -->
        <div class="flex flex-col gap-4">

          <!-- Photo stub -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="h-[150px] bg-[#f5f6f7] flex items-center justify-center text-muted text-[13px]">No photo</div>
          </div>

          <!-- Status card -->
          <div
            :class="statusConfig.green
              ? 'bg-[#e7f1eb] border-[#9ccbb1]'
              : statusConfig.brass
                ? 'bg-[#fdf6e7] border-[#e3d3a3]'
                : 'bg-[#f5f6f7] border-[#c2c6ca]'"
            class="border rounded-sm p-[13px_16px] flex items-center gap-3"
          >
            <div
              :class="statusConfig.green
                ? 'border-[#9ccbb1] text-[#2f7d57]'
                : statusConfig.brass
                  ? 'border-[#e3d3a3] text-[#7d6320]'
                  : 'border-[#c2c6ca] text-[#5b6066]'"
              class="w-9 h-9 rounded-sm bg-white border flex items-center justify-center flex-none"
            >
              <!-- in_gun -->
              <svg v-if="statusConfig.green" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
              <!-- loaded -->
              <svg v-else-if="statusConfig.brass" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>
              <!-- empty -->
              <svg v-else class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div
                :class="statusConfig.green ? 'text-[#2f7d57]' : statusConfig.brass ? 'text-[#7d6320]' : 'text-[#5b6066]'"
                class="font-mono text-[10px] tracking-[0.06em]"
              >
                {{ statusConfig.mono }}
              </div>
              <div class="text-[16px] font-semibold">{{ statusConfig.label }}</div>
            </div>
          </div>

          <!-- Specs -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Specs</div>
            <div class="px-4 py-1.5">
              <div v-if="magazine.id_marking" class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]">
                <span class="text-[14px] text-[#6b7077]">ID marking</span>
                <span class="font-mono text-[14px]">{{ magazine.id_marking }}</span>
              </div>
              <div class="flex items-center justify-between py-[9px]" :class="magazine.serial_number ? 'border-b border-[#f1f2f3]' : ''">
                <span class="text-[14px] text-[#6b7077]">Capacity</span>
                <span class="text-[14px]">{{ magazine.capacity }} rounds</span>
              </div>
              <div v-if="magazine.serial_number" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Serial #</span>
                <span class="font-mono text-[14px]">{{ magazine.serial_number }}</span>
              </div>
            </div>
          </div>

          <!-- Compatible firearms -->
          <div v-if="magazine.firearms?.length" class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Compatible with</div>
            <div class="px-4 py-1.5">
              <div
                v-for="(firearm, i) in magazine.firearms"
                :key="firearm.id"
                class="flex items-center justify-between py-[9px]"
                :class="i < magazine.firearms.length - 1 ? 'border-b border-[#f1f2f3]' : ''"
              >
                <span class="text-[14px]">{{ firearm.manufacturer }} {{ firearm.label }}</span>
                <router-link
                  :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
                  class="text-[13px] text-brass font-semibold hover:underline"
                >
                  View ›
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: history stub -->
        <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
          <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
            <span class="font-display font-semibold text-[18px]">History</span>
            <span class="font-mono text-[11px] text-muted tracking-[0.04em]">LOADS · MOVES</span>
          </div>
          <div class="px-[18px] py-12 text-center text-muted text-[14px]">
            History timeline coming soon.
          </div>
        </div>

      </div>
    </template>
  </div>
</template>
