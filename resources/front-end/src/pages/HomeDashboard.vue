<template>
  <!-- Loading skeleton -->
  <div v-if="loading" class="mx-auto max-w-[1280px] px-8 py-7 pb-16">
    <div class="mb-6 flex items-end gap-4">
      <div>
        <div class="h-9 w-64 animate-pulse rounded bg-ink-100" />
        <div class="mt-2 h-5 w-80 animate-pulse rounded bg-ink-100" />
      </div>
    </div>
    <div class="mb-6 grid grid-cols-5 overflow-hidden rounded border border-line bg-surface">
      <div v-for="n in 5" :key="n" class="border-r border-line p-4 last:border-r-0">
        <div class="h-8 w-16 animate-pulse rounded bg-ink-100" />
        <div class="mt-2 h-3 w-20 animate-pulse rounded bg-ink-100" />
      </div>
    </div>
    <div class="grid grid-cols-[1.45fr_1fr] gap-5">
      <div class="flex flex-col gap-5">
        <div class="h-32 animate-pulse rounded border border-line bg-surface" />
        <div class="h-64 animate-pulse rounded border border-line bg-surface" />
      </div>
      <div class="flex flex-col gap-5">
        <div class="h-64 animate-pulse rounded border border-line bg-surface" />
        <div class="h-48 animate-pulse rounded border border-line bg-surface" />
      </div>
    </div>
  </div>

  <!-- Dashboard content -->
  <div v-else class="mx-auto max-w-[1280px] px-8 py-7 pb-16">
    <!-- Greeting + CTAs -->
    <div class="mb-6 flex flex-wrap items-end gap-4">
      <div>
        <h1 class="font-display text-[32px] font-bold tracking-[-0.025em]">
          {{ greetingWord }}, {{ userName }}.
        </h1>
        <div class="mt-[6px] flex items-center gap-2.5 text-[15px] text-ink-500">
          <span
            v-if="data.stats.days_since_last_session !== null"
            class="inline-flex items-center gap-1.5"
          >
            <Clock class="h-[15px] w-[15px] text-muted" />
            Last range trip
            <b class="font-semibold text-ink-700">{{ data.stats.days_since_last_session }} days</b>
            ago
          </span>
          <span
            v-if="data.stats.days_since_last_session !== null && attentionCount > 0"
            class="h-[3px] w-[3px] rounded-full bg-ink-300"
          />
          <span v-if="attentionCount > 0" class="inline-flex items-center gap-1.5 text-caution">
            <TriangleAlert class="h-[15px] w-[15px]" />
            <b class="font-semibold"
              >{{ attentionCount }} {{ attentionCount === 1 ? 'thing' : 'things' }}</b
            >
            need attention
          </span>
        </div>
      </div>
    </div>

    <!-- Stats strip -->
    <div class="mb-6 grid grid-cols-5 overflow-hidden rounded border border-line bg-surface">
      <div class="border-r border-line p-4">
        <div class="font-mono text-[30px] font-medium leading-none tracking-[-0.01em]">
          {{ data.stats.firearms_count }}
        </div>
        <div class="mt-[6px] font-mono text-[10px] tracking-[0.08em] text-muted">FIREARMS</div>
      </div>
      <div class="relative border-r border-line p-4">
        <div
          class="font-mono text-[30px] font-medium leading-none tracking-[-0.01em] text-brass-800"
        >
          {{ formatNumber(data.stats.rounds_on_hand) }}
        </div>
        <div class="mt-[6px] font-mono text-[10px] tracking-[0.08em] text-muted">RNDS ON HAND</div>
        <div class="absolute bottom-0 left-0 h-0.5 w-full bg-brass" />
      </div>
      <div class="border-r border-line p-4">
        <div class="font-mono text-[30px] font-medium leading-none tracking-[-0.01em]">
          {{ formatNumber(data.stats.rounds_fired_12mo) }}
        </div>
        <div class="mt-[6px] font-mono text-[10px] tracking-[0.08em] text-muted">
          RNDS FIRED · 12 MO
        </div>
      </div>
      <div class="border-r border-line p-4">
        <div class="font-mono text-[30px] font-medium leading-none tracking-[-0.01em]">
          {{ data.stats.sessions_12mo }}
        </div>
        <div class="mt-[6px] font-mono text-[10px] tracking-[0.08em] text-muted">
          SESSIONS · 12 MO
        </div>
      </div>
      <div class="p-4">
        <div class="font-mono text-[30px] font-medium leading-none tracking-[-0.01em]">
          {{ formatCurrency(data.stats.ammo_cost_12mo) }}
        </div>
        <div class="mt-[6px] font-mono text-[10px] tracking-[0.08em] text-muted">
          AMMO COST · 12 MO
        </div>
      </div>
    </div>

    <!-- Two-column layout -->
    <div class="grid grid-cols-[1.45fr_1fr] items-start gap-5">
      <!-- Left column -->
      <div class="flex flex-col gap-5">
        <!-- All clear -->
        <div
          v-if="attentionCount === 0"
          class="overflow-hidden rounded border border-line bg-surface"
        >
          <div class="flex items-center gap-3 border-l-[3px] border-success px-[22px] py-[26px]">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-success-border bg-success-bg text-success"
            >
              <CircleCheck class="h-5 w-5" />
            </div>
            <div>
              <div class="font-display text-[18px] font-semibold">All clear</div>
              <div class="text-[14px] text-ink-500">
                Stock is healthy, nothing overdue, no pending paperwork.
              </div>
            </div>
          </div>
        </div>

        <!-- Needs attention -->
        <div v-else class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-center justify-between border-b border-line px-[18px] py-[14px]">
            <div class="flex items-center gap-2.5">
              <TriangleAlert class="h-[18px] w-[18px] text-caution" />
              <span class="font-display text-[18px] font-semibold">Needs attention</span>
            </div>
            <span
              class="rounded border border-line px-2 py-0.5 font-mono text-[11px] tracking-[0.04em] text-muted"
            >
              {{ attentionCount }} ITEM{{ attentionCount !== 1 ? 'S' : '' }}
            </span>
          </div>

          <!-- Low stock row -->
          <div
            v-if="data.low_stock_ammo.length > 0"
            class="flex cursor-pointer items-center gap-3.5 border-b border-line px-[18px] py-[14px] hover:bg-ink-50"
            :class="data.pending_nfa.length === 0 ? 'border-b-0' : ''"
            @click="$router.push({ name: 'AmmoIndex' })"
          >
            <div
              class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded border border-caution-border bg-caution-bg text-caution"
            >
              <TriangleAlert class="h-[19px] w-[19px]" />
            </div>
            <div class="flex-1">
              <div class="text-[16px] font-medium">
                {{ data.low_stock_ammo.length }} ammo load{{
                  data.low_stock_ammo.length !== 1 ? 's' : ''
                }}
                low on stock
              </div>
              <div class="text-[13px] text-ink-500">{{ lowStockSummary }}</div>
            </div>
            <span
              class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line bg-surface px-3 py-1.5 text-[13px] font-semibold text-ink-900 hover:bg-ink-50"
            >
              Review<ChevronRight class="h-[13px] w-[13px] text-muted" />
            </span>
          </div>

          <!-- Pending NFA rows -->
          <div
            v-for="(nfa, i) in data.pending_nfa"
            :key="nfa.id"
            class="flex cursor-pointer items-center gap-3.5 px-[18px] py-[14px] hover:bg-ink-50"
            :class="i < data.pending_nfa.length - 1 ? 'border-b border-line' : ''"
            @click="$router.push({ name: 'AccessoriesIndex' })"
          >
            <div
              class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded border border-special-border bg-special-bg text-special"
            >
              <Hourglass class="h-[19px] w-[19px]" />
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <span class="text-[16px] font-medium">{{ nfa.label }}</span>
                <span
                  class="rounded border border-special-border bg-special-bg px-1.5 py-0.5 font-mono text-[10px] tracking-[0.04em] text-special"
                  >NFA</span
                >
              </div>
              <div class="text-[13px] text-ink-500">
                {{ nfa.form_type ?? 'Form 4' }} pending{{
                  nfa.submitted_at ? ' · submitted ' + formatRelativeDate(nfa.submitted_at) : ''
                }}
              </div>
            </div>
            <span
              class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line bg-surface px-3 py-1.5 text-[13px] font-semibold text-ink-900 hover:bg-ink-50"
            >
              View<ChevronRight class="h-[13px] w-[13px] text-muted" />
            </span>
          </div>
        </div>

        <!-- Recent activity -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-center justify-between border-b border-line px-[18px] py-[14px]">
            <span class="font-display text-[18px] font-semibold">Recent activity</span>
            <router-link
              :to="{ name: 'TrainingIndex' }"
              class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800"
            >
              View all<ChevronRight class="h-[13px] w-[13px]" />
            </router-link>
          </div>
          <div
            v-if="data.recent_activity.length === 0"
            class="px-[18px] py-6 text-center text-[14px] text-muted"
          >
            No activity yet. Log your first session!
          </div>
          <div
            v-for="(item, i) in data.recent_activity"
            :key="`${item.type}-${item.id}`"
            class="flex items-center gap-3 px-[18px] py-3"
            :class="i < data.recent_activity.length - 1 ? 'border-b border-line' : ''"
          >
            <span
              class="w-[58px] shrink-0 rounded border py-0.5 text-center font-mono text-[10px] tracking-[0.04em]"
              :class="activityTagClass(item.type)"
              >{{ activityTagLabel(item.type) }}</span
            >
            <span class="flex-1 text-[15px]">{{ item.label }}</span>
            <span class="font-mono text-[12px] text-muted">{{ formatDate(item.date) }}</span>
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="flex flex-col gap-5">
        <!-- Ammo on hand by caliber -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-center justify-between border-b border-line px-[18px] py-[14px]">
            <span class="font-display text-[18px] font-semibold">Ammo on hand</span>
            <router-link
              :to="{ name: 'AmmoIndex' }"
              class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800"
            >
              Ammo<ChevronRight class="h-[13px] w-[13px]" />
            </router-link>
          </div>
          <div
            v-if="data.ammo_by_caliber.length === 0"
            class="px-[18px] py-6 text-center text-[14px] text-muted"
          >
            No ammo on hand.
          </div>
          <div v-else class="flex flex-col gap-[15px] px-[18px] py-4">
            <div v-for="caliber in data.ammo_by_caliber" :key="caliber.caliber_id">
              <div class="mb-[6px] flex items-baseline justify-between text-[15px]">
                <span class="flex items-center gap-1.5">
                  <router-link
                    :to="{ name: 'AmmoIndex', query: { caliber_id: caliber.caliber_id } }"
                    class="cursor-pointer font-medium text-brass-800 underline decoration-dotted underline-offset-[3px]"
                    >{{ caliber.caliber_label }}</router-link
                  >
                  <span
                    v-if="caliber.is_low"
                    class="rounded border border-caution-border bg-caution-bg px-1.5 py-0.5 font-mono text-[10px] tracking-[0.04em] text-caution"
                    >LOW</span
                  >
                </span>
                <span class="font-mono" :class="caliber.is_low ? 'text-caution' : 'text-ink-700'">
                  {{ formatNumber(caliber.on_hand) }}
                </span>
              </div>
              <div class="h-2 overflow-hidden rounded-full bg-canvas">
                <div
                  class="h-full rounded-full"
                  :class="caliber.is_low ? 'bg-caution' : 'bg-brass'"
                  :style="{ width: caliber.bar_pct + '%' }"
                />
              </div>
            </div>
          </div>
          <div
            class="flex items-center justify-between border-t border-line bg-ink-50 px-[18px] py-[11px]"
          >
            <span class="font-mono text-[10px] tracking-[0.08em] text-muted">TOTAL ON HAND</span>
            <span class="font-mono text-[17px] font-medium">{{
              formatNumber(data.stats.rounds_on_hand)
            }}</span>
          </div>
        </div>

        <!-- Most shot firearms -->
        <div class="overflow-hidden rounded border border-line bg-surface">
          <div class="flex items-center justify-between border-b border-line px-[18px] py-[14px]">
            <span class="font-display text-[18px] font-semibold">
              Most shot
              <span class="ml-2 font-mono text-[11px] font-normal tracking-[0.06em] text-muted"
                >12 MO</span
              >
            </span>
            <router-link
              :to="{ name: 'FirearmsIndex' }"
              class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800"
            >
              Firearms<ChevronRight class="h-[13px] w-[13px]" />
            </router-link>
          </div>
          <div
            v-if="data.most_shot_firearms.length === 0"
            class="px-[18px] py-6 text-center text-[14px] text-muted"
          >
            No range data yet.
          </div>
          <router-link
            v-for="(firearm, i) in data.most_shot_firearms"
            :key="firearm.id"
            :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
            class="flex items-center gap-3.5 px-[18px] py-3 transition-colors hover:bg-ink-50"
            :class="i < data.most_shot_firearms.length - 1 ? 'border-b border-line' : ''"
          >
            <div
              class="h-[42px] w-[42px] shrink-0 overflow-hidden rounded border border-line bg-ink-50"
            >
              <img
                v-if="firearm.primary_photo_url"
                :src="firearm.primary_photo_url"
                :alt="firearm.label"
                class="h-full w-full object-cover"
              />
              <div v-else class="flex h-full w-full items-center justify-center">
                <Camera class="h-5 w-5 text-muted" />
              </div>
            </div>
            <div class="flex-1">
              <div class="font-display text-[16px] font-semibold leading-tight">
                {{ firearm.label }}
              </div>
              <div class="text-[12px] text-ink-500">
                {{ firearm.manufacturer }} · {{ firearm.model }}
              </div>
            </div>
            <div class="text-right">
              <div class="font-mono text-[17px] font-medium leading-none">
                {{ formatNumber(firearm.rounds_12mo) }}
              </div>
              <div class="mt-0.5 font-mono text-[9px] tracking-[0.06em] text-muted">RNDS</div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  Camera,
  ChevronRight,
  CircleCheck,
  Clock,
  Hourglass,
  TriangleAlert,
} from 'lucide-vue-next';
import { axiosInstance } from '@/plugins/axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const loading = ref(true);
const data = ref({
  stats: {
    firearms_count: 0,
    rounds_on_hand: 0,
    rounds_fired_12mo: 0,
    sessions_12mo: 0,
    ammo_cost_12mo: 0,
    days_since_last_session: null,
  },
  ammo_by_caliber: [],
  low_stock_ammo: [],
  pending_nfa: [],
  most_shot_firearms: [],
  recent_activity: [],
});

