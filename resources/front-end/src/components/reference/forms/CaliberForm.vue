<template>
  <ReferenceModalShell
    :icon="meta.icon"
    :title="title"
    :kind-subline="meta.kindSubline"
    :mode="mode"
    :saving="saving"
    :can-save="canSave"
    :save-label="saveLabel"
    :can-delete="canDelete"
    :delete-blocked="deleteBlocked"
    :usage-note="usageNote"
    :error="error"
    @close="$emit('close')"
    @save="submit"
    @delete="remove"
  >
    <!-- Label · short name -->
    <div class="flex flex-col gap-1.5">
      <label for="ref-label" class="text-[13px] font-semibold text-ink-700">
        {{ meta.field.label }} <span class="font-normal text-muted">{{ meta.field.labelSub }}</span>
      </label>
      <input
        id="ref-label"
        ref="labelInput"
        v-model="form.label"
        type="text"
        :placeholder="meta.field.placeholder"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
        @keydown.enter.prevent="canSave && submit()"
      />
      <p class="text-[12px] text-muted">{{ meta.field.hint }}</p>
    </div>

    <!-- Caliber · official name -->
    <div class="flex flex-col gap-1.5">
      <label for="ref-official" class="text-[13px] font-semibold text-ink-700">
        Caliber <span class="font-normal text-muted">· official name</span>
      </label>
      <input
        id="ref-official"
        v-model="form.official"
        type="text"
        placeholder="e.g. 9×19mm Parabellum"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
      />
      <p class="text-[12px] text-muted">
        The full cartridge designation, usually with measurements.
      </p>
    </div>

    <!-- Type -->
    <div class="flex flex-col gap-1.5">
      <label for="ref-type" class="text-[13px] font-semibold text-ink-700">
        Type <span class="text-caution">*</span>
      </label>
      <select
        id="ref-type"
        v-model="form.caliber_type_id"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
      >
        <option v-for="type in caliberTypes" :key="type.id" :value="type.id">
          {{ type.label }}
        </option>
      </select>
      <p class="text-[12px] text-muted">Rimfire, centerfire, shotgun, etc.</p>
    </div>
  </ReferenceModalShell>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useCalibersStore } from '@/stores/calibers';
import { useReferenceStore } from '@/stores/reference';
import { REFERENCE_TYPES, usageOf, usageSummary } from '@/components/reference/referenceMeta';
import ReferenceModalShell from '@/components/reference/ReferenceModalShell.vue';

const props = defineProps({
  /** @type {'add' | 'edit'} */
  mode: { type: String, default: 'add' },
  /** Existing item when editing: { id, label, caliber, caliber_type_id, firearms_count, loads_count } */
  item: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved', 'deleted']);

const meta = REFERENCE_TYPES.caliber;
const calibersStore = useCalibersStore();
const referenceStore = useReferenceStore();

const caliberTypes = computed(() => referenceStore.caliberType);

const labelInput = ref(null);
const saving = ref(false);
const error = ref(null);

const form = ref({
  label: props.item?.label ?? '',
  // Only surface the official name when it differs from the short label.
  official:
    props.item?.caliber && props.item.caliber !== props.item.label ? props.item.caliber : '',
  caliber_type_id: props.item?.caliber_type_id ?? null,
});

onMounted(async () => {
  if (!form.value.caliber_type_id) {
    form.value.caliber_type_id = caliberTypes.value[0]?.id ?? null;
  }
  await nextTick();
  labelInput.value?.focus();
});

const usage = computed(() => usageOf('caliber', props.item));
const isEdit = computed(() => props.mode === 'edit');

const title = computed(() => (isEdit.value ? 'Edit caliber' : meta.addLabel));
const canSave = computed(() => form.value.label.trim().length > 0 && !saving.value);
const saveLabel = computed(() => {
  if (saving.value) {
    return 'Saving…';
  }
  return isEdit.value ? 'Save changes' : meta.addLabel;
});
const usageNote = computed(() =>
  isEdit.value && usage.value > 0
    ? `Used by ${usageSummary('caliber', props.item)}. Renaming updates it everywhere it appears.`
    : null
);
const canDelete = computed(() => isEdit.value && usage.value === 0);
const deleteBlocked = computed(() => isEdit.value && usage.value > 0);

async function submit() {
  if (!canSave.value) {
    return;
  }
  error.value = null;
  saving.value = true;
  try {
    const label = form.value.label.trim();
    const official = form.value.official.trim();
    const payload = {
      label,
      // Backend requires a non-null `caliber` (official) — fall back to the label.
      caliber: official || label,
      caliber_type_id: form.value.caliber_type_id,
    };
    const result = isEdit.value
      ? await calibersStore.update(props.item.id, payload)
      : await calibersStore.create(payload);
    emit('saved', result.data);
  } catch (err) {
    if (err.response?.data?.errors) {
      err.errorBag = err.response.data.errors;
    }
    error.value = err;
  } finally {
    saving.value = false;
  }
}

async function remove() {
  if (usage.value > 0) {
    return;
  }
  error.value = null;
  saving.value = true;
  try {
    await calibersStore.remove(props.item.id);
    emit('deleted', props.item.id);
  } catch (err) {
    error.value = err;
  } finally {
    saving.value = false;
  }
}
</script>
