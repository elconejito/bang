<script setup>
import { computed, nextTick, onMounted, onBeforeUnmount, reactive, ref, watch } from 'vue';
import { LoaderCircle, X } from 'lucide-vue-next';
import MagazineBulkEditFieldCard from '@/components/magazines/MagazineBulkEditFieldCard.vue';
import MagazineBulkEditOptionPicker from '@/components/magazines/MagazineBulkEditOptionPicker.vue';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';

const props = defineProps({
  magazines: { type: Array, required: true },
  group: { type: Object, default: null },
  locations: { type: Array, default: () => [] },
  saving: { type: Boolean, default: false },
  serverError: { type: String, default: '' },
});
const emit = defineEmits(['close', 'save']);

const ammunitionStore = useAmmunitionStore();
const calibersStore = useCalibersStore();
const colorsStore = useColorsStore();
const firearmsStore = useFirearmsStore();

const dialog = ref(null);
const heading = ref(null);
const loading = ref(true);
const loadError = ref(false);
const validationError = ref('');
const errorSummary = ref(null);
const ammunition = ref([]);
const calibers = ref([]);
const colors = ref([]);
const firearms = ref([]);
const placement = ref('unassigned');
const locationId = ref('');
const contents = ref('empty');
const ammunitionId = ref('');
const loadedRounds = ref(0);

const apply = reactive({
  manufacturer: false,
  model_name: false,
  label: false,
  color_id: false,
  capacity: false,
  calibers: false,
  firearms: false,
  placement: false,
  contents: false,
});
const form = reactive({
  manufacturer: '',
  model_name: '',
  label: '',
  color_id: '',
  capacity: '',
  calibers: [],
  firearms: [],
});

const selectedFirearmMagazines = computed(() =>
  props.magazines.filter((magazine) => magazine.current_firearm)
);
const minimumCapacity = computed(() => {
  const capacities = props.magazines.map((magazine) => Number(magazine.capacity)).filter(Boolean);
  return capacities.length ? Math.min(...capacities) : 0;
});
const effectiveCapacity = computed(() =>
  apply.capacity ? Number(form.capacity) : minimumCapacity.value
);
const selectedAmmunition = computed(() =>
  ammunition.value.find((item) => Number(item.id) === Number(ammunitionId.value))
);
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
    if (field === 'placement') {
      return magazine.current_firearm
        ? `firearm:${magazine.current_firearm.id}`
        : magazine.location?.id
          ? `location:${magazine.location.id}`
          : 'unassigned';
    }
    if (field === 'calibers')
      return (magazine.calibers ?? props.group?.calibers ?? []).map((item) => item.id);
    if (field === 'firearms')
      return (magazine.compatible_firearms ?? magazine.firearms ?? []).map((item) => item.id);
    if (field === 'contents')
      return [
        magazine.loaded_ammunition?.id ?? magazine.loaded_ammunition_id ?? null,
        Number(magazine.loaded_rounds ?? 0),
      ];
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
  if (field === 'placement') {
    if (value === 'unassigned') return 'Unassigned';
    const [type, id] = value.split(':');
    if (type === 'location') {
      const location = props.locations.find((item) => String(item.id) === id);
      return location?.full_label ?? location?.label ?? `Location #${id}`;
    }
    const magazine = props.magazines.find(
      (item) => String(item.current_firearm?.id) === String(id)
    );
    return magazine?.current_firearm
      ? `In ${optionLabel(magazine.current_firearm)}`
      : `In firearm #${id}`;
  }
  if (field === 'contents') {
    const [loadedAmmunitionId, rounds] = value;
    return Number(rounds) > 0 && loadedAmmunitionId
      ? `${labelForId(ammunition.value, loadedAmmunitionId)} · ${rounds} rounds`
      : 'Empty';
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
  if (field === 'placement')
    return placement.value === 'location' ? `location:${locationId.value}` : 'unassigned';
  if (field === 'calibers') return form.calibers;
  if (field === 'firearms') return form.firearms;
  if (field === 'contents')
    return [
      contents.value === 'loaded' ? Number(ammunitionId.value) : null,
      contents.value === 'loaded' ? Number(loadedRounds.value) : 0,
    ];
  if (field === 'manufacturer') return form.manufacturer.trim();
  if (field === 'model_name' || field === 'label') return form[field].trim() || null;
  if (field === 'color_id') return form.color_id ? Number(form.color_id) : null;
  if (field === 'capacity') return Number(form.capacity);
  return form[field];
}

