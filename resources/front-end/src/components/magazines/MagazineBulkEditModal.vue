<script setup>
import { computed, nextTick, onMounted, onBeforeUnmount, reactive, ref, watch } from 'vue';
import { LoaderCircle, X } from 'lucide-vue-next';
import MagazineBulkEditFieldCard from '@/components/magazines/MagazineBulkEditFieldCard.vue';
import MagazineBulkEditOptionPicker from '@/components/magazines/MagazineBulkEditOptionPicker.vue';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';

const props = defineProps({
  magazines: { type: Array, required: true },
  group: { type: Object, default: null },
  saving: { type: Boolean, default: false },
  serverError: { type: String, default: '' },
});
const emit = defineEmits(['close', 'save']);

const calibersStore = useCalibersStore();
const colorsStore = useColorsStore();
const firearmsStore = useFirearmsStore();

const dialog = ref(null);
const heading = ref(null);
const loading = ref(true);
const loadError = ref(false);
const validationError = ref('');
const errorSummary = ref(null);
const calibers = ref([]);
const colors = ref([]);
const firearms = ref([]);

const apply = reactive({
  manufacturer: false,
  model_name: false,
  model_number: false,
  label: false,
  serial_number: false,
  id_marking: false,
  color_id: false,
  capacity: false,
  calibers: false,
  firearms: false,
});
const form = reactive({
  manufacturer: '',
  model_name: '',
  model_number: '',
  label: '',
  serial_number: '',
  id_marking: '',
  color_id: '',
  capacity: '',
  calibers: [],
  firearms: [],
});

const hasAppliedField = computed(() => Object.values(apply).some(Boolean));
const selectedCount = computed(() => props.magazines.length);

function summaryKey(value) {
  return value === null || value === undefined ? '' : String(value);
}

function optionLabel(item) {
  return [item.manufacturer, item.label].filter(Boolean).join(' ') || item.name || `#${item.id}`;
}

function fieldId(name) {
  return `bulk-magazine-${name === 'color_id' ? 'color' : name}`;
}

function valuesFor(field) {
  return props.magazines.map((magazine) => {
    if (field === 'calibers')
      return (magazine.calibers ?? props.group?.calibers ?? []).map((item) => item.id);
    if (field === 'firearms')
      return (magazine.compatible_firearms ?? magazine.firearms ?? []).map((item) => item.id);
    return magazine[field] ?? props.group?.[field];
  });
}

function sameSet(left, right) {
  const a = new Set((left ?? []).map((value) => String(value)));
  const b = new Set((right ?? []).map((value) => String(value)));
  return a.size === b.size && [...a].every((value) => b.has(value));
}

function labelForId(items, id) {
  const item = items.find((option) => String(option.id) === String(id));
  return item ? optionLabel(item) : `#${id}`;
}

function displayValue(field, value) {
  if (field === 'calibers') {
    return value.length
      ? value.map((id) => labelForId(calibers.value, id)).join(', ')
      : 'No calibers';
  }
  if (field === 'firearms') {
    return value.length
      ? value.map((id) => labelForId(firearms.value, id)).join(', ')
      : 'No compatible firearms';
  }
  if (field === 'color_id' && value) return labelForId(colors.value, value);
  return value === null || value === undefined || value === '' ? 'blank' : String(value);
}

function summary(field) {
  const values = valuesFor(field);
  if (!values.length) return 'No magazines selected.';
  if (field === 'calibers' || field === 'firearms') {
    const sets = values.map((value) => [...value].map(String).sort().join(','));
    return new Set(sets).size === 1
      ? `All selected: ${displayValue(field, values[0])}`
      : `${new Set(sets).size} distinct values across ${selectedCount.value} magazines`;
  }
  const distinct = new Set(values.map((value) => summaryKey(value)));
  return distinct.size === 1
    ? `All selected: ${displayValue(field, values[0])}`
    : `${distinct.size} distinct values across ${selectedCount.value} magazines`;
}

function targetValue(field) {
  if (field === 'calibers') return form.calibers;
  if (field === 'firearms') return form.firearms;
  if (field === 'manufacturer') return form.manufacturer.trim();
  if (['model_name', 'model_number', 'label', 'serial_number', 'id_marking'].includes(field))
    return form[field].trim() || null;
  if (field === 'color_id') return form.color_id ? Number(form.color_id) : null;
  if (field === 'capacity') return Number(form.capacity);
  return form[field];
}