const hour = new Date().getHours();
const greetingWord = computed(() => {
  if (hour < 12) return 'Morning';
  if (hour < 18) return 'Afternoon';
  return 'Evening';
});

const userName = computed(() => {
  const name = authStore.currentUser?.name ?? '';
  return name.split(' ')[0];
});

const attentionCount = computed(
  () => data.value.low_stock_ammo.length + data.value.pending_nfa.length
);

const lowStockSummary = computed(() =>
  data.value.low_stock_ammo
    .slice(0, 3)
    .map((a) => a.label)
    .join(' · ')
);

function formatNumber(n) {
  return Number(n).toLocaleString('en-US');
}

function formatCurrency(n) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
  }).format(n);
}

function formatDate(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function formatRelativeDate(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  const now = new Date();
  const months = (now.getFullYear() - d.getFullYear()) * 12 + now.getMonth() - d.getMonth();
  if (months < 1) return 'recently';
  if (months === 1) return '1 month ago';
  return `${months} months ago`;
}

function activityTagClass(type) {
  switch (type) {
    case 'RANGE':
      return 'text-brass-800 border-brass-300 bg-brass-200';
    case 'STOCK':
      return 'text-success border-success-border bg-success-bg';
    case 'MOUNT':
    case 'UNMOUNT':
      return 'text-special border-special-border bg-special-bg';
    case 'CLEAN':
      return 'text-ink-700 border-line bg-ink-50';
    default:
      return 'text-ink-500 border-line bg-ink-50';
  }
}

function activityTagLabel(type) {
  return type === 'UNMOUNT' ? 'UNMNT' : type;
}

onMounted(async () => {
  try {
    const { data: res } = await axiosInstance.get('/dashboard');
    data.value = res.data;
  } catch (e) {
    console.error('Dashboard load failed', e);
  } finally {
    loading.value = false;
  }
});
</script>