function fieldChanged(field) {
  const target = targetValue(field);
  return valuesFor(field).some((value) => {
    if (field === 'calibers' || field === 'firearms') return !sameSet(value, target);
    if (field === 'contents')
      return (
        Number(value[0] ?? 0) !== Number(target[0] ?? 0) ||
        Number(value[1] ?? 0) !== Number(target[1] ?? 0)
      );
    if (field === 'color_id' || field === 'capacity') {
      return (value === null ? null : Number(value)) !== target;
    }
    return value !== target;
  });
}

function fieldStatus(field) {
  if (!apply[field]) return 'KEEP';
  if (
    (field === 'model_name' || field === 'label' || field === 'color_id') &&
    targetValue(field) === null
  )
    return 'WILL CLEAR';
  return fieldChanged(field) ? 'WILL CHANGE' : 'NO CHANGE';
}

const identityChange = computed(() =>
  ['manufacturer', 'model_name', 'capacity', 'calibers'].some(
    (field) => apply[field] && fieldChanged(field)
  )
);
const placementChange = computed(() => apply.placement && fieldChanged('placement'));
const incompleteInput = computed(
  () =>
    (apply.contents &&
      contents.value === 'loaded' &&
      (!ammunitionId.value || !Number.isInteger(Number(loadedRounds.value)))) ||
    (apply.placement && placement.value === 'location' && !locationId.value)
);
const noOp = computed(
  () =>
    hasAppliedField.value &&
    !incompleteInput.value &&
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
  form.label = firstValue('label');
  form.color_id = firstValue('color_id') ? String(firstValue('color_id')) : '';
  form.capacity = firstValue('capacity') || '';
  form.calibers = (props.magazines[0]?.calibers ?? props.group?.calibers ?? []).map((item) =>
    String(item.id)
  );
  form.firearms = props.magazines[0]?.compatible_firearms?.map((item) => String(item.id)) ?? [];
  const placements = valuesFor('placement');
  if (
    placements.length &&
    placements.every((value) => value === placements[0]) &&
    placements[0].startsWith('location:')
  ) {
    placement.value = 'location';
    locationId.value = placements[0].split(':')[1];
  }
  const firstContents = valuesFor('contents')[0] ?? [null, 0];
  ammunitionId.value = firstContents[0] ? String(firstContents[0]) : '';
  loadedRounds.value = Number(firstContents[1] ?? 0);
  const contentValues = valuesFor('contents');
  if (
    firstContents[0] &&
    Number(firstContents[1]) > 0 &&
    contentValues.every(
      (value) =>
        Number(value[0]) === Number(firstContents[0]) &&
        Number(value[1]) === Number(firstContents[1])
    )
  ) {
    contents.value = 'loaded';
  }
}

