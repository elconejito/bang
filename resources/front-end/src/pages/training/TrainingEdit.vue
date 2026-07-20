<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EditTrainingForm from '@/components/training/EditTrainingForm.vue';
import { useTrainingStore } from '@/stores/training';

const props = defineProps({
  trainingId: { type: Number, required: true },
});

const router = useRouter();
const trainingStore = useTrainingStore();

const session = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await trainingStore.fetchOne(props.trainingId);
  session.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Training', to: { name: 'TrainingIndex' } },
  {
    label: session.value?.label ?? '…',
    to: session.value
      ? { name: 'TrainingShow', params: { training_id: session.value.id } }
      : undefined,
  },
  { label: 'Edit' },
]);

function onComplete(updated) {
  router.push({ name: 'TrainingShow', params: { training_id: updated?.id } });
}
</script>

<template>
  <div class="mx-auto max-w-[760px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <LoadingState v-if="loading" message="Loading training session…" />

    <template v-else-if="session">
      <h1 class="mb-6 font-display text-[28px] font-bold tracking-[-0.02em]">Edit Session</h1>
      <EditTrainingForm :session="session" @complete="onComplete" />
    </template>
  </div>
</template>
