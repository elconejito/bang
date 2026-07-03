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
    <div class="flex flex-col gap-1.5">
      <label for="ref-label" class="text-[13px] font-semibold text-ink-700">
        {{ meta.field.label }}
        <span v-if="meta.field.labelSub" class="font-normal text-muted">{{
          meta.field.labelSub
        }}</span>
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
  </ReferenceModalShell>
</template>

<script setup>
import { usePurposesStore } from '@/stores/purposes';
import { useReferenceForm } from '@/components/reference/useReferenceForm';
import ReferenceModalShell from '@/components/reference/ReferenceModalShell.vue';

const props = defineProps({
  /** @type {'add' | 'edit'} */
  mode: { type: String, default: 'add' },
  item: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved', 'deleted']);

const store = usePurposesStore();
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
  type: 'purpose',
  props,
  emit,
  create: store.create,
  update: store.update,
  remove: store.remove,
});
</script>
