<template>
  <div v-if="isLoading" class="flex h-64 items-center justify-center">
    <Loading class="text-3xl text-gray-400" />
  </div>

  <div v-else class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'CalibersIndex' }" class="hover:text-gray-700"
        >All Calibers</router-link
      >
      <span>›</span>
      <span class="text-gray-700">Caliber</span>
    </nav>

    <div class="mb-6 flex items-center gap-3">
      <h1 class="text-2xl font-bold text-gray-900">{{ caliber.label }}</h1>
      <router-link
        :to="{ name: 'CalibersEdit', params: { caliber_id: caliberId } }"
        class="inline-flex items-center gap-1 rounded border border-gray-400 px-2 py-1 text-sm text-gray-600 transition-colors hover:bg-gray-100"
      >
        <font-awesome-icon icon="edit" />
      </router-link>
      <span class="text-sm text-gray-500">{{ caliberTypeLabel }}</span>
    </div>

    <div class="mb-6">
      <router-link
        :to="{ name: 'AmmoCreate', query: { caliber_id: caliberId } }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 bg-blue-600 px-3 py-1.5 text-sm text-white transition-colors hover:bg-blue-700"
      >
        Add Ammunition
      </router-link>
    </div>

    <AmmunitionList :ammunition="ammunition.data" :caliber="caliber" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useCalibersStore } from '@/stores/calibers';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useLoading } from '@/composables/useLoading';
import AmmunitionList from '@/components/ammunition/AmmunitionList.vue';
import Loading from '@/components/Loading.vue';

const props = defineProps({
  caliberId: {
    type: Number,
    required: true,
  },
});

const calibersStore = useCalibersStore();
const ammunitionStore = useAmmunitionStore();
const { isLoading, loadingQueue } = useLoading();

const caliber = ref({});
const ammunition = ref({ data: [], meta: {} });

const caliberTypeLabel = computed(() => caliber.value.caliber_type?.label ?? '');

onMounted(() => fetchData());

function fetchData() {
  isLoading.value = true;
  loadingQueue.caliber = false;
  loadingQueue.ammunition = false;
  fetchCaliber();
  fetchAmmunition();
}

async function fetchCaliber() {
  const { data } = await calibersStore.fetchOne(props.caliberId);
  caliber.value = data;
  loadingQueue.caliber = true;
}

async function fetchAmmunition() {
  const response = await ammunitionStore.fetchAll(props.caliberId);
  ammunition.value.data = response.data;
  ammunition.value.meta = response.meta ?? {};
  loadingQueue.ammunition = true;
}
</script>
