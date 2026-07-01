<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <span class="text-gray-700">All Calibers</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Calibers</h1>
      <router-link
        :to="{ name: 'CalibersCreate' }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      >
        <font-awesome-icon icon="plus-circle" /> Add Caliber
      </router-link>
    </div>

    <CaliberList :calibers="calibers" :is-loading="isLoading" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useCalibersStore } from '@/stores/calibers';
import { useLoading } from '@/composables/useLoading';
import CaliberList from '@/components/caliber/CaliberList.vue';

const calibersStore = useCalibersStore();
const { isLoading, loadingQueue } = useLoading();

const calibers = ref([]);

onMounted(() => fetchData());

async function fetchData() {
  isLoading.value = true;
  loadingQueue.calibers = false;
  try {
    const { data } = await calibersStore.fetchAll();
    calibers.value = data;
  } finally {
    loadingQueue.calibers = true;
  }
}
</script>
