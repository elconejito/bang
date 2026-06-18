<script setup>
import { ref, computed, onMounted } from 'vue';
import dayjs from 'dayjs';
import PageHeader from '@/components/PageHeader.vue';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import TrainingCard from '@/components/training/TrainingCard.vue';
import { useTrainingStore } from '@/stores/training';

const trainingStore = useTrainingStore();

const loading = ref(true);
const sessions = ref([]);
const stats = ref(null);

const search = ref('');

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Training' },
];

onMounted(async () => {
  const [sessionsRes, statsRes] = await Promise.all([
    trainingStore.fetchAll(),
    trainingStore.fetchStats(),
  ]);
  sessions.value = sessionsRes.data;
  stats.value = statsRes.data;
  loading.value = false;
});

const filtered = computed(() => {
  if (!search.value) return sessions.value;
  const q = search.value.toLowerCase();
  return sessions.value.filter(
    (s) =>
      s.label?.toLowerCase().includes(q) ||
      s.location?.label?.toLowerCase().includes(q),
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
  return dayjs(key + '-01').format('MMMM YYYY').toUpperCase();
}

function monthRounds(sessions) {
  return sessions.reduce((sum, s) => sum + (s.total_rounds ?? 0), 0);
}
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <div class="mb-5">
      <PageHeader title="Training" :count="loading ? undefined : sessions.length">
        <template #actions>
          <router-link
            :to="{ name: 'TrainingCreate' }"
            class="inline-flex items-center gap-1.5 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-4 py-2 rounded border border-[#b08a2e] hover:bg-[#b8902f] transition-colors"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Log Session
          </router-link>
        </template>
      </PageHeader>
    </div>

    <!-- Stat strip -->
    <div class="grid grid-cols-3 gap-3 mb-7">
      <div class="bg-white border border-[#e2e4e6] rounded-sm px-4 py-3">
        <div class="font-mono text-[11px] text-muted tracking-[0.06em] mb-1">SESSIONS · {{ new Date().getFullYear() }}</div>
        <div class="font-display font-bold text-[24px] leading-none">{{ loading ? '—' : (stats?.sessions_this_year ?? 0) }}</div>
      </div>
      <div class="bg-white border border-[#e2e4e6] rounded-sm px-4 py-3">
        <div class="font-mono text-[11px] text-muted tracking-[0.06em] mb-1">ROUNDS · {{ new Date().getFullYear() }}</div>
        <div class="font-display font-bold text-[24px] leading-none">{{ loading ? '—' : (stats?.rounds_this_year ?? 0).toLocaleString() }}</div>
      </div>
      <div class="bg-white border border-[#e2e4e6] rounded-sm px-4 py-3">
        <div class="font-mono text-[11px] text-muted tracking-[0.06em] mb-1">LAST SESSION</div>
        <div class="font-display font-bold text-[24px] leading-none">
          {{ loading ? '—' : (stats?.last_session_date ? dayjs(stats.last_session_date).format('MMM D') : '—') }}
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex items-center gap-2.5 mb-7">
      <div class="flex-1 flex items-center gap-2 border border-[#c2c6ca] rounded-sm bg-white px-3 py-2">
        <svg class="w-[17px] h-[17px] text-muted flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input v-model="search" type="text" placeholder="Search sessions…" class="flex-1 text-[15px] bg-transparent outline-none placeholder:text-muted" />
      </div>
    </div>

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else-if="grouped.length">
      <div v-for="([monthKey, monthSessions]) in grouped" :key="monthKey" class="mb-8">
        <div class="flex items-baseline gap-3 border-b border-[#d6d9dc] pb-2 mb-4">
          <span class="font-display font-bold text-[18px] tracking-[-0.01em]">{{ formatMonthKey(monthKey) }}</span>
          <span class="font-mono text-[12px] text-muted">
            {{ monthSessions.length }} SESSION{{ monthSessions.length !== 1 ? 'S' : '' }} · {{ monthRounds(monthSessions).toLocaleString() }} RDS
          </span>
        </div>
        <div class="flex flex-col gap-2.5">
          <TrainingCard v-for="s in monthSessions" :key="s.id" :session="s" />
        </div>
      </div>
    </template>

    <div v-else class="flex flex-col items-center justify-center gap-3 py-24 text-muted">
      <p class="text-[15px]">{{ search ? 'No sessions match your search.' : 'No training sessions yet.' }}</p>
      <router-link v-if="!search" :to="{ name: 'TrainingCreate' }" class="text-[14px] text-brass font-semibold hover:underline">
        Log your first session
      </router-link>
    </div>
  </div>
</template>
