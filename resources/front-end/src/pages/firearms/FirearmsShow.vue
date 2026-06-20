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
          <router-link :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }" class="block">
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
              class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
            >
              <img :src="url" class="h-full w-full object-cover" alt="" />
            </router-link>
            <!-- Fill remaining slots with placeholders up to 3 -->
            <router-link
              v-for="n in Math.max(0, 3 - firearm.thumbnail_urls.length)"
              :key="`ph-${n}`"
              :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }"
              class="h-[54px] rounded border border-line bg-ink-50 block"
            />
            <router-link
              :to="{ name: 'FirearmGallery', params: { firearm_id: firearmId } }"
              class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
            >
              <Plus class="h-4 w-4" />
            </router-link>
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

      <!-- ── Activity feed ── -->
      <div class="overflow-hidden rounded border border-line bg-surface">
        <!-- Header -->
        <div class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] px-[18px] py-4">
          <span class="font-display text-[18px] font-semibold">Activity</span>
          <span v-if="activityMeta.range_count" class="font-mono text-[11px] tracking-[0.04em] text-muted">
            {{ activityMeta.range_count }} {{ activityMeta.range_count === 1 ? 'SESSION' : 'SESSIONS' }}{{ lastShotLabel ? ' · ' + lastShotLabel : '' }}
          </span>
          <div v-if="activity.length" class="ml-auto flex items-center gap-2">
            <button
              class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-[11px] py-[6px] text-[13px] text-ink-700 transition-colors hover:bg-[#f5f6f7]"
              @click="cycleTypeFilter"
            >
              <ListFilter class="h-[14px] w-[14px] text-muted" />
              {{ activityTypeFilter === 'ALL' ? 'All' : activityTypeFilter }}
              <ChevronDown class="h-[13px] w-[13px] text-muted" />
            </button>
            <button
              class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-[11px] py-[6px] text-[13px] text-ink-700 transition-colors hover:bg-[#f5f6f7]"
              @click="activityReversed = !activityReversed"
            >
              <ArrowUpDown class="h-[14px] w-[14px] text-muted" />
              {{ activityReversed ? 'Oldest' : 'Newest' }}
            </button>
          </div>
        </div>

        <!-- Timeline -->
        <div v-if="filteredActivity.length" class="px-[18px] pb-2 pt-5">
          <div
            v-for="(entry, i) in visibleActivity"
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
                v-if="i < visibleActivity.length - 1"
                class="my-1 w-0.5 flex-1 bg-[#eef0f1]"
                style="min-height: 16px"
              />
            </div>

            <!-- Content -->
            <div class="flex-1" :class="i < visibleActivity.length - 1 ? 'pb-5' : 'pb-0'">
              <div class="flex items-center gap-[9px]">
                <span
                  class="shrink-0 rounded border font-mono text-[10px] tracking-[0.05em]"
                  style="padding: 1px 6px"
                  :class="typeBadgeClass(entry.type)"
                >{{ entry.type }}</span>
                <router-link
                  v-if="entry.session_id"
                  :to="{ name: 'TrainingShow', params: { training_id: entry.session_id } }"
                  class="min-w-0 flex-1 text-[16px] font-medium hover:text-brass-800"
                >{{ entry.title }}</router-link>
                <span v-else class="min-w-0 flex-1 text-[16px] font-medium">{{ entry.title }}</span>
                <span class="ml-auto shrink-0 font-mono text-[12px] text-muted">{{ formatActivityDate(entry.date) }}</span>
              </div>
              <div v-if="entry.subtitle" class="mt-1 text-[14px] text-[#6b7077]">{{ entry.subtitle }}</div>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-else-if="!isLoadingActivity" class="flex flex-col items-center justify-center px-6 py-16 text-center">
          <p class="text-[15px] font-medium text-ink-700">No sessions logged yet</p>
          <p class="mt-1.5 max-w-[280px] text-[14px] text-muted">Range sessions, cleaning, and accessory mounts will appear here once you start logging activity.</p>
        </div>

        <!-- View all footer -->
        <div
          v-if="filteredActivity.length > ACTIVITY_LIMIT && !showAllActivity"
          class="border-t border-[#eef0f1] px-[18px] py-[13px] text-center"
        >
          <button class="text-[14px] font-semibold text-brass-800" @click="showAllActivity = true">
            View all {{ filteredActivity.length }} entries
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import dayjs from 'dayjs'
import numeral from 'numeral'
import { ArrowLeftRight, ArrowUpDown, Camera, ChevronDown, ListFilter, MapPin, Pencil, Plus, Target } from 'lucide-vue-next'
import { useFirearmsStore } from '@/stores/firearms'
import { useNumbers } from '@/composables/useNumbers'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'

const ACTIVITY_LIMIT = 10

const props = defineProps({
  firearmId: { type: Number, required: true },
})

const firearmsStore = useFirearmsStore()
const { formatQuantity } = useNumbers()

const firearm = ref({})
const isLoading = ref(true)

const activity = ref([])
const activityMeta = ref({ total: 0, range_count: 0, last_session_date: null })
const isLoadingActivity = ref(true)
const activityTypeFilter = ref('ALL')
const activityReversed = ref(false)
const showAllActivity = ref(false)

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

const lastShotLabel = computed(() => {
  if (!activityMeta.value.last_session_date) return null
  return 'LAST SHOT ' + dayjs(activityMeta.value.last_session_date).format('MMM D').toUpperCase()
})

const filteredActivity = computed(() => {
  const list = activityTypeFilter.value === 'ALL'
    ? activity.value
    : activity.value.filter(e => e.type === activityTypeFilter.value)
  return activityReversed.value ? [...list].reverse() : list
})

const visibleActivity = computed(() =>
  showAllActivity.value ? filteredActivity.value : filteredActivity.value.slice(0, ACTIVITY_LIMIT),
)

function cycleTypeFilter() {
  const types = ['ALL', 'RANGE', 'MOUNT']
  const idx = types.indexOf(activityTypeFilter.value)
  activityTypeFilter.value = types[(idx + 1) % types.length]
}

function typeIconClass(type) {
  if (type === 'RANGE') return 'bg-[#f4ecd6] border-[#e3d3a3] text-[#7d6320]'
  if (type === 'MOUNT') return 'bg-[#eee9f3] border-[#ddd4ea] text-[#6b5a8c]'
  return 'bg-[#f5f6f7] border-[#e2e4e6] text-[#5b6066]'
}

function typeBadgeClass(type) {
  if (type === 'RANGE') return 'bg-[#f4ecd6] border-[#e3d3a3] text-[#7d6320]'
  if (type === 'MOUNT') return 'bg-[#eee9f3] border-[#c3b6d6] text-[#6b5a8c]'
  return 'bg-[#f5f6f7] border-[#c2c6ca] text-[#5b6066]'
}

function formatActivityDate(dateStr) {
  return dayjs(dateStr).format('MMM D')
}

onMounted(async () => {
  const [firearmRes, activityRes] = await Promise.allSettled([
    firearmsStore.fetchOne(props.firearmId),
    firearmsStore.fetchActivity(props.firearmId),
  ])

  if (firearmRes.status === 'fulfilled') {
    firearm.value = firearmRes.value.data
  }
  if (activityRes.status === 'fulfilled') {
    activity.value = activityRes.value.data
    activityMeta.value = activityRes.value.meta
  }

  isLoading.value = false
  isLoadingActivity.value = false
})
</script>
