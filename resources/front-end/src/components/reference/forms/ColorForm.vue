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
    @delete="destroy"
  >
    <div class="flex flex-col gap-4">
      <div class="flex flex-col gap-1.5">
        <label for="ref-label" class="text-[13px] font-semibold text-ink-700">{{
          meta.field.label
        }}</label>
        <input
          id="ref-label"
          ref="labelInput"
          v-model="form.label"
          type="text"
          :placeholder="meta.field.placeholder"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px]"
          @keydown.enter.prevent="canSave && submit()"
        />
        <p class="text-[12px] text-muted">{{ meta.field.hint }}</p>
      </div>

      <div class="flex flex-col gap-1.5">
        <label for="ref-short-label" class="text-[13px] font-semibold text-ink-700">
          Short label
        </label>
        <input
          id="ref-short-label"
          v-model="form.short_label"
          type="text"
          maxlength="20"
          placeholder="e.g. FDE"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[15px]"
          @keydown.enter.prevent="canSave && submit()"
        />
        <p class="text-[12px] text-muted">Compact name used in dense lists and labels.</p>
      </div>
    </div>
  </ReferenceModalShell>
</template>

<script setup>
import { useColorsStore } from '@/stores/colors';
import { useReferenceForm } from '@/components/reference/useReferenceForm';
import ReferenceModalShell from '@/components/reference/ReferenceModalShell.vue';

const props = defineProps({
  mode: { type: String, default: 'add' },
  item: { type: Object, default: null },
});
const emit = defineEmits(['close', 'saved', 'deleted']);
const store = useColorsStore();
const {
  meta,
  form,
  labelInput,
  saving,
  error,
  title,
  canSave,
  saveLabel,
  usageNote,
  canDelete,
  deleteBlocked,
  submit,
  destroy,
} = useReferenceForm({
  type: 'color',
  props,
  emit,
  create: store.create,
  update: store.update,
  remove: store.remove,
  additionalRequiredFields: ['short_label'],
});
</script>
