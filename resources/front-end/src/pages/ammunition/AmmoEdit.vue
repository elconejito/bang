<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <template v-if="ammo">
      <AppBreadcrumb
        :crumbs="[
          { label: 'Home', to: '/' },
          { label: 'Ammo', to: { name: 'AmmoIndex' } },
          { label: ammo.label, to: { name: 'AmmoShow', params: { ammunition_id: ammo.id } } },
          { label: 'Edit' },
        ]"
        class="mb-4"
      />
      <PageHeader :title="ammo.label" class="mb-6" />
      <div class="max-w-2xl rounded border border-line bg-white p-6">
        <AmmoFormCard
          :ammo="ammo"
          @complete="onComplete"
          @cancel="router.push({ name: 'AmmoShow', params: { ammunition_id: props.ammunitionId } })"
        />
      </div>
    </template>

    <template v-else-if="!loading">
      <p class="text-muted">Load not found.</p>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAmmunitionStore } from '@/stores/ammunition';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import PageHeader from '@/components/PageHeader.vue';
import AmmoFormCard from '@/components/ammunition/AmmoFormCard.vue';

const props = defineProps({
  ammunitionId: { type: Number, required: true },
});

const router = useRouter();
const ammunitionStore = useAmmunitionStore();

const ammo = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const response = await ammunitionStore.fetchOne(props.ammunitionId);
    ammo.value = response.data;
  } finally {
    loading.value = false;
  }
});

function onComplete(updated) {
  router.push({ name: 'AmmoShow', params: { ammunition_id: updated.id } });
}
</script>