function fieldChanged(field) {
  const target = targetValue(field);
  return valuesFor(field).some((value) => {
    if (field === 'calibers' || field === 'firearms') return !sameSet(value, target);
    if (field === 'color_id' || field === 'capacity') {
      return (value === null ? null : Number(value)) !== target;
    }
    return value !== target;
  });
}

function fieldStatus(field) {
  if (!apply[field]) return 'KEEP';
  if (
    (field === 'model_name' ||
      field === 'model_number' ||
      field === 'label' ||
      field === 'serial_number' ||
      field === 'id_marking' ||
      field === 'color_id') &&
    targetValue(field) === null
  )
    return 'WILL CLEAR';
  return fieldChanged(field) ? 'WILL CHANGE' : 'NO CHANGE';
}

const identityChange = computed(() =>
  ['manufacturer', 'model_name', 'model_number', 'capacity', 'calibers'].some(
    (field) => apply[field] && fieldChanged(field)
  )
);
const noOp = computed(
  () =>
    hasAppliedField.value &&
    !Object.keys(apply).some((field) => apply[field] && fieldChanged(field))
);

const selectedCaliberIds = computed(() => form.calibers.map((id) => Number(id)));
const selectedFirearmIds = computed(() => form.firearms.map((id) => Number(id)));

function firstValue(field, fallback = '') {
  const values = valuesFor(field);
  return values.length && values.every((value) => summaryKey(value) === summaryKey(values[0]))
    ? (values[0] ?? fallback)
    : fallback;
}

function initializeForm() {
  form.manufacturer = firstValue('manufacturer');
  form.model_name = firstValue('model_name');
  form.model_number = firstValue('model_number');
  form.label = firstValue('label');
  form.serial_number = firstValue('serial_number');
  form.id_marking = firstValue('id_marking');
  form.color_id = firstValue('color_id') ? String(firstValue('color_id')) : '';
  form.capacity = firstValue('capacity') || '';
  form.calibers = (props.magazines[0]?.calibers ?? props.group?.calibers ?? []).map((item) =>
    String(item.id)
  );
  form.firearms = props.magazines[0]?.compatible_firearms?.map((item) => String(item.id)) ?? [];
}

function buildChanges() {
  const changes = {};
  if (apply.manufacturer) changes.manufacturer = form.manufacturer.trim();
  if (apply.model_name) changes.model_name = form.model_name.trim() || null;
  if (apply.model_number) changes.model_number = form.model_number.trim() || null;
  if (apply.label) changes.label = form.label.trim() || null;
  if (apply.serial_number) changes.serial_number = form.serial_number.trim() || null;
  if (apply.id_marking) changes.id_marking = form.id_marking.trim() || null;
  if (apply.color_id) changes.color_id = form.color_id ? Number(form.color_id) : null;
  if (apply.capacity) changes.capacity = Number(form.capacity);
  if (apply.calibers) changes.calibers = selectedCaliberIds.value;
  if (apply.firearms) changes.firearms = selectedFirearmIds.value;
  return changes;
}

function validate() {
  validationError.value = '';
  if (!hasAppliedField.value) validationError.value = 'Choose at least one field to apply.';
  else if (noOp.value)
    validationError.value =
      'All applied fields already have these values. Choose a different value or turn off the applied fields.';
  else if (apply.manufacturer && !form.manufacturer.trim())
    validationError.value = 'Manufacturer cannot be empty.';
  else if (
    apply.capacity &&
    (!Number.isInteger(Number(form.capacity)) || Number(form.capacity) < 1)
  )
    validationError.value = 'Capacity must be a whole number greater than zero.';
  if (validationError.value) nextTick(() => errorSummary.value?.focus());
  return !validationError.value;
}

function submit() {
  if (validate()) emit('save', buildChanges());
}

function close() {
  if (!props.saving) emit('close');
}

function focusableElements() {
  return [
    ...(dialog.value?.querySelectorAll(
      'button:not(:disabled), input:not(:disabled), select:not(:disabled), [tabindex]:not([tabindex="-1"])'
    ) ?? []),
  ];
}

function handleKeydown(event) {
  if (event.key === 'Escape') return close();
  if (event.key !== 'Tab') return;
  const elements = focusableElements();
  if (!elements.length) return;
  const first = elements[0];
  const last = elements.at(-1);
  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault();
    first.focus();
  }
}

