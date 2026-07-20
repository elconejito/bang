<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import dayjs from 'dayjs';
import { ChevronDown, Plus, Search } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import TrainingCard from '@/components/training/TrainingCard.vue';
import { useTrainingStore } from '@/stores/training';
import { axiosInstance } from '@/plugins/axios';

const trainingStore = useTrainingStore();
const router = useRouter();
const route = useRoute();

const loading = ref(true);
const error = ref(null);
const sessions = ref([]);
const stats = ref(null);
const ranges = ref([]);

const search = ref('');
const activeRangeId = ref(route.query.range_id ? Number(route.query.range_id) : null);
const activeYear = ref(route.query.year ? Number(route.query.year) : null);
const perPage = ref(15);
const currentPage = ref(1);
const totalPages = ref(1);
const total = ref(0);
const openDropdown = ref(null);

const perPageOptions = [10, 15, 25, 50];

const crumbs = [{ label: 'Home', to: '/' }, { label: 'Training' }];

function availableYears() {
  const currentYear = new Date().getFullYear();
  return Array.from({ length: 5 }, (_, i) => currentYear - i);
}

async function fetchSessions() {
  loading.value = true;
  error.value = null;
  const params = {
    page: currentPage.value,
    per_page: perPage.value,
    ...(activeRangeId.value ? { 'filter[range_id]': activeRangeId.value } : {}),
    ...(activeYear.value ? { year: activeYear.value } : {}),
  };
  try {
    const res = await trainingStore.fetchAll(params);
    sessions.value = res.data;
    totalPages.value = res.meta?.last_page ?? 1;
    total.value = res.meta?.total ?? res.data.length;
  } catch (exception) {
    error.value = exception;
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  document.addEventListener('click', handleOutsideClick);
  try {
    const [, statsRes, rangesRes] = await Promise.all([
      fetchSessions(),
      trainingStore.fetchStats(),
      axiosInstance.get('/ranges'),
    ]);
    stats.value = statsRes.data;
    ranges.value = rangesRes.data.data ?? [];
  } catch (exception) {
    error.value = exception;
  }
});

onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick));

function handleOutsideClick() {
  openDropdown.value = null;
}

function setRangeFilter(id) {
  activeRangeId.value = id;
  openDropdown.value = null;
  currentPage.value = 1;
  router.replace({ query: { ...route.query, range_id: id ?? undefined } });
  fetchSessions();
}

function setYearFilter(year) {
  activeYear.value = year;
  openDropdown.value = null;
  currentPage.value = 1;
  router.replace({ query: { ...route.query, year: year ?? undefined } });
  fetchSessions();
}

function setPerPage(value) {
  perPage.value = value;
  currentPage.value = 1;
  openDropdown.value = null;
  fetchSessions();
}

function goToPage(page) {
  currentPage.value = page;
  fetchSessions();
}

const activeRange = computed(() => ranges.value.find((r) => r.id === activeRangeId.value) ?? null);

const filtered = computed(() => {
  if (!search.value) return sessions.value;
  const q = search.value.toLowerCase();
  return sessions.value.filter(
    (s) => s.label?.toLowerCase().includes(q) || s.range?.label?.toLowerCase().includes(q)
  );
});

const grouped = computed(() => {
  const map = {};
  for (const s of filtered.value) {
    const key = s.session_date.substring(0, 7);
    if (!map[key]) map[key] = [];
    map[key].push(s);
  }
  return Object.entries(map).sort((a, b) => b[0].localeCompare(a[0]));
});

function formatMonthKey(key) {
  return dayjs(key + '-01')
    .format('MMMM YYYY')
    .toUpperCase();
}

function monthRounds(sessions) {
  return sessions.reduce((sum, s) => sum + (s.total_rounds ?? 0), 0);
}

function formatCurrency(n) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
  }).format(n ?? 0);
}

const summaryStats = computed(() => [
  {
    value: loading.value ? '—' : (stats.value?.sessions_this_year ?? 0),
    label: `SESSIONS · ${new Date().getFullYear()}`,
  },
  {
    value: loading.value ? '—' : (stats.value?.rounds_this_year ?? 0).toLocaleString(),
    label: `ROUNDS · ${new Date().getFullYear()}`,
  },
  {
    value: loading.value ? '—' : formatCurrency(stats.value?.ammo_cost_this_year),
    label: `AMMO COST · ${new Date().getFullYear()}`,
  },
  {
    value: loading.value
      ? '—'
      : stats.value?.last_session_date
        ? dayjs(stats.value.last_session_date).format('MMM D')
        : '—',
    label: 'LAST SESSION',
  },
]);
</script>

