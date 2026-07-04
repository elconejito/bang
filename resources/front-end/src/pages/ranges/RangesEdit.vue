<script setup>
import { reactive, ref, onMounted } from 'vue';
import { LoaderCircle } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import FormError from '@/components/FormError.vue';
import { useRangesStore } from '@/stores/ranges';

const props = defineProps({
  rangeId: { type: Number, required: true },
});

const router = useRouter();
const rangesStore = useRangesStore();

const crumbs = ref([
  { label: 'Home', to: '/' },
  { label: 'Ranges', to: { name: 'RangesIndex' } },
  { label: '…', to: null },
  { label: 'Edit' },
]);

const form = reactive({ label: '', description: '', address: '' });
const loading = ref(true);
const saving = ref(false);
const error = ref(null);

onMounted(async () => {
  const { data } = await rangesStore.fetchOne(props.rangeId);
  form.label = data.label;
  form.description = data.description ?? '';
  form.address = data.address ?? '';
  crumbs.value[2] = {
    label: data.label,
    to: { name: 'RangesShow', params: { range_id: props.rangeId } },
  };
  loading.value = false;
});

async function submit() {
  if (!form.label.trim()) return;
  saving.value = true;
  error.value = null;
  try {
    await rangesStore.update(props.rangeId, form);
    router.push({ name: 'RangesShow', params: { range_id: props.rangeId } });
  } catch (err) {
    error.value = err;
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Edit Range</h1>

    <div v-if="loading" class="py-12 text-center text-sm text-muted">Loading…</div>

    <div v-else class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
      <div class="px-6 py-5 flex flex-col gap-4">
        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5"
            >Name <span class="text-[#b4452f]">*</span></label
          >
          <input
            v-model="form.label"
            type="text"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>

        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5">Address</label>
          <input
            v-model="form.address"
            type="text"
            placeholder="123 Main St, City, State"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>

        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6] resize-none"
          />
        </div>

        <FormError v-if="error" :error="error" />
      </div>

      <div
        class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-[#eef0f1] bg-[#fafbfb]"
      >
        <button
          class="font-semibold text-[14px] bg-white text-[#3a3e44] px-4 py-2 border border-[#c2c6ca] rounded hover:bg-[#f5f6f7] transition-colors"
          @click="router.back()"
        >
          Cancel
        </button>
        <button
          :disabled="saving || !form.label.trim()"
          class="inline-flex items-center justify-center gap-2 font-semibold text-[14px] bg-brass text-[#1a1c1f] px-4 py-2 border border-[#b08a2e] rounded hover:bg-[#b8902f] disabled:opacity-50 transition-colors"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Saving…' : 'Save Changes' }}
        </button>
      </div>
    </div>
  </div>
</template>