async function loadOptions() {
  try {
    const [calibersResponse, colorsResponse, firearmsResponse] = await Promise.all([
      calibersStore.fetchAll(),
      colorsStore.fetchAll(),
      firearmsStore.fetchAll(),
    ]);
    calibers.value = calibersResponse.data ?? [];
    colors.value = colorsResponse.data ?? [];
    firearms.value = firearmsResponse.data ?? [];
    initializeForm();
  } catch {
    loadError.value = true;
  } finally {
    loading.value = false;
  }
}

watch(
  () => props.serverError,
  (value) => {
    if (value) nextTick(() => errorSummary.value?.focus());
  }
);

onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
  nextTick(() => heading.value?.focus());
  loadOptions();
});
onBeforeUnmount(() => document.removeEventListener('keydown', handleKeydown));
</script>

<template>
  <div class="modal-scrim z-50 p-4 sm:p-8" @click.self="close">
    <section
      ref="dialog"
      class="modal-shell flex w-full max-w-3xl flex-col"
      role="dialog"
      aria-modal="true"
      aria-labelledby="bulk-magazine-heading"
    >
      <header class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
        <div>
          <h2
            id="bulk-magazine-heading"
            ref="heading"
            tabindex="-1"
            class="font-display text-lg font-semibold text-ink-900"
          >
            Bulk Edit Magazines
          </h2>
          <p class="mt-1 text-sm text-muted">
            Applying changes to {{ selectedCount }} selected magazines.
          </p>
        </div>
        <button
          type="button"
          class="rounded p-1 text-muted hover:text-ink-900"
          aria-label="Close bulk edit"
          :disabled="saving"
          @click="close"
        >
          <X class="h-5 w-5" />
        </button>
      </header>

      <div class="max-h-[min(72vh,720px)] overflow-y-auto p-5">
        <div v-if="loading" class="py-10 text-center text-sm text-muted">Loading edit options…</div>
        <p
          v-else-if="loadError"
          class="rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
          role="alert"
        >
          The edit options could not be loaded. Close this dialog and try again.
        </p>
        <form id="bulk-magazine-form" v-else class="flex flex-col gap-4" @submit.prevent="submit">
          <p class="rounded border border-brass-300 bg-brass-100 px-3 py-2 text-xs text-brass-900">
            Only fields you switch on are written. Each card shows whether the selected magazines
            are identical or have distinct values. A blank applied value clears nullable fields.
          </p>

          <div
            v-if="serverError"
            ref="errorSummary"
            tabindex="-1"
            class="rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
            role="alert"
          >
            <strong>Nothing was changed.</strong> The bulk update is atomic, so all selected
            magazines remain unchanged. {{ serverError }}
          </div>
          <div
            v-else-if="validationError"
            ref="errorSummary"
            tabindex="-1"
            class="rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
            role="alert"
          >
            {{ validationError }}
          </div>

          <div class="font-mono text-[10px] uppercase tracking-[0.1em] text-muted">Identity</div>
          <div class="grid gap-3 sm:grid-cols-2">
            <MagazineBulkEditFieldCard
              v-for="field in [
                'manufacturer',
                'model_name',
                'model_number',
                'label',
                'serial_number',
                'id_marking',
                'color_id',
                'capacity',
              ]"
              :key="field"
              :name="field"
              :title="
                {
                  manufacturer: 'Manufacturer',
                  model_name: 'Model name',
                  model_number: 'Model #',
                  label: 'Nickname',
                  serial_number: 'Serial number',
                  id_marking: 'ID marking',
                  color_id: 'Color',
                  capacity: 'Capacity',
                }[field]
              "
              :input-id="fieldId(field)"
              :apply="apply[field]"
              :status="fieldStatus(field)"
              :summary="summary(field)"
              @update:apply="apply[field] = $event"
            >
              <template #default="{ ariaDescribedby }">
                <datalist id="magazine-manufacturers">
                  <option
                    v-for="manufacturer in [
                      ...new Set(
                        props.magazines.map((magazine) => magazine.manufacturer).filter(Boolean)
                      ),
                    ]"
                    :key="manufacturer"
                    :value="manufacturer"
                  />
                </datalist>
                <input
                  v-if="field === 'manufacturer'"
                  :id="fieldId(field)"
                  v-model="form.manufacturer"
                  list="magazine-manufacturers"
                  type="text"
                  placeholder="e.g. Magpul"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
                <input
                  v-else-if="field === 'model_name'"
                  :id="fieldId(field)"
                  v-model="form.model_name"
                  type="text"
                  placeholder="Blank clears model name"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
                <input
                  v-else-if="field === 'model_number'"
                  :id="fieldId(field)"
                  v-model="form.model_number"
                  type="text"
                  placeholder="Blank clears model number"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
                <input
                  v-else-if="field === 'label'"
                  :id="fieldId(field)"
                  v-model="form.label"
                  type="text"
                  placeholder="Blank clears nickname"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
                <input
                  v-else-if="field === 'serial_number'"
                  :id="fieldId(field)"
                  v-model="form.serial_number"
                  type="text"
                  placeholder="Blank clears serial number"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
                <input
                  v-else-if="field === 'id_marking'"
                  :id="fieldId(field)"
                  v-model="form.id_marking"
                  type="text"
                  placeholder="Blank clears ID marking"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
                <select
                  v-else-if="field === 'color_id'"
                  :id="fieldId(field)"
                  v-model="form.color_id"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                >
                  <option value="">No color selected</option>
                  <option v-for="color in colors" :key="color.id" :value="String(color.id)">
                    {{ color.label }}
                  </option>
                </select>
                <input
                  v-else
                  :id="fieldId(field)"
                  v-model.number="form.capacity"
                  type="number"
                  min="1"
                  placeholder="e.g. 17"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                />
              </template>
            </MagazineBulkEditFieldCard>
          </div>

          <div class="font-mono text-[10px] uppercase tracking-[0.1em] text-muted">
            Compatibility
          </div>
          <div class="grid gap-3 sm:grid-cols-2">
            <MagazineBulkEditFieldCard
              name="calibers"
              title="Calibers"
              :input-id="fieldId('calibers')"
              :apply="apply.calibers"
              :status="fieldStatus('calibers')"
              :summary="summary('calibers')"
              @update:apply="apply.calibers = $event"
              ><template #default="{ ariaDescribedby }"
                ><MagazineBulkEditOptionPicker
                  v-model="form.calibers"
                  :options="calibers"
                  multiple
                  label="Calibers"
                  placeholder="Search calibers"
                  input-id="bulk-magazine-calibers"
                  :option-label="(item) => item.label"
                  :describedby="ariaDescribedby" /></template
            ></MagazineBulkEditFieldCard>
            <MagazineBulkEditFieldCard
              name="firearms"
              title="Compatible firearms"
              :input-id="fieldId('firearms')"
              :apply="apply.firearms"
              :status="fieldStatus('firearms')"
              :summary="summary('firearms')"
              @update:apply="apply.firearms = $event"
              ><template #default="{ ariaDescribedby }"
                ><MagazineBulkEditOptionPicker
                  v-model="form.firearms"
                  :options="firearms"
                  multiple
                  label="Compatible firearms"
                  placeholder="Search firearms"
                  input-id="bulk-magazine-firearms"
                  :option-label="optionLabel"
                  :describedby="ariaDescribedby" /></template
            ></MagazineBulkEditFieldCard>
          </div>

          <p
            v-if="identityChange"
            class="rounded border border-brass-300 bg-brass-100 px-3 py-2 text-xs text-brass-900"
          >
            These identity changes may move the selected magazines into a different magazine group.
          </p>
        </form>
      </div>

      <footer
        class="flex flex-wrap items-center justify-end gap-2 border-t border-line bg-ink-50 px-5 py-4"
      >
        <span v-if="noOp" class="mr-auto text-xs font-semibold text-success"
          >NO CHANGE — choose a new value to apply.</span
        >
        <button
          type="button"
          class="rounded border border-line bg-white px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50"
          :disabled="saving"
          @click="close"
        >
          Cancel
        </button>
        <button
          type="button"
          data-testid="bulk-magazine-submit"
          class="inline-flex items-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-sm font-semibold text-ink-900 hover:bg-brass-600 disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="loading || loadError || saving || noOp"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />{{
            saving ? 'Saving…' : 'Apply changes'
          }}
        </button>
      </footer>
    </section>
  </div>
</template>