function buildChanges() {
  const changes = {};
  if (apply.manufacturer) changes.manufacturer = form.manufacturer.trim();
  if (apply.model_name) changes.model_name = form.model_name.trim() || null;
  if (apply.label) changes.label = form.label.trim() || null;
  if (apply.color_id) changes.color_id = form.color_id ? Number(form.color_id) : null;
  if (apply.capacity) changes.capacity = Number(form.capacity);
  if (apply.calibers) changes.calibers = selectedCaliberIds.value;
  if (apply.firearms) changes.firearms = selectedFirearmIds.value;
  if (apply.placement)
    changes.location_id =
      placement.value === 'location' && locationId.value ? Number(locationId.value) : null;
  if (apply.contents) {
    changes.loaded_ammunition_id = contents.value === 'loaded' ? Number(ammunitionId.value) : null;
    changes.loaded_rounds = contents.value === 'loaded' ? Number(loadedRounds.value) : 0;
  }
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
  else if (apply.placement && placement.value === 'location' && !locationId.value)
    validationError.value = 'Choose a storage location.';
  else if (apply.contents && contents.value === 'loaded') {
    const rounds = Number(loadedRounds.value);
    if (!ammunitionId.value || !Number.isInteger(rounds) || rounds < 1)
      validationError.value = 'Loaded contents need ammunition and at least one round.';
    else if (rounds > effectiveCapacity.value)
      validationError.value = `Loaded rounds cannot exceed the effective capacity of ${effectiveCapacity.value} rounds.`;
    else if (
      apply.calibers &&
      selectedAmmunition.value?.caliber_id &&
      !selectedCaliberIds.value.includes(Number(selectedAmmunition.value.caliber_id))
    )
      validationError.value =
        'The selected ammunition caliber must be included in the applied calibers.';
  }
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
    const [ammunitionResponse, calibersResponse, colorsResponse, firearmsResponse] =
      await Promise.all([
        ammunitionStore.fetchAll(),
        calibersStore.fetchAll(),
        colorsStore.fetchAll(),
        firearmsStore.fetchAll(),
      ]);
    ammunition.value = ammunitionResponse.data ?? [];
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
              v-for="field in ['manufacturer', 'model_name', 'label', 'color_id', 'capacity']"
              :key="field"
              :name="field"
              :title="
                {
                  manufacturer: 'Manufacturer',
                  model_name: 'Model name',
                  label: 'Nickname',
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
                  v-else-if="field === 'label'"
                  :id="fieldId(field)"
                  v-model="form.label"
                  type="text"
                  placeholder="Blank clears nickname"
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

          <div class="font-mono text-[10px] uppercase tracking-[0.1em] text-muted">State</div>
          <div class="grid gap-3 sm:grid-cols-2">
            <MagazineBulkEditFieldCard
              name="placement"
              title="Placement"
              :input-id="fieldId('placement')"
              :apply="apply.placement"
              :status="fieldStatus('placement')"
              :summary="summary('placement')"
              description="Bulk edit can eject magazines from firearms but never assigns one into a firearm."
              @update:apply="apply.placement = $event"
              ><template #default="{ ariaDescribedby }"
                ><div class="grid gap-2">
                  <label
                    class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-brass-100"
                    ><input
                      v-model="placement"
                      name="bulk-placement"
                      type="radio"
                      value="unassigned"
                      :aria-describedby="ariaDescribedby"
                    />Unassigned</label
                  ><label
                    class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-brass-100"
                    ><input
                      v-model="placement"
                      name="bulk-placement"
                      type="radio"
                      value="location"
                      :aria-describedby="ariaDescribedby"
                    />Storage location</label
                  >
                </div>
                <select
                  v-if="placement === 'location'"
                  id="bulk-magazine-location"
                  v-model="locationId"
                  class="mt-2 w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                >
                  <option value="">Select a location</option>
                  <option
                    v-for="location in locations"
                    :key="location.id"
                    :value="String(location.id)"
                  >
                    {{ location.full_label ?? location.label }}
                  </option>
                </select></template
              ></MagazineBulkEditFieldCard
            >
            <MagazineBulkEditFieldCard
              name="contents"
              title="Contents"
              :input-id="fieldId('contents')"
              :apply="apply.contents"
              :status="fieldStatus('contents')"
              :summary="summary('contents')"
              description="Contents remain unchanged unless Contents is applied."
              @update:apply="apply.contents = $event"
              ><template #default="{ ariaDescribedby }"
                ><div class="grid gap-2">
                  <label
                    class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-brass-100"
                    ><input
                      v-model="contents"
                      name="bulk-contents"
                      type="radio"
                      value="empty"
                      :aria-describedby="ariaDescribedby"
                    />Empty</label
                  ><label
                    class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-brass-100"
                    ><input
                      v-model="contents"
                      name="bulk-contents"
                      type="radio"
                      value="loaded"
                      :aria-describedby="ariaDescribedby"
                    />Loaded</label
                  >
                </div>
                <div v-if="contents === 'loaded'" class="mt-2 grid gap-3 sm:grid-cols-[1fr_120px]">
                  <select
                    id="bulk-magazine-ammunition"
                    v-model="ammunitionId"
                    class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                    :aria-describedby="ariaDescribedby"
                  >
                    <option value="">Select ammunition</option>
                    <option v-for="item in ammunition" :key="item.id" :value="String(item.id)">
                      {{ optionLabel(item) }}
                    </option></select
                  ><input
                    id="bulk-magazine-rounds"
                    v-model.number="loadedRounds"
                    type="number"
                    min="1"
                    :max="effectiveCapacity"
                    placeholder="Rounds"
                    class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                    :aria-describedby="ariaDescribedby"
                  /></div></template
            ></MagazineBulkEditFieldCard>
          </div>

          <p
            v-if="placementChange && selectedFirearmMagazines.length"
            class="rounded border border-brass-300 bg-brass-100 px-3 py-2 text-xs text-brass-900"
          >
            <strong
              >Placement change will eject {{ selectedFirearmMagazines.length }} selected magazine{{
                selectedFirearmMagazines.length === 1 ? '' : 's'
              }}
              from firearms.</strong
            >
            {{
              selectedFirearmMagazines
                .slice(0, 3)
                .map(
                  (magazine) =>
                    `${magazine.id_marking || `#${magazine.id}`} (${optionLabel(magazine.current_firearm)})`
                )
                .join(', ')
            }}<span v-if="selectedFirearmMagazines.length > 3">
              +{{ selectedFirearmMagazines.length - 3 }} more</span
            >. Contents remain unchanged unless Contents is also applied.
          </p>
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
