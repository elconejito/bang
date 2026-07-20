<script setup>
import { ref, onMounted } from 'vue';
import { Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import { useRangesStore } from '@/stores/ranges';

const rangesStore = useRangesStore();
const ranges = ref([]);
const loading = ref(true);
const error = ref(null);

const crumbs = [{ label: 'Home', to: '/' }, { label: 'Ranges' }];

onMounted(async () => {
  try {
    const { data } = await rangesStore.fetchAll();
    ranges.value = data;
  } catch (exception) {
    error.value = exception;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div class="flex items-center gap-4 mb-6 flex-wrap">
      <div class="flex-1 min-w-0">
        <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">Ranges</h1>
      </div>
      <router-link
        :to="{ name: 'RangesCreate' }"
        class="inline-flex items-center gap-1.5 bg-[#1a1c1f] text-white font-semibold text-[14px] px-[14px] py-2 rounded hover:bg-[#2a2d32] transition-colors"
      >
        <Plus class="w-[15px] h-[15px]" />
        Add Range
      </router-link>
    </div>

    <LoadingState v-if="loading" message="Loading ranges…" />

    <ErrorCard v-else-if="error" :error="error" />

    <template v-else>
      <EmptyState
        v-if="!ranges.length"
        title="No ranges yet"
        message="Add ranges to group training sessions by location and track rounds over time."
        action-label="Add Range"
        :action-to="{ name: 'RangesCreate' }"
      />

      <div v-else class="grid grid-cols-3 gap-4">
        <router-link
          v-for="range in ranges"
          :key="range.id"
          :to="{ name: 'RangesShow', params: { range_id: range.id } }"
          class="block overflow-hidden rounded-sm border border-[#e2e4e6] bg-white transition-[border-color,box-shadow] duration-150 hover:border-[#c2c6ca] hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]"
        >
          <!-- Photo thumbnail -->
          <div class="aspect-[5/3] w-full bg-ink-100 overflow-hidden">
            <img
              v-if="range.primary_photo_url"
              :src="range.primary_photo_url"
              :alt="range.label"
              class="h-full w-full object-cover"
            />
            <div v-else class="flex h-full w-full items-center justify-center text-ink-300">
              <svg
                class="w-8 h-8"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="10" />
                <circle cx="12" cy="12" r="6" />
                <circle cx="12" cy="12" r="2" />
              </svg>
            </div>
          </div>
          <div class="p-[14px_16px]">
            <div class="font-display font-semibold text-[16px] mb-0.5">{{ range.label }}</div>
            <div v-if="range.address" class="text-[13px] text-[#6b7077] truncate">
              {{ range.address }}
            </div>
            <div class="flex items-center gap-2 mt-2">
              <span
                class="font-mono text-[10px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px]"
              >
                {{ range.sessions_count ?? 0 }} SESSION{{
                  (range.sessions_count ?? 0) !== 1 ? 'S' : ''
                }}
              </span>
            </div>
          </div>
        </router-link>

        <!-- Add card -->
        <router-link
          :to="{ name: 'RangesCreate' }"
          class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[200px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors"
        >
          <Plus class="w-[22px] h-[22px] text-brass" />
          <span class="text-[14px]">Add range</span>
        </router-link>
      </div>
    </template>
  </div>
</template>
