import { ref, computed, onMounted, nextTick } from 'vue';
import { REFERENCE_TYPES, usageOf, usageSummary } from '@/components/reference/referenceMeta';

/**
 * Shared state and behavior for the single-field (label-only) reference forms —
 * Purpose, Storage Location, Store, and Range. Each form component stays distinct
 * (its own template + store wiring) but leans on this for validation, the save/
 * delete flow, and the derived modal props. Calibers have extra fields and manage
 * their own state directly.
 *
 * @param {object} options
 * @param {import('./referenceMeta').ReferenceType} options.type
 * @param {{ mode: string, item: ?object }} options.props
 * @param {(event: string, ...args: any[]) => void} options.emit
 * @param {(payload: object) => Promise<{ data: object }>} options.create
 * @param {(id: number, payload: object) => Promise<{ data: object }>} options.update
 * @param {(id: number) => Promise<void>} options.remove
 */
export function useReferenceForm({
  type,
  props,
  emit,
  create,
  update,
  remove,
  additionalRequiredFields = [],
}) {
  const meta = REFERENCE_TYPES[type];
  const labelInput = ref(null);
  const saving = ref(false);
  const error = ref(null);
  const form = ref({
    label: props.item?.label ?? '',
    ...Object.fromEntries(
      additionalRequiredFields.map((field) => [field, props.item?.[field] ?? ''])
    ),
  });

  onMounted(async () => {
    await nextTick();
    labelInput.value?.focus();
  });

  const isEdit = computed(() => props.mode === 'edit');
  const usage = computed(() => usageOf(type, props.item));

  const title = computed(() => (isEdit.value ? `Edit ${meta.singular}` : meta.addLabel));
  const canSave = computed(
    () =>
      [form.value.label, ...additionalRequiredFields.map((field) => form.value[field])].every(
        (value) => value.trim().length > 0
      ) && !saving.value
  );
  const saveLabel = computed(() => {
    if (saving.value) {
      return 'Saving…';
    }
    return isEdit.value ? 'Save changes' : meta.addLabel;
  });
  const usageNote = computed(() =>
    isEdit.value && usage.value > 0
      ? `Used by ${usageSummary(type, props.item)}. Renaming updates it everywhere it appears.`
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
      const payload = {
        label: form.value.label.trim(),
        ...Object.fromEntries(
          additionalRequiredFields.map((field) => [field, form.value[field].trim()])
        ),
      };
      const result = isEdit.value ? await update(props.item.id, payload) : await create(payload);
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

  async function destroy() {
    if (usage.value > 0) {
      return;
    }
    error.value = null;
    saving.value = true;
    try {
      await remove(props.item.id);
      emit('deleted', props.item.id);
    } catch (err) {
      error.value = err;
    } finally {
      saving.value = false;
    }
  }

  return {
    meta,
    form,
    labelInput,
    saving,
    error,
    isEdit,
    title,
    canSave,
    saveLabel,
    usageNote,
    canDelete,
    deleteBlocked,
    submit,
    destroy,
  };
}
