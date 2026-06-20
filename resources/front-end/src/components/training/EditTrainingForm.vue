<script setup>
import { ref, onMounted } from 'vue';
import { useTrainingStore } from '@/stores/training';
import { useRangesStore } from '@/stores/ranges';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  session: { type: Object, required: true },
});

const emit = defineEmits(['complete']);

const trainingStore = useTrainingStore();
const rangesStore = useRangesStore();

const loading = ref(false);
const loadingData = ref(true);
const error = ref(null);
const ranges = ref([]);

const form = ref({
  label: props.session.label,
  session_date: props.session.session_date,
  range_id: props.session.range_id ?? '',
  description: props.session.description ?? '',
});

onMounted(async () => {
  const { data } = await rangesStore.fetchAll();
  ranges.value = data;
  loadingData.value = false;
});

async function submit() {
  error.value = null;
  loading.value = true;
  try {
    const payload = {
      ...form.value,
      range_id: form.value.range_id || null,
    };
    const { data } = await trainingStore.update(props.session.id, payload);
    emit('complete', data);
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div v-if="loadingData" class="text-sm text-muted py-8 text-center">Loading…</div>

  <form v-else @submit.prevent="submit">
    <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden mb-5">
      <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Session details</div>
      <div class="px-4 pt-4 pb-5 grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Label <span class="text-red-500">*</span></label>
          <input
            v-model="form.label"
            type="text"
            required
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
          />
        </div>
        <div>
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Date <span class="text-red-500">*</span></label>
          <input
            v-model="form.session_date"
            type="date"
            required
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
          />
        </div>
        <div>
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Range</label>
          <select
            v-model="form.range_id"
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
          >
            <option value="">— None —</option>
            <option v-for="range in ranges" :key="range.id" :value="range.id">{{ range.label }}</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Notes</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] resize-y focus:outline-none focus:border-brass"
          />
        </div>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="flex items-center gap-3">
      <ActionButton text="Save changes" :is-loading="loading" variant="primary" type="submit" />
      <router-link
        :to="{ name: 'TrainingShow', params: { training_id: session.id } }"
        class="text-[14px] text-muted hover:text-ink-700"
      >Cancel</router-link>
    </div>
  </form>
</template>
