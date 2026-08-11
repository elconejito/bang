<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import { LoaderCircle, X } from 'lucide-vue-next';
import MagazineBulkEditFieldCard from '@/components/magazines/MagazineBulkEditFieldCard.vue';
import { useAmmunitionStore } from '@/stores/ammunition';

const props = defineProps({
  magazines: { type: Array, required: true },
  locations: { type: Array, default: () => [] },
  saving: { type: Boolean, default: false },
  serverError: { type: String, default: '' },
});
const emit = defineEmits(['close', 'save']);
const ammunitionStore = useAmmunitionStore();
const dialog = ref(null);
const heading = ref(null);
const errorSummary = ref(null);
const ammunition = ref([]);
const loading = ref(true);
const loadError = ref(false);
const validationError = ref('');
const placement = ref('unassigned');
const locationId = ref('');
const contents = ref('empty');
const ammunitionId = ref('');
const loadedRounds = ref(0);
const apply = reactive({ placement: false, contents: false });

const selectedCount = computed(() => props.magazines.length);
const minimumCapacity = computed(() =>
  Math.min(...props.magazines.map((magazine) => Number(magazine.capacity)))
);
const selectedFirearmMagazines = computed(() =>
  props.magazines.filter((magazine) => magazine.current_firearm)
);
const compatibleAmmunition = computed(() => {
  const caliberIds = props.magazines.map(
    (magazine) => new Set((magazine.calibers ?? []).map((caliber) => Number(caliber.id)))
  );

  return ammunition.value.filter((item) =>
    caliberIds.every((ids) => ids.has(Number(item.caliber_id)))
  );
});
const hasAppliedField = computed(() => Object.values(apply).some(Boolean));
const incompleteInput = computed(
  () =>
    (apply.placement && placement.value === 'location' && !locationId.value) ||
    (apply.contents &&
      contents.value === 'loaded' &&
      (!ammunitionId.value || !Number.isInteger(Number(loadedRounds.value))))
);

function fieldId(name) {
  return `bulk-magazine-state-${name}`;
}
function optionLabel(item) {
  return [item.manufacturer, item.label].filter(Boolean).join(' ') || `#${item.id}`;
}
function valuesFor(field) {
  if (field === 'placement')
    return props.magazines.map((magazine) =>
      magazine.current_firearm
        ? `firearm:${magazine.current_firearm.id}`
        : magazine.location?.id
          ? `location:${magazine.location.id}`
          : 'unassigned'
    );
  return props.magazines.map((magazine) => [
    magazine.loaded_ammunition?.id ?? magazine.loaded_ammunition_id ?? null,
    Number(magazine.loaded_rounds ?? 0),
  ]);
}
function targetValue(field) {
  return field === 'placement'
    ? placement.value === 'location'
      ? `location:${locationId.value}`
      : 'unassigned'
    : [
        contents.value === 'loaded' ? Number(ammunitionId.value) : null,
        contents.value === 'loaded' ? Number(loadedRounds.value) : 0,
      ];
}
function fieldChanged(field) {
  const target = targetValue(field);
  return valuesFor(field).some((value) =>
    Array.isArray(value)
      ? Number(value[0] ?? 0) !== Number(target[0] ?? 0) ||
        Number(value[1] ?? 0) !== Number(target[1] ?? 0)
      : value !== target
  );
}
function displayValue(field, value) {
  if (field === 'placement') {
    if (value === 'unassigned') return 'Unassigned';
    if (value.startsWith('location:')) {
      const location = props.locations.find((item) => String(item.id) === value.split(':')[1]);
      return location?.full_label ?? location?.label ?? value;
    }
    return 'In firearm';
  }
  const item = ammunition.value.find((option) => Number(option.id) === Number(value[0]));
  return Number(value[1]) && item ? `${optionLabel(item)} · ${value[1]} rounds` : 'Empty';
}
function summary(field) {
  const values = valuesFor(field);
  const keys = values.map((value) => (Array.isArray(value) ? value.join(':') : value));
  return new Set(keys).size === 1
    ? `All selected: ${displayValue(field, values[0])}`
    : `${new Set(keys).size} distinct values across ${selectedCount.value} magazines`;
}
function status(field) {
  return !apply[field] ? 'KEEP' : fieldChanged(field) ? 'WILL CHANGE' : 'NO CHANGE';
}
const noOp = computed(
  () =>
    hasAppliedField.value &&
    !incompleteInput.value &&
    !Object.keys(apply).some((field) => apply[field] && fieldChanged(field))
);

