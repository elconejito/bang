<script setup>
import { ref, onMounted } from 'vue';
import { Calendar, Check, ChevronDown, LoaderCircle, MapPin, Plus } from 'lucide-vue-next';
import { useTrainingStore } from '@/stores/training';
import { useRangesStore } from '@/stores/ranges';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
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

  <form v-else class="space-y-[18px]" @submit.prevent="submit">
    <div class="rounded border border-line bg-white p-5">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Session label <span class="text-red-500">*</span></label
          >
          <input
            v-model="form.label"
            type="text"
            required
            placeholder="Steel plate drills"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
        <div>
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Date <span class="text-red-500">*</span></label
          >
          <div class="relative">
            <input
              v-model="form.session_date"
              type="date"
              required
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] pr-9 font-mono text-[14px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            />
            <Calendar
              class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
          </div>
        </div>
        <div>
          <div class="mb-1.5 flex items-center justify-between">
            <label class="block text-[14px] font-medium text-[#3a3e44]">Location</label>
            <button
              type="button"
              class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
              @click="openQuickAdd('range')"
            >
              <Plus class="h-3.5 w-3.5" /> Add range
            </button>
          </div>
          <div class="relative">
            <MapPin
              class="pointer-events-none absolute left-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
            <select
              v-model="form.range_id"
              class="w-full appearance-none rounded border border-[#c2c6ca] bg-white py-[9px] pl-9 pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            >
              <option value="">No range selected</option>
              <option v-for="range in ranges" :key="range.id" :value="range.id">
                {{ range.label }}
              </option>
            </select>
            <ChevronDown
              class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
          </div>
        </div>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="flex flex-wrap items-center justify-end gap-2.5 border-t border-line pt-4">
      <router-link
        :to="{ name: 'TrainingShow', params: { training_id: session.id } }"
        class="rounded border border-[#c2c6ca] bg-white px-[18px] py-[9px] text-[15px] font-semibold text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
        >Cancel</router-link
      >
      <button
        type="submit"
        :disabled="loading"
        class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-5 py-[9px] text-[15px] font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:cursor-not-allowed disabled:opacity-60"
      >
        <LoaderCircle v-if="loading" class="h-4 w-4 animate-spin" />
        <Check v-else class="h-4 w-4" />
        Save changes
      </button>
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
