<script setup>
import { ref, onMounted } from 'vue';
import { Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import { useGunStoresStore } from '@/stores/gunStores';

const gunStoresStore = useGunStoresStore();
const stores = ref([]);
const loading = ref(true);

const crumbs = [{ label: 'Home', to: '/' }, { label: 'Stores' }];

onMounted(async () => {
  const { data } = await gunStoresStore.fetchAll();
  stores.value = data;
  loading.value = false;
});
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div class="flex items-center gap-4 mb-6 flex-wrap">
      <div class="flex-1 min-w-0">
        <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">Stores</h1>
      </div>
      <router-link
        :to="{ name: 'StoreCreate' }"
        class="inline-flex items-center gap-1.5 bg-[#1a1c1f] text-white font-semibold text-[14px] px-[14px] py-2 rounded hover:bg-[#2a2d32] transition-colors"
      >
        <Plus class="w-[15px] h-[15px]" />
        Add Store
      </router-link>
    </div>

    <div v-if="loading" class="py-12 text-center text-sm text-muted">Loading…</div>

    <template v-else>
      <EmptyState
        v-if="!stores.length"
        title="No stores yet"
        message="Add stores or FFLs to connect ammo purchases with cost and order history."
        action-label="Add Store"
        :action-to="{ name: 'StoreCreate' }"
      />

      <div v-else class="grid grid-cols-3 gap-4">
        <router-link
          v-for="store in stores"
          :key="store.id"
          :to="{ name: 'StoreShow', params: { store_id: store.id } }"
          class="block bg-white border border-[#e2e4e6] rounded-sm overflow-hidden hover:border-[#c2c6ca] hover:shadow-md transition-all duration-150"
        >
          <div class="h-[120px] w-full bg-ink-100 overflow-hidden">
            <img
              v-if="store.primary_photo_url"
              :src="store.primary_photo_url"
              :alt="store.label"
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
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 0 1-8 0" />
              </svg>
            </div>
          </div>
          <div class="p-[14px_16px]">
            <div class="font-display font-semibold text-[16px] mb-0.5">{{ store.label }}</div>
            <div v-if="store.orders_count !== undefined" class="flex items-center gap-2 mt-1">
              <span
                class="font-mono text-[10px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px]"
              >
                {{ store.orders_count }} ORDER{{ store.orders_count !== 1 ? 'S' : '' }}
              </span>
            </div>
          </div>
        </router-link>

        <router-link
          :to="{ name: 'StoreCreate' }"
          class="border border-dashed border-[#c2c6ca] rounded-sm bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 min-h-[200px] text-muted hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors"
        >
          <Plus class="w-[22px] h-[22px] text-brass" />
          <span class="text-[14px]">Add store</span>
        </router-link>
      </div>
    </template>
  </div>
</template>
