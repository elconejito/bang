<script setup>
import { ref, onMounted } from 'vue';
import { Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import { useLocationsStore } from '@/stores/locations';

const locationsStore = useLocationsStore();
const locations = ref([]);
const loading = ref(true);
const error = ref(null);

const crumbs = [{ label: 'Home', to: '/' }, { label: 'Storage Locations' }];

onMounted(async () => {
  try {
    const { data } = await locationsStore.fetchAll();
    locations.value = data;
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
        <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">Storage Locations</h1>
      </div>
      <router-link
        :to="{ name: 'LocationsCreate' }"
        class="inline-flex items-center gap-1.5 bg-[#1a1c1f] text-white font-semibold text-[14px] px-[14px] py-2 rounded hover:bg-[#2a2d32] transition-colors"
      >
        <Plus class="w-[15px] h-[15px]" />
        Add Location
      </router-link>
    </div>

    <LoadingState v-if="loading" message="Loading locations…" />

    <ErrorCard v-else-if="error" :error="error" />

    <template v-else>
      <EmptyState
        v-if="!locations.length"
        title="No storage locations yet"
        message="Add safes, rooms, or cases so firearms and accessories have a place."
        action-label="Add Location"
        :action-to="{ name: 'LocationsCreate' }"
      />

      <div v-else class="grid grid-cols-3 gap-4">
        <router-link
          v-for="loc in locations"
          :key="loc.id"
          :to="{ name: 'LocationsShow', params: { location_id: loc.id } }"
          class="block overflow-hidden rounded-sm border border-[#e2e4e6] bg-white transition-[border-color,box-shadow] duration-150 hover:border-[#c2c6ca] hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]"
        >
          <div class="aspect-[5/3] w-full bg-ink-100 overflow-hidden">
            <img
              v-if="loc.primary_photo_url"
              :src="loc.primary_photo_url"
              :alt="loc.label"
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
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
              </svg>
            </div>
          </div>
          <div class="p-[14px_16px]">
            <div class="font-display font-semibold text-[16px]">{{ loc.label }}</div>
          </div>
        </router-link>

        <router-link
          :to="{ name: 'LocationsCreate' }"
          class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[200px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors"
        >
          <Plus class="w-[22px] h-[22px] text-brass" />
          <span class="text-[14px]">Add location</span>
        </router-link>
      </div>
    </template>
  </div>
</template>