function initializeForm() {
  const placements = valuesFor('placement');
  if (
    placements.every((value) => value === placements[0]) &&
    placements[0].startsWith('location:')
  ) {
    placement.value = 'location';
    locationId.value = placements[0].split(':')[1];
  }
  const values = valuesFor('contents');
  if (
    values.every((value) => value[0] === values[0][0] && value[1] === values[0][1]) &&
    values[0][0] &&
    values[0][1]
  ) {
    contents.value = 'loaded';
    ammunitionId.value = String(values[0][0]);
    loadedRounds.value = values[0][1];
  }
}
function validate() {
  validationError.value = '';
  if (!hasAppliedField.value) validationError.value = 'Choose at least one field to apply.';
  else if (noOp.value) validationError.value = 'All applied fields already have these values.';
  else if (apply.placement && placement.value === 'location' && !locationId.value)
    validationError.value = 'Choose a storage location.';
  else if (
    apply.contents &&
    contents.value === 'loaded' &&
    (!ammunitionId.value ||
      !Number.isInteger(Number(loadedRounds.value)) ||
      Number(loadedRounds.value) < 1)
  )
    validationError.value = 'Loaded contents need ammunition and at least one round.';
  else if (apply.contents && Number(loadedRounds.value) > minimumCapacity.value)
    validationError.value = `Loaded rounds cannot exceed the smallest selected capacity of ${minimumCapacity.value} rounds.`;
  if (validationError.value) nextTick(() => errorSummary.value?.focus());
  return !validationError.value;
}
function submit() {
  if (!validate()) return;
  const changes = {};
  if (apply.placement)
    changes.location_id = placement.value === 'location' ? Number(locationId.value) : null;
  if (apply.contents) {
    changes.loaded_ammunition_id = contents.value === 'loaded' ? Number(ammunitionId.value) : null;
    changes.loaded_rounds = contents.value === 'loaded' ? Number(loadedRounds.value) : 0;
  }
  emit('save', changes);
}
function close() {
  if (!props.saving) emit('close');
}
function handleKeydown(event) {
  if (event.key === 'Escape') close();
  if (event.key !== 'Tab') return;
  const items = [
    ...(dialog.value?.querySelectorAll(
      'button:not(:disabled), input:not(:disabled), select:not(:disabled), [tabindex]:not([tabindex="-1"])'
    ) ?? []),
  ];
  if (!items.length) return;
  if (event.shiftKey && document.activeElement === items[0]) {
    event.preventDefault();
    items.at(-1).focus();
  } else if (!event.shiftKey && document.activeElement === items.at(-1)) {
    event.preventDefault();
    items[0].focus();
  }
}
watch(
  () => props.serverError,
  (value) => {
    if (value) nextTick(() => errorSummary.value?.focus());
  }
);
onMounted(async () => {
  document.addEventListener('keydown', handleKeydown);
  nextTick(() => heading.value?.focus());
  try {
    ammunition.value = (await ammunitionStore.fetchAll()).data ?? [];
    initializeForm();
  } catch {
    loadError.value = true;
  } finally {
    loading.value = false;
  }
});
onBeforeUnmount(() => document.removeEventListener('keydown', handleKeydown));
</script>

<template>
  <div class="modal-scrim z-50 p-4 sm:p-8" @click.self="close">
    <section
      ref="dialog"
      class="modal-shell flex w-full max-w-2xl flex-col"
      role="dialog"
      aria-modal="true"
      aria-labelledby="bulk-magazine-state-heading"
    >
      <header class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
        <div>
          <h2
            id="bulk-magazine-state-heading"
            ref="heading"
            tabindex="-1"
            class="font-display text-lg font-semibold text-ink-900"
          >
            Bulk State
          </h2>
          <p class="mt-1 text-sm text-muted">
            Apply state changes to {{ selectedCount }} selected magazines.
          </p>
        </div>
        <button
          type="button"
          class="rounded p-1 text-muted hover:text-ink-900"
          aria-label="Close bulk state"
          :disabled="saving"
          @click="close"
        >
          <X class="h-5 w-5" />
        </button>
      </header>
      <div class="max-h-[min(72vh,720px)] overflow-y-auto p-5">
        <div v-if="loading" class="py-10 text-center text-sm text-muted">
          Loading state options…
        </div>
        <p
          v-else-if="loadError"
          class="rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
          role="alert"
        >
          The state options could not be loaded. Close this dialog and try again.
        </p>
        <form v-else class="flex flex-col gap-4" @submit.prevent="submit">
          <p class="rounded border border-brass-300 bg-brass-100 px-3 py-2 text-xs text-brass-900">
            Only fields you switch on are written. Bulk state can eject magazines from firearms but
            never assigns one into a firearm.
          </p>
          <p
            v-if="serverError || validationError"
            ref="errorSummary"
            tabindex="-1"
            class="rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
            role="alert"
          >
            {{ serverError || validationError }}
          </p>
          <MagazineBulkEditFieldCard
            name="placement"
            title="Location"
            :input-id="fieldId('placement')"
            :apply="apply.placement"
            :status="status('placement')"
            :summary="summary('placement')"
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
                id="bulk-magazine-state-location"
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
            title="Loaded"
            :input-id="fieldId('contents')"
            :apply="apply.contents"
            :status="status('contents')"
            :summary="summary('contents')"
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
                  id="bulk-magazine-state-ammunition"
                  v-model="ammunitionId"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                >
                  <option value="">Select ammunition</option>
                  <option
                    v-for="item in compatibleAmmunition"
                    :key="item.id"
                    :value="String(item.id)"
                  >
                    {{ optionLabel(item) }}
                  </option></select
                ><input
                  id="bulk-magazine-state-rounds"
                  v-model.number="loadedRounds"
                  type="number"
                  min="1"
                  :max="minimumCapacity"
                  placeholder="Rounds"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                  :aria-describedby="ariaDescribedby"
                /></div></template
          ></MagazineBulkEditFieldCard>
          <p
            v-if="apply.placement && fieldChanged('placement') && selectedFirearmMagazines.length"
            class="rounded border border-brass-300 bg-brass-100 px-3 py-2 text-xs text-brass-900"
          >
            Changing location will eject {{ selectedFirearmMagazines.length }} selected magazine{{
              selectedFirearmMagazines.length === 1 ? '' : 's'
            }}
            from firearms.
          </p>
        </form>
      </div>
      <footer
        class="flex flex-wrap items-center justify-end gap-2 border-t border-line bg-ink-50 px-5 py-4"
      >
        <span v-if="noOp" class="mr-auto text-xs font-semibold text-success"
          >NO CHANGE — choose a new value to apply.</span
        ><button
          type="button"
          class="rounded border border-line bg-white px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50"
          :disabled="saving"
          @click="close"
        >
          Cancel</button
        ><button
          type="button"
          data-testid="bulk-magazine-state-submit"
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