<template>
  <div class="mx-auto max-w-[1280px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <div class="mb-5">
      <PageHeader title="Training" :count="loading ? undefined : total">
        <template #actions>
          <router-link
            :to="{ name: 'TrainingCreate' }"
            class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
          >
            <Plus class="h-4 w-4" />
            Log Session
          </router-link>
        </template>
      </PageHeader>
    </div>

    <!-- Stat strip -->
    <div class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div
        v-for="stat in summaryStats"
        :key="stat.label"
        data-testid="training-summary-stat"
        class="min-w-0 rounded border border-line bg-surface px-4 py-[15px]"
      >
        <div
          class="break-words font-mono text-[26px] font-medium leading-none tracking-[-0.01em] tabular-nums"
        >
          {{ stat.value }}
        </div>
        <div class="mt-[3px] font-mono text-[10px] tracking-[0.06em] text-muted">
          {{ stat.label }}
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="index-toolbar mb-7 flex flex-wrap items-center gap-2.5">
      <div class="index-toolbar-search">
        <Search class="h-[17px] w-[17px] flex-none text-muted" />
        <input
          v-model="search"
          type="text"
          placeholder="Search sessions…"
          class="placeholder:text-muted"
        />
      </div>

      <!-- Range filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-700 transition-colors hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'range' ? null : 'range'"
        >
          {{ activeRange ? activeRange.label : 'Range' }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'range'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[180px] rounded border border-line bg-white shadow-lg"
        >
          <button
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="!activeRangeId ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="setRangeFilter(null)"
          >
            All ranges
          </button>
          <button
            v-for="r in ranges"
            :key="r.id"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="activeRangeId === r.id ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="setRangeFilter(r.id)"
          >
            {{ r.label }}
          </button>
        </div>
      </div>

      <!-- Year filter -->
      <div class="relative">
        <button
          class="inline-flex items-center gap-[7px] rounded border border-[#c2c6ca] bg-white px-3 py-2 text-[14px] text-ink-700 transition-colors hover:bg-[#f5f6f7]"
          @click.stop="openDropdown = openDropdown === 'year' ? null : 'year'"
        >
          {{ activeYear ?? 'All time' }}
          <ChevronDown class="h-[15px] w-[15px] text-muted" />
        </button>
        <div
          v-if="openDropdown === 'year'"
          class="absolute left-0 top-full z-20 mt-1 min-w-[120px] rounded border border-line bg-white shadow-lg"
        >
          <button
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="!activeYear ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="setYearFilter(null)"
          >
            All time
          </button>
          <button
            v-for="y in availableYears()"
            :key="y"
            class="block w-full px-4 py-2 text-left text-[14px] hover:bg-ink-50"
            :class="activeYear === y ? 'font-medium text-ink-900' : 'text-ink-700'"
            @click.stop="setYearFilter(y)"
          >
            {{ y }}
          </button>
        </div>
      </div>
    </div>

    <LoadingState v-if="loading" message="Loading training sessions…" />

    <ErrorCard v-else-if="error" :error="error" />

    <template v-else-if="grouped.length">
      <div v-for="[monthKey, monthSessions] in grouped" :key="monthKey" class="mb-8">
        <div
          class="mb-3 flex flex-wrap items-baseline gap-3 font-mono text-[11px] tracking-[0.1em] text-muted"
        >
          <span>{{ formatMonthKey(monthKey) }}</span>
          <span class="font-mono text-[12px] text-muted">
            {{ monthSessions.length }} SESSION{{ monthSessions.length !== 1 ? 'S' : '' }} ·
            {{ monthRounds(monthSessions).toLocaleString() }} RDS
          </span>
        </div>
        <div class="flex flex-col gap-2.5">
          <TrainingCard v-for="s in monthSessions" :key="s.id" :session="s" />
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex items-center justify-between border-t border-line pt-5 mt-2"
      >
        <div class="flex items-center gap-2">
          <span class="text-[14px] text-muted">Per page</span>
          <div class="relative">
            <button
              class="inline-flex items-center gap-1.5 rounded border border-line bg-white px-3 py-1.5 text-[13px] text-ink-700 hover:bg-ink-50"
              @click.stop="openDropdown = openDropdown === 'perpage' ? null : 'perpage'"
            >
              {{ perPage }}<ChevronDown class="h-[13px] w-[13px] text-muted" />
            </button>
            <div
              v-if="openDropdown === 'perpage'"
              class="absolute bottom-full mb-1 left-0 z-20 min-w-[80px] rounded border border-line bg-white shadow-lg"
            >
              <button
                v-for="opt in perPageOptions"
                :key="opt"
                class="block w-full px-3 py-1.5 text-left text-[13px] hover:bg-ink-50"
                :class="perPage === opt ? 'font-medium text-ink-900' : 'text-ink-700'"
                @click.stop="setPerPage(opt)"
              >
                {{ opt }}
              </button>
            </div>
          </div>
          <span class="text-[13px] text-muted">{{ total }} total</span>
        </div>
        <div class="flex items-center gap-1">
          <button
            class="rounded border border-line bg-white px-3 py-1.5 text-[13px] text-ink-700 hover:bg-ink-50 disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            Prev
          </button>
          <span class="px-3 text-[13px] text-muted">{{ currentPage }} / {{ totalPages }}</span>
          <button
            class="rounded border border-line bg-white px-3 py-1.5 text-[13px] text-ink-700 hover:bg-ink-50 disabled:opacity-40"
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </template>

    <EmptyState
      v-else
      :title="search ? 'No sessions match your search' : 'No training sessions yet'"
      :message="
        search
          ? 'Try a different search term or clear the current filters.'
          : 'Log a session to apply rounds, ammo usage, and suppressor counts automatically.'
      "
      :action-label="search ? '' : 'Log Session'"
      :action-to="search ? null : { name: 'TrainingCreate' }"
    />
  </div>
</template>
