<script setup>
import { ref, onMounted } from 'vue';
import { Plus } from 'lucide-vue-next';
import { useTrainingStore } from '@/stores/training';
import { useRangesStore } from '@/stores/ranges';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const props = defineProps({
  session: { type: Object, required: true },
});

const emit = defineEmits(['complete']);

const trainingStore = useTrainingStore();
const rangesStore = useRangesStore();
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();

const loading = ref(false);
const loadingData = ref(true);
const error = ref(null);
const ranges = ref([]);

function onQuickAddSaved(item) {
  ranges.value.push(item);
  form.value.range_id = item.id;
  closeQuickAdd();
}

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
  <LoadingState v-if="loadingData" message="Loading training options…" />

  <form v-else @submit.prevent="submit">
    <div class="mb-5 overflow-hidden rounded border border-line bg-white">
      <div class="border-b border-[#eef0f1] px-4 py-3 font-display text-[16px] font-semibold">
        Session details
      </div>
      <div class="grid grid-cols-1 gap-4 px-4 pb-5 pt-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Label <span class="text-red-500">*</span></label
          >
          <input
            v-model="form.label"
            type="text"
            required
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
        <div>
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Date <span class="text-red-500">*</span></label
          >
          <input
            v-model="form.session_date"
            type="date"
            required
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[14px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
        <div>
          <div class="mb-1 flex items-center justify-between">
            <label class="block text-[14px] font-medium text-[#3a3e44]">Range</label>
            <button
              type="button"
              class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
              @click="openQuickAdd('range')"
            >
              <Plus class="h-3.5 w-3.5" /> Add range
            </button>
          </div>
          <select
            v-model="form.range_id"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          >
            <option value="">— None —</option>
            <option v-for="range in ranges" :key="range.id" :value="range.id">
              {{ range.label }}
            </option>
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]">Notes</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full resize-y rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-[14px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-line pt-4">
      <ActionButton text="Save changes" :is-loading="loading" variant="primary" type="submit" />
      <router-link
        :to="{ name: 'TrainingShow', params: { training_id: session.id } }"
        class="text-[14px] text-muted hover:text-ink-700"
        >Cancel</router-link
      >
    </div>

    <ReferenceItemModal
      v-if="quickAddType"
      :type="quickAddType"
      mode="add"
      @close="closeQuickAdd"
      @saved="onQuickAddSaved"
    />
  </form>
</template>
